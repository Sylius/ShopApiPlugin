<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        Assert::implementsInterface($requestClass, RequestInterface::class);

        $this->requestClass = $requestClass;
        $this->validator = $validator;
    }

    public function validate(Request $httpRequest, array $constraints = null, array $groups = null): ConstraintViolationListInterface
    {
        return $this->validator->validate($this->transformHttpRequest($httpRequest), $constraints, $groups);
    }

    public function getCommand(Request $httpRequest): CommandInterface
    {
        return $this->transformHttpRequest($httpRequest)->getCommand();
    }

    private function transformHttpRequest(Request $httpRequest): RequestInterface
    {
        /** @var RequestInterface $request */
        $request = $this->requestClass::fromHttpRequest($httpRequest);

        return $request;
    }
}
