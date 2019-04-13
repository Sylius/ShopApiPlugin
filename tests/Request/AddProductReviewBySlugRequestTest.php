<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\Product\AddProductReviewBySlug;
use Sylius\ShopApiPlugin\Request\Product\AddProductReviewBySlugRequest;
use Symfony\Component\HttpFoundation\Request;

final class AddProductReviewBySlugRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_review_with_author()
    {
        $addReviewRequest = new AddProductReviewBySlugRequest(new Request([], [
            'title' => 'Awesome beer',
            'rating' => 5,
            'comment' => 'I love this beer',
            'email' => 'pale.ale@brewery.com',
        ], [
            'channelCode' => 'WEB_GB',
            'slug' => 'pale-ale',
        ]));

        $this->assertEquals($addReviewRequest->getCommand(), new AddProductReviewBySlug(
            'pale-ale',
            'WEB_GB',
            'Awesome beer',
            5,
            'I love this beer',
            'pale.ale@brewery.com'
        ));
    }
}
