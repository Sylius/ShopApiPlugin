<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\PurgeSpooledMessagesTrait;

final class CustomerVerifyApiTest extends JsonApiTestCase
{
    use PurgeSpooledMessagesTrait;

    /**
     * @test
     */
    public function it_allows_to_verify_customer()
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

        $this->client->request('POST', '/shop-api/WEB_GB/register', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->get('sylius.repository.shop_user');
        $user = $userRepository->findOneByEmail('vinny@fandf.com');

        $verifyEmail = sprintf('{"token": "%s"}', $user->getEmailVerificationToken());

        $this->client->request('PUT', '/shop-api/WEB_GB/verify-account', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $verifyEmail);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_verify_customer_in_non_existent_channel()
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

        $this->client->request('POST', '/shop-api/WEB_GB/register', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->get('sylius.repository.shop_user');
        $user = $userRepository->findOneByEmail('vinny@fandf.com');

        $verifyEmail = sprintf('{"token": "%s"}', $user->getEmailVerificationToken());

        $this->client->request('PUT', '/shop-api/SPACE_KLINGON/verify-account', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $verifyEmail);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    protected function getContainer(): ContainerInterface
    {
        return static::$sharedKernel->getContainer();
    }
}
