<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

final class ValidationFailedException extends \InvalidArgumentException implements ValidationFailedExceptionInterface
{
    /** @var ConstraintViolationListInterface */
    private $validationErrors;

    private function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromSymfonyConstraintValidationList(ConstraintViolationListInterface $violationList): self
    {
        $self = new self('Validation failed!');
        $self->validationErrors = $violationList;

        return $self;
    }

    public function getValidationErrors(): ConstraintViolationListInterface
    {
        return $this->validationErrors;
    }
}
