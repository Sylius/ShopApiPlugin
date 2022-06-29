<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Cart;

use Sylius\ShopApiPlugin\Command\CommandInterface;

class RemoveItemFromCart implements CommandInterface
{
    /** @var string */
    protected $orderToken;

    /** @var int|string */
    protected $itemIdentifier;

    /**
     * @param string|int $itemIdentifier
     */
    public function __construct(string $orderToken, $itemIdentifier)
    {
        $this->orderToken = $orderToken;
        $this->itemIdentifier = $itemIdentifier;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    /**
     * @return string|int
     */
    public function itemIdentifier()
    {
        return $this->itemIdentifier;
    }
}
