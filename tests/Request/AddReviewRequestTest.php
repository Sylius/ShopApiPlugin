<?php

declare(strict_types = 1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\AddReview;
use Sylius\ShopApiPlugin\Request\AddReviewRequest;
use Symfony\Component\HttpFoundation\Request;

final class AddReviewRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_review_with_author()
    {
        $addReviewRequest = new AddReviewRequest(new Request([], [
            'channelCode' => 'WEB_GB',
            'title' => 'Awesome beer',
            'rating' => 5,
            'comment' => 'I love this beer',
            'email' => 'pale.ale@brewery.com',
        ], [
            'slug' => 'pale-ale',
        ]));

        $this->assertEquals($addReviewRequest->getCommand(), new AddReview(
            'pale-ale',
            'WEB_GB',
            'Awesome beer',
            5,
            'I love this beer',
            'pale.ale@brewery.com'
        ));
    }
}
