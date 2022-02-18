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

namespace Sylius\ShopApiPlugin\Http;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;

final class RequestBasedLocaleProvider implements RequestBasedLocaleProviderInterface
{
    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var SupportedLocaleProviderInterface */
    private $supportedLocaleProvider;

    public function __construct(
        ChannelContextInterface $channelContext,
        SupportedLocaleProviderInterface $supportedLocaleProvider
    ) {
        $this->channelContext = $channelContext;
        $this->supportedLocaleProvider = $supportedLocaleProvider;
    }

    /** @throws ChannelNotFoundException */
    public function getLocaleCode(Request $request): string
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        return $this->supportedLocaleProvider->provide($request->getLocale(), $channel);
    }
}
