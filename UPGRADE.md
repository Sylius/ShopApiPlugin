# UPGRADE FROM 1.0.0-beta.18 to 1.0.0-beta.20
* The configuration key for the shop api is now `sylius_shop_api`.

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
