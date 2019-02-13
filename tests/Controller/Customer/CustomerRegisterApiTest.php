<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Customer;

use PHPUnit\Framework\Assert;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\PurgeSpooledMessagesTrait;

final class CustomerRegisterApiTest extends JsonApiTestCase
{
    use PurgeSpooledMessagesTrait;

    /**
     * @test
     */
    public function it_allows_to_register_in_shop_and_sends_a_verification_email_if_channel_requires_verification()
    {
        $this->loadFixturesFromFiles(['channel.yml']);

        $data =
<<<EOT
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "email": "vinny@fandf.com",
            "plainPassword": "somepass"
        }
EOT;

        $this->client->request('POST', '/shop-api/WEB_GB/register', [], [], self::CONTENT_TYPE_HEADER, $data);

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
    public function it_allows_to_register_in_shop_and_automatically_enables_user_if_channel_does_not_require_verification()
    {
        $this->loadFixturesFromFiles(['channel.yml']);

        $data =
<<<EOT
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "email": "vinny@fandf.com",
            "plainPassword": "somepass"
        }
EOT;

        $this->client->request('POST', '/shop-api/WEB_DE/register', [], [], self::CONTENT_TYPE_HEADER, $data);

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
    public function it_does_not_allow_to_register_in_shop_if_email_is_already_taken()
    {
        $this->loadFixturesFromFiles(['customer.yml', 'channel.yml']);

        $data =
<<<EOT
        {
            "firstName": "Oliver",
            "lastName": "Queen",
            "email": "oliver@queen.com",
            "plainPassword": "somepass"
        }
EOT;

        $this->client->request('POST', '/shop-api/WEB_GB/register', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'customer/validation_registration_email_taken_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_register_in_shop_without_passing_required_data()
    {
        $this->loadFixturesFromFiles(['channel.yml']);

        $data =
<<<EOT
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "plainPassword": "somepass"
        }
EOT;

        $this->client->request('POST', '/shop-api/WEB_GB/register', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'customer/validation_registration_data_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_register_in_non_existent_channel()
    {
        $this->loadFixturesFromFiles(['channel.yml']);

        $data =
<<<EOT
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "plainPassword": "somepass"
        }
EOT;

        $this->client->request('POST', '/shop-api/SPACE_KLINGON/register', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    protected function getContainer(): ContainerInterface
    {
        return static::$sharedKernel->getContainer();
    }
}
