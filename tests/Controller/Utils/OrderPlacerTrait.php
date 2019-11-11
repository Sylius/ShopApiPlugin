<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Utils;

use Sylius\ShopApiPlugin\Command\Cart\AddressOrder;
use Sylius\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
use Sylius\ShopApiPlugin\Command\Cart\ChoosePaymentMethod;
use Sylius\ShopApiPlugin\Command\Cart\ChooseShippingMethod;
use Sylius\ShopApiPlugin\Command\Cart\CompleteOrder;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\Messenger\MessageBusInterface;

trait OrderPlacerTrait
{
    protected function placeOrderForCustomerWithEmail(string $email, string $token): void
    {
        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new AssignCustomerToCart($token, $email));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->dispatch(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
                'provinceCode' => 'GB-ENG',
            ]), Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
            'provinceCode' => 'GB-ENG',
        ])
        ));
        $bus->dispatch(new ChooseShippingMethod($token, 0, 'DHL'));
        $bus->dispatch(new ChoosePaymentMethod($token, 0, 'PBC'));
        $bus->dispatch(new CompleteOrder($token));
    }

    /** Function is not typehinted because has to be compatible with \ApiTestCase\ApiTestCase::get($id) */
    abstract protected function get($id);
}
