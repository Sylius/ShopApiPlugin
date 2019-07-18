<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Http;

use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Exception\ChannelNotFoundException;
use Sylius\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;

final class RequestBasedLocaleProvider implements RequestBasedLocaleProviderInterface
{
    /** @var RequestResolverInterface */
    private $hostnameBasedRequestResolver;

    /** @var SupportedLocaleProviderInterface */
    private $supportedLocaleProvider;

    public function __construct(
        RequestResolverInterface $hostnameBasedRequestResolver,
        SupportedLocaleProviderInterface $supportedLocaleProvider
    ) {
        $this->hostnameBasedRequestResolver = $hostnameBasedRequestResolver;
        $this->supportedLocaleProvider = $supportedLocaleProvider;
    }

    /** @throws ChannelNotFoundException */
    public function getLocaleCode(Request $request): string
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->hostnameBasedRequestResolver->findChannel($request);
        if (null === $channel) {
            throw ChannelNotFoundException::occur();
        }

        return $this->supportedLocaleProvider->provide($request->query->get('locale'), $channel);
    }
}
