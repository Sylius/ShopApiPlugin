<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Sylius\SyliusShopApiPlugin\View\ValidationErrorView;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ValidationErrorViewFactoryInterface
{
    public function create(ConstraintViolationListInterface $validationResults): ValidationErrorView;
}
