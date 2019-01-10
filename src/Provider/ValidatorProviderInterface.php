<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Provider;

use FOS\RestBundle\View\View;

interface ValidationViewProviderInterface
{
    public function provide($request): ?View;
}
