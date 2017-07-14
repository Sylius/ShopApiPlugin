<?php

declare(strict_types = 1);

namespace spec\Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ProductReview;
use Sylius\Component\Core\Model\ProductReviewerInterface;
use Sylius\ShopApiPlugin\Factory\ProductReviewViewFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ShopApiPlugin\Factory\ProductReviewViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ProductReviewView;

final class ProductReviewViewFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(ProductReviewView::class);
    }

    function it_is_product_review_view_factory()
    {
        $this->shouldHaveType(ProductReviewViewFactoryInterface::class);
    }

    function it_creates_product_review_view(ProductReview $productReview, ProductReviewerInterface $reviewer)
    {
        $productReview->getAuthor()->willReturn($reviewer);
        $productReview->getComment()->willReturn('Lorem ipsum');
        $productReview->getRating()->willReturn(5);
        $productReview->getTitle()->willReturn('Super review, you ...');

        $reviewer->getEmail()->willReturn('shepard@mass.com');

        $reviewView = new ProductReviewView();
        $reviewView->title = 'Super review, you ...';
        $reviewView->comment = 'Lorem ipsum';
        $reviewView->author = 'shepard@mass.com';
        $reviewView->rating = 5;

        $this->create($productReview)->shouldBeLike($reviewView);
    }
}
