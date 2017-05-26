<?php

namespace Sylius\ShopApiPlugin\EventListener;

use League\Tactician\CommandBus;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Command\GenerateVerificationToken;
use Sylius\ShopApiPlugin\Command\SendVerificationToken;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class UserRegistrationListener
{
    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus) {
        $this->bus = $bus;
    }

    public function handleUserVerification(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        Assert::isInstanceOf($customer, CustomerInterface::class);

        /** @var ShopUserInterface $user $user */
        $user = $customer->getUser();
        Assert::notNull($user);

        $this->bus->handle(new GenerateVerificationToken($user->getEmail()));
        $this->bus->handle(new SendVerificationToken($user->getEmail()));
    }
}
