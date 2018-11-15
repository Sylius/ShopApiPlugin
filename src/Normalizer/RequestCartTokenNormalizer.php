<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Normalizer;

use League\Tactician\CommandBus;
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

    public function __invoke(Request $request): Request
    {
        if ($request->attributes->has('token')) {
            return $request;
        }

        $pickupRequest = new PickupCartRequest($request);

        $validationResults = $this->validator->validate($pickupRequest);

        if (0 !== $validationResults->count()) {
            throw new \InvalidArgumentException('Pickup request was not valid');
        }

        $pickupCartCommand = $pickupRequest->getCommand();

        $this->bus->handle($pickupCartCommand);

        $token = $pickupCartCommand->orderToken();

        $request->attributes->set('token', $token);

        return $request;
    }
}
