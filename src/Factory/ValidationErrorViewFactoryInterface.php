<?php

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
    public function create(ConstraintViolationListInterface $validationResults);
}
