<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Parser;

use Sylius\ShopApiPlugin\Request\CommandRequestInterface;
use Symfony\Component\HttpFoundation\Request;

interface CommandRequestParserInterface
{
    public function parse(Request $request, string $commandName): CommandRequestInterface;
}
