<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Symfony\Component\HttpFoundation\Response;

final class ProductShowReviewsBySlugApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_reviews_for_product_by_slug()
    {
        $this->loadFixturesFromFiles(['shop.yml', 'customer.yml', 'mug_review.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/product-reviews-by-slug/logan-mug?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_review_list_page_by_slug_response', Response::HTTP_OK);
    }
}
