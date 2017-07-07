<?php

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Repository\ProductAssociationTypeRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

final class ProductShowDetailsByCodeApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_simple_product_details_page()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products/LOGAN_MUG_CODE?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/simple_product_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_throws_a_not_found_exception_if_channel_has_not_been_found()
    {
        $this->client->request('GET', '/shop-api/products/LOGAN_MUG_CODE?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_throws_a_not_found_exception_if_product_has_not_been_found()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products/WRONG_PRODUCT_CODE?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_has_not_been_found_for_given_code_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_shows_simple_product_details_page_in_different_locale()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products/LOGAN_MUG_CODE?channel=WEB_GB&locale=de_DE', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/german_simple_product_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_product_with_variant_details_page()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products/LOGAN_T_SHIRT_CODE?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_with_variant_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_product_with_options_details_page()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products/LOGAN_HAT_CODE?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_with_options_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_product_with_options_details_page_in_different_locale()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products/LOGAN_HAT_CODE?channel=WEB_GB&locale=de_DE', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/german_product_with_options_details_page', Response::HTTP_OK);
    }
}
