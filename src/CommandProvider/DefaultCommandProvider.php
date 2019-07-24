<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

final class DefaultCommandProvider implements CommandProviderInterface
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

    public function validate(Request $httpRequest): ConstraintViolationListInterface
    {
        return $this->validator->validate($this->transformHttpRequest($httpRequest));
    }

    public function getCommand(Request $httpRequest): CommandInterface
    {
        return $this->transformHttpRequest($httpRequest)->getCommand();
    }

    private function transformHttpRequest(Request $httpRequest): RequestInterface
    {
        Assert::methodExists($this->requestClass, 'fromHttpRequest');
        Assert::implementsInterface($this->requestClass, RequestInterface::class);

        /** @var RequestInterface $request */
        $request = $this->requestClass::fromHttpRequest($httpRequest);

        return $request;
    }
}
