<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class ChangeItemQuantity
{
    /**
     * @var string
     */
    private $orderToken;

    /**
     * @var mixed
     */
    private $itemIdentifier;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @param string $orderToken
     * @param mixed $itemIdentifier
     * @param int $quantity
     */
    public function __construct($orderToken, $itemIdentifier, $quantity)
    {
        Assert::string($orderToken, 'Expected order token to be string, got %s');
        Assert::integer($quantity, 'Expected quantity to be integer, got %s');
        Assert::greaterThan($quantity, 0, 'Quantity should be greater than 0');

        $this->orderToken = $orderToken;
        $this->itemIdentifier = $itemIdentifier;
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function orderToken()
    {
        return $this->orderToken;
    }

    /**
     * @return mixed
     */
    public function itemIdentifier()
    {
        return $this->itemIdentifier;
    }

    /**
     * @return int
     */
    public function quantity()
    {
        return $this->quantity;
    }
}
