<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Customer;

use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\PurgeSpooledMessagesTrait;

final class VerifyApiTest extends JsonApiTestCase
{
    use PurgeSpooledMessagesTrait;

    /**
     * @test
     */
    public function it_allows_to_verify_customer(): void
    {
        $this->loadFixturesFromFiles(['channel.yml']);

        $data =
<<<JSON
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "email": "vinny@fandf.com",
            "plainPassword": "somepass"
        }
JSON;

        $this->client->request('POST', '/shop-api/register', [], [], self::CONTENT_TYPE_HEADER, $data);

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->get('sylius.repository.shop_user');
        $user = $userRepository->findOneByEmail('vinny@fandf.com');

        $parameters = ['token' => $user->getEmailVerificationToken()];

        $this->client->request('GET', '/shop-api/verify-account', $parameters, [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    protected function getContainer(): ContainerInterface
    {
        return static::$sharedKernel->getContainer();
    }
}
