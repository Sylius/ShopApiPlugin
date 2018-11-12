<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Symfony\Component\HttpFoundation\Response;

final class ProductShowDetailsBySlugApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_simple_product_details_page()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/products-by-slug/logan-mug', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/simple_product_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_product_without_taxon_details_page()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/products-by-slug/logan-shoes', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_without_taxons_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_throws_a_not_found_exception_if_channel_has_not_been_found()
    {
        $this->client->request('GET', '/shop-api/WEB_GB/products-by-slug/logan-mug', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_throws_a_not_found_exception_if_product_has_not_been_found()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/products-by-slug/some-weird-stuff', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_has_not_been_found_for_given_slug_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_shows_simple_product_details_page_in_different_locale()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/products-by-slug/logan-becher?locale=de_DE', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/german_simple_product_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_product_with_variant_details_page()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/products-by-slug/logan-t-shirt', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_with_variant_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_product_with_options_details_page()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/products-by-slug/logan-hat', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_with_options_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_product_with_options_details_page_in_different_locale()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/products-by-slug/logan-hut?locale=de_DE', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/german_product_with_options_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_show_product_details_by_slug_in_non_existent_channel()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/SPACE_KLINGON/products-by-slug/logan-mug', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }
}
