<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

<h1 align="center">Sylius Shop API </h1>

[![License](https://img.shields.io/packagist/l/sylius/shop-api-plugin.svg)](https://packagist.org/packages/sylius/shop-api-plugin) [![Version](https://img.shields.io/packagist/v/sylius/shop-api-plugin.svg)](https://packagist.org/packages/sylius/shop-api-plugin) [![Build Status](https://travis-ci.org/Sylius/ShopApiPlugin.svg?branch=master)](https://travis-ci.org/Sylius/ShopApiPlugin) [![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/Sylius/SyliusShopApiPlugin.svg)](https://scrutinizer-ci.com/g/Sylius/SyliusShopApiPlugin/)

<p align="center"><a href="https://sylius.com/plugins/" target="_blank"><img src="https://sylius.com/assets/badge-official-sylius-plugin.png" width="200"></a></p>

<p align="center">This repository contains a plugin that extends the <a href="https://github.com/Sylius/Sylius">Sylius eCommerce Framework</a> with an API in JSON that allows performing all standard shop operations from the customer perspective.</p>

## Documentation

The latest documentation is available [here](https://app.swaggerhub.com/apis/Sylius/sylius-shop-api/1.0.0).

## Installation

1. Run `composer require sylius/shop-api-plugin:^1.0@beta`.
2. Extend config files:
    1. Add SyliusShopApi to AppKernel.
    ```php
    // app/AppKernel.php
    
        public function registerBundles(): array
        {
            return array_merge(parent::registerBundles(), [
                new \Sylius\ShopApiPlugin\ShopApiPlugin(),
            ]);
        }
    ```
    2. Add `- { path: '^/shop-api', priorities: ['json'], fallback_format: json, prefer_extension: true }` to `fos_rest.format_listener.rules` 
    section in `app/config/config.yml` file and import config from Plugin.
    ```yml
    # app/config/config.yml
    
    imports:
        # ...
        - { resource: "@ShopApiPlugin/Resources/config/app/config.yml" }
        - { resource: "@ShopApiPlugin/Resources/config/app/sylius_mailer.yml" }

    # ...
    
    fos_rest:
        # ...
        
        format_listener:
            rules:
                - { path: '^/shop-api', priorities: ['json'], fallback_format: json, prefer_extension: true } # <-- Add this
                - { path: '^/api', priorities: ['json', 'xml'], fallback_format: json, prefer_extension: true }
                - { path: '^/', stop: true }
    
    ```
    
    3. Adjust checkout configuration to not collide with Sylius shop API. For example
    (assuming, that you are using regular Sylius security definition):
    ```yml
    # app/config/config.yml

    # ...

    sylius_shop:
        checkout_resolver:
            pattern: "%sylius.security.shop_regex%/checkout/.+"
    ```

    4. Add routing to `app/config/routing.yml`
    ```yml
    # app/config/routing.yml
    
    # ...
    
    sylius_shop_api:
        resource: "@ShopApiPlugin/Resources/config/routing.yml"
    ```
    5. Configure firewall
        1. Change `sylius.security.shop_regex` parameter to exclude `shop-api` prefix also
        2. Add ShopAPI regex parameter `shop_api.security.regex: "^/shop-api"`
        3. Add ShopAPI firewall config:
    ```yml
    parameters:
        # ...
    
        sylius.security.shop_regex: "^/(?!admin|api/.*|api$|shop-api)[^/]++" # shop-api has been added inside the brackets
        shop_api.security.regex: "^/shop-api"

    # ... 

    security:
        firewalls:
            // ...
    
            shop_api:
                pattern: "%shop_api.security.regex%"
                stateless:  true
                anonymous:  true
    ```
    
    6. (optional) if you have installed `nelmio/NelmioCorsBundle` for Support of Cross-Origin Ajax Request,
        1. Add the NelmioCorsBundle to the AppKernel
    
        ```php
        // app/AppKernel.php
        
        /**
         * {@inheritdoc}
         */
        public function registerBundles()
        {
            $bundles = array(
                // ...
                new Nelmio\CorsBundle\NelmioCorsBundle(),
                // ...
            );
            // ...
        }
        ```
    
        2. Add the configuration to the `config.yml  
    
        ```yml
        # app/config/config.yml
        
        # ...
        
        nelmio_cors:
            defaults:
                allow_credentials: false
                allow_origin: []
                allow_headers: []
                allow_methods: []
                expose_headers: []
                max_age: 0
                hosts: []
                origin_regex: false
                forced_allow_origin_value: ~
            paths:
                '^/shop-api/':
                    allow_origin: ['*']
                    allow_headers: ['Content-Type', 'authorization']
                    allow_methods: ['POST', 'PUT', 'GET', 'DELETE', 'OPTIONS']
                    max_age: 3600
        ```

## Additional features

### Attributes

If you would like to receive serialized attributes you need to define an array of theirs codes under `sylius_shop_api.included_attributes` key. E.g.
```yml
sylius_shop_api:
    included_attributes:
        - "MUG_MATERIAL_CODE"
```

### Authorization

By default no authorization is provided together with this bundle. But it is tested to work along with [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle)
In order to check example configuration check 
 - [security.yml](https://github.com/Sylius/SyliusShopApiPlugin/blob/master/tests/Application/app/config/security.yml)
 - [jwt parameters](https://github.com/Sylius/SyliusShopApiPlugin/blob/master/tests/Application/app/config/config.yml#L4-L7) and [jwt config](https://github.com/Sylius/SyliusShopApiPlugin/blob/master/tests/Application/app/config/config.yml#L55-L59) in config.yml
 - [example rsa keys](https://github.com/Sylius/SyliusShopApiPlugin/tree/master/tests/Application/app/config/jwt)
 - [login request](https://github.com/Sylius/SyliusShopApiPlugin/blob/master/tests/Controller/CustomerShopApiTest.php#L52-L68)
 
From the test app.

## Testing

The application can be tested with API Test Case. In order to run test suite execute the following commands:

```bash
$ cp tests/Application/.env.test.dist tests/Application/.env.test
$ set -a && source tests/Application/.env.test && set +a
$ (cd tests/Application && bin/console doctrine:database:create -e test)
$ (cd tests/Application && bin/console doctrine:schema:create -e test)

$ vendor/bin/phpunit
```

The application can be also tested with PHPSpec:

```bash
$ vendor/bin/phpspec run
```

## Security issues

If you think that you have found a security issue, please do not use the issue tracker and do not post it publicly. 
Instead, all security issues must be sent to `security@sylius.com`.

## Maintenance

This library is officially maintained by [Sylius](https://sylius.com) together with the following contributors outside of the organization:
 * [Maximilian Pesch](https://github.com/mamazu)
