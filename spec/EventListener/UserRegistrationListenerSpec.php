<?php

declare(strict_types = 1);

namespace spec\Sylius\ShopApiPlugin\EventListener;

use League\Tactician\CommandBus;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Command\GenerateVerificationToken;
use Sylius\ShopApiPlugin\Command\SendVerificationToken;
use Sylius\ShopApiPlugin\EventListener\UserRegistrationListener;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\GenericEvent;

final class UserRegistrationListenerSpec extends ObjectBehavior
{
    private const EXAMPLE_EMAIL = 'shop@example.com';

    function let(CommandBus $bus)
    {
        $this->beConstructedWith($bus);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UserRegistrationListener::class);
    }

    function it_generates_and_sends_verification_token(
        CommandBus $bus,
        CustomerInterface $customer,
        GenericEvent $event,
        ShopUserInterface $user
    ) {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);
        $user->getEmail()->willReturn(self::EXAMPLE_EMAIL);

        $bus->handle(new GenerateVerificationToken(self::EXAMPLE_EMAIL))->shouldBeCalled();
        $bus->handle(new SendVerificationToken(self::EXAMPLE_EMAIL))->shouldBeCalled();

        $this->handleUserVerification($event);
    }

    function it_throws_exception_if_other_class_then_customer_is_passed_to_event(
        GenericEvent $event
    ) {
        $event->getSubject()->willReturn(new \stdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->during('handleUserVerification', [$event]);
    }

    function it_throws_exception_if_customer_does_not_have_user_assigned(
        CustomerInterface $customer,
        GenericEvent $event
    ) {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handleUserVerification', [$event]);
    }
}
