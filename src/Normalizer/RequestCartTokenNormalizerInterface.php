<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Normalizer;

use Symfony\Component\HttpFoundation\Request;

interface RequestCartTokenNormalizerInterface
{
    /** Should be used only when passing nullable cart token is an acceptable behavior */
    public function __invoke(Request $request): Request;
}
