<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\PurgeSpooledMessagesTrait;

final class CustomerResendVerificationTokenApiTest extends JsonApiTestCase
{
    use PurgeSpooledMessagesTrait;

    /**
     * @test
     */
    public function it_allows_to_resend_verification_token()
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

        $resendForEmail = '{"email": "vinny@fandf.com"}';

        $this->client->request('POST', '/shop-api/resend-verification-link', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $resendForEmail);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_CREATED);

        /** @var EmailCheckerInterface $emailChecker */
        $emailChecker = $this->get('sylius.behat.email_checker');

        $this->assertSame(2, $emailChecker->countMessagesTo('vinny@fandf.com'));
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_resend_verification_email_if_email_is_not_defined()
    {
        $this->client->request('POST', '/shop-api/resend-verification-link', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/validation_email_not_defined_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_resend_verification_email_if_email_is_not_malformed()
    {
        $resendForEmail = '{"email": "vinnyfandf.com"}';

        $this->client->request('POST', '/shop-api/resend-verification-link', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $resendForEmail);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/validation_email_not_valid_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_resend_verification_email_if_customer_does_not_exists()
    {
        $resendForEmail = '{"email": "vinny@fandf.com"}';

        $this->client->request('POST', '/shop-api/resend-verification-link', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $resendForEmail);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/validation_email_not_found_response', Response::HTTP_BAD_REQUEST);
    }

    protected function getContainer(): ContainerInterface
    {
        return static::$sharedKernel->getContainer();
    }
}
