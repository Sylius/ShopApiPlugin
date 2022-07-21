<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Product;

use DateTime;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductReview;
use Sylius\Component\Core\Model\ProductReviewerInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductReviewViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Product\ProductReviewView;

final class ProductReviewViewFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(ProductReviewView::class);
    }

    function it_is_product_review_view_factory(): void
    {
        $this->shouldHaveType(ProductReviewViewFactoryInterface::class);
    }

    function it_creates_product_review_view(
        ProductReview $productReview,
        ProductReviewerInterface $reviewer,
    ): void {
        $createdAt = new DateTime();

        $productReview->getAuthor()->willReturn($reviewer);
        $productReview->getComment()->willReturn('Lorem ipsum');
        $productReview->getCreatedAt()->willReturn($createdAt);
        $productReview->getRating()->willReturn(5);
        $productReview->getTitle()->willReturn('Super review, you ...');

        $reviewer->getEmail()->willReturn('shepard@mass.com');

        $reviewView = new ProductReviewView();
        $reviewView->title = 'Super review, you ...';
        $reviewView->comment = 'Lorem ipsum';
        $reviewView->createdAt = $createdAt;
        $reviewView->author = 'shepard@mass.com';
        $reviewView->rating = 5;

        $this->create($productReview)->shouldBeLike($reviewView);
    }
}
