# Swagger\Client\AddressApi

All URIs are relative to *https://demo.sylius.org/shop-api/{channelCode}*

Method | HTTP request | Description
------------- | ------------- | -------------
[**createAddress**](AddressApi.md#createAddress) | **POST** /address-book | Creates a new address in the the address book
[**deleteAddress**](AddressApi.md#deleteAddress) | **DELETE** /address-book/{id} | Deletes an address from the address book
[**getAddressBook**](AddressApi.md#getAddressBook) | **GET** /address-book | Gets the address book of the currently logged in user
[**updateAddressBook**](AddressApi.md#updateAddressBook) | **PUT** /address-book/{id} | Updates an address in the address book
[**updateDefaultAddress**](AddressApi.md#updateDefaultAddress) | **PATCH** /address-book/{id}/default | Change the default address in the address book


# **createAddress**
> createAddress($content)

Creates a new address in the the address book

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure API key authorization: bearerAuth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('Authorization', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('Authorization', 'Bearer');

$apiInstance = new Swagger\Client\Api\AddressApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$content = new \Swagger\Client\Model\LoggedInCustomerAddressBookAddress(); // \Swagger\Client\Model\LoggedInCustomerAddressBookAddress | 

try {
    $apiInstance->createAddress($content);
} catch (Exception $e) {
    echo 'Exception when calling AddressApi->createAddress: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **content** | [**\Swagger\Client\Model\LoggedInCustomerAddressBookAddress**](../Model/LoggedInCustomerAddressBookAddress.md)|  |

### Return type

void (empty response body)

### Authorization

[bearerAuth](../../README.md#bearerAuth)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteAddress**
> deleteAddress($id)

Deletes an address from the address book

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure API key authorization: bearerAuth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('Authorization', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('Authorization', 'Bearer');

$apiInstance = new Swagger\Client\Api\AddressApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 56; // int | Id of the address to update

try {
    $apiInstance->deleteAddress($id);
} catch (Exception $e) {
    echo 'Exception when calling AddressApi->deleteAddress: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Id of the address to update |

### Return type

void (empty response body)

### Authorization

[bearerAuth](../../README.md#bearerAuth)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getAddressBook**
> \Swagger\Client\Model\LoggedInCustomerAddressBook getAddressBook()

Gets the address book of the currently logged in user

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure API key authorization: bearerAuth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('Authorization', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('Authorization', 'Bearer');

$apiInstance = new Swagger\Client\Api\AddressApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);

try {
    $result = $apiInstance->getAddressBook();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AddressApi->getAddressBook: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**\Swagger\Client\Model\LoggedInCustomerAddressBook**](../Model/LoggedInCustomerAddressBook.md)

### Authorization

[bearerAuth](../../README.md#bearerAuth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateAddressBook**
> \Swagger\Client\Model\LoggedInCustomerAddressBookAddress updateAddressBook($id, $content)

Updates an address in the address book

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure API key authorization: bearerAuth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('Authorization', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('Authorization', 'Bearer');

$apiInstance = new Swagger\Client\Api\AddressApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 56; // int | Id of the address to update
$content = new \Swagger\Client\Model\LoggedInCustomerAddressBookAddress(); // \Swagger\Client\Model\LoggedInCustomerAddressBookAddress | 

try {
    $result = $apiInstance->updateAddressBook($id, $content);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AddressApi->updateAddressBook: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Id of the address to update |
 **content** | [**\Swagger\Client\Model\LoggedInCustomerAddressBookAddress**](../Model/LoggedInCustomerAddressBookAddress.md)|  |

### Return type

[**\Swagger\Client\Model\LoggedInCustomerAddressBookAddress**](../Model/LoggedInCustomerAddressBookAddress.md)

### Authorization

[bearerAuth](../../README.md#bearerAuth)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateDefaultAddress**
> updateDefaultAddress($id)

Change the default address in the address book

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure API key authorization: bearerAuth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('Authorization', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('Authorization', 'Bearer');

$apiInstance = new Swagger\Client\Api\AddressApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 56; // int | Id of the address to be the default address

try {
    $apiInstance->updateDefaultAddress($id);
} catch (Exception $e) {
    echo 'Exception when calling AddressApi->updateDefaultAddress: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Id of the address to be the default address |

### Return type

void (empty response body)

### Authorization

[bearerAuth](../../README.md#bearerAuth)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

