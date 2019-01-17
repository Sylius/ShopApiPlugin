<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Parser;

use Symfony\Component\HttpFoundation\Request;

interface CommandRequestParserInterface
{
    public function parse(Request $request, string $commandName): object;
}
