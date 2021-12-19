<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Country;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class ShowCountriesApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_list_of_all_countries_with_fallback_locale_from_channel(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/countries', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'country/all_countries_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_list_of_all_countries_in_different_language(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/countries?locale=en_GB', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'country/all_countries_response', Response::HTTP_OK);
    }
}
