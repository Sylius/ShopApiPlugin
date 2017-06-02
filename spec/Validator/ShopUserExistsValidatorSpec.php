<?php

declare(strict_types = 1);

namespace spec\Sylius\ShopApiPlugin\Validator;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\ShopUserExistsValidator;
use Sylius\ShopApiPlugin\Validator\Constraints\ShopUserExists;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ShopUserExistsValidatorSpec extends ObjectBehavior
{
    private const EXAMPLE_EMAIL = 'shop@example.com';

    function let(ExecutionContextInterface $executionContext, UserRepositoryInterface $userRepository): void
    {
        $this->beConstructedWith($userRepository);

        $this->initialize($executionContext);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ShopUserExistsValidator::class);
    }

    function it_does_not_add_constraint_if_user_exists(
        ShopUserInterface $user,
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext
    ) {
        $userRepository->findOneByEmail(self::EXAMPLE_EMAIL)->willReturn($user);

        $executionContext->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->validate('' . self::EXAMPLE_EMAIL . '', new ShopUserExists());
    }

    function it_adds_constraint_if_user_does_not_exits_exists(
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext
    ) {
        $userRepository->findOneByEmail(self::EXAMPLE_EMAIL)->willReturn(null);

        $executionContext->addViolation('sylius.shop_api.email.not_found')->shouldBeCalled();

        $this->validate(self::EXAMPLE_EMAIL, new ShopUserExists());
    }
}
