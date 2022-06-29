<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Customer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\ShopUserExists;
use Sylius\ShopApiPlugin\Validator\Customer\ShopUserExistsValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ShopUserExistsValidatorSpec extends ObjectBehavior
{
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
        ExecutionContextInterface $executionContext,
    ): void {
        $userRepository->findOneByEmail('shop@example.com')->willReturn($user);

        $executionContext->addViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate('shop@example.com', new ShopUserExists());
    }

    function it_adds_constraint_if_user_does_not_exits_exists(
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $userRepository->findOneByEmail('shop@example.com')->willReturn(null);

        $executionContext->addViolation('sylius.shop_api.email.not_found')->shouldBeCalled();

        $this->validate('shop@example.com', new ShopUserExists());
    }
}
