<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Customer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\ShopApiPlugin\Command\Customer\SendOrderConfirmation;
use Sylius\ShopApiPlugin\Handler\Customer\SendOrderConfirmationHandler;
use Sylius\ShopApiPlugin\Mailer\Emails;

final class SendOrderConfirmationHandlerSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, SenderInterface $sender): void
    {
        $this->beConstructedWith($orderRepository, $sender);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(SendOrderConfirmationHandler::class);
    }

    function it_handles_emailing_user_when_order_has_been_completed(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        CustomerInterface $customer,
        SenderInterface $sender
    ): void {
        $orderRepository->findOneByTokenValue('ORDERTOKEN')->willReturn($order);
        $order->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('example@customer.com');

        $sender->send(Emails::EMAIL_ORDER_CONFIRMATION, ['example@customer.com'], ['order' => $order])->shouldBeCalled();

        $this(new SendOrderConfirmation('ORDERTOKEN'));
    }

    function it_throws_an_exception_if_user_has_not_been_found(OrderRepositoryInterface $orderRepository): void
    {
        $orderRepository->findOneByTokenValue('ORDERTOKEN')->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new SendOrderConfirmation('ORDERTOKEN')])
        ;
    }
}
