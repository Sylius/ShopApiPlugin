<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Normalizer;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\CommandProvider\ChannelBasedCommandProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

final class RequestCartTokenNormalizer implements RequestCartTokenNormalizerInterface
{
    /** @var MessageBusInterface */
    private $bus;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var ChannelBasedCommandProviderInterface */
    private $pickupCartCommandProvider;

    public function __construct(
        MessageBusInterface $bus,
        ChannelContextInterface $channelContext,
        ChannelBasedCommandProviderInterface $pickupCartCommandProvider
    ) {
        $this->bus = $bus;
        $this->channelContext = $channelContext;
        $this->pickupCartCommandProvider = $pickupCartCommandProvider;
    }

    public function doNotAllowNullCartToken(Request $request): Request
    {
        if ($request->attributes->has('token')) {
            return $request;
        }

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $validationResults = $this->pickupCartCommandProvider->validate($request, $channel);
        if (0 !== $validationResults->count()) {
            throw new \InvalidArgumentException('Pickup request was not valid');
        }

        /** @var PickupCart $pickupCartCommand */
        $pickupCartCommand = $this->pickupCartCommandProvider->getCommand($request, $channel);

        $this->bus->dispatch($pickupCartCommand);

        $request->attributes->set('token', $pickupCartCommand->orderToken());

        return $request;
    }
}
