<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Customer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\ShopApiPlugin\Command\VerifyAccount;
use Sylius\ShopApiPlugin\Handler\Customer\VerifyAccountHandler;

final class VerifyAccountHandlerSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository): void
    {
        $this->beConstructedWith($userRepository);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(VerifyAccountHandler::class);
    }

    function it_handles_emailing_user_with_verification_email(
        UserRepositoryInterface $userRepository,
        ShopUserInterface $user
    ): void {
        $userRepository->findOneBy(['emailVerificationToken' => 'RANDOM_TOKEN'])->willReturn($user);

        $user->setEmailVerificationToken(null)->shouldBeCalled();
        $user->setVerifiedAt(Argument::type(\DateTime::class))->shouldBeCalled();
        $user->enable()->shouldBeCalled();

        $this->handle(new VerifyAccount('RANDOM_TOKEN'));
    }

    function it_throws_an_exception_if_user_has_not_been_found(
        UserRepositoryInterface $userRepository
    ): void {
        $userRepository->findOneBy(['emailVerificationToken' => 'RANDOM_TOKEN'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new VerifyAccount('RANDOM_TOKEN')]);
    }
}
