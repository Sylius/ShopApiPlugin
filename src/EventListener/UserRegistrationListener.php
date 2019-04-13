<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
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

    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var ObjectManager */
    private $userManager;

    public function __construct(
        MessageBusInterface $bus,
        ChannelRepositoryInterface $channelRepository,
        UserRepositoryInterface $userRepository,
        ObjectManager $userManager
    ) {
        $this->bus = $bus;
        $this->channelRepository = $channelRepository;
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
    }

    public function handleUserVerification(CustomerRegistered $event): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($event->channelCode());

        Assert::isInstanceOf($channel, ChannelInterface::class);

        if (!$channel->isAccountVerificationRequired()) {
            $user = $this->userRepository->findOneByEmail($event->email());
            $user->enable();

            // TODO: Get rid of implementation details here
            $this->userManager->persist($user);
            $this->userManager->flush();

            return;
        }

        $this->bus->dispatch(new GenerateVerificationToken($event->email()));
        $this->bus->dispatch(new SendVerificationToken($event->email(), $event->channelCode()));
    }
}
