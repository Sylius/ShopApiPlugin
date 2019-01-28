<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ValidationFailedExceptionInterface extends \Throwable
{
    public function getValidationErrors(): ConstraintViolationListInterface;
}
