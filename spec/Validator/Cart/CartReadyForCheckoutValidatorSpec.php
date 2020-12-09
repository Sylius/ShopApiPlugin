<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Cart;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CartReadyForCheckout;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CartReadyForCheckoutValidatorSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $repository,
        FactoryInterface $stateMachineFactory,
        ExecutionContextInterface $context
    ): void {
        $this->beConstructedWith($repository, $stateMachineFactory);
        $this->initialize($context);
    }

    function it_does_not_validate_a_cart_if_it_does_not_exist(
        RepositoryInterface $repository,
        FactoryInterface $stateMachineFactory
    ): void {
        $repository->findOneBy(['tokenValue' => 'CART_TOKEN'])->willReturn(null);

        $stateMachineFactory->get(Argument::cetera())->shouldNotBeCalled();

        $this->validate('CART_TOKEN', new CartReadyForCheckout());
    }

    function it_adds_a_violation_if_cart_has_no_shipping_or_billig_address(
        RepositoryInterface $repository,
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        ExecutionContextInterface $context
    ): void {
        $repository->findOneBy(['tokenValue' => 'CART_TOKEN'])->willReturn($order);

        $stateMachineFactory->get($order, 'sylius_order_checkout')->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(false);

        $order->getCheckoutState()->willReturn('cart');

        $context->addViolation('sylius.shop_api.checkout.address_required')->shouldBeCalled();

        $this->validate('CART_TOKEN', new CartReadyForCheckout());
    }

    function it_adds_a_violation_if_cart_has_no_shipping_method(
        RepositoryInterface $repository,
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        ExecutionContextInterface $context
    ): void {
        $repository->findOneBy(['tokenValue' => 'CART_TOKEN'])->willReturn($order);

        $stateMachineFactory->get($order, 'sylius_order_checkout')->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(false);

        $order->getCheckoutState()->willReturn('addressed');

        $context->addViolation('sylius.shop_api.checkout.shipping_required')->shouldBeCalled();

        $this->validate('CART_TOKEN', new CartReadyForCheckout());
    }

    function it_adds_a_violation_if_cart_can_not_checkout(
        RepositoryInterface $repository,
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        ExecutionContextInterface $context
    ): void {
        $repository->findOneBy(['tokenValue' => 'CART_TOKEN'])->willReturn($order);

        $stateMachineFactory->get($order, 'sylius_order_checkout')->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(false);

        $order->getCheckoutState()->willReturn('payment_selected');

        $context->addViolation('sylius.shop_api.checkout.not_ready_for_checkout')->shouldBeCalled();

        $this->validate('CART_TOKEN', new CartReadyForCheckout());
    }

    function it_add_no_violation_if_cart_can_checkout(
        RepositoryInterface $repository,
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        ExecutionContextInterface $context
    ): void {
        $repository->findOneBy(['tokenValue' => 'CART_TOKEN'])->willReturn($order);

        $stateMachineFactory->get($order, 'sylius_order_checkout')->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(true);

        $context->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate('CART_TOKEN', new CartReadyForCheckout());
    }
}
