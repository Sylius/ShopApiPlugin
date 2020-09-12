<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Address;

use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
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
        try {
            $phoneUtil        = PhoneNumberUtil::getInstance();
            $swissNumberProto = $phoneUtil->parse($request, "RU");
            $valid            = $phoneUtil->isValidNumber($swissNumberProto);
        } catch (\Exception $e) {
            return $this->context->addViolation($constraint->message);
        }
        if (!($valid && ($phoneUtil->getNumberType($swissNumberProto) == PhoneNumberType::MOBILE || $phoneUtil->getNumberType($swissNumberProto) == PhoneNumberType::FIXED_LINE_OR_MOBILE))) {
            return $this->context->addViolation($constraint->message);
        }
    }
}
