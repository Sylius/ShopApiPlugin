<?php

declare(strict_types=1);

namespace Tests\Sylius\SyliusShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\SyliusShopApiPlugin\Command\AddProductReviewByCode;
use Sylius\SyliusShopApiPlugin\Request\AddProductReviewByCodeRequest;
use Symfony\Component\HttpFoundation\Request;

final class AddProductReviewByCodeRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_review_with_author()
    {
        $addReviewRequest = new AddProductReviewByCodeRequest(new Request([], [
            'channelCode' => 'WEB_GB',
            'title' => 'Awesome beer',
            'rating' => 5,
            'comment' => 'I love this beer',
            'email' => 'pale.ale@brewery.com',
        ], [
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
