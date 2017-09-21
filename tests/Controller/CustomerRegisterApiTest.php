<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\PurgeSpooledMessagesTrait;

final class CustomerRegisterApiTest extends JsonApiTestCase
{
    use PurgeSpooledMessagesTrait;

    /**
     * @test
     */
    public function it_allows_to_register_in_shop_and_sends_a_verification_email()
    {
        $this->loadFixturesFromFile('channel.yml');

        $data =
<<<EOT
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "email": "vinny@fandf.com",
            "plainPassword": "somepass",
            "channel": "WEB_GB"
        }
EOT;

        $this->client->request('POST', '/shop-api/register', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        /** @var EmailCheckerInterface $emailChecker */
        $emailChecker = $this->get('sylius.behat.email_checker');

        $this->assertTrue($emailChecker->hasRecipient('vinny@fandf.com'));
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_register_in_shop_if_email_is_already_taken()
    {
        $this->loadFixturesFromFile('customer.yml');
        $this->loadFixturesFromFile('channel.yml');

        $data =
<<<EOT
        {
            "firstName": "Oliver",
            "lastName": "Queen",
            "email": "oliver@queen.com",
            "plainPassword": "somepass",
            "channel": "WEB_GB"
        }
EOT;

        $this->client->request('POST', '/shop-api/register', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'customer/validation_registration_email_taken_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_register_in_shop_without_passing_required_data()
    {
        $this->loadFixturesFromFile('channel.yml');

        $data =
<<<EOT
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "plainPassword": "somepass",
            "channel": "WEB_GB"
        }
EOT;

        $this->client->request('POST', '/shop-api/register', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'customer/validation_registration_data_response', Response::HTTP_BAD_REQUEST);
    }

    protected function getContainer(): ContainerInterface
    {
        return static::$sharedKernel->getContainer();
    }
}
