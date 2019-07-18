<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Product;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class ProductAddReviewByCodeApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_adds_review_to_product(): void
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
        $this->client->request('POST', '/shop-api/products/by-code/LOGAN_MUG_CODE/reviews', [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_adds_review_to_the_product_for_registered_user(): void
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
        $this->client->request('POST', '/shop-api/products/by-code/LOGAN_MUG_CODE/reviews', [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_review_when_rating_is_out_of_bounds(): void
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

        $this->client->request('POST', '/shop-api/products/by-code/LOGAN_MUG_CODE/reviews', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'reviews/add_review_failed_rating', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_review_when_email_is_not_valid(): void
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

        $this->client->request('POST', '/shop-api/products/by-code/LOGAN_MUG_CODE/reviews', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'reviews/add_review_failed_email', Response::HTTP_BAD_REQUEST);
    }
}
