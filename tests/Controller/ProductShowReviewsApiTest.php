<?php

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductShowReviewsApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_reviews_for_product()
    {
        $this->loadFixturesFromFile('shop.yml');
        $this->loadFixturesFromFile('customer.yml');
        $this->loadFixturesFromFile('mug_review.yml');

        $this->client->request('GET', '/shop-api/products/logan-mug/reviews?channel=WEB_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_review_list_page', Response::HTTP_OK);
    }
}
