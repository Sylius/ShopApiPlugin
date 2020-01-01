<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

<h1 align="center">Sylius Shop API </h1>

[![License](https://img.shields.io/packagist/l/sylius/shop-api-plugin.svg)](https://packagist.org/packages/sylius/shop-api-plugin) [![Build Status](https://travis-ci.org/Sylius/ShopApiPlugin.svg?branch=master)](https://travis-ci.org/Sylius/ShopApiPlugin) [![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/Sylius/SyliusShopApiPlugin.svg)](https://scrutinizer-ci.com/g/Sylius/SyliusShopApiPlugin/)

<p align="center"><a href="https://sylius.com/plugins/" target="_blank"><img src="https://sylius.com/assets/badge-official-sylius-plugin.png" width="200"></a></p>

<p align="center">This repository contains a plugin that extends the <a href="https://github.com/Sylius/Sylius">Sylius eCommerce platform</a> with an API in JSON that allows performing all standard shop operations from the customer perspective.</p>

## Documentation

The latest documentation is available [here](https://app.swaggerhub.com/apis/Sylius/sylius-shop-api/1.0.0). If you are looking for more information how the system works have a look at the [cookbook](doc/Cookbook.md)

## Installation

##### Before installing SyliusShopApiPlugin, you should disable all SyliusShopBundle's dependencies. You cannot use these packages together.

1. Run `composer require sylius/shop-api-plugin` and, when asked if you want to execute the Flex recipe, answer 'Yes'.
2. Extend config files:
    1. Add SyliusShopApi to `config/bundles.php`.
    ```php
    // config/bundles.php
    
        return [
            Sylius\ShopApiPlugin\SyliusShopApiPlugin::class => ['all' => true],
        ];
    ```
    2. Add `- { path: '^/shop-api', priorities: ['json'], fallback_format: json, prefer_extension: true }` to `fos_rest.format_listener.rules` 
    section in `config/packages/fos_rest.yaml` file and import config from Plugin.
    ```yml
    # config/packages/_sylius_shop_api.yaml
    
    imports: # <-- Add this section if it does not already exist and add the lines below
        # ...
        - { resource: "@SyliusShopApiPlugin/Resources/config/app/config.yml" }
        - { resource: "@SyliusShopApiPlugin/Resources/config/app/sylius_mailer.yml" }

    # config/packages/fos_rest.yaml
    
    fos_rest:
        # ...
        
        format_listener:
            rules:
                - { path: '^/shop-api', priorities: ['json'], fallback_format: json, prefer_extension: true } # <-- Add this
                - { path: '^/api', priorities: ['json', 'xml'], fallback_format: json, prefer_extension: true }
                - { path: '^/', stop: true }
    
    ```

    4. Add new routes file to import routes from the SyliusShopApiPlugin
    ```yml
    # config/routes/sylius_shop_api.yaml

    sylius_shop_api:
        resource: "@SyliusShopApiPlugin/Resources/config/routing.yml"
    ```
    5. Configure firewall
        1. Change `sylius.security.shop_regex` parameter to exclude `shop-api` prefix also
        2. Add ShopAPI regex parameter `shop_api.security.regex: "^/shop-api"`
        3. Add ShopAPI firewall config:
    ```yml
    # config/packages/security.yaml

    parameters:
        # ...
    
        sylius.security.shop_regex: "^/(?!admin|api/.*|api$|shop-api|media/.*)[^/]++" # shop-api has been added inside the brackets
        sylius_shop_api.security.regex: "^/shop-api"

    # ... 

    security:
        firewalls:
            // ...

            sylius_shop_api:
                pattern: "%sylius_shop_api.security.regex%"
                stateless: true
                anonymous: true
                provider: sylius_shop_user_provider
                json_login:
                    check_path: /shop-api/login
                    username_path: email
                    password_path: password
                    success_handler: lexik_jwt_authentication.handler.authentication_success
                    failure_handler: lexik_jwt_authentication.handler.authentication_failure
                guard:
                    authenticators:
                        - lexik_jwt_authentication.jwt_token_authenticator
       access_control:
       - { path: "%sylius_shop_api.security.regex%/address-book", role: ROLE_USER}
       - { path: "%sylius_shop_api.security.regex%/me", role: ROLE_USER}

    ```
    
    6. (optional) if you have installed `nelmio/NelmioCorsBundle` for Support of Cross-Origin Ajax Request,
        1. Add the NelmioCorsBundle to the AppKernel
    
        ```php
        // config/bundles.php
        
        return [
            Nelmio\CorsBundle\NelmioCorsBundle:class => ['all' => true],
        ];

        ```
    
        2. Add the new configuration file  
    
        ```yml
        # config/packages/nelmio_cors.yml
        
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
                    allow_methods: ['POST', 'PUT', 'GET', 'DELETE', 'PATCH', 'OPTIONS']
                    max_age: 3600
        ```

3. Follow https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#installation

Sample configuration of Shop API can be found here: https://github.com/Sylius/SyliusDemo/commit/4872350dcd6c987d54dec1f365b4bb890d7183c9

## Additional features

### Attributes

If you would like to receive serialized attributes you need to define an array of theirs codes under `sylius_shop_api.included_attributes` key. E.g.
```yml
sylius_shop_api:
    included_attributes:
        - "MUG_MATERIAL_CODE"
```

This plugin comes with an integration with [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle/). 
More information about security customizations may be found there.

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
