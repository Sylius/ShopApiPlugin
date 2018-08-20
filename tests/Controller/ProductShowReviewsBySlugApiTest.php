<?php

declare(strict_types=1);

namespace Tests\Sylius\SyliusShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductShowReviewsBySlugApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_reviews_for_product_by_slug()
    {
        $this->loadFixturesFromFile('shop.yml');
        $this->loadFixturesFromFile('customer.yml');
        $this->loadFixturesFromFile('mug_review.yml');

        $this->client->request('GET', '/shop-api/product-reviews-by-slug/logan-mug?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_review_list_page_by_slug_response', Response::HTTP_OK);
    }
}
