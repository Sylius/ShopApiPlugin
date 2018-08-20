<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Provider;

use Sylius\Component\Core\Model\ProductReviewerInterface;

interface ProductReviewerProviderInterface
{
    public function provide(string $email): ProductReviewerInterface;
}
