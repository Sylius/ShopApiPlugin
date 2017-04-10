<?php

namespace Tests\Acme\ExampleBundle\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductShopAPITest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_simple_product_details_page()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products/logan-mug?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/simple_product_details_page', Response::HTTP_OK);
    }

    public function it_shows_simple_product_details_page_in_different_locale()
    {
        $this->client->request('GET', '/shop-api/products/logan-mug?channel=WEB_GB&locale=de_DE', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/simple_product_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_product_with_variant_details_page()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products/logan-t-shirt?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_with_variant_details_page', Response::HTTP_OK);
    }

    public function it_shows_product_with_variant_details_page_in_different_locale()
    {
        $this->client->request('GET', '/shop-api/products/logan-t-shirt?channel=WEB_GB&locale=de_DE', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/simple_product_details_page', Response::HTTP_OK);
    }

    public function it_shows_product_with_options_details_page()
    {
        $this->client->request('GET', '/shop-api/products/logan-hat?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/simple_product_details_page', Response::HTTP_OK);
    }

    public function it_shows_product_with_options_details_page_in_different_locale()
    {
        $this->client->request('GET', '/shop-api/products/logan-hat?channel=WEB_GB&locale=de_DE', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/simple_product_details_page', Response::HTTP_OK);
    }

    public function it_shows_paginated_products_from_some_taxon()
    {
        $this->client->request('GET', '/shop-api/taxons/x-man/products/?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page', Response::HTTP_OK);
    }

    public function it_shows_paginated_products_from_some_taxon_in_different_language()
    {
        $this->client->request('GET', '/shop-api/taxons/x-man/products/?channel=WEB_GB&language=de_DE', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page', Response::HTTP_OK);
    }

    public function it_shows_second_page_of_paginated_products_from_some_taxon()
    {
        $this->client->request('GET', '/shop-api/taxons/x-man/products/?channel=WEB_GB&limit=1&page=2', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page', Response::HTTP_OK);
    }

    public function it_shows_sorted_product_list()
    {
        $this->client->request('GET', '/shop-api/taxons/x-man/products/?channel=WEB_GB&sorting[createdAt]=desc', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page', Response::HTTP_OK);
    }

    public function it_expose_only_some_of_products_in_the_list()
    {
        $this->client->request('GET', '/shop-api/taxons/x-man/products/?channel=WEB_GB&criteria[search][value]=Logans+Hat', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_page', Response::HTTP_OK);
    }
}
