<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\DropCart;

final class DropCartHandlerSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $cartRepository): void
    {
        $this->beConstructedWith($cartRepository);
    }

    function it_handles_dropping_a_cart(OrderInterface $cart, OrderRepositoryInterface $cartRepository): void
    {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $cart->getState()->willReturn(OrderInterface::STATE_CART);

        $cartRepository->remove($cart)->shouldBeCalled();

        $this->handle(new DropCart('ORDERTOKEN'));
    }

    function it_throws_an_exception_if_cart_does_not_exist(OrderRepositoryInterface $cartRepository): void
    {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new DropCart('ORDERTOKEN')]);
    }

    function it_throws_an_exception_if_order_is_not_in_a_cart_state(OrderInterface $cart, OrderRepositoryInterface $cartRepository): void
    {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $cart->getState()->willReturn(OrderInterface::STATE_NEW);

        $cartRepository->remove($cart)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new DropCart('ORDERTOKEN')]);
    }
}
