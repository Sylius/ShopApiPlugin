<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Normalizer;

use Symfony\Component\HttpFoundation\Request;

interface RequestCartTokenNormalizerInterface
{
    public function doNotAllowNullCartToken(Request $request): Request;
}
