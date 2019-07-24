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

    public function validate(Request $httpRequest, ChannelInterface $channel): ConstraintViolationListInterface
    {
        return $this->validator->validate($this->transformHttpRequest($httpRequest, $channel));
    }

    public function getCommand(Request $httpRequest, ChannelInterface $channel): CommandInterface
    {
        return $this->transformHttpRequest($httpRequest, $channel)->getCommand();
    }

    private function transformHttpRequest(Request $httpRequest, ChannelInterface $channel): ChannelBasedRequestInterface
    {
        Assert::methodExists($this->requestClass, 'fromHttpRequestAndChannel');
        Assert::implementsInterface($this->requestClass, ChannelBasedRequestInterface::class);

        /** @var ChannelBasedRequestInterface $request */
        $request = $this->requestClass::fromHttpRequestAndChannel($httpRequest, $channel);

        return $request;
    }
}
