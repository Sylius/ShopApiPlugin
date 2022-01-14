<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Checkout;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

class CountriesApiTest extends JsonApiTestCase
{

    /**
     * @test
     */
    public function it_shows_list_of_all_countries(): void
    {
        $this->loadFixturesFromFiles(['country.yml']);

        $this->client->request('GET', '/shop-api/checkout/countries', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'checkout/all_countries_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_country_details_by_code(): void
    {
        $this->loadFixturesFromFiles(['country.yml']);

        $this->client->request('GET', '/shop-api/checkout/countries/GB', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'checkout/one_of_countries_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_fails_with_404_when_country_code_does_not_exist(): void
    {
        $this->loadFixturesFromFiles(['country.yml']);

        $this->client->request('GET', '/shop-api/checkout/countries/XX', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }
}
