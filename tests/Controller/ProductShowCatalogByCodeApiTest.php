<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductShowCatalogByCodeApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_paginated_products_from_some_taxon_by_code()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxon-products/BRAND?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page_by_code_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_products_for_sub_taxons_by_code()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxon-products/WOMEN_T_SHIRTS?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_t_shirt_list_page_by_code_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_paginated_products_from_some_taxon_by_code_in_different_language()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxon-products/BRAND?channel=WEB_GB&locale=de_DE', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/german_product_list_page_by_code_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_second_page_of_paginated_products_from_some_taxon_by_code()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxon-products/BRAND?channel=WEB_GB&limit=1&page=2', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/limited_product_list_page_by_code_response', Response::HTTP_OK);
    }

    /**
     * @test
     * @group filtered
     */
    public function it_shows_paginated_products_from_some_taxon_by_code_boolean_filtered()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxon-products/FRUITS?channel=WEB_FR&filters[boolean][variants.shippingRequired]=false', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_by_code_boolean_filtered_false_response', Response::HTTP_OK);

        $this->client->request('GET', '/shop-api/taxon-products/FRUITS?channel=WEB_FR&&filters[boolean][variants.shippingRequired]=true', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_by_code_boolean_filtered_true_response', Response::HTTP_OK);
    }

    /**
     * @test
     * @group filtered
     */
    public function it_shows_paginated_products_from_some_taxon_by_code_string_filtered()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxon-products/FRUITS?channel=WEB_FR&filters[search][translations.name][partial][]=bana', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_by_code_search_partial_filtered_true_response', Response::HTTP_OK);

        $this->client->request('GET', '/shop-api/taxon-products/FRUITS?channel=WEB_FR&filters[search][translations.name][exact][]=Banane', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_by_code_search_exact_filtered_true_response', Response::HTTP_OK);

        $this->client->request('GET', '/shop-api/taxon-products/FRUITS?channel=WEB_FR&filters[search][translations.name][start][]=Ban', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_by_code_search_start_filtered_true_response', Response::HTTP_OK);

        $this->client->request('GET', '/shop-api/taxon-products/FRUITS?channel=WEB_FR&filters[search][translations.name][end][]=ane', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_by_code_search_end_filtered_true_response', Response::HTTP_OK);

        $this->client->request('GET', '/shop-api/taxon-products/FRUITS?channel=WEB_FR&filters[search][translations.name][partial][]=erry', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_by_code_search_partial_filtered_false_response', Response::HTTP_OK);

        $this->client->request('GET', '/shop-api/taxon-products/FRUITS?channel=WEB_FR&filters[search][translations.name][exact][]=Cherry', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_by_code_search_exact_filtered_false_response', Response::HTTP_OK);

        $this->client->request('GET', '/shop-api/taxon-products/FRUITS?channel=WEB_FR&filters[search][translations.name][start][]=Che', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_by_code_search_start_filtered_false_response', Response::HTTP_OK);

        $this->client->request('GET', '/shop-api/taxon-products/FRUITS?channel=WEB_FR&filters[search][translations.name][end][]=rry', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_by_code_search_end_filtered_false_response', Response::HTTP_OK);
    }
}
