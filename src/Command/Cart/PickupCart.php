<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Cart;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Command\LocaleAwareCommandInterface;

class PickupCart implements CommandInterface, LocaleAwareCommandInterface
{
    /** @var string */
    protected $orderToken;

    /** @var string */
    protected $channelCode;

    /** @var string|null */
    protected $localeCode;

    public function __construct(string $orderToken, string $channelCode)
    {
        $this->orderToken = $orderToken;
        $this->channelCode = $channelCode;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    public function channelCode(): string
    {
        return $this->channelCode;
    }

    public function setLocaleCode(string $localeCode)
    {
        $this->localeCode = $localeCode;
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }
}
