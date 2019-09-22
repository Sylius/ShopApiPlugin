<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Customer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\VerificationTokenExists;
use Sylius\ShopApiPlugin\Validator\Customer\VerificationTokenExistsValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class VerificationTokenExistsValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, UserRepositoryInterface $userRepository): void
    {
        $this->beConstructedWith($userRepository);

        $this->initialize($executionContext);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(VerificationTokenExistsValidator::class);
    }

    function it_does_not_add_constraint_if_verification_token_is_empty(
        ExecutionContextInterface $executionContext
    ): void {
        $executionContext->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate('', new VerificationTokenExists());
    }

    function it_does_not_add_constraint_if_verification_token_is_null(
        ExecutionContextInterface $executionContext
    ): void {
        $executionContext->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate(null, new VerificationTokenExists());
    }

    function it_does_not_add_constraint_if_verification_token_exists(
        ShopUserInterface $user,
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $userRepository->findOneBy(['emailVerificationToken' => 'token'])->willReturn($user);

        $executionContext->addViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate('token', new VerificationTokenExists());
    }

    function it_adds_constraint_if_verification_token_does_not_exist(
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $userRepository->findOneBy(['emailVerificationToken' => 'token'])->willReturn(null);

        $executionContext->addViolation('sylius.shop_api.token.not_exists')->shouldBeCalled();

        $this->validate('token', new VerificationTokenExists());
    }
}
