<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

class DefaultCommandProvider implements CommandProviderInterface
{
    /** @var string */
    private $requestClass;

    /** @var ValidatorInterface */
    private $validator;

    public function __construct(string $requestClass, ValidatorInterface $validator)
    {
        $this->requestClass = $requestClass;
        $this->validator = $validator;
    }

    protected function transformRequest(Request $request): RequestInterface
    {
        $requestModel = call_user_func([$this->requestClass, 'fromRequest'], $request);

        Assert::implementsInterface($requestModel, RequestInterface::class);

        /** @var RequestInterface $requestModel */
        return $requestModel;
    }

    final public function validate(Request $request): ConstraintViolationListInterface
    {
        return $this->validator->validate($this->transformRequest($request));
    }

    final public function getCommand(Request $request): CommandInterface
    {
        return $this->transformRequest($request)->getCommand();
    }
}
