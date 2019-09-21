# Swagger\Client\CheckoutApi

All URIs are relative to *https://demo.sylius.org/shop-api/{channelCode}*

Method | HTTP request | Description
------------- | ------------- | -------------
[**checkoutAddress**](CheckoutApi.md#checkoutAddress) | **PUT** /checkout/{token}/address | Address cart.
[**checkoutChoosePaymentMethod**](CheckoutApi.md#checkoutChoosePaymentMethod) | **PUT** /checkout/{token}/payment/{id} | Choosing cart payment method.
[**checkoutChooseShippingMethod**](CheckoutApi.md#checkoutChooseShippingMethod) | **PUT** /checkout/{token}/shipping/{id} | Choosing a cart shipping method.
[**checkoutComplete**](CheckoutApi.md#checkoutComplete) | **PUT** /checkout/{token}/complete | Completing checkout.
[**checkoutShowAvailablePaymentMethods**](CheckoutApi.md#checkoutShowAvailablePaymentMethods) | **GET** /checkout/{token}/payment/ | Get available payment methods.
[**checkoutShowAvailableShippingMethods**](CheckoutApi.md#checkoutShowAvailableShippingMethods) | **GET** /checkout/{token}/shipping/ | Get available shipping methods.
[**checkoutSummarize**](CheckoutApi.md#checkoutSummarize) | **GET** /checkout/{token} | Show checkout summary


# **checkoutAddress**
> checkoutAddress($token, $content)

Address cart.

This endpoint will allow you to add billing and shipping addresses to the cart and begin the checkout process. You can either define the same shipping and billing address or specify them separately.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CheckoutApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.
$content = new \Swagger\Client\Model\CheckoutAddressRequest(); // \Swagger\Client\Model\CheckoutAddressRequest | 

try {
    $apiInstance->checkoutAddress($token, $content);
} catch (Exception $e) {
    echo 'Exception when calling CheckoutApi->checkoutAddress: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier. |
 **content** | [**\Swagger\Client\Model\CheckoutAddressRequest**](../Model/CheckoutAddressRequest.md)|  |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **checkoutChoosePaymentMethod**
> checkoutChoosePaymentMethod($token, $id, $content)

Choosing cart payment method.

This endpoint will allow you to choose cart a payment method.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CheckoutApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.
$id = "id_example"; // string | Order number of payment for which payment method should be specified.
$content = new \Swagger\Client\Model\CheckoutChoosePaymentMethodRequest(); // \Swagger\Client\Model\CheckoutChoosePaymentMethodRequest | 

try {
    $apiInstance->checkoutChoosePaymentMethod($token, $id, $content);
} catch (Exception $e) {
    echo 'Exception when calling CheckoutApi->checkoutChoosePaymentMethod: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier. |
 **id** | **string**| Order number of payment for which payment method should be specified. |
 **content** | [**\Swagger\Client\Model\CheckoutChoosePaymentMethodRequest**](../Model/CheckoutChoosePaymentMethodRequest.md)|  |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **checkoutChooseShippingMethod**
> checkoutChooseShippingMethod($token, $id, $content)

Choosing a cart shipping method.

This endpoint will allow you to choose a cart shipping method.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CheckoutApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.
$id = "id_example"; // string | Order number of shipment for which shipping method should be specified.
$content = new \Swagger\Client\Model\CheckoutChooseShippingMethodRequest(); // \Swagger\Client\Model\CheckoutChooseShippingMethodRequest | 

try {
    $apiInstance->checkoutChooseShippingMethod($token, $id, $content);
} catch (Exception $e) {
    echo 'Exception when calling CheckoutApi->checkoutChooseShippingMethod: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier. |
 **id** | **string**| Order number of shipment for which shipping method should be specified. |
 **content** | [**\Swagger\Client\Model\CheckoutChooseShippingMethodRequest**](../Model/CheckoutChooseShippingMethodRequest.md)|  |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **checkoutComplete**
> checkoutComplete($token, $content)

Completing checkout.

This endpoint will allow you to complete the checkout.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CheckoutApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.
$content = new \Swagger\Client\Model\CheckoutCompleteRequest(); // \Swagger\Client\Model\CheckoutCompleteRequest | 

try {
    $apiInstance->checkoutComplete($token, $content);
} catch (Exception $e) {
    echo 'Exception when calling CheckoutApi->checkoutComplete: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier. |
 **content** | [**\Swagger\Client\Model\CheckoutCompleteRequest**](../Model/CheckoutCompleteRequest.md)|  |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **checkoutShowAvailablePaymentMethods**
> \Swagger\Client\Model\AvailablePaymentMethods checkoutShowAvailablePaymentMethods($token)

Get available payment methods.

This endpoint will show you available payment methods for all cart payments.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CheckoutApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.

try {
    $result = $apiInstance->checkoutShowAvailablePaymentMethods($token);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CheckoutApi->checkoutShowAvailablePaymentMethods: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier. |

### Return type

[**\Swagger\Client\Model\AvailablePaymentMethods**](../Model/AvailablePaymentMethods.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **checkoutShowAvailableShippingMethods**
> \Swagger\Client\Model\AvailableShippingMethods checkoutShowAvailableShippingMethods($token)

Get available shipping methods.

This endpoint will show you available shipping methods for all cart shipments.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CheckoutApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.

try {
    $result = $apiInstance->checkoutShowAvailableShippingMethods($token);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CheckoutApi->checkoutShowAvailableShippingMethods: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **token** | **string**| Cart identifier. |

### Return type

[**\Swagger\Client\Model\AvailableShippingMethods**](../Model/AvailableShippingMethods.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **checkoutSummarize**
> \Swagger\Client\Model\Cart checkoutSummarize($token)

Show checkout summary

This endpoint will show the summarized cart during checkout. This action is an equivalent of cart summarize action.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\CheckoutApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$token = "token_example"; // string | Cart identifier.

try {
    $result = $apiInstance->checkoutSummarize($token);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CheckoutApi->checkoutSummarize: ', $e->getMessage(), PHP_EOL;
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

