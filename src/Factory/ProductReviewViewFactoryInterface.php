<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ProductReview;
use Sylius\SyliusShopApiPlugin\View\ProductReviewView;

interface ProductReviewViewFactoryInterface
{
    public function create(ProductReview $productReview): ProductReviewView;
}
