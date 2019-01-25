<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface CommandProviderInterface
{
    public function validate(Request $request): ConstraintViolationListInterface;

    public function getCommand(Request $request): object;
}
