<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\ShopApiPlugin\View\ValidationErrorView;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ValidationErrorViewFactoryInterface
{
    public function create(ConstraintViolationListInterface $validationResults): ValidationErrorView;
}
