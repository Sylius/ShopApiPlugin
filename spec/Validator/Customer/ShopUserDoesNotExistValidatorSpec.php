<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Customer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\ShopUserDoesNotExist;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ShopUserDoesNotExistValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, UserRepositoryInterface $userRepository): void
    {
        $this->beConstructedWith($userRepository);
        $this->initialize($executionContext);
    }

    function it_adds_a_violation_if_the_email_is_already_taken(
        ExecutionContextInterface $executionContext,
        UserRepositoryInterface $userRepository,
        ShopUserInterface $user
    ): void {
        $userRepository->findOneByEmail('test@sylius.com')->willReturn($user);

        $executionContext->addViolation('sylius.shop_api.email.unique')->shouldBeCalled();

        $this->validate('test@sylius.com', new ShopUserDoesNotExist());
    }

    function it_does_not_add_a_violation_if_the_email_is_empty(
        ExecutionContextInterface $executionContext,
        UserRepositoryInterface $userRepository
    ): void {
        $userRepository->findOneByEmail(Argument::any())->shouldNotBeCalled();

        $executionContext->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate(null, new ShopUserDoesNotExist());
    }

    function it_does_not_add_a_violation_if_the_email_is_available(
        ExecutionContextInterface $executionContext,
        UserRepositoryInterface $userRepository
    ): void {
        $userRepository->findOneByEmail('test@sylius.com')->willReturn(null);

        $executionContext->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate('test@sylius.com', new ShopUserDoesNotExist());
    }
}
