# Swagger\Client\ClientConfigurationApi

All URIs are relative to *https://localhost*

Method | HTTP request | Description
------------- | ------------- | -------------
[**editClientConfiguration**](ClientConfigurationApi.md#editClientConfiguration) | **PATCH** /api/v1/clientConfiguration | Edit client configuration
[**getClientConfiguration**](ClientConfigurationApi.md#getClientConfiguration) | **GET** /api/v1/clientConfiguration | Get client configuration


# **editClientConfiguration**
> \Swagger\Client\Model\ClientConfiguration editClientConfiguration($body)

Edit client configuration

Edit your client's configuration. Must pass your global (i.e. client) access_token.<br/><br/> <b>NOTE</b>: When token validity periods are changed, this only applies to newly requested tokens, and does not change the expiration time of already existing tokens.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\ClientConfigurationApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\ClientConfigurationParams(); // \Swagger\Client\Model\ClientConfigurationParams | Client configuration parameters

try {
    $result = $apiInstance->editClientConfiguration($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ClientConfigurationApi->editClientConfiguration: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\ClientConfigurationParams**](../Model/ClientConfigurationParams.md)| Client configuration parameters | [optional]

### Return type

[**\Swagger\Client\Model\ClientConfiguration**](../Model/ClientConfiguration.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getClientConfiguration**
> \Swagger\Client\Model\ClientConfiguration getClientConfiguration()

Get client configuration

Get your client's configuration. Must pass your global (i.e. client) access_token.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\ClientConfigurationApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);

try {
    $result = $apiInstance->getClientConfiguration();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ClientConfigurationApi->getClientConfiguration: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**\Swagger\Client\Model\ClientConfiguration**](../Model/ClientConfiguration.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

