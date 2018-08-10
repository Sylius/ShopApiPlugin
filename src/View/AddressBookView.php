<?php
declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View;

class AddressBookView
{
    /** @var AddressView|null */
    public $defaultAddress;

    /** @var AddressView[] */
    public $addresses;
}
