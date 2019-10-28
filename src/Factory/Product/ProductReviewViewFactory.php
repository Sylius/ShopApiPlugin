<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

use Sylius\Component\Core\Model\ProductReview;
use Sylius\ShopApiPlugin\View\Product\ProductReviewView;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;

final class ProductReviewViewFactory implements ProductReviewViewFactoryInterface
{

    /** @var string */
    private $productReviewViewClass;

    /** @var ImageViewFactoryInterface */
    private $imageViewFactory;

    public function __construct(
        string $productReviewViewClass,
        ImageViewFactoryInterface $imageViewFactory
    ) {
        $this->productReviewViewClass = $productReviewViewClass;
        $this->imageViewFactory       = $imageViewFactory;
    }

    /** {@inheritdoc} */
    public function create(ProductReview $productReview): ProductReviewView
    {
        /** @var ProductReviewView $productReviewView */
        $productReviewView = new $this->productReviewViewClass();

        $productReviewView->authorEmail     = $productReview->getAuthor()->getEmail();
        $productReviewView->authorFirstName = $productReview->getAuthor()->getFirstName();
        $productReviewView->authorLastName  = $productReview->getAuthor()->getLastName();

        if ($productReview->getAuthor()->getUser() && $productReview->getAuthor()->getUser()->getAvatar()) {
            $image = $productReview->getAuthor()->getUser()->getAvatar();

            $productReviewView->authorAvatar = $this->imageViewFactory->create($image);
        }
        $productReviewView->comment   = $productReview->getComment();
        $productReviewView->rating    = $productReview->getRating();
        $productReviewView->createdAt = $productReview->getCreatedAt();
        $productReviewView->title     = $productReview->getTitle();

        return $productReviewView;
    }
}
