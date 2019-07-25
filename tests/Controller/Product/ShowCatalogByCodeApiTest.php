<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Product;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class ShowCatalogByCodeApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_paginated_products_from_some_taxon_by_code(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/taxon-products/by-code/BRAND', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page_by_code_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_products_for_sub_taxons_by_code(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/taxon-products/by-code/WOMEN_T_SHIRTS', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_t_shirt_list_page_by_code_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_paginated_products_from_some_taxon_by_code_in_different_language(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/taxon-products/by-code/BRAND?locale=de_DE', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/german_product_list_page_by_code_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_second_page_of_paginated_products_from_some_taxon_by_code(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/taxon-products/by-code/BRAND?limit=1&page=2', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/limited_product_list_page_by_code_response', Response::HTTP_OK);
    }
}
