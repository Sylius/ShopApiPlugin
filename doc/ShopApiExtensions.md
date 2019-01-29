# Extending the API
If you want to extend the API functionality, the methodology to use depends on the part that needs to be changed.

## Extending an existing view
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
class CartViewFactory implements Sylius\ShopApiPlugin\Factory\Cart\CartSummaryFactoryInterface
{
    public function __construct(CartSummaryFactoryInterface $cartSummaryViewFactory)
    {
        $this->baseCartFactory = $cartSummaryViewFactory;
    }

    public function create(OrderItem $cart, string $locale): CartSummaryView
    {
        $cart = $this->baseCartFactory->create($cart, $locale);

        $cart->someProperty = $oder->getSomeProperty();

        return $cart;
    }
}
```

## Extending adding a request
If you want to add an argument to the route or the request then you can 
* Extend the `Request` and add the new property and fill it in the constructor
* Overwrite the controller to create a different request
* Create a command that extends the old one and return it in the `getCommand` of the request
* If the logic of the handler needs to change as well, then register a different handler for the command otherwise register the ShopApi Handler for the new command to execute the old logic.
