<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class CustomerShopApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_allows_to_create_customer_with_user_account()
    {
        $data =
<<<EOT
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "email": "vinny@fandf.com",
            "user": {
                "plainPassword" : {
                    "first": "somepass",
                    "second": "somepass"
                }
            }
        }
EOT;

        $this->client->request('POST', '/shop-api/register', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_to_log_customer_in()
    {
        $this->loadFixturesFromFile('customer.yml');

        $data =
<<<EOT
        {
            "_username": "oliver@queen.com",
            "_password": "123pa\$\$word"
        }
EOT;

        $this->client->request('POST', '/shop-api/login_check', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/customer_log_in_response', Response::HTTP_OK);
    }
}
