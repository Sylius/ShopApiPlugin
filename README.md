# Sylius Shop API [![License](https://img.shields.io/packagist/l/sylius/shop-api-plugin.svg)](https://packagist.org/packages/sylius/shop-api-plugin) [![Version](https://img.shields.io/packagist/v/sylius/shop-api-plugin.svg)](https://packagist.org/packages/sylius/shop-api-plugin) [![Build Status on linux](https://travis-ci.org/Sylius/SyliusShopApiPlugin.svg?branch=master)](https://travis-ci.org/Sylius/SyliusShopApiPlugin) [![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/Sylius/SyliusShopApiPlugin.svg)](https://scrutinizer-ci.com/g/Sylius/SyliusShopApiPlugin/)

This repository provides a ShopApi implementation on the top of [Sylius E-Commerce platform](https://github.com/Sylius/Sylius).
 
# Beware

It is also just an addition to Sylius - Standard. Please, check [official documentation](http://docs.sylius.org/en/latest/) in order to understand the basic concepts.

## Pre - requirements
 
In order to run this plugin you need to fulfill following requirements:
1. Installed composer [Composer](https://getcomposer.org/).
    ```bash
    $ wget http://getcomposer.org/composer.phar
    $ php composer.phar create-project -s beta sylius/sylius-standard project
    ```

2. Installed Sylius
    ```bash
    $ cd project
    $ php bin/console sylius:install
    ```

Rest of the command are executed inside `project` folder.

## Usage

1. Run `composer require sylius/shop-api-plugin`.
2. Extend config files:
    1. Add SyliusShopApi to AppKernel.
    ```php
    // app/AppKernel.php
    
        /**
         * {@inheritdoc}
         */
        public function registerBundles()
        {
            $bundles = [
                // ...
    
                new \Sylius\ShopApiPlugin\ShopApiPlugin(),
                new \League\Tactician\Bundle\TacticianBundle(),
            ];
    
            return array_merge(parent::registerBundles(), $bundles);
        }
    ```
    2. Add `- { path: '^/shop-api', priorities: ['json'], fallback_format: json, prefer_extension: true }` to `fos_rest.format_listener.rules` 
    section in `app/config/config.yml` file and import config from Plugin.
    ```yml
    # app/config/config.yml
    
    imports:
        # ...
        - { resource: "@ShopApiPlugin/Resources/config/app/config.yml" }

    # ...
    
    fos_rest:
        # ...
        
        format_listener:
            rules:
                - { path: '^/shop-api', priorities: ['json'], fallback_format: json, prefer_extension: true } # <-- Add this
                - { path: '^/api', priorities: ['json', 'xml'], fallback_format: json, prefer_extension: true }
                - { path: '^/', stop: true }
    
    ```
    3. Add routing to `app/config/routing.yml`
    ```yml
    # app/config/routing.yml
    
    # ...
    
    sylius_shop_api:
        resource: "@ShopApiPlugin/Resources/config/routing.yml"
    ```
    4. Configure firewall
        1. Change `sylius.security.shop_regex` parameter to exclude `shop-api` prefix also
        2. Add ShopAPI regex parameter `shop_api.security.regex: "^/shop-api"`
        3. Add ShopAPI firewall config:
    ```yml
    parameters:
        # ...
    
        sylius.security.shop_regex: "^/(?!admin|api|shop-api)[^/]++" # shop-api has been added inside the brackets 
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
    
    5. Adjust checkout configuration to not collide with Sylius shop API. For example
    (assuming, that you are using regular Sylius security definition):
    ```yml
    # app/config/config.yml

    # ...

    sylius_shop:
        checkout_resolver:
            pattern: "%sylius.security.shop_regex%/checkout/"
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

If you would like to receive serialized attributes you need to define an array of theirs codes under `shop_api.included_attributes` key. E.g.
```yml
shop_api:
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

The application can be tested with API Test Case. In order to run test suite execute the following command:

```bash
$ bin/phpunit
```
