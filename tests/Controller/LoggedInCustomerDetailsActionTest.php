<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Customer;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class LoggedInCustomerDetailsActionTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_currently_logged_in_customer_details()
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

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $response['token']));

        $this->client->request('GET', '/me', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/logged_in_customer_detail_response', Response::HTTP_OK);
    }
}
