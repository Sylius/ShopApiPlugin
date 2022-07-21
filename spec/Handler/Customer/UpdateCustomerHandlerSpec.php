<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Customer;

use DateTimeImmutable;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\Customer\UpdateCustomer;

final class UpdateCustomerHandlerSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $customerRepository,
    ): void {
        $this->beConstructedWith(
            $customerRepository,
        );
    }

    function it_updates_customer(
        RepositoryInterface $customerRepository,
        CustomerInterface $customer,
    ): void {
        $birthday = new DateTimeImmutable('2019-02-10 10:22:00');

        $customerRepository->findOneBy(['email' => 'sherlock@holmes.com'])->willReturn($customer);

        $customer->getId()->willReturn('USER_ID');

        $customer->setFirstName('Sherlock')->shouldBeCalled();
        $customer->setLastName('Holmes')->shouldBeCalled();
        $customer->setBirthday($birthday)->shouldBeCalled();
        $customer->setGender('m')->shouldBeCalled();
        $customer->setPhoneNumber('091231512512')->shouldBeCalled();
        $customer->setSubscribedToNewsletter(true)->shouldBeCalled();

        $customerRepository->add($customer)->shouldBeCalled();

        $this(
            new UpdateCustomer(
                'Sherlock',
                'Holmes',
                'sherlock@holmes.com',
                $birthday,
                'm',
                '091231512512',
                true,
            ),
        );
    }
}
