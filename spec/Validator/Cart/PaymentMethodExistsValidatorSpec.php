<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Cart;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CartExists;
use Sylius\ShopApiPlugin\Validator\Constraints\PaymentMethodExists;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class PaymentMethodExistsValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $executionContext,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
    ): void {
        $this->beConstructedWith($paymentMethodRepository);

        $this->initialize($executionContext);
    }

    function it_does_not_add_constraint_if_payment_method_exists(
        OrderInterface $order,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $paymentMethodRepository->findOneBy(['code' => 'paypal'])->willReturn($order);

        $executionContext->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->validate('paypal', new PaymentMethodExists());
    }

    function it_adds_constraint_if_payment_method_does_not_exists(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $paymentMethodRepository->findOneBy(['code' => 'paypal'])->willReturn(null);

        $executionContext->addViolation('sylius.shop_api.checkout.payment_method_does_not_exist')->shouldBeCalled();

        $this->validate('paypal', new PaymentMethodExists());
    }

    function it_throws_an_exception_if_constraint_is_not_payment_exists(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['paypal', new CartExists()])
        ;
    }
}
