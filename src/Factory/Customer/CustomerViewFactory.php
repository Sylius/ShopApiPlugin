<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Customer;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\ShopApiPlugin\View\Customer\CustomerView;

final class CustomerViewFactory implements CustomerViewFactoryInterface
{
    /** @var string */
    private $customerViewClass;

    public function __construct(string $customerViewClass)
    {
        $this->customerViewClass = $customerViewClass;
    }

    /** {@inheritdoc} */
    public function create(CustomerInterface $customer): CustomerView
    {
        /** @var CustomerView $customerView */
        $customerView = new $this->customerViewClass();

        $customerView->id = $customer->getId();
        $customerView->firstName = $customer->getFirstName();
        $customerView->lastName = $customer->getLastName();
        $customerView->email = $customer->getEmail();
        $customerView->birthday = $customer->getBirthday();
        $customerView->gender = $customer->getGender();
        $customerView->phoneNumber = $customer->getPhoneNumber();
        $customerView->subscribedToNewsletter = $customer->isSubscribedToNewsletter();

        return $customerView;
    }
}
