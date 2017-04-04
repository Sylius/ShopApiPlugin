# Sylius Shop API [![License](https://img.shields.io/packagist/l/sylius/shop-api-plugin.svg)](https://packagist.org/packages/sylius/shop-api-plugin) [![Version](https://img.shields.io/packagist/v/sylius/shop-api-plugin.svg)](https://packagist.org/packages/sylius/shop-api-plugin) [![Build status on Linux](https://img.shields.io/travis/Sylius/BundleSkeleton/master.svg)](http://travis-ci.org/Sylius/BundleSkeleton) [![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/Sylius/BundleSkeleton.svg)](https://scrutinizer-ci.com/g/Sylius/BundleSkeleton/)

This repository provides a ShopApi implementation on the top of [Sylius E-Commerce platform](https://github.com/Sylius/Sylius).
 
# Beware

:warning: The project is experimental and pretty unstable at this moment. Also, it will not allow using both Sylius Shop and Sylius Shop API, with the current implementation. :warning:

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
            ];
    
            return array_merge(parent::registerBundles(), $bundles);
        }
    ```
    2. Add `- { path: '^/shop-api', priorities: ['json'], fallback_format: json, prefer_extension: true }` to `fos_rest.format_listener.rules` section in `app/config/config.yml` file.
    ```yml
    # app/config/config.yml
    
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
    security:
        firewalls:
            // ...
    
            shop_api:
                pattern: "%shop_api.security.regex%"
                stateless:  true
                anonymous:  true
    ```

## Testing

The application can be tested with API Test Case. In order to run test suite add following PHPUnit config to the root of your folder:
```xml
<?xml version="1.0" encoding="UTF-8"?>

<!-- http://phpunit.de/manual/4.1/en/appendixes.configuration.html -->
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="http://schema.phpunit.de/5.5/phpunit.xsd"
         backupGlobals="false"
         colors="true"
         bootstrap="vendor/autoload.php"
>
    <testsuites>
        <testsuite name="Sylius Shop Api Test Suite">
            <directory>./vendor/sylius/shop-api-plugin/tests</directory>
        </testsuite>
    </testsuites>

    <php>
        <server name="KERNEL_CLASS_PATH" value="app/AppKernel.php" />
        <server name="IS_DOCTRINE_ORM_SUPPORTED" value="true" />
    </php>

    <filter>
        <whitelist>
            <directory>./vendor/sylius/shop-api-plugin/tests/Application</directory>
            <directory>./vendor/sylius/shop-api-plugin/tests/DataFixtures</directory>
            <directory>./vendor/sylius/shop-api-plugin/tests/Responses</directory>
        </whitelist>
    </filter>
</phpunit>
```

And run following command:
```bash
$ bin/phpunit
```
