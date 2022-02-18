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

namespace Sylius\ShopApiPlugin\Command\Cart;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Webmozart\Assert\Assert;

class ChangeItemQuantity implements CommandInterface
{
    /** @var string */
    protected $orderToken;

    /** @var int|string */
    protected $itemIdentifier;

    /** @var int */
    protected $quantity;

    /**
     * @param int|string $itemIdentifier
     */
    public function __construct(string $orderToken, $itemIdentifier, int $quantity)
    {
        Assert::greaterThan($quantity, 0, 'Quantity should be greater than 0');

        $this->orderToken = $orderToken;
        $this->itemIdentifier = $itemIdentifier;
        $this->quantity = $quantity;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    /**
     * @return int|string
     */
    public function itemIdentifier()
    {
        return $this->itemIdentifier;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}
