<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\UpdateCustomer;

final class UpdateCustomerHandlerSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $customerRepository
    )
    {
        $this->beConstructedWith(
            $customerRepository
        );
    }

    function it_updates_customer(
        RepositoryInterface $customerRepository,
        CustomerInterface $customer
    )
    {
        $customerRepository->findOneBy(['email' => 'sherlock@holmes.com'])->willReturn($customer);

        $customer->getId()->willReturn('USER_ID');

        $customer->setFirstName('Sherlock')->shouldBeCalled();
        $customer->setLastName('Holmes')->shouldBeCalled();
        $customer->setEmail('sherlock@holmes.com')->shouldBeCalled();
        $customer->setBirthday(new \DateTimeImmutable('2017-11-01'))->shouldBeCalled();
        $customer->setGender('m')->shouldBeCalled();
        $customer->setPhoneNumber('091231512512')->shouldBeCalled();
        $customer->setSubscribedToNewsletter(true)->shouldBeCalled();

        $customerRepository->add($customer)->shouldBeCalled();

        $this->handle(new UpdateCustomer(
                'Sherlock',
                'Holmes',
                'sherlock@holmes.com',
                '2017-11-01',
                'm',
                '091231512512',
                true
            )
        );
    }
}
