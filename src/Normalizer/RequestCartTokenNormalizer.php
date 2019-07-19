<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Normalizer;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\ShopApiPlugin\Request\Cart\PickupCartRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RequestCartTokenNormalizer implements RequestCartTokenNormalizerInterface
{
    /** @var ValidatorInterface */
    private $validator;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        ValidatorInterface $validator,
        MessageBusInterface $bus,
        ChannelContextInterface $channelContext
    ) {
        $this->validator = $validator;
        $this->bus = $bus;
        $this->channelContext = $channelContext;
    }

    public function doNotAllowNullCartToken(Request $request): Request
    {
        if ($request->attributes->has('token')) {
            return $request;
        }

        $channel = $this->channelContext->getChannel();
        $pickupRequest = new PickupCartRequest($channel->getCode());

        $validationResults = $this->validator->validate($pickupRequest);

        if (0 !== $validationResults->count()) {
            throw new \InvalidArgumentException('Pickup request was not valid');
        }

        $pickupCartCommand = $pickupRequest->getCommand();
        $this->bus->dispatch($pickupCartCommand);

        $request->attributes->set('token', $pickupCartCommand->orderToken());

        return $request;
    }
}
