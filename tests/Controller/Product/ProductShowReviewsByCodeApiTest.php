<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Product;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class ProductShowReviewsByCodeApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_reviews_for_product_by_slug(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'customer.yml', 'mug_review.yml']);

        $this->client->request('GET', '/shop-api/products/by-code/LOGAN_MUG_CODE/reviews', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_review_list_page_by_code_response', Response::HTTP_OK);
    }
}
