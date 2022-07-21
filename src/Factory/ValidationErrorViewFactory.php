<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    /** @inheritdoc */
    public function create(ConstraintViolationListInterface $validationResults): ValidationErrorView
    {
        /** @var ValidationErrorView $errorMessage */
        $errorMessage = new $this->validationErrorViewClass();

        $errorMessage->code = Response::HTTP_BAD_REQUEST;
        $errorMessage->message = 'Validation failed';
        /** @var ConstraintViolationInterface $result */
        foreach ($validationResults as $result) {
            $errorMessage->errors[$result->getPropertyPath()][] = $result->getMessage();
        }

        return $errorMessage;
    }
}
