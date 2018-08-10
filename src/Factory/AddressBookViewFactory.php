<?php
declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\ShopApiPlugin\View\AddressBookView;
use Sylius\ShopApiPlugin\View\AddressView;

final class AddressBookViewFactory implements AddressBookViewFactoryInterface
{
    /**
     * @var AddressViewFactoryInterface
     */
    private $addressViewFactory;

    /**
     * AddressBookViewFactory constructor.
     *
     * @param AddressViewFactoryInterface $addressViewFactory
     */
    public function __construct(
        AddressViewFactoryInterface $addressViewFactory
    ) {
        $this->addressViewFactory = $addressViewFactory;
    }

    public function create(?AddressInterface $address, Collection $otherAddress): AddressBookView
    {
        /** @var AddressBookView $addressBookView */
        $addressBookView                 = new AddressBookView();
        $addressBookView->defaultAddress = ($address === null) ? null : $this->addressViewFactory->create($address);
        $addressBookView->addresses      = $otherAddress->map(
            function (AddressInterface $address): AddressView {
                return $this->addressViewFactory->create($address);
            }
        );
        return $addressBookView;
    }
}
