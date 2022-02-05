<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Command\LocaleAwareCommandInterface;
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

    /** @var LocaleContextInterface|null */
    private $localeContext;

    public function __construct(string $requestClass, ValidatorInterface $validator, ?LocaleContextInterface $localeContext = null)
    {
        Assert::implementsInterface($requestClass, ChannelBasedRequestInterface::class);

        $this->requestClass = $requestClass;
        $this->validator = $validator;
        $this->localeContext = $localeContext;
    }

    public function validate(
        Request $httpRequest,
        ChannelInterface $channel,
        array $constraints = null,
        array $groups = null
    ): ConstraintViolationListInterface {
        return $this->validator->validate($this->transformHttpRequest($httpRequest, $channel), $constraints, $groups);
    }

    public function getCommand(Request $httpRequest, ChannelInterface $channel): CommandInterface
    {
        $command = $this->transformHttpRequest($httpRequest, $channel)->getCommand();
        if ($command instanceof LocaleAwareCommandInterface && $this->localeContext !== null) {
            $command->setLocaleCode($this->localeContext->getLocaleCode());
        }

        return $command;
    }

    private function transformHttpRequest(Request $httpRequest, ChannelInterface $channel): ChannelBasedRequestInterface
    {
        /** @var ChannelBasedRequestInterface $request */
        $request = $this->requestClass::fromHttpRequestAndChannel($httpRequest, $channel);

        return $request;
    }
}
