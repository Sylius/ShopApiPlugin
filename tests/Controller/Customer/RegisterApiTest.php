<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Customer;

use PHPUnit\Framework\Assert;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\PurgeSpooledMessagesTrait;

final class RegisterApiTest extends JsonApiTestCase
{
    use PurgeSpooledMessagesTrait;

    /**
     * @test
     */
    public function it_allows_to_register_in_shop_and_sends_a_verification_email_if_channel_requires_verification(): void
    {
        $this->loadFixturesFromFiles(['channel.yml']);

        $data =
<<<JSON
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "email": "vinny@fandf.com",
            "plainPassword": "bananas1234"
        }
JSON;

        $this->client->request('POST', '/shop-api/register', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->get('sylius.repository.shop_user');
        $user = $userRepository->findOneByEmail('vinny@fandf.com');

        Assert::assertNotNull($user);
        Assert::assertFalse($user->isEnabled());

        /** @var EmailCheckerInterface $emailChecker */
        $emailChecker = $this->get('sylius.behat.email_checker');
        Assert::assertTrue($emailChecker->hasRecipient('vinny@fandf.com'));
    }

    /**
     * @test
     */
    public function it_allows_to_register_a_customer_if_the_customer_did_a_guest_checkout_already(): void
    {
        $this->loadFixturesFromFiles(['channel.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new AssignCustomerToCart($token, 'vinny@fandf.com'));

        $data =
<<<JSON
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "email": "vinny@fandf.com",
            "plainPassword": "bananas1234"
        }
JSON;

        $this->client->request('POST', '/shop-api/register', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->get('sylius.repository.shop_user');
        $user = $userRepository->findOneByEmail('vinny@fandf.com');

        Assert::assertNotNull($user);
        Assert::assertFalse($user->isEnabled());

        /** @var EmailCheckerInterface $emailChecker */
        $emailChecker = $this->get('sylius.behat.email_checker');
        Assert::assertTrue($emailChecker->hasRecipient('vinny@fandf.com'));
    }

    /**
     * @test
     */
    public function it_allows_to_register_in_shop_and_automatically_enables_user_if_channel_does_not_require_verification(): void
    {
        $this->loadFixturesFromFiles(['channel.yml']);

        $data =
<<<JSON
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "email": "vinny@fandf.com",
            "plainPassword": "12345password"
        }
JSON;

        $this->client->request('POST', 'http://web-de.com/shop-api/register', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->get('sylius.repository.shop_user');
        $user = $userRepository->findOneByEmail('vinny@fandf.com');

        Assert::assertNotNull($user);
        Assert::assertTrue($user->isEnabled());

        /** @var EmailCheckerInterface $emailChecker */
        $emailChecker = $this->get('sylius.behat.email_checker');

        try {
            Assert::assertFalse($emailChecker->hasRecipient('vinny@fandf.com'));
        } catch (\InvalidArgumentException $exception) {
            // Email checker throws an invalid argument exception if spool directory does not exist
            // It means no mails were sent
            // Should be fixed in Sylius though
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_register_in_shop_if_email_is_already_taken(): void
    {
        $this->loadFixturesFromFiles(['customer.yml', 'channel.yml']);

        $data =
<<<JSON
        {
            "firstName": "Oliver",
            "lastName": "Queen",
            "email": "oliver@queen.com",
            "plainPassword": "somepass"
        }
JSON;

        $this->client->request('POST', '/shop-api/register', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'customer/validation_registration_email_taken_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_register_in_shop_without_passing_required_data(): void
    {
        $this->loadFixturesFromFiles(['channel.yml']);

        $data =
<<<JSON
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "plainPassword": "somepass"
        }
JSON;

        $this->client->request('POST', '/shop-api/register', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'customer/validation_registration_data_response', Response::HTTP_BAD_REQUEST);
    }

    protected static function getContainer(): ContainerInterface
    {
        return static::$sharedKernel->getContainer();
    }
}
