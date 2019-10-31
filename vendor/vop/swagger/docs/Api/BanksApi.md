# Swagger\Client\BanksApi

All URIs are relative to *https://localhost*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getAndSearchAllBanks**](BanksApi.md#getAndSearchAllBanks) | **GET** /api/v1/banks | Get and search all banks
[**getBank**](BanksApi.md#getBank) | **GET** /api/v1/banks/{id} | Get a bank
[**getMultipleBanks**](BanksApi.md#getMultipleBanks) | **GET** /api/v1/banks/{ids} | Get multiple banks


# **getAndSearchAllBanks**
> \Swagger\Client\Model\PageableBankList getAndSearchAllBanks($ids, $search, $is_supported, $pins_are_volatile, $supported_data_sources, $supported_interfaces, $location, $tpp_authentication_group_ids, $is_test_bank, $page, $per_page, $order)

Get and search all banks

Get and search banks from finAPI's database of banks. Must pass the authorized user's access_token, or your client's access_token. You can set optional search criteria to get only those banks that you are interested in. If you do not specify any search criteria, then this service functions as a 'get all' service.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\BanksApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$ids = array(56); // int[] | A comma-separated list of bank identifiers. If specified, then only banks whose identifier match any of the given identifiers will be regarded. The maximum number of identifiers is 1000.
$search = "search_example"; // string | If specified, then only those banks will be contained in the result whose 'name', 'blz', 'bic' or 'city' contains the given search string (the matching works case-insensitive). If no banks contain the search string in any of the regarded fields, then the result will be an empty list. Note that you may also pass an IBAN in this field, in which case finAPI will try to find the related bank in its database and regard only this bank for the search. Also note: If the given search string consists of several terms (separated by whitespace), then ALL of these terms must apply to a bank in order for it to get included into the result.
$is_supported = true; // bool | If specified, then only supported (in case of 'true' value) or unsupported (in case of 'false' value) banks will be regarded.  NOTE: This field is deprecated and will be removed at some point. Please refer to field 'supportedInterfaces' instead.
$pins_are_volatile = true; // bool | If specified, then only those banks will be regarded that have the given value (true or false) for their 'pinsAreVolatile' field.  NOTE: This field is deprecated and will be removed at some point.
$supported_data_sources = array("supported_data_sources_example"); // string[] | Comma-separated list of data sources. Possible values: WEB_SCRAPER,FINTS_SERVER. If this parameter is specified, then only those banks will be regarded in the search that support ALL of the given data sources. Note that this does NOT imply that those data sources must be the only data sources that are supported by a bank.  NOTE: This field is deprecated and will be removed at some point. Please refer to field 'supportedInterfaces' instead.
$supported_interfaces = array("supported_interfaces_example"); // string[] | Comma-separated list of bank interfaces. Possible values: FINTS_SERVER,WEB_SCRAPER,XS2A. If this parameter is specified, then all the banks that support at least one of the given interfaces will be returned. Note that this does NOT imply that those interfaces must be the only ones that are supported by a bank.
$location = array("location_example"); // string[] | Comma-separated list of two-letter country codes (ISO 3166 ALPHA-2). If set, then only those banks will be regarded in the search that are located in the specified countries. Notes: Banks which do not have a location set (i.e. international institutes) will ALWAYS be regarded in the search, independent of what you specify for this field. When you pass a country code that doesn't exist in the ISO 3166 ALPHA-2 standard, then the service will respond with 400 BAD_REQUEST.
$tpp_authentication_group_ids = array(56); // int[] | A comma-separated list of TPP authentication group identifiers. If specified, then only banks who have at least one interface belonging to one of the given groups will be regarded. The maximum number of identifiers is 1000.
$is_test_bank = true; // bool | If specified, then only those banks will be regarded that have the given value (true or false) for their 'isTestBank' field.
$page = 1; // int | Result page that you want to retrieve.
$per_page = 20; // int | Maximum number of records per page. Can be at most 500. NOTE: Due to its validation and visualization, the swagger frontend might show very low performance, or even crashes, when a service responds with a lot of data. It is recommended to use a HTTP client like Postman or DHC instead of our swagger frontend for service calls with large page sizes.
$order = array("order_example"); // string[] | Determines the order of the results. You can order the results by 'id', 'name', 'blz', 'bic' or 'popularity'. The default order for all services is 'id,asc'. You can also order by multiple properties. In that case the order of the parameters passed is important. Example: '/banks?order=name,desc&order=id,asc' will return banks ordered by 'name' (descending), where banks with the same 'name' are ordered by 'id' (ascending). The general format is: 'property[,asc|desc]', with 'asc' being the default value. Please note that ordering by multiple fields is not supported in our swagger frontend, but you can test this feature with any HTTP tool of your choice (e.g. postman or DHC).

try {
    $result = $apiInstance->getAndSearchAllBanks($ids, $search, $is_supported, $pins_are_volatile, $supported_data_sources, $supported_interfaces, $location, $tpp_authentication_group_ids, $is_test_bank, $page, $per_page, $order);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BanksApi->getAndSearchAllBanks: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ids** | [**int[]**](../Model/int.md)| A comma-separated list of bank identifiers. If specified, then only banks whose identifier match any of the given identifiers will be regarded. The maximum number of identifiers is 1000. | [optional]
 **search** | **string**| If specified, then only those banks will be contained in the result whose &#39;name&#39;, &#39;blz&#39;, &#39;bic&#39; or &#39;city&#39; contains the given search string (the matching works case-insensitive). If no banks contain the search string in any of the regarded fields, then the result will be an empty list. Note that you may also pass an IBAN in this field, in which case finAPI will try to find the related bank in its database and regard only this bank for the search. Also note: If the given search string consists of several terms (separated by whitespace), then ALL of these terms must apply to a bank in order for it to get included into the result. | [optional]
 **is_supported** | **bool**| If specified, then only supported (in case of &#39;true&#39; value) or unsupported (in case of &#39;false&#39; value) banks will be regarded.  NOTE: This field is deprecated and will be removed at some point. Please refer to field &#39;supportedInterfaces&#39; instead. | [optional]
 **pins_are_volatile** | **bool**| If specified, then only those banks will be regarded that have the given value (true or false) for their &#39;pinsAreVolatile&#39; field.  NOTE: This field is deprecated and will be removed at some point. | [optional]
 **supported_data_sources** | [**string[]**](../Model/string.md)| Comma-separated list of data sources. Possible values: WEB_SCRAPER,FINTS_SERVER. If this parameter is specified, then only those banks will be regarded in the search that support ALL of the given data sources. Note that this does NOT imply that those data sources must be the only data sources that are supported by a bank.  NOTE: This field is deprecated and will be removed at some point. Please refer to field &#39;supportedInterfaces&#39; instead. | [optional]
 **supported_interfaces** | [**string[]**](../Model/string.md)| Comma-separated list of bank interfaces. Possible values: FINTS_SERVER,WEB_SCRAPER,XS2A. If this parameter is specified, then all the banks that support at least one of the given interfaces will be returned. Note that this does NOT imply that those interfaces must be the only ones that are supported by a bank. | [optional]
 **location** | [**string[]**](../Model/string.md)| Comma-separated list of two-letter country codes (ISO 3166 ALPHA-2). If set, then only those banks will be regarded in the search that are located in the specified countries. Notes: Banks which do not have a location set (i.e. international institutes) will ALWAYS be regarded in the search, independent of what you specify for this field. When you pass a country code that doesn&#39;t exist in the ISO 3166 ALPHA-2 standard, then the service will respond with 400 BAD_REQUEST. | [optional]
 **tpp_authentication_group_ids** | [**int[]**](../Model/int.md)| A comma-separated list of TPP authentication group identifiers. If specified, then only banks who have at least one interface belonging to one of the given groups will be regarded. The maximum number of identifiers is 1000. | [optional]
 **is_test_bank** | **bool**| If specified, then only those banks will be regarded that have the given value (true or false) for their &#39;isTestBank&#39; field. | [optional]
 **page** | **int**| Result page that you want to retrieve. | [optional] [default to 1]
 **per_page** | **int**| Maximum number of records per page. Can be at most 500. NOTE: Due to its validation and visualization, the swagger frontend might show very low performance, or even crashes, when a service responds with a lot of data. It is recommended to use a HTTP client like Postman or DHC instead of our swagger frontend for service calls with large page sizes. | [optional] [default to 20]
 **order** | [**string[]**](../Model/string.md)| Determines the order of the results. You can order the results by &#39;id&#39;, &#39;name&#39;, &#39;blz&#39;, &#39;bic&#39; or &#39;popularity&#39;. The default order for all services is &#39;id,asc&#39;. You can also order by multiple properties. In that case the order of the parameters passed is important. Example: &#39;/banks?order&#x3D;name,desc&amp;order&#x3D;id,asc&#39; will return banks ordered by &#39;name&#39; (descending), where banks with the same &#39;name&#39; are ordered by &#39;id&#39; (ascending). The general format is: &#39;property[,asc|desc]&#39;, with &#39;asc&#39; being the default value. Please note that ordering by multiple fields is not supported in our swagger frontend, but you can test this feature with any HTTP tool of your choice (e.g. postman or DHC). | [optional]

### Return type

[**\Swagger\Client\Model\PageableBankList**](../Model/PageableBankList.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getBank**
> \Swagger\Client\Model\Bank getBank($id)

Get a bank

Get a single bank from finAPI's database of banks. You have to pass the bank's identifier, and either the authorized user's access_token, or your client's access token.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\BanksApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 789; // int | Identifier of requested bank

try {
    $result = $apiInstance->getBank($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BanksApi->getBank: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Identifier of requested bank |

### Return type

[**\Swagger\Client\Model\Bank**](../Model/Bank.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getMultipleBanks**
> \Swagger\Client\Model\BankList getMultipleBanks($ids)

Get multiple banks

Get a list of multiple banks from finAPI's database of banks. You have to pass a list of bank identifiers, and either the authorized user's access_token, or your client's access token. Note that banks whose identifiers do not exist will not be contained in the result (If this applies to all of the given identifiers, then the result will be an empty list).<br/><br/><b>WARNING</b>: This service is deprecated and will be removed at some point. If you want to get multiple banks, please instead use the service 'Get and search all banks' and pass a comma-separated list of identifiers with the parameter 'ids'.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\BanksApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$ids = array(56); // int[] | Comma-separated list of identifiers of requested banks

try {
    $result = $apiInstance->getMultipleBanks($ids);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BanksApi->getMultipleBanks: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ids** | [**int[]**](../Model/int.md)| Comma-separated list of identifiers of requested banks |

### Return type

[**\Swagger\Client\Model\BankList**](../Model/BankList.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

