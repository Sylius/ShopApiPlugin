<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Product;

use PHPUnit\Framework\Assert;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class AddProductReviewByCodeTest extends JsonApiTestCase
{
    private static $acceptAndContentTypeHeader = ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'];

    /**
     * @test
     */
    public function fails_to_add_review_because_rating_is_out_of_bounds()
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
    public function fails_to_add_review_because_email_is_not_valid()
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml']);

        $data =
<<<EOT
        {
            "comment": "Hello",
            "rating": 100,
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
