<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\EventListener;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Command\Customer\EnableCustomer;
use Sylius\ShopApiPlugin\Command\Customer\GenerateVerificationToken;
use Sylius\ShopApiPlugin\Command\Customer\SendVerificationToken;
use Sylius\ShopApiPlugin\Event\CustomerRegistered;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class UserRegistrationListener
{
    /** @var MessageBusInterface */
    private $bus;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    public function __construct(MessageBusInterface $bus, ChannelRepositoryInterface $channelRepository)
    {
        $this->bus = $bus;
        $this->channelRepository = $channelRepository;
    }

    public function handleUserVerification(CustomerRegistered $event): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($event->channelCode());

        Assert::isInstanceOf($channel, ChannelInterface::class);

        if (!$channel->isAccountVerificationRequired()) {
            $this->bus->dispatch(new EnableCustomer($event->email()));

            return;
        }

        $this->bus->dispatch(new GenerateVerificationToken($event->email()));
        $this->bus->dispatch(new SendVerificationToken($event->email(), $event->channelCode()));
    }
}
