<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ProductReview;
use Sylius\ShopApiPlugin\View\ProductReviewView;

final class ProductReviewViewFactory implements ProductReviewViewFactoryInterface
{
    public function create(ProductReview $productReview): ProductReviewView
    {
        $productReviewView = new ProductReviewView();

        $productReviewView->author = $productReview->getAuthor()->getEmail();
        $productReviewView->comment = $productReview->getComment();
        $productReviewView->rating = $productReview->getRating();
        $productReviewView->title = $productReview->getTitle();

        return $productReviewView;
    }
}
