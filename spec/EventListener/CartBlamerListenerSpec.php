<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
use Sylius\ShopApiPlugin\EventListener\CartBlamerListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CartBlamerListenerSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, MessageBusInterface $bus, RequestStack $requestStack): void
    {
        $this->beConstructedWith($orderRepository, $bus, $requestStack);
    }

    function it_should_be_initializable(): void
    {
        $this->beAnInstanceOf(CartBlamerListener::class);
    }

    function it_assigns_the_cart_to_the_logged_in_user(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart,
        MessageBusInterface $bus,
        RequestStack $requestStack,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        JWTCreatedEvent $jwtCreated,
    ): void {
        $request = new Request([], ['token' => 'CART_TOKEN']);
        $requestStack->getCurrentRequest()->willReturn($request);

        $jwtCreated->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('test@sylius.com');

        $orderRepository->findOneBy(['tokenValue' => 'CART_TOKEN'])->willReturn($cart);

        $assignCartCommand = new AssignCustomerToCart('CART_TOKEN', 'test@sylius.com');
        $bus->dispatch($assignCartCommand)->willReturn(new Envelope($assignCartCommand))->shouldBeCalled();

        $this->onJwtLogin($jwtCreated);
    }

    function it_does_not_assign_the_cart_to_admin_user(
        OrderRepositoryInterface $orderRepository,
        MessageBusInterface $bus,
        RequestStack $requestStack,
        AdminUserInterface $adminUser,
        JWTCreatedEvent $jwtCreated,
    ): void {
        $request = new Request([], ['token' => 'CART_TOKEN']);
        $requestStack->getCurrentRequest()->willReturn($request);

        $jwtCreated->getUser()->willReturn($adminUser);

        $orderRepository->findOneBy(Argument::any())->shouldNotBeCalled();

        $bus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->onJwtLogin($jwtCreated);
    }

    function it_does_not_assign_a_cart_if_there_is_no_cart_with_this_token(
        OrderRepositoryInterface $orderRepository,
        MessageBusInterface $bus,
        RequestStack $requestStack,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        JWTCreatedEvent $jwtCreated,
    ): void {
        $request = new Request([], ['token' => 'CART_TOKEN']);
        $requestStack->getCurrentRequest()->willReturn($request);

        $jwtCreated->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('test@sylius.com');

        $orderRepository->findOneBy(['tokenValue' => 'CART_TOKEN'])->willReturn(null);

        $bus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->onJwtLogin($jwtCreated);
    }

    function it_does_not_assign_a_cart_if_the_token_was_created_on_the_console(
        OrderRepositoryInterface $orderRepository,
        MessageBusInterface $bus,
        RequestStack $requestStack,
        JWTCreatedEvent $jwtCreated,
    ): void {
        $requestStack->getCurrentRequest()->willReturn(null);

        $orderRepository->findOneBy(Argument::any())->shouldNotBeCalled();

        $bus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->onJwtLogin($jwtCreated);
    }
}
