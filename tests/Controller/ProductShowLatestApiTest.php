<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Symfony\Component\HttpFoundation\Response;

final class ProductShowLatestApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_latest_products_with_default_count()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/product-latest/', ['channel' => 'WEB_GB'], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_latest_4_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_latest_2_products()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/product-latest/', ['channel' => 'WEB_GB', 'limit' => 2], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_latest_2_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_show_latest_products_in_non_existent_channel()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/SPACE_KLINGON/product-latest/', ['channel' => 'WEB_GB'], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }
}
