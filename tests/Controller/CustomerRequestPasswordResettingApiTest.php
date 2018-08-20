<?php

declare(strict_types=1);

namespace Tests\Sylius\SyliusShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\SyliusShopApiPlugin\Controller\Utils\PurgeSpooledMessagesTrait;

final class CustomerRequestPasswordResettingApiTest extends JsonApiTestCase
{
    use PurgeSpooledMessagesTrait;

    /**
     * @test
     */
    public function it_allows_to_reset_user_password()
    {
        $this->loadFixturesFromFile('customer.yml');

        $data = '{"email": "oliver@queen.com"}';

        $this->client->request('PUT', '/shop-api/request-password-reset', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        /** @var EmailCheckerInterface $emailChecker */
        $emailChecker = $this->get('sylius.behat.email_checker');

        $this->assertTrue($emailChecker->hasRecipient('oliver@queen.com'));
    }

    protected function getContainer(): ContainerInterface
    {
        return static::$sharedKernel->getContainer();
    }
}
