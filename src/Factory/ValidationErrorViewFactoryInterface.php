<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\ShopApiPlugin\View\ValidationErrorView;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ValidationErrorViewFactoryInterface
{
    /**
     * @param ConstraintViolationListInterface $validationResults
     *
     * @return ValidationErrorView
     */
    public function create(ConstraintViolationListInterface $validationResults): \Sylius\ShopApiPlugin\View\ValidationErrorView;
}
