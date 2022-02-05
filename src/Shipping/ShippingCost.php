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

namespace Sylius\ShopApiPlugin\Shipping;

final class ShippingCost
{
    /** @var int */
    private $price;

    /** @var string */
    private $currency;

    public function __construct(int $price, string $currency)
    {
        $this->price = $price;
        $this->currency = $currency;
    }

    public function price(): int
    {
        return $this->price;
    }

    public function currency(): string
    {
        return $this->currency;
    }
}
