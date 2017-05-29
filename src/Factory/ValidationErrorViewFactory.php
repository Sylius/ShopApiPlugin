<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\ShopApiPlugin\View\ValidationErrorView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ValidationErrorViewFactory implements ValidationErrorViewFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(ConstraintViolationListInterface $validationResults): \Sylius\ShopApiPlugin\View\ValidationErrorView
    {
        $errorMessage = new ValidationErrorView();
        $errorMessage->code = Response::HTTP_BAD_REQUEST;
        $errorMessage->message = 'Validation failed';
        /** @var ConstraintViolationInterface $result */
        foreach ($validationResults as $result) {
            $errorMessage->errors[$result->getPropertyPath()][] = $result->getMessage();
        }

        return $errorMessage;
    }
}
