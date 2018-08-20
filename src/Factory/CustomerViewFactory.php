<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\SyliusShopApiPlugin\View\CustomerView;

final class CustomerViewFactory implements CustomerViewFactoryInterface
{
    /** @var string */
    private $customerViewClass;

    public function __construct(string $customerViewClass)
    {
        $this->customerViewClass = $customerViewClass;
    }

    /**
     * {@inheritdoc}
     */
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
