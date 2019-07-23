<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestInterface
{
    public static function fromRequest(Request $request): self;

    public function getCommand(): CommandInterface;
}
