<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class DropCart
{
    /** @var string */
    private $orderToken;

    public function __construct($orderToken)
    {
        Assert::string($orderToken);

        $this->orderToken = $orderToken;
    }

    public function orderToken()
    {
        return $this->orderToken;
    }
}
