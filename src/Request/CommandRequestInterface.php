<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\CommandInterface;

interface CommandRequestInterface
{
    public function getCommand(): CommandInterface;
}
