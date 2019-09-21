# Swagger\Client\CartApi

All URIs are relative to *https://demo.sylius.org/shop-api/{channelCode}*

Method | HTTP request | Description
------------- | ------------- | -------------
[**cartAddCoupon**](CartApi.md#cartAddCoupon) | **PUT** /carts/{token}/coupon | Add a promotion coupon code to the cart.
[**cartDrop**](CartApi.md#cartDrop) | **DELETE** /carts/{token} | Drop your cart.
[**cartPickUp**](CartApi.md#cartPickUp) | **POST** /carts | Pick up your cart from the store
[**cartPutItem**](CartApi.md#cartPutItem) | **POST** /carts/{token}/items | Add an item to your cart.
[**cartPutItem_0**](CartApi.md#cartPutItem_0) | **PUT** /carts/{token}/items/{identifier} | Change quantity of a cart item.
[**cartPutItems**](CartApi.md#cartPutItems) | **POST** /carts/{token}/multiple-items | Add multiple items to your cart.
[**cartRemoveCoupon**](CartApi.md#cartRemoveCoupon) | **DELETE** /carts/{token}/coupon | Remove a promotion coupon code from the cart.
[**cartSummarize**](CartApi.md#cartSummarize) | **GET** /carts/{token} | Show summarized cart.
[**cartsTokenItemsIdentifierDelete**](CartApi.md#cartsTokenItemsIdentifierDelete) | **DELETE** /carts/{token}/items/{identifier} | Remove cart item.
[**deprecatedCartPickUp**](CartApi.md#deprecatedCartPickUp) | **POST** /carts/{token} | Pick up your cart from the store
[**estimateShippingCost**](CartApi.md#estimateShippingCost) | **GET** /carts/{token}/estimated-shipping-cost | Estimates the shipping cost of the cart


# **cartAddCoupon**
> cartAddCoupon($token, $content)

Add a promotion coupon code to the cart.

This endpoint will allow you to add a promotion coupon code to the cart and receive the discount.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CartApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.
$content = new \Swagger\Client\Model\AddCouponRequest(); // \Swagger\Client\Model\AddCouponRequest | 

try {
    $apiInstance->cartAddCoupon($token, $content);
} catch (Exception $e) {
    echo 'Exception when calling CartApi->cartAddCoupon: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier. |
 **content** | [**\Swagger\Client\Model\AddCouponRequest**](../Model/AddCouponRequest.md)|  |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **cartDrop**
> cartDrop($token)

Drop your cart.

This endpoint will remove the cart and all of the related cart items.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CartApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.

try {
    $apiInstance->cartDrop($token);
} catch (Exception $e) {
    echo 'Exception when calling CartApi->cartDrop: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **cartPickUp**
> cartPickUp($content)

Pick up your cart from the store

This endpoint will allow you to create a new cart.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CartApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$content = new \Swagger\Client\Model\PickupCartRequest(); // \Swagger\Client\Model\PickupCartRequest | Contains an information about the channel which should be associated with the newly created cart

try {
    $apiInstance->cartPickUp($content);
} catch (Exception $e) {
    echo 'Exception when calling CartApi->cartPickUp: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **content** | [**\Swagger\Client\Model\PickupCartRequest**](../Model/PickupCartRequest.md)| Contains an information about the channel which should be associated with the newly created cart |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **cartPutItem**
> \Swagger\Client\Model\Cart cartPutItem($token, $content)

Add an item to your cart.

This endpoint will allow you to add a new item to your cart.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CartApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.
$content = new \Swagger\Client\Model\PutItemToCartRequest(); // \Swagger\Client\Model\PutItemToCartRequest | Description of an item. The smallest required amount of data is a product code and quantity for a simple product. Configurable products will require an additional `variant_code` or `options` field, but never both.

try {
    $result = $apiInstance->cartPutItem($token, $content);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CartApi->cartPutItem: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier. |
 **content** | [**\Swagger\Client\Model\PutItemToCartRequest**](../Model/PutItemToCartRequest.md)| Description of an item. The smallest required amount of data is a product code and quantity for a simple product. Configurable products will require an additional &#x60;variant_code&#x60; or &#x60;options&#x60; field, but never both. |

### Return type

[**\Swagger\Client\Model\Cart**](../Model/Cart.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **cartPutItem_0**
> cartPutItem_0($token, $identifier, $content)

Change quantity of a cart item.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CartApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.
$identifier = "identifier_example"; // string | Identifier of a specific item. Can be found in the cart summary.
$content = new \Swagger\Client\Model\ChangeItemQuantityRequest(); // \Swagger\Client\Model\ChangeItemQuantityRequest | 

try {
    $apiInstance->cartPutItem_0($token, $identifier, $content);
} catch (Exception $e) {
    echo 'Exception when calling CartApi->cartPutItem_0: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier. |
 **identifier** | **string**| Identifier of a specific item. Can be found in the cart summary. |
 **content** | [**\Swagger\Client\Model\ChangeItemQuantityRequest**](../Model/ChangeItemQuantityRequest.md)|  |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **cartPutItems**
> \Swagger\Client\Model\Cart cartPutItems($token, $content)

Add multiple items to your cart.

This endpoint will allow you to add a new item to your cart.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CartApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.
$content = new \Swagger\Client\Model\PutItemsToCartRequest(); // \Swagger\Client\Model\PutItemsToCartRequest | Description of items. The same rules applied to each of the array values as to the previous point.

try {
    $result = $apiInstance->cartPutItems($token, $content);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CartApi->cartPutItems: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier. |
 **content** | [**\Swagger\Client\Model\PutItemsToCartRequest**](../Model/PutItemsToCartRequest.md)| Description of items. The same rules applied to each of the array values as to the previous point. |

### Return type

[**\Swagger\Client\Model\Cart**](../Model/Cart.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **cartRemoveCoupon**
> cartRemoveCoupon($token)

Remove a promotion coupon code from the cart.

This endpoint will allow you to remove a promotion coupon code from the cart.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CartApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.

try {
    $apiInstance->cartRemoveCoupon($token);
} catch (Exception $e) {
    echo 'Exception when calling CartApi->cartRemoveCoupon: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **cartSummarize**
> \Swagger\Client\Model\Cart cartSummarize($token)

Show summarized cart.

This endpoint shows you the current calculated state of cart.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CartApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.

try {
    $result = $apiInstance->cartSummarize($token);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CartApi->cartSummarize: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier. |

### Return type

[**\Swagger\Client\Model\Cart**](../Model/Cart.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **cartsTokenItemsIdentifierDelete**
> cartsTokenItemsIdentifierDelete($token, $identifier)

Remove cart item.

This endpoint will remove one item from your cart

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CartApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.
$identifier = "identifier_example"; // string | Identifier of a specific item. Can be found in the cart summary.

try {
    $apiInstance->cartsTokenItemsIdentifierDelete($token, $identifier);
} catch (Exception $e) {
    echo 'Exception when calling CartApi->cartsTokenItemsIdentifierDelete: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier. |
 **identifier** | **string**| Identifier of a specific item. Can be found in the cart summary. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deprecatedCartPickUp**
> deprecatedCartPickUp($token, $content)

Pick up your cart from the store

This endpoint will allow you to assign a new cart to the provided token. We recommend using UUID as a token to avoid duplication. If any of previous carts or orders already have the same token value an exception will be thrown.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CartApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.
$content = new \Swagger\Client\Model\PickupCartRequest(); // \Swagger\Client\Model\PickupCartRequest | Contains an information about the channel which should be associated with the newly created cart

try {
    $apiInstance->deprecatedCartPickUp($token, $content);
} catch (Exception $e) {
    echo 'Exception when calling CartApi->deprecatedCartPickUp: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier. |
 **content** | [**\Swagger\Client\Model\PickupCartRequest**](../Model/PickupCartRequest.md)| Contains an information about the channel which should be associated with the newly created cart |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **estimateShippingCost**
> \Swagger\Client\Model\EstimatedShippingCost estimateShippingCost($token, $country_code, $province_code)

Estimates the shipping cost of the cart

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CartApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier
$country_code = "country_code_example"; // string | Shipping Country
$province_code = "province_code_example"; // string | Province to ship to

try {
    $result = $apiInstance->estimateShippingCost($token, $country_code, $province_code);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CartApi->estimateShippingCost: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier |
 **country_code** | **string**| Shipping Country |
 **province_code** | **string**| Province to ship to |

### Return type

[**\Swagger\Client\Model\EstimatedShippingCost**](../Model/EstimatedShippingCost.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

