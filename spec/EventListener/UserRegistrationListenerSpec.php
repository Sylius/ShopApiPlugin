<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Customer\EnableCustomer;
use Sylius\ShopApiPlugin\Command\Customer\GenerateVerificationToken;
use Sylius\ShopApiPlugin\Command\Customer\SendVerificationToken;
use Sylius\ShopApiPlugin\Event\CustomerRegistered;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class UserRegistrationListenerSpec extends ObjectBehavior
{
    function let(
        MessageBusInterface $bus,
        ChannelRepositoryInterface $channelRepository,
        UserRepositoryInterface $userRepository,
        ObjectManager $userManager
    ): void {
        $this->beConstructedWith($bus, $channelRepository, $userRepository, $userManager);
    }

    function it_generates_and_sends_verification_token_if_channel_requires_verification(
        MessageBusInterface $bus,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel
    ): void {
        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);

        $channel->isAccountVerificationRequired()->willReturn(true);

        $firstCommand = new GenerateVerificationToken('shop@example.com');
        $bus->dispatch($firstCommand)->willReturn(new Envelope($firstCommand))->shouldBeCalled();

        $secondCommand = new SendVerificationToken('shop@example.com', 'WEB_GB');
        $bus->dispatch($secondCommand)->willReturn(new Envelope($secondCommand))->shouldBeCalled();

        $this->handleUserVerification(new CustomerRegistered(
            'shop@example.com',
            'Shop',
            'Example',
            'WEB_GB'
        ));
    }

    function it_enables_user_if_channel_does_not_require_verification(
        MessageBusInterface $bus,
        ChannelRepositoryInterface $channelRepository,
        UserRepositoryInterface $userRepository,
        ShopUserInterface $user,
        ChannelInterface $channel
    ): void {
        $userRepository->findOneByEmail('shop@example.com')->willReturn($user);
        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);

        $channel->isAccountVerificationRequired()->willReturn(false);

        $command = new EnableCustomer('shop@example.com');
        $bus->dispatch($command)->willReturn(new Envelope($command))->shouldBeCalled();

        $this->handleUserVerification(new CustomerRegistered(
            'shop@example.com',
            'Shop',
            'Example',
            'WEB_GB'
        ));
    }

    function it_throws_an_exception_if_channel_cannot_be_found(ChannelRepositoryInterface $channelRepository): void
    {
        $channelRepository->findOneByCode('WEB_GB')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handleUserVerification', [new CustomerRegistered(
            'shop@example.com',
            'Shop',
            'Example',
            'WEB_GB'
        )]);
    }
}
