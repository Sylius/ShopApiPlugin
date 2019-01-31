<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Customer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\ShopApiPlugin\Factory\Customer\CustomerViewFactory;
use Sylius\ShopApiPlugin\Factory\Customer\CustomerViewFactoryInterface;
use Sylius\ShopApiPlugin\View\CustomerView;

final class CustomerViewFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(CustomerView::class);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CustomerViewFactory::class);
    }

    function it_is_a_customer_view_factory(): void
    {
        $this->shouldImplement(CustomerViewFactoryInterface::class);
    }

    function it_creates_customer_view(CustomerInterface $customer): void
    {
        $customer->getId()->willReturn('CUSTOMER_ID');
        $customer->getFirstName()->willReturn('Sherlock');
        $customer->getLastName()->willReturn('Holmes');
        $customer->getEmail()->willReturn('sherlock@holmes.com');
        $customer->getBirthday()->willReturn(new \DateTime('2017-11-01'));
        $customer->getGender()->willReturn('m');
        $customer->getPhoneNumber()->willReturn('0912538092');
        $customer->isSubscribedToNewsletter()->willReturn(true);

        $customerView = new CustomerView();

        $customerView->id = 'CUSTOMER_ID';
        $customerView->firstName = 'Sherlock';
        $customerView->lastName = 'Holmes';
        $customerView->email = 'sherlock@holmes.com';
        $customerView->birthday = new \DateTime('2017-11-01');
        $customerView->gender = 'm';
        $customerView->phoneNumber = '0912538092';
        $customerView->subscribedToNewsletter = true;

        $this->create($customer)->shouldBeLike($customerView);
    }
}
