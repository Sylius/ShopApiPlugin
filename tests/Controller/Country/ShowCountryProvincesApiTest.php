<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Country;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class ShowCountryProvincesApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_list_of_all_countries_with_fallback_locale_from_channel(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/countries/GB/provinces', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'country/one_of_countries_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_list_of_all_countries_in_different_language(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/countries/GB/provinces?locale=en_GB', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'country/one_of_countries_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_fails_with_404_when_country_code_does_not_exist(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/countries/XX/provinces', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'country/one_of_countries_response', Response::HTTP_NOT_FOUND);
    }
}
