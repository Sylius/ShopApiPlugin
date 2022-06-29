<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Http;

use Sylius\ShopApiPlugin\Exception\ChannelNotFoundException;
use Symfony\Component\HttpFoundation\Request;

interface RequestBasedLocaleProviderInterface
{
    /** @throws ChannelNotFoundException|\InvalidArgumentException */
    public function getLocaleCode(Request $request): string;
}
