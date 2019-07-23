<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\ChannelBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

final class ChannelBasedCommandProvider implements ChannelBasedCommandProviderInterface
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

    public function validate(Request $request, ChannelInterface $channel): ConstraintViolationListInterface
    {
        return $this->validator->validate($this->transformRequest($request, $channel));
    }

    public function getCommand(Request $request, ChannelInterface $channel): CommandInterface
    {
        return $this->transformRequest($request, $channel)->getCommand();
    }

    private function transformRequest(Request $request, ChannelInterface $channel): ChannelBasedRequestInterface
    {
        $requestModel = call_user_func([$this->requestClass, 'fromRequestAndChannel'], $request, $channel);

        Assert::implementsInterface($requestModel, ChannelBasedRequestInterface::class);

        /** @var ChannelBasedRequestInterface $requestModel */
        return $requestModel;
    }
}
