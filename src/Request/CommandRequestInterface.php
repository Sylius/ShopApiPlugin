<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Symfony\Component\HttpFoundation\Request;

interface CommandRequestInterface
{
    public function populateData(Request $request): void;

    public function getCommand(): object;
}
