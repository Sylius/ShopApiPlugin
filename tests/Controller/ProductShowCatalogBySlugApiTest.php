<?php

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductShowCatalogBySlugApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_paginated_products_from_some_taxon_by_slug()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxon-products-by-slug/brands?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_products_for_sub_taxons_by_slug()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxon-products-by-slug/categories/t-shirts/women-t-shirts?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_t_shirt_list_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_paginated_products_from_some_taxon_by_slug_in_different_language()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxon-products-by-slug/marken?channel=WEB_GB&locale=de_DE', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/german_product_list_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_second_page_of_paginated_products_from_some_taxon_by_slug()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxon-products-by-slug/brands?channel=WEB_GB&limit=1&page=2', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/limited_product_list_page', Response::HTTP_OK);
    }

    public function it_shows_sorted_product_list()
    {
        $this->client->request('GET', '/shop-api/taxon-products-by-slug/x-man?channel=WEB_GB&sorting[createdAt]=desc', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page', Response::HTTP_OK);
    }

    public function it_expose_only_some_of_products_in_the_list()
    {
        $this->client->request('GET', '/shop-api/taxon-products-by-slug/x-man?channel=WEB_GB&criteria[search][value]=Logans+Hat', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page', Response::HTTP_OK);
    }
}
