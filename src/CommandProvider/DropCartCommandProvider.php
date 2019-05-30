<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Sylius\ShopApiPlugin\Command\Cart\DropCart;
use Sylius\ShopApiPlugin\Request\Cart\DropCartRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DropCartCommandProvider implements CommandProviderInterface
{
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    protected function transformRequest(Request $request): DropCartRequest
    {
        return new DropCartRequest($request);
    }

    protected function transformCommand(DropCartRequest $dropCartRequest): DropCart
    {
        return new DropCart($dropCartRequest->token);
    }

    final public function validate(Request $request): ConstraintViolationListInterface
    {
        return $this->validator->validate($this->transformRequest($request));
    }

    final public function getCommand(Request $request): object
    {
        return $this->transformCommand($this->transformRequest($request));
    }
}
