# Swagger\Client\TaxonsApi

All URIs are relative to *https://demo.sylius.org/shop-api/{channelCode}*

Method | HTTP request | Description
------------- | ------------- | -------------
[**taxonDetails**](TaxonsApi.md#taxonDetails) | **GET** /taxons/{code} | Show taxon with given code.
[**taxonTree**](TaxonsApi.md#taxonTree) | **GET** /taxons | Show taxon tree.


# **taxonDetails**
> \Swagger\Client\Model\Taxon taxonDetails($code, $locale)

Show taxon with given code.

This endpoint will return a taxon with given code, children and the root node with direct path to this taxon.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\TaxonsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$code = "code_example"; // string | Code of expected taxon.
$locale = "locale_example"; // string | Locale in which taxons should be shown.

try {
    $result = $apiInstance->taxonDetails($code, $locale);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling TaxonsApi->taxonDetails: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **code** | **string**| Code of expected taxon. |
 **locale** | **string**| Locale in which taxons should be shown. | [optional]

### Return type

[**\Swagger\Client\Model\Taxon**](../Model/Taxon.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **taxonTree**
> \Swagger\Client\Model\Taxon[] taxonTree($locale)

Show taxon tree.

This endpoint will return an array of all available taxon roots with all of its children.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\TaxonsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$locale = "locale_example"; // string | Locale in which taxons should be shown.

try {
    $result = $apiInstance->taxonTree($locale);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling TaxonsApi->taxonTree: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **locale** | **string**| Locale in which taxons should be shown. | [optional]

### Return type

[**\Swagger\Client\Model\Taxon[]**](../Model/Taxon.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

