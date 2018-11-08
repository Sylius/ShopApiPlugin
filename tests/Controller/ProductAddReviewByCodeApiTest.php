<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Symfony\Component\HttpFoundation\Response;

final class ProductAddReviewByCodeApiTest extends JsonApiTestCase
{
    private static $acceptAndContentTypeHeader = ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'];

    /**
     * @test
     */
    public function it_adds_review_to_product()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $data =
<<<EOT
        {
            "channelCode": "WEB_GB",
            "title": "Awesome product",
            "rating": 5,
            "comment": "If I were a mug, I would like to be like this one!",
            "email": "oliver@example.com"
        }
EOT;
        $this->client->request('POST', '/shop-api/WEB_GB/products/LOGAN_MUG_CODE/reviews', [], [], self::$acceptAndContentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_adds_review_to_the_product_for_registered_user()
    {
        $this->loadFixturesFromFiles(['shop.yml', 'customer.yml']);

        $data =
<<<EOT
        {
            "channelCode": "WEB_GB",
            "title": "Awesome product",
            "rating": 5,
            "comment": "If I were a mug, I would like to be like this one!",
            "email": "oliver@example.com"
        }
EOT;
        $this->client->request('POST', '/shop-api/WEB_GB/products/LOGAN_MUG_CODE/reviews', [], [], self::$acceptAndContentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }
}
