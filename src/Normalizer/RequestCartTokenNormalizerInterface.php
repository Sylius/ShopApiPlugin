<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Normalizer;

use Symfony\Component\HttpFoundation\Request;

interface RequestCartTokenNormalizerInterface
{
    public function doNotAllowNullCartToken(Request $request): Request;
}
