<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Symfony\Component\HttpFoundation\Request;

interface ShopUserBasedRequestInterface
{
    public static function fromHttpRequestAndShopUser(Request $request, ShopUserInterface $user): self;

    public function getCommand(): CommandInterface;
}
