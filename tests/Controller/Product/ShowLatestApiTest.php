<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Product;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class ShowLatestApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_latest_products_with_default_count(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/product-latest/', [], [], self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_latest_4_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_latest_2_products(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/product-latest/', ['limit' => 2], [], self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_latest_2_response', Response::HTTP_OK);
    }
}
