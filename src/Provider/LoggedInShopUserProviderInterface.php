<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Provider;

use Sylius\Component\Core\Model\ShopUserInterface;

interface LoggedInShopUserProviderInterface
{
    public function provide(): ShopUserInterface;

    public function check(): bool;
}
