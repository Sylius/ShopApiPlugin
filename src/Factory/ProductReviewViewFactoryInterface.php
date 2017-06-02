<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ProductReview;

interface ProductReviewViewFactoryInterface
{
    public function create(ProductReview $productReview);
}
