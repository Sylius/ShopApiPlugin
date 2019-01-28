<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Symfony\Component\HttpFoundation\Request;

interface CommandProviderInterface
{
    /**
     * @throws ValidationFailedExceptionInterface
     */
    public function provide(Request $request): object;
}
