<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Sylius\ShopApiPlugin\Command\DropCart;
use Sylius\ShopApiPlugin\Request\DropCartRequest;
use Sylius\ShopApiPlugin\Validator\Constraints\CartExists;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class DropCartCommandProvider implements CommandProviderInterface
{
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(Request $request): ConstraintViolationListInterface
    {
        return $this->validator->validate(new DropCartRequest($request));
    }

    public function getCommand(Request $request): object
    {
        $dropCartRequest = new DropCartRequest($request);

        return new DropCart($dropCartRequest->token);
    }
}
