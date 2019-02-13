<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Product;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class ProductShowCatalogBySlugApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_paginated_products_from_some_taxon_by_slug(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/taxon-products-by-slug/brands', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page_by_slug_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_products_for_sub_taxons_by_slug(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/taxon-products-by-slug/categories/t-shirts/women-t-shirts', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_t_shirt_list_page_by_slug_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_paginated_products_from_some_taxon_by_slug_in_different_language(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/taxon-products-by-slug/marken?locale=de_DE', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/german_product_list_page_by_slug_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_second_page_of_paginated_products_from_some_taxon_by_slug(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/taxon-products-by-slug/brands?limit=1&page=2', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/limited_product_list_page_by_slug_response', Response::HTTP_OK);
    }

    /**
     * TODO check is it possible (test annotation make it fail)
     */
    public function it_shows_sorted_product_list(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/taxon-products-by-slug/x-man?sorting[createdAt]=desc', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page_by_slug_response', Response::HTTP_OK);
    }

    /**
     * TODO check is it possible (test annotation make it fail)
     */
    public function it_expose_only_some_of_products_in_the_list(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/taxon-products-by-slug/x-man?criteria[search][value]=Logans+Hat', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page_by_slug_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_show_product_catalog_by_slug_in_non_existent_channel(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/SPACE_KLINGON/taxon-products-by-slug/brands', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }
}
