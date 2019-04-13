<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Cart;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class CartEstimateShippingTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_returns_not_found_exception_if_cart_has_not_been_found(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/carts/SDAOSLEFNWU35H3QLI5325/estimated-shipping-cost', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/cart_and_country_does_not_exist_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_calculates_estimated_shipping_cost_based_on_country(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->pickupCart($token);
        $this->putItemToCart($token);

        $this->client->request('GET', sprintf('/shop-api/WEB_GB/carts/%s/estimated-shipping-cost?countryCode=GB', $token), [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/estimated_shipping_cost_bases_on_country_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_calculates_estimated_shipping_cost_based_on_country_and_province(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->pickupCart($token);
        $this->putItemToCart($token);

        $this->client->request('GET', sprintf('/shop-api/WEB_GB/carts/%s/estimated-shipping-cost?countryCode=GB&provinceCode=GB-SCT', $token), [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/estimated_shipping_cost_bases_on_country_and_province_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_estimate_shipping_in_non_existent_channel(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml']);

        $this->client->request('GET', '/shop-api/SPACE_KLINGON/carts/SDAOSLEFNWU35H3QLI5325/estimated-shipping-cost', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    private function pickupCart(string $token): void
    {
        $this->client->request('POST', '/shop-api/WEB_GB/carts/' . $token, [], [], static::CONTENT_TYPE_HEADER);
    }

    private function putItemToCart(string $token): void
    {
        $data =
<<<EOT
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 5
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], static::CONTENT_TYPE_HEADER, $data);
    }
}
