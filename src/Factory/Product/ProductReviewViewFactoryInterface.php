<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

use Sylius\Component\Core\Model\ProductReview;
use Sylius\ShopApiPlugin\View\ProductReviewView;

interface ProductReviewViewFactoryInterface
{
    public function create(ProductReview $productReview): ProductReviewView;
}
