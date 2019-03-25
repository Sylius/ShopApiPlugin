<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\Product\AddProductReviewByCode;
use Sylius\ShopApiPlugin\Request\AddProductReviewByCodeRequest;
use Symfony\Component\HttpFoundation\Request;

final class AddProductReviewByCodeRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_review_with_author()
    {
        $addReviewRequest = new AddProductReviewByCodeRequest(new Request([], [
            'title' => 'Awesome beer',
            'rating' => 5,
            'comment' => 'I love this beer',
            'email' => 'pale.ale@brewery.com',
        ], [
            'channelCode' => 'WEB_GB',
            'code' => 'PALE_ALE_CODE',
        ]));

        $this->assertEquals($addReviewRequest->getCommand(), new AddProductReviewByCode(
            'PALE_ALE_CODE',
            'WEB_GB',
            'Awesome beer',
            5,
            'I love this beer',
            'pale.ale@brewery.com'
        ));
    }
}
