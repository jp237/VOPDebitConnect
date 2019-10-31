# Swagger\Client\TPPCredentialsApi

All URIs are relative to *https://localhost*

Method | HTTP request | Description
------------- | ------------- | -------------
[**createTppCredential**](TPPCredentialsApi.md#createTppCredential) | **POST** /api/v1/tppCredentials | Create new TPP credentials
[**deleteTppCredential**](TPPCredentialsApi.md#deleteTppCredential) | **DELETE** /api/v1/tppCredentials/{id} | Delete a set of TPP credentials
[**editTppCredential**](TPPCredentialsApi.md#editTppCredential) | **PATCH** /api/v1/tppCredentials/{id} | Edit a set of TPP credentials
[**getAllTppCredentials**](TPPCredentialsApi.md#getAllTppCredentials) | **GET** /api/v1/tppCredentials | Get all TPP credentials
[**getAndSearchTppAuthenticationGroups**](TPPCredentialsApi.md#getAndSearchTppAuthenticationGroups) | **GET** /api/v1/tppCredentials/tppAuthenticationGroups | Get all TPP Authentication Groups
[**getTppCredential**](TPPCredentialsApi.md#getTppCredential) | **GET** /api/v1/tppCredentials/{id} | Get a set of TPP credentials


# **createTppCredential**
> \Swagger\Client\Model\TppCredentials createTppCredential($body)

Create new TPP credentials

Upload TPP credentials for a TPP Authentication Group. Must pass the <a href='https://finapi.zendesk.com/hc/en-us/articles/115003661827-Difference-between-app-clients-and-mandator-admin-client' target='_blank'>mandator admin client</a>'s access_token.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\TPPCredentialsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\TppCredentialsParams(); // \Swagger\Client\Model\TppCredentialsParams | Parameters of a new set of TPP credentials

try {
    $result = $apiInstance->createTppCredential($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling TPPCredentialsApi->createTppCredential: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\TppCredentialsParams**](../Model/TppCredentialsParams.md)| Parameters of a new set of TPP credentials |

### Return type

[**\Swagger\Client\Model\TppCredentials**](../Model/TppCredentials.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteTppCredential**
> deleteTppCredential($id)

Delete a set of TPP credentials

Delete a single set of TPP credentials by its id. Must pass the <a href='https://finapi.zendesk.com/hc/en-us/articles/115003661827-Difference-between-app-clients-and-mandator-admin-client' target='_blank'>mandator admin client</a>'s access_token.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\TPPCredentialsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 789; // int | Id of the TPP credentials to delete

try {
    $apiInstance->deleteTppCredential($id);
} catch (Exception $e) {
    echo 'Exception when calling TPPCredentialsApi->deleteTppCredential: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Id of the TPP credentials to delete |

### Return type

void (empty response body)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **editTppCredential**
> \Swagger\Client\Model\TppCredentials editTppCredential($id, $body)

Edit a set of TPP credentials

Edit TPP credentials data. Must pass the <a href='https://finapi.zendesk.com/hc/en-us/articles/115003661827-Difference-between-app-clients-and-mandator-admin-client' target='_blank'>mandator admin client</a>'s access_token.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\TPPCredentialsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 789; // int | Id of the TPP credentials to edit
$body = new \Swagger\Client\Model\EditTppCredentialParams(); // \Swagger\Client\Model\EditTppCredentialParams | New TPP credentials parameters

try {
    $result = $apiInstance->editTppCredential($id, $body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling TPPCredentialsApi->editTppCredential: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Id of the TPP credentials to edit |
 **body** | [**\Swagger\Client\Model\EditTppCredentialParams**](../Model/EditTppCredentialParams.md)| New TPP credentials parameters |

### Return type

[**\Swagger\Client\Model\TppCredentials**](../Model/TppCredentials.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getAllTppCredentials**
> \Swagger\Client\Model\PageableTppCredentialResources getAllTppCredentials($search, $page, $per_page)

Get all TPP credentials

Get and search all TPP credentials. Must pass the <a href='https://finapi.zendesk.com/hc/en-us/articles/115003661827-Difference-between-app-clients-and-mandator-admin-client' target='_blank'>mandator admin client</a>'s access_token. You can set optional search criteria to get only those TPP credentials that you are interested in. If you do not specify any search criteria, then this service functions as a 'get all' service.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\TPPCredentialsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$search = "search_example"; // string | Returns only the TPP credentials belonging to those banks whose 'name', 'blz', or 'bic' contains the given search string (the matching works case-insensitive). Note: If the given search string consists of several terms (separated by whitespace), then ALL of these terms must apply to a bank in order for it to get included into the result.
$page = 1; // int | Result page that you want to retrieve
$per_page = 20; // int | Maximum number of records per page. Can be at most 500. NOTE: Due to its validation and visualization, the swagger frontend might show very low performance, or even crashes, when a service responds with a lot of data. It is recommended to use a HTTP client like Postman or DHC instead of our swagger frontend for service calls with large page sizes.

try {
    $result = $apiInstance->getAllTppCredentials($search, $page, $per_page);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling TPPCredentialsApi->getAllTppCredentials: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **search** | **string**| Returns only the TPP credentials belonging to those banks whose &#39;name&#39;, &#39;blz&#39;, or &#39;bic&#39; contains the given search string (the matching works case-insensitive). Note: If the given search string consists of several terms (separated by whitespace), then ALL of these terms must apply to a bank in order for it to get included into the result. | [optional]
 **page** | **int**| Result page that you want to retrieve | [optional] [default to 1]
 **per_page** | **int**| Maximum number of records per page. Can be at most 500. NOTE: Due to its validation and visualization, the swagger frontend might show very low performance, or even crashes, when a service responds with a lot of data. It is recommended to use a HTTP client like Postman or DHC instead of our swagger frontend for service calls with large page sizes. | [optional] [default to 20]

### Return type

[**\Swagger\Client\Model\PageableTppCredentialResources**](../Model/PageableTppCredentialResources.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getAndSearchTppAuthenticationGroups**
> \Swagger\Client\Model\PageableTppAuthenticationGroupResources getAndSearchTppAuthenticationGroups($name, $bank_blz, $bank_name, $page, $per_page)

Get all TPP Authentication Groups

Get and search across all available TPP authentication groups. Must pass the <a href='https://finapi.zendesk.com/hc/en-us/articles/115003661827-Difference-between-app-clients-and-mandator-admin-client' target='_blank'>mandator admin client</a>'s access_token. You can set optional search criteria to get only those TPP authentication groups that you are interested in. If you do not specify any search criteria, then this service functions as a 'get all' service.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\TPPCredentialsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$name = "name_example"; // string | Only the tpp authentication groups with name matching the given one should appear in the result list
$bank_blz = "bank_blz_example"; // string | Search by connected banks: only the banks with BLZ matching the given one should appear in the result list
$bank_name = "bank_name_example"; // string | Search by connected banks: only the banks with name matching the given one should appear in the result list
$page = 1; // int | Result page that you want to retrieve
$per_page = 20; // int | Maximum number of records per page. Can be at most 500. NOTE: Due to its validation and visualization, the swagger frontend might show very low performance, or even crashes, when a service responds with a lot of data. It is recommended to use a HTTP client like Postman or DHC instead of our swagger frontend for service calls with large page sizes.

try {
    $result = $apiInstance->getAndSearchTppAuthenticationGroups($name, $bank_blz, $bank_name, $page, $per_page);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling TPPCredentialsApi->getAndSearchTppAuthenticationGroups: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **string**| Only the tpp authentication groups with name matching the given one should appear in the result list | [optional]
 **bank_blz** | **string**| Search by connected banks: only the banks with BLZ matching the given one should appear in the result list | [optional]
 **bank_name** | **string**| Search by connected banks: only the banks with name matching the given one should appear in the result list | [optional]
 **page** | **int**| Result page that you want to retrieve | [optional] [default to 1]
 **per_page** | **int**| Maximum number of records per page. Can be at most 500. NOTE: Due to its validation and visualization, the swagger frontend might show very low performance, or even crashes, when a service responds with a lot of data. It is recommended to use a HTTP client like Postman or DHC instead of our swagger frontend for service calls with large page sizes. | [optional] [default to 20]

### Return type

[**\Swagger\Client\Model\PageableTppAuthenticationGroupResources**](../Model/PageableTppAuthenticationGroupResources.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getTppCredential**
> \Swagger\Client\Model\TppCredentials getTppCredential($id)

Get a set of TPP credentials

Get a single set of TPP credentials by its id. Must pass the <a href='https://finapi.zendesk.com/hc/en-us/articles/115003661827-Difference-between-app-clients-and-mandator-admin-client' target='_blank'>mandator admin client</a>'s access_token.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\TPPCredentialsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 789; // int | Id of the requested TPP credentials

try {
    $result = $apiInstance->getTppCredential($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling TPPCredentialsApi->getTppCredential: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Id of the requested TPP credentials |

### Return type

[**\Swagger\Client\Model\TppCredentials**](../Model/TppCredentials.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

