<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Provider;

use Sylius\Component\Core\Model\ShopUserInterface;

interface LoggedInUserProviderInterface
{
    public function provide(): ShopUserInterface;
}
