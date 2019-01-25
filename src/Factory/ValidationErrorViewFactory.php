<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\ShopApiPlugin\View\ValidationErrorView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ValidationErrorViewFactory implements ValidationErrorViewFactoryInterface
{
    /** @var string */
    private $validationErrorViewClass;

    public function __construct(string $validationErrorViewClass)
    {
        $this->validationErrorViewClass = $validationErrorViewClass;
    }

    /** {@inheritdoc} */
    public function create(ConstraintViolationListInterface $validationResults): ValidationErrorView
    {
        /** @var ValidationErrorView $errorMessage */
        $errorMessage = new $this->validationErrorViewClass();

        $errorMessage->code = Response::HTTP_BAD_REQUEST;
        $errorMessage->message = 'Validation failed';
        /** @var ConstraintViolationInterface $result */
        foreach ($validationResults as $result) {
            // ACL because of new providers path surrounded with `[` and `]`
            $property = $result->getPropertyPath();
            if (\preg_match('/\\[.+\\]/', $property) === 1) {
                $property = substr($property, 1, -1);
            }

            $errorMessage->errors[$property][] = $result->getMessage();
        }

        return $errorMessage;
    }
}
