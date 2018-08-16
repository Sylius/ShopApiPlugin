<?php
declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\ShopApiPlugin\View\AddressBookView;

interface AddressBookViewFactoryInterface
{
    /**
     * Creates a view for the address book
     *
     * @param null|AddressInterface $address
     * @param Collection            $otherAddress
     *
     * @return AddressBookView
     */
    public function create(?AddressInterface $address, Collection $otherAddress): AddressBookView;
}
