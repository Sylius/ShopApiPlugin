<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestInterface
{
    public static function fromHttpRequest(Request $request): self;

    public function getCommand(): CommandInterface;
}
