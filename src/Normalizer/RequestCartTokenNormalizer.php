<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Normalizer;

use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Request\PickupCartRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RequestCartTokenNormalizer implements RequestCartTokenNormalizerInterface
{
    /** @var ValidatorInterface */
    private $validator;

    /** @var CommandBus */
    private $bus;

    public function __construct(ValidatorInterface $validator, CommandBus $bus)
    {
        $this->validator = $validator;
        $this->bus = $bus;
    }

    public function doNotAllowNullCartToken(Request $request): Request
    {
        if ($request->attributes->has('token')) {
            return $request;
        }

        $pickupRequest = new PickupCartRequest();
        $pickupRequest->populateData($request);

        $validationResults = $this->validator->validate($pickupRequest);

        if (0 !== $validationResults->count()) {
            throw new \InvalidArgumentException('Pickup request was not valid');
        }

        $pickupCartCommand = $pickupRequest->getCommand();
        assert($pickupCartCommand instanceof PickupCart);

        $this->bus->handle($pickupCartCommand);

        $request->attributes->set('token', $pickupCartCommand->orderToken());

        return $request;
    }
}
