<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Customer;

use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\PurgeSpooledMessagesTrait;

final class CustomerRequestPasswordResettingApiTest extends JsonApiTestCase
{
    use PurgeSpooledMessagesTrait;

    /**
     * @test
     */
    public function it_allows_to_reset_user_password()
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml']);

        $data = '{"email": "oliver@queen.com"}';

        $this->client->request('PUT', '/shop-api/WEB_GB/request-password-reset', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        /** @var EmailCheckerInterface $emailChecker */
        $emailChecker = $this->get('sylius.behat.email_checker');

        $this->assertTrue($emailChecker->hasRecipient('oliver@queen.com'));
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_reset_user_password_in_non_existent_channel()
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml']);

        $data = '{"email": "oliver@queen.com"}';

        $this->client->request('PUT', '/shop-api/SPACE_KLINGON/request-password-reset', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    protected function getContainer(): ContainerInterface
    {
        return static::$sharedKernel->getContainer();
    }
}
