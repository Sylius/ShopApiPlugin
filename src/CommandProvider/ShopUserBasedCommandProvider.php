<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\ShopUserBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

final class ShopUserBasedCommandProvider implements ShopUserBasedCommandProviderInterface
{
    /** @var string */
    private $requestClass;

    /** @var ValidatorInterface */
    private $validator;

    public function __construct(string $requestClass, ValidatorInterface $validator)
    {
        Assert::implementsInterface($requestClass, ShopUserBasedRequestInterface::class);

        $this->requestClass = $requestClass;
        $this->validator = $validator;
    }

    public function validate(
        Request $httpRequest,
        ShopUserInterface $user,
        array $constraints = null,
        array $groups = null
    ): ConstraintViolationListInterface {
        return $this->validator->validate($this->transformHttpRequest($httpRequest, $user), $constraints, $groups);
    }

    public function getCommand(Request $httpRequest, ShopUserInterface $user): CommandInterface
    {
        return $this->transformHttpRequest($httpRequest, $user)->getCommand();
    }

    private function transformHttpRequest(Request $httpRequest, ShopUserInterface $user): ShopUserBasedRequestInterface
    {
        /** @var ShopUserBasedRequestInterface $request */
        $request = $this->requestClass::fromHttpRequestAndShopUser($httpRequest, $user);

        return $request;
    }
}
