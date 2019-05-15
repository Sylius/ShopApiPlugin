# Extending the API
If you want to extend the API functionality, the methodology to use depends on the part that needs to be changed.

## Extending the factories
If you don't want to change the underlying logic of the ShopApi and only want to extend the view by some properties, then the following steps can be taken:
* Create a new `View` that extends the old one (eg. `\Vendor\ShopApiPlugin\View\Cart\CartSummaryView extends Sylius\ShopApi\View\Cart\CartSummaryView`)
* Change the `View` class in the configuration of the plugin:
```yaml
shop_api:
    view_classes:
        cart_summary: \Vendor\ShopApiPlugin\View\Cart\CartSummaryView
```

* Decorate the factory like so:
```php
<?php
declare(strict_types=1);

namespace Vendor\ShopApiPlugin\Factory\Cart;

use Sylius\ShopApiPlugin\Factory\Cart\CartSummaryFactoryInterface;

class CartSummaryViewFactory implements CartSummaryFactoryInterface
{
   /** @var CartSummaryFactoryInterface */
   private $baseCartFactory;

    public function __construct(CartSummaryFactoryInterface $cartSummaryViewFactory)
    {
        $this->baseCartFactory = $cartSummaryViewFactory;
    }

    public function create(OrderItem $cart, string $locale): CartSummaryView
    {
        /** @var \Vendor\ShopApiPlugin\View\Cart\CartSummaryView $cartView */
        $cartView = $this->baseCartFactory->create($cart, $locale);

        $cartView->someProperty = $cart->getSomePropertyView();

        return $cart;
    }
}
```

If you have customized the entity that should be used in the factory, for example in this case the `OrderItem` then you can do something like the following:

```php
    public function create(OrderItem $cart, string $locale): CartSummaryView
    {
        /** @var \Vendor\ShopApiPlugin\View\Cart\CartSummaryView $cartView */
        $cartView = $this->baseCartFactory->create($cart, $locale);

        if ($cart instanceof \Vendor\Entity\OrderItem) {
            $cartView->someProperty = $cart->getSomePropertyView();
        }

        return $cart;
    }
```

This will make it backwards compatible with Sylius and the Typechecker will also approve on this.
