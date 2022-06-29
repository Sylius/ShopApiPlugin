<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

use Sylius\Component\Core\Model\ProductReview;
use Sylius\ShopApiPlugin\View\Product\ProductReviewView;

final class ProductReviewViewFactory implements ProductReviewViewFactoryInterface
{
    /** @var string */
    private $productReviewViewClass;

    public function __construct(string $productReviewViewClass)
    {
        $this->productReviewViewClass = $productReviewViewClass;
    }

    /** @inheritdoc */
    public function create(ProductReview $productReview): ProductReviewView
    {
        /** @var ProductReviewView $productReviewView */
        $productReviewView = new $this->productReviewViewClass();

        $productReviewView->author = $productReview->getAuthor()->getEmail();
        $productReviewView->comment = $productReview->getComment();
        $productReviewView->rating = $productReview->getRating();
        $productReviewView->createdAt = $productReview->getCreatedAt();
        $productReviewView->title = $productReview->getTitle();

        return $productReviewView;
    }
}
