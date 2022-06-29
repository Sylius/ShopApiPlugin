<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    /**
     * @test
     */
    public function it_does_not_allow_to_verify_account_without_required_data(): void
    {
        $response = $this->verifyAccount(null);
        $this->assertResponse($response, 'customer/verify_account_required_data', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_verify_customer_and_returns_properly_error_code(): void
    {
        $response = $this->verifyAccount('token');
        $this->assertResponse($response, 'customer/verify_account_token_not_exists', Response::HTTP_BAD_REQUEST);
    }

    private function verifyAccount(?string $token): Response
    {
        $this->client->request(
            'GET',
            '/shop-api/verify-account',
            $token !== null ? ['token' => $token] : [],
            [],
            self::CONTENT_TYPE_HEADER,
        );

        return $this->client->getResponse();
    }

    protected static function getContainer(): ContainerInterface
    {
        return static::$sharedKernel->getContainer();
    }
}
