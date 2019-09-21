# Swagger\Client\UsersApi

All URIs are relative to *https://demo.sylius.org/shop-api/{channelCode}*

Method | HTTP request | Description
------------- | ------------- | -------------
[**me**](UsersApi.md#me) | **GET** /me | Provides currently logged in user details.
[**registerUser**](UsersApi.md#registerUser) | **POST** /register | Registering a new user
[**requestPasswordReset**](UsersApi.md#requestPasswordReset) | **PUT** /request-password-reset | Request resetting password of user with passed email.
[**updateUser**](UsersApi.md#updateUser) | **PUT** /me | Updates currently logged in users details.


# **me**
> \Swagger\Client\Model\LoggedInCustomerDetails me()

Provides currently logged in user details.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure API key authorization: bearerAuth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('Authorization', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('Authorization', 'Bearer');

$apiInstance = new Swagger\Client\Api\UsersApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);

try {
    $result = $apiInstance->me();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling UsersApi->me: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**\Swagger\Client\Model\LoggedInCustomerDetails**](../Model/LoggedInCustomerDetails.md)

### Authorization

[bearerAuth](../../README.md#bearerAuth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **registerUser**
> registerUser($content)

Registering a new user

This creates a new user that can log in the shop

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\UsersApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$content = new \Swagger\Client\Model\RegisterRequest(); // \Swagger\Client\Model\RegisterRequest | 

try {
    $apiInstance->registerUser($content);
} catch (Exception $e) {
    echo 'Exception when calling UsersApi->registerUser: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **content** | [**\Swagger\Client\Model\RegisterRequest**](../Model/RegisterRequest.md)|  |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **requestPasswordReset**
> requestPasswordReset($email)

Request resetting password of user with passed email.

Email with reset password path will be sent to user. Default path for password resetting is `/password-reset/{token}`. To change it, you need to override template `@SyliusShopApi\\Email\\passwordReset.html.twig`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\UsersApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$email = new \Swagger\Client\Model\RequestPasswordResetting(); // \Swagger\Client\Model\RequestPasswordResetting | Email of user which want to reset password.

try {
    $apiInstance->requestPasswordReset($email);
} catch (Exception $e) {
    echo 'Exception when calling UsersApi->requestPasswordReset: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **email** | [**\Swagger\Client\Model\RequestPasswordResetting**](../Model/RequestPasswordResetting.md)| Email of user which want to reset password. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateUser**
> \Swagger\Client\Model\LoggedInCustomerDetails updateUser($content)

Updates currently logged in users details.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure API key authorization: bearerAuth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('Authorization', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('Authorization', 'Bearer');

$apiInstance = new Swagger\Client\Api\UsersApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$content = new \Swagger\Client\Model\UpdateUserRequest(); // \Swagger\Client\Model\UpdateUserRequest | 

try {
    $result = $apiInstance->updateUser($content);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling UsersApi->updateUser: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **content** | [**\Swagger\Client\Model\UpdateUserRequest**](../Model/UpdateUserRequest.md)|  |

### Return type

[**\Swagger\Client\Model\LoggedInCustomerDetails**](../Model/LoggedInCustomerDetails.md)

### Authorization

[bearerAuth](../../README.md#bearerAuth)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

