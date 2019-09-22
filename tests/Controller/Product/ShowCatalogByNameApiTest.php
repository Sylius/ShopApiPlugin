<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Product;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class ShowCatalogByNameApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_paginated_products_for_phrase(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/products/by-phrase/mug', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page_by_phrase_mug_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_paginated_products_for_phrase_including_description(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/products/by-phrase/Lorem', ['includeDescription' => true], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page_by_phrase_mug_including_description_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_paginated_products_for_phrase_including_short_description(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/products/by-phrase/short', ['includeDescription' => true], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page_by_phrase_mug_including_short_description_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_paginated_products_for_phrase_in_different_language(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/products/by-phrase/becher?locale=de_DE', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page_by_phrase_mug_in_german_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_paginated_products_for_phrase_in_different_language_including_description(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/products/by-phrase/Beschreibung?locale=de_DE', ['includeDescription' => true], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page_by_phrase_mug_in_german_including_description_response', Response::HTTP_OK);
    }
}
