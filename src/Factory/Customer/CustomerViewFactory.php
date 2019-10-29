<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Customer;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ShopApiPlugin\Factory\ImageViewFactory;
use Sylius\ShopApiPlugin\View\Customer\CustomerView;

final class CustomerViewFactory implements CustomerViewFactoryInterface
{

    /** @var string */
    private $customerViewClass;

    /** @var ImageViewFactory */
    private $imageViewFactory;

    public function __construct(string $customerViewClass, ImageViewFactory $imageViewFactory)
    {
        $this->customerViewClass = $customerViewClass;
        $this->imageViewFactory  = $imageViewFactory;
    }

    /** {@inheritdoc} */
    public function create(CustomerInterface $customer): CustomerView
    {
        /** @var CustomerView $customerView */
        $customerView = new $this->customerViewClass();

        $customerView->id                     = $customer->getId();
        $customerView->firstName              = $customer->getFirstName();
        $customerView->lastName               = $customer->getLastName();
        $customerView->email                  = $customer->getEmail();
        $customerView->birthday               = $customer->getBirthday();
        $customerView->gender                 = $customer->getGender();
        $customerView->phoneNumber            = $customer->getPhoneNumber();
        $customerView->subscribedToNewsletter = $customer->isSubscribedToNewsletter();
        $customerView->group                  = $customer->getGroup();

        if ($customer->getUser() && $customer->getUser()->getAvatar()) {
            /** @var ImageInterface $image */
            $image = $customer->getUser()->getAvatar();

            $customerView->avatar = $this->imageViewFactory->create($image);
        }
        if($customer->getCustomerPoint()){
            $customerView->points               = $customer->getCustomerPoint()->getPoints();
        }
        return $customerView;
    }
}
