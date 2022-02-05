<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public function create(CustomerInterface $customer)
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
