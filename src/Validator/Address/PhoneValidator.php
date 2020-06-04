<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Address;

use libphonenumber\PhoneNumberType;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class PhoneValidator extends ConstraintValidator
{

    private $orderRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    public function validate($request, Constraint $constraint)
    {
        $phoneUtil        = \libphonenumber\PhoneNumberUtil::getInstance();
        $swissNumberProto = $phoneUtil->parse($request, "CH");
        $valid            = $phoneUtil->isValidNumber($swissNumberProto);
        if (! ($valid && $phoneUtil->getNumberType($swissNumberProto) == PhoneNumberType::MOBILE)) {
            return $this->context->addViolation($constraint->message);
        }
    }
}
