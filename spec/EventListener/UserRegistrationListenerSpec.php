<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use League\Tactician\CommandBus;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\SyliusShopApiPlugin\Command\GenerateVerificationToken;
use Sylius\SyliusShopApiPlugin\Command\SendVerificationToken;
use Sylius\SyliusShopApiPlugin\Event\CustomerRegistered;

final class UserRegistrationListenerSpec extends ObjectBehavior
{
    function let(
        CommandBus $bus,
        ChannelRepositoryInterface $channelRepository,
        UserRepositoryInterface $userRepository,
        ObjectManager $userManager
    ) {
        $this->beConstructedWith($bus, $channelRepository, $userRepository, $userManager);
    }

    function it_generates_and_sends_verification_token_if_channel_requires_verification(
        CommandBus $bus,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel
    ) {
        $channelRepository->findOneByCode('FOOBAR')->willReturn($channel);

        $channel->isAccountVerificationRequired()->willReturn(true);

        $bus->handle(new GenerateVerificationToken('shop@example.com'))->shouldBeCalled();
        $bus->handle(new SendVerificationToken('shop@example.com'))->shouldBeCalled();

        $this->handleUserVerification(new CustomerRegistered(
            'shop@example.com',
            'Shop',
            'Example',
            'FOOBAR'
        ));
    }

    function it_enables_user_if_channel_does_not_require_verification(
        ChannelRepositoryInterface $channelRepository,
        UserRepositoryInterface $userRepository,
        ObjectManager $userManager,
        ShopUserInterface $user,
        ChannelInterface $channel
    ) {
        $userRepository->findOneByEmail('shop@example.com')->willReturn($user);
        $channelRepository->findOneByCode('FOOBAR')->willReturn($channel);

        $channel->isAccountVerificationRequired()->willReturn(false);

        $user->enable()->shouldBeCalled();

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush()->shouldBeCalled();

        $this->handleUserVerification(new CustomerRegistered(
            'shop@example.com',
            'Shop',
            'Example',
            'FOOBAR'
        ));
    }

    function it_throws_an_exception_if_channel_cannot_be_found(ChannelRepositoryInterface $channelRepository)
    {
        $channelRepository->findOneByCode('FOOBAR')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handleUserVerification', [new CustomerRegistered(
            'shop@example.com',
            'Shop',
            'Example',
            'FOOBAR'
        )]);
    }
}
