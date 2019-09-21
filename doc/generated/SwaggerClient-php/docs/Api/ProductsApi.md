# Swagger\Client\ProductsApi

All URIs are relative to *https://demo.sylius.org/shop-api/{channelCode}*

Method | HTTP request | Description
------------- | ------------- | -------------
[**productAddReview**](ProductsApi.md#productAddReview) | **POST** /product-reviews-by-slug/{slug} | Add a review to the product.
[**productAddReview_0**](ProductsApi.md#productAddReview_0) | **POST** /product/{code}/reviews | Add a review to the product.
[**productCatalog**](ProductsApi.md#productCatalog) | **GET** /taxon-products-by-slug/{slug} | Show product catalog.
[**productCatalog_0**](ProductsApi.md#productCatalog_0) | **GET** /taxon-products/{code} | Show product catalog.
[**productDetails**](ProductsApi.md#productDetails) | **GET** /products-by-slug/{slug} | Show a product with the given slug.
[**productDetails_0**](ProductsApi.md#productDetails_0) | **GET** /products/{code} | Show a product with the given code.
[**productReviews**](ProductsApi.md#productReviews) | **GET** /product-reviews-by-slug/{slug} | Show reviews.
[**productReviews_0**](ProductsApi.md#productReviews_0) | **GET** /product/{code}/reviews | Show reviews.


# **productAddReview**
> productAddReview($slug, $content)

Add a review to the product.

This endpoint will allow you to add a new review to the product. Remember, that it should be accepted by an administrator before it will be available in the review list.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\ProductsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$slug = "slug_example"; // string | Slug of expected product.
$content = new \Swagger\Client\Model\AddReviewRequest(); // \Swagger\Client\Model\AddReviewRequest | 

try {
    $apiInstance->productAddReview($slug, $content);
} catch (Exception $e) {
    echo 'Exception when calling ProductsApi->productAddReview: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **slug** | **string**| Slug of expected product. |
 **content** | [**\Swagger\Client\Model\AddReviewRequest**](../Model/AddReviewRequest.md)|  |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **productAddReview_0**
> productAddReview_0($code, $content)

Add a review to the product.

This endpoint will allow you to add a new review to the product. Remember, that it should be accepted by an administrator before it will be available in the review list.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\ProductsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$code = "code_example"; // string | Code of expected product.
$content = new \Swagger\Client\Model\AddReviewRequest(); // \Swagger\Client\Model\AddReviewRequest | 

try {
    $apiInstance->productAddReview_0($code, $content);
} catch (Exception $e) {
    echo 'Exception when calling ProductsApi->productAddReview_0: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **code** | **string**| Code of expected product. |
 **content** | [**\Swagger\Client\Model\AddReviewRequest**](../Model/AddReviewRequest.md)|  |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **productCatalog**
> \Swagger\Client\Model\ProductsPage productCatalog($slug, $channel, $locale, $limit, $page)

Show product catalog.

This endpoint will return a paginated list of products for given taxon.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\ProductsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$slug = "slug_example"; // string | Slug of taxonomy for which products should be listed.
$channel = "channel_example"; // string | Channel from which products should be gathered.
$locale = "locale_example"; // string | Locale in which products should be shown.
$limit = 56; // int | Number of expected products per page.
$page = 56; // int | Page number.

try {
    $result = $apiInstance->productCatalog($slug, $channel, $locale, $limit, $page);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ProductsApi->productCatalog: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **slug** | **string**| Slug of taxonomy for which products should be listed. |
 **channel** | **string**| Channel from which products should be gathered. |
 **locale** | **string**| Locale in which products should be shown. | [optional]
 **limit** | **int**| Number of expected products per page. | [optional]
 **page** | **int**| Page number. | [optional]

### Return type

[**\Swagger\Client\Model\ProductsPage**](../Model/ProductsPage.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **productCatalog_0**
> \Swagger\Client\Model\ProductsPage productCatalog_0($code, $channel, $locale, $limit, $page)

Show product catalog.

This endpoint will return a paginated list of products for given taxon.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\ProductsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$code = "code_example"; // string | Code of taxonomy for which products should be listed.
$channel = "channel_example"; // string | Channel code from which products should be gathered.
$locale = "locale_example"; // string | Locale in which products should be shown.
$limit = 56; // int | Number of expected products per page.
$page = 56; // int | Page number.

try {
    $result = $apiInstance->productCatalog_0($code, $channel, $locale, $limit, $page);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ProductsApi->productCatalog_0: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **code** | **string**| Code of taxonomy for which products should be listed. |
 **channel** | **string**| Channel code from which products should be gathered. |
 **locale** | **string**| Locale in which products should be shown. | [optional]
 **limit** | **int**| Number of expected products per page. | [optional]
 **page** | **int**| Page number. | [optional]

### Return type

[**\Swagger\Client\Model\ProductsPage**](../Model/ProductsPage.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **productDetails**
> \Swagger\Client\Model\ProductDetails productDetails($slug, $channel, $locale)

Show a product with the given slug.

This endpoint will return a product with the given slug.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\ProductsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$slug = "slug_example"; // string | Slug of expected product.
$channel = "channel_example"; // string | Channel from which products should be gathered.
$locale = "locale_example"; // string | Locale in which products should be shown.

try {
    $result = $apiInstance->productDetails($slug, $channel, $locale);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ProductsApi->productDetails: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **slug** | **string**| Slug of expected product. |
 **channel** | **string**| Channel from which products should be gathered. |
 **locale** | **string**| Locale in which products should be shown. | [optional]

### Return type

[**\Swagger\Client\Model\ProductDetails**](../Model/ProductDetails.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **productDetails_0**
> \Swagger\Client\Model\ProductDetails productDetails_0($code, $channel, $locale)

Show a product with the given code.

This endpoint will return a product with the given code.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\ProductsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$code = "code_example"; // string | Code of expected product.
$channel = "channel_example"; // string | Channel from which products should be gathered.
$locale = "locale_example"; // string | Locale in which products should be shown.

try {
    $result = $apiInstance->productDetails_0($code, $channel, $locale);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ProductsApi->productDetails_0: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **code** | **string**| Code of expected product. |
 **channel** | **string**| Channel from which products should be gathered. |
 **locale** | **string**| Locale in which products should be shown. | [optional]

### Return type

[**\Swagger\Client\Model\ProductDetails**](../Model/ProductDetails.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **productReviews**
> \Swagger\Client\Model\ProductReviewsPage productReviews($slug, $channel)

Show reviews.

This endpoint will return a paginated list of all reviews related to the product identified by slug.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\ProductsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$slug = "slug_example"; // string | Slug of expected product.
$channel = "channel_example"; // string | Channel from which products should be gathered.

try {
    $result = $apiInstance->productReviews($slug, $channel);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ProductsApi->productReviews: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **slug** | **string**| Slug of expected product. |
 **channel** | **string**| Channel from which products should be gathered. |

### Return type

[**\Swagger\Client\Model\ProductReviewsPage**](../Model/ProductReviewsPage.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **productReviews_0**
> \Swagger\Client\Model\ProductReviewsPage productReviews_0($code, $channel)

Show reviews.

This endpoint will return a paginated list of all reviews related to the product identified by slug.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiInstance = new Swagger\Client\Api\ProductsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$code = "code_example"; // string | Code of expected product.
$channel = "channel_example"; // string | Channel from which products should be gathered.

try {
    $result = $apiInstance->productReviews_0($code, $channel);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ProductsApi->productReviews_0: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **code** | **string**| Code of expected product. |
 **channel** | **string**| Channel from which products should be gathered. |

### Return type

[**\Swagger\Client\Model\ProductReviewsPage**](../Model/ProductReviewsPage.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

