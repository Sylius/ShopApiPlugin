<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\UpdateCustomer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class UpdateCustomerHandlerSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $customerRepository,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->beConstructedWith(
            $customerRepository,
            $tokenStorage
        );
    }

    function it_updates_customer(
        TokenStorageInterface $tokenStorage,
        JWTUserToken $userToken,
        ShopUserInterface $shopUser,
        RepositoryInterface $customerRepository,
        CustomerInterface $customer
    )
    {
        $tokenStorage->getToken()->willReturn($userToken);
        $userToken->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);

        $customer->getId()->willReturn('USER_ID');
        $shopUser->getId()->willReturn('USER_ID');

        $customer->setFirstName('Sherlock')->shouldBeCalled();
        $customer->setLastName('Holmes')->shouldBeCalled();
        $customer->setEmail('sherlock@holmes.com')->shouldBeCalled();
        $customer->setBirthday(new \DateTime('2017-11-01'))->shouldBeCalled();
        $customer->setGender('male')->shouldBeCalled();
        $customer->setPhoneNumber('091231512512')->shouldBeCalled();
        $customer->setSubscribedToNewsletter(true)->shouldBeCalled();

        $customerRepository->add($customer)->shouldBeCalled();

        $this->handle(new UpdateCustomer(
                'Sherlock',
                'Holmes',
                'sherlock@holmes.com',
                '2017-11-01',
                'male',
                '091231512512',
                true
            )
        );
    }
}
