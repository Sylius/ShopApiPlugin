# UPGRADE FROM 1.0.0-beta.21 to 1.0.0-beta.22

* The configuration key for the shop api is now `sylius_shop_api`.
* Plugin name has been changed from `ShopApiPlugin` to `SyliusShopApiPlugin`. As a result all resources which were loaded with this prefix: `@ShopApiPlugin` should be prefixed with `@SyliusShopApiPlugin`
* The route names of the address book and the order are now renames to fit the schema `sylius_shop_api...`
* The commands have been moved to the appropriate directories depending on the context.
* The requests have been moved to the appropriate directories depending on the context.
* The views have been moved to the appropriate directories depending on the context.
    * This might require changing the serializer settings in your application
* Tactician has been replaced with Symfony Messenger.
    * Used `League\Tactician\CommandBus` has been replaced with `Symfony\Component\Messenger\MessageBusInterface`
    * The commands are now dispatched using `dispatch()` method instead of `handle()`
    * The method name in handlers has been changed from `handle()` to `__invoke()`

* The product routes have been changed:

    | Old Route                             | New route                              |
    |:--------------------------------------|:---------------------------------------|
    | `products/{code}`                     | `products/by-code/{code}`              |
    | `products-by-slug/{slug}`             | `products/by-slug/{slug}`              |
    | `products/{code}/reviews`             | `products/by-code/{code}/reviews`      |
    | `products-reviews-by-slug/{slug}`     | `products/by-slug/{slug}/reviews`      |
    | `taxon-products/{code}`               | `taxon-products/by-code/{taxonCode}`   |
    | `taxon-products-by-slug/{taxonSlug}`  | `taxon-products/by-slug/{taxonSlug}`   |
    | `product/by-slug/{slug}/reviews`      | `products/by-slug/{slug}/reviews`      |

* The channel code has been removed from routes:

    | Old Route                                | New route                           |
    |:-----------------------------------------|:------------------------------------|
    | `{channelCode}/address-book/*`           | `address-book/*`                    |
    | `{channelCode}/carts/*`                  | `carts/*`                           |
    | `{channelCode}/checkout/*`               | `checkout/*`                        |
    | `{channelCode}/me`                       | `me`                                |
    | `{channelCode}/orders/*`                 | `orders/*`                          |
    | `{channelCode}/password-reset/*`         | `password-reset/*`                  |
    | `{channelCode}/product-latest`           | `product-latest`                    |
    | `{channelCode}/products/*`               | `products/*`                        |
    | `{channelCode}/register`                 | `orders/*`                          |
    | `{channelCode}/request-password-reset`   | `request-password-reset`            |
    | `{channelCode}/resend-verification-link` | `resend-verification-link`          |
    | `{channelCode}/verify-account`           | `verify-account`                    |
    | `{channelCode}/taxon-products/*`         | `taxon-products/*`                  |
    | `{channelCode}/taxons/*`                 | `taxons/*`                          |

* The channel code has been added as a second argument to `AddProductReviewByCodeRequest`, 
`AddProductReviewBySlugRequest`, `ResendVerificationTokenRequest` and `RegisterCustomerRequest` classes.

* The argument in constructor of `PickupCartRequest` class has been changed from `Request $request `to `string channelCode`.

* The address-book create route have been changed :
    | Old Route                             | New route                              |
    |:--------------------------------------|:---------------------------------------|
    | `address-book`                        | `address-book/`                        |

# UPGRADE FROM 1.0.0-beta.17 to 1.0.0-beta.18

* Customer registration payload changed:

    ```diff,json
    {
    -  "user": {
    -    "plainPassword": {
    -      "first": "foobar",
    -      "second": "foobar"
    -    }
    -  }
    +  "plainPassword": "foobar",
    +  "channel": "CHANNEL_CODE"
    }
    ```
