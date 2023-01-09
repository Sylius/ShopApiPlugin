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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\MailerAssertionsTrait;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\PurgeMessagesTrait;

final class RequestPasswordResettingApiTest extends JsonApiTestCase
{
    use PurgeMessagesTrait;
    use MailerAssertionsTrait;

    /**
     * @test
     */
    public function it_allows_to_reset_user_password(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml']);

        $data = '{"email": "oliver@queen.com"}';

        $this->client->request('PUT', '/shop-api/request-password-reset', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        if (!self::isSymfonyMailerAvailable()) {
            /** @var EmailCheckerInterface $emailChecker */
            $emailChecker = $this->get('sylius.behat.email_checker');
            Assert::assertTrue($emailChecker->hasRecipient('oliver@queen.com'));
        } else {
            $email = self::getMailerMessage();
            $this->assertEmailAddressContains($email, 'to', 'oliver@queen.com');
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_reset_user_password_without_entering_valid_email(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml']);

        $data = '{"email": "oliver"}';

        $this->client->request('PUT', '/shop-api/request-password-reset', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/request_reset_password_failed_email', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_reset_user_password_without_entering_email(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml']);

        $data = '{}';

        $this->client->request('PUT', '/shop-api/request-password-reset', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/request_reset_password_empty_email', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allow_to_reset_user_password_without_sending_mail_user_not_exist(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml']);

        $data = '{"email": "Amr@amr.com"}';

        $this->client->request('PUT', '/shop-api/request-password-reset', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    protected static function getContainer(): ContainerInterface
    {
        return static::$sharedKernel->getContainer();
    }
}
