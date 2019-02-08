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
    public function it_does_not_allow_to_add_product_review_by_code_in_non_existent_channel()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $data =
<<<EOT
        {
            "title": "Awesome product",
            "rating": 5,
            "comment": "If I were a mug, I would like to be like this one!",
            "email": "oliver@example.com"
        }
EOT;
        $this->client->request('POST', '/shop-api/SPACE_KLINGON/products/LOGAN_MUG_CODE/reviews', [], [], self::$acceptAndContentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_review_when_rating_is_out_of_bounds()
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml']);

        $data =
<<<EOT
        {
            "comment": "Hello",
            "rating": 100,
            "email": "test@test.com",
            "title": "Testing",
            "code": "LOGAN_MUG_CODE"
        }
EOT;

        $this->client->request('POST', '/shop-api/WEB_GB/products/LOGAN_MUG_CODE/reviews', [], [], self::$acceptAndContentTypeHeader, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'reviews/add_review_failed_rating', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_review_when_email_is_not_valid()
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml']);

        $data =
<<<EOT
        {
            "comment": "Hello",
            "rating": 4,
            "email": "test.com",
            "title": "Testing",
            "code": "LOGAN_MUG_CODE"
        }
EOT;

        $this->client->request('POST', '/shop-api/WEB_GB/products/LOGAN_MUG_CODE/reviews', [], [], self::$acceptAndContentTypeHeader, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'reviews/add_review_failed_email', Response::HTTP_BAD_REQUEST);
    }
}
