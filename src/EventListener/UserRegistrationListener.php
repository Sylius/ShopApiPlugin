<?php

namespace Sylius\ShopApiPlugin\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use League\Tactician\CommandBus;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\ShopApiPlugin\Command\GenerateVerificationToken;
use Sylius\ShopApiPlugin\Command\SendVerificationToken;
use Sylius\ShopApiPlugin\Event\CustomerRegistered;
use Webmozart\Assert\Assert;

final class UserRegistrationListener
{
    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var ObjectManager
     */
    private $userManager;

    /**
     * @param CommandBus $bus
     * @param ChannelRepositoryInterface $channelRepository
     * @param UserRepositoryInterface $userRepository
     * @param ObjectManager $userManager
     */
    public function __construct(
        CommandBus $bus,
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

        $this->bus->handle(new GenerateVerificationToken($event->email()));
        $this->bus->handle(new SendVerificationToken($event->email()));
    }
}
