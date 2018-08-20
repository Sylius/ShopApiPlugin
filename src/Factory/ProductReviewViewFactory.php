<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ProductReview;
use Sylius\ShopApiPlugin\View\ProductReviewView;

final class ProductReviewViewFactory implements ProductReviewViewFactoryInterface
{
    /** @var string */
    private $productReviewViewClass;

    public function __construct(string $productReviewViewClass)
    {
        $this->productReviewViewClass = $productReviewViewClass;
    }

    public function create(ProductReview $productReview): ProductReviewView
    {
        /** @var ProductReviewView $productReviewView */
        $productReviewView = new $this->productReviewViewClass();

        $productReviewView->author = $productReview->getAuthor()->getEmail();
        $productReviewView->comment = $productReview->getComment();
        $productReviewView->rating = $productReview->getRating();
        $productReviewView->title = $productReview->getTitle();

        return $productReviewView;
    }
}
