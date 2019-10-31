# Swagger\Client\AccountsApi

All URIs are relative to *https://localhost*

Method | HTTP request | Description
------------- | ------------- | -------------
[**deleteAccount**](AccountsApi.md#deleteAccount) | **DELETE** /api/v1/accounts/{id} | Delete an account
[**deleteAllAccounts**](AccountsApi.md#deleteAllAccounts) | **DELETE** /api/v1/accounts | Delete all accounts
[**editAccount**](AccountsApi.md#editAccount) | **PATCH** /api/v1/accounts/{id} | Edit an account
[**executeSepaDirectDebit**](AccountsApi.md#executeSepaDirectDebit) | **POST** /api/v1/accounts/executeSepaDirectDebit | Execute SEPA Direct Debit
[**executeSepaMoneyTransfer**](AccountsApi.md#executeSepaMoneyTransfer) | **POST** /api/v1/accounts/executeSepaMoneyTransfer | Execute SEPA Money Transfer
[**getAccount**](AccountsApi.md#getAccount) | **GET** /api/v1/accounts/{id} | Get an account
[**getAndSearchAllAccounts**](AccountsApi.md#getAndSearchAllAccounts) | **GET** /api/v1/accounts | Get and search all accounts
[**getDailyBalances**](AccountsApi.md#getDailyBalances) | **GET** /api/v1/accounts/dailyBalances | Get daily balances
[**getMultipleAccounts**](AccountsApi.md#getMultipleAccounts) | **GET** /api/v1/accounts/{ids} | Get multiple accounts
[**requestSepaDirectDebit**](AccountsApi.md#requestSepaDirectDebit) | **POST** /api/v1/accounts/requestSepaDirectDebit | Request SEPA Direct Debit
[**requestSepaMoneyTransfer**](AccountsApi.md#requestSepaMoneyTransfer) | **POST** /api/v1/accounts/requestSepaMoneyTransfer | Request SEPA Money Transfer


# **deleteAccount**
> deleteAccount($id)

Delete an account

Delete a single bank account of the user that is authorized by the access_token, including its transactions and balance data. Must pass the account's identifier and the user's access_token.<br/><br/>Notes: <br/>- You cannot delete an account while the bank connection that it relates to is currently in the process of import, update, or transactions categorization. <br/>- When the last remaining account of a bank connection gets deleted, then the bank connection itself will get deleted as well! <br/>- All notification rules that are connected to the account will get adjusted so that they no longer have this account listed. Notification rules that are connected to just this account (and no other accounts) will get deleted altogether.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\AccountsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 789; // int | Identifier of the account to delete

try {
    $apiInstance->deleteAccount($id);
} catch (Exception $e) {
    echo 'Exception when calling AccountsApi->deleteAccount: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Identifier of the account to delete |

### Return type

void (empty response body)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteAllAccounts**
> \Swagger\Client\Model\IdentifierList deleteAllAccounts()

Delete all accounts

Delete all accounts of the user that is authorized by the access_token, including all transactions and balance data. Must pass the user's access_token.<br/><br/>Notes: <br/>- Deleting all of the user's accounts also deletes all of his bank connections. <br/>- All notification rules that are connected to any specific accounts will get deleted as well. <br/>- If at least one of the user's bank connections in currently in the process of import, update, or transactions categorization, then this service will perform no action at all.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\AccountsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);

try {
    $result = $apiInstance->deleteAllAccounts();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountsApi->deleteAllAccounts: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**\Swagger\Client\Model\IdentifierList**](../Model/IdentifierList.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **editAccount**
> \Swagger\Client\Model\Account editAccount($id, $body)

Edit an account

Change the name and/or the type and/or the 'isNew' flag of a single bank account of the user that is authorized by the access_token. Must pass the account's identifier, the account's new name and/or type and/or 'isNew' flag, and the user's access_token.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\AccountsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 789; // int | Identifier of the account to edit
$body = new \Swagger\Client\Model\AccountParams(); // \Swagger\Client\Model\AccountParams | New account name and/or type and/or 'isNew' flag

try {
    $result = $apiInstance->editAccount($id, $body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountsApi->editAccount: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Identifier of the account to edit |
 **body** | [**\Swagger\Client\Model\AccountParams**](../Model/AccountParams.md)| New account name and/or type and/or &#39;isNew&#39; flag | [optional]

### Return type

[**\Swagger\Client\Model\Account**](../Model/Account.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **executeSepaDirectDebit**
> \Swagger\Client\Model\PaymentExecutionResponse executeSepaDirectDebit($body)

Execute SEPA Direct Debit

Execute a SEPA direct debit order that has been previously submitted by the use of the /requestSepaDirectDebit service.<br/><br/>Note: in case of using finAPI's web form flow, the web form is dealing with triggering this service itself.<br/><br/>Note that this service only works when your client has payments enabled (see client configuration).<br/><br/>DEPRECATED: This service will be removed at some point. Please refer to the 'Payments' section of the API instead.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\AccountsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\ExecuteSepaDirectDebitParams(); // \Swagger\Client\Model\ExecuteSepaDirectDebitParams | Parameters for the execution of a SEPA direct debit order

try {
    $result = $apiInstance->executeSepaDirectDebit($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountsApi->executeSepaDirectDebit: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\ExecuteSepaDirectDebitParams**](../Model/ExecuteSepaDirectDebitParams.md)| Parameters for the execution of a SEPA direct debit order |

### Return type

[**\Swagger\Client\Model\PaymentExecutionResponse**](../Model/PaymentExecutionResponse.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **executeSepaMoneyTransfer**
> \Swagger\Client\Model\PaymentExecutionResponse executeSepaMoneyTransfer($body)

Execute SEPA Money Transfer

Execute a SEPA money transfer order that has been previously submitted by the use of the /requestSepaMoneyTransfer service.<br/><br/>Note: in case of using finAPI's web form flow, the web form is dealing with triggering this service itself.<br/><br/>Note that this service only works when your client has payments enabled (see client configuration).<br/><br/>DEPRECATED: This service will be removed at some point. Please refer to the 'Payments' section of the API instead.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\AccountsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\ExecuteSepaMoneyTransferParams(); // \Swagger\Client\Model\ExecuteSepaMoneyTransferParams | Parameters for the execution of a SEPA money transfer order

try {
    $result = $apiInstance->executeSepaMoneyTransfer($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountsApi->executeSepaMoneyTransfer: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\ExecuteSepaMoneyTransferParams**](../Model/ExecuteSepaMoneyTransferParams.md)| Parameters for the execution of a SEPA money transfer order |

### Return type

[**\Swagger\Client\Model\PaymentExecutionResponse**](../Model/PaymentExecutionResponse.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getAccount**
> \Swagger\Client\Model\Account getAccount($id)

Get an account

Get a single bank account of the user that is authorized by the access_token. Must pass the account's identifier and the user's access_token.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\AccountsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 789; // int | Identifier of requested account

try {
    $result = $apiInstance->getAccount($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountsApi->getAccount: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Identifier of requested account |

### Return type

[**\Swagger\Client\Model\Account**](../Model/Account.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getAndSearchAllAccounts**
> \Swagger\Client\Model\AccountList getAndSearchAllAccounts($ids, $search, $account_type_ids, $account_types, $bank_connection_ids, $min_last_successful_update, $max_last_successful_update, $min_balance, $max_balance)

Get and search all accounts

Get bank accounts of the user that is authorized by the access_token. Must pass the user's access_token. You can set optional search criteria to get only those bank accounts that you are interested in. If you do not specify any search criteria, then this service functions as a 'get all' service.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\AccountsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$ids = array(56); // int[] | A comma-separated list of account identifiers. If specified, then only accounts whose identifier match any of the given identifiers will be regarded. The maximum number of identifiers is 1000.
$search = "search_example"; // string | If specified, then only those accounts will be contained in the result whose 'accountName', 'iban', 'accountNumber' or 'subAccountNumber' contains the given search string (the matching works case-insensitive). If no accounts contain the search string in any of these fields, then the result will be an empty list. NOTE: If the given search string consists of several terms (separated by whitespace), then ALL of these terms must be contained in the searched fields in order for an account to get included into the result.
$account_type_ids = array(56); // int[] | A comma-separated list of account type ids. If specified, then only accounts that relate to the given types will be regarded. If not specified, then all accounts will be regarded. This field is deprecated and will be removed at some point, please refer to the accountTypes field instead.
$account_types = array("account_types_example"); // string[] | A comma-separated list of account types. If specified, then only accounts that relate to the given types will be regarded. If not specified, then all accounts will be regarded.
$bank_connection_ids = array(56); // int[] | A comma-separated list of bank connection identifiers. If specified, then only accounts that relate to the given bank connections will be regarded. If not specified, then all accounts will be regarded.
$min_last_successful_update = "min_last_successful_update_example"; // string | Lower bound for a account's last successful update date, in the format 'YYYY-MM-DD' (e.g. '2016-01-01'). If specified, then only accounts whose 'lastSuccessfulUpdate' is equal to or later than the given date will be regarded.
$max_last_successful_update = "max_last_successful_update_example"; // string | Upper bound for a account's last successful update date, in the format 'YYYY-MM-DD' (e.g. '2016-01-01'). If specified, then only accounts whose 'lastSuccessfulUpdate' is equal to or earlier than the given date will be regarded.
$min_balance = 8.14; // float | If specified, then only accounts whose balance is equal to or greater than the given balance will be regarded. Can contain a positive or negative number with at most two decimal places. Examples: -300.12, or 90.95
$max_balance = 8.14; // float | If specified, then only accounts whose balance is equal to or less than the given balance will be regarded. Can contain a positive or negative number with at most two decimal places. Examples: -300.12, or 90.95

try {
    $result = $apiInstance->getAndSearchAllAccounts($ids, $search, $account_type_ids, $account_types, $bank_connection_ids, $min_last_successful_update, $max_last_successful_update, $min_balance, $max_balance);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountsApi->getAndSearchAllAccounts: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ids** | [**int[]**](../Model/int.md)| A comma-separated list of account identifiers. If specified, then only accounts whose identifier match any of the given identifiers will be regarded. The maximum number of identifiers is 1000. | [optional]
 **search** | **string**| If specified, then only those accounts will be contained in the result whose &#39;accountName&#39;, &#39;iban&#39;, &#39;accountNumber&#39; or &#39;subAccountNumber&#39; contains the given search string (the matching works case-insensitive). If no accounts contain the search string in any of these fields, then the result will be an empty list. NOTE: If the given search string consists of several terms (separated by whitespace), then ALL of these terms must be contained in the searched fields in order for an account to get included into the result. | [optional]
 **account_type_ids** | [**int[]**](../Model/int.md)| A comma-separated list of account type ids. If specified, then only accounts that relate to the given types will be regarded. If not specified, then all accounts will be regarded. This field is deprecated and will be removed at some point, please refer to the accountTypes field instead. | [optional]
 **account_types** | [**string[]**](../Model/string.md)| A comma-separated list of account types. If specified, then only accounts that relate to the given types will be regarded. If not specified, then all accounts will be regarded. | [optional]
 **bank_connection_ids** | [**int[]**](../Model/int.md)| A comma-separated list of bank connection identifiers. If specified, then only accounts that relate to the given bank connections will be regarded. If not specified, then all accounts will be regarded. | [optional]
 **min_last_successful_update** | **string**| Lower bound for a account&#39;s last successful update date, in the format &#39;YYYY-MM-DD&#39; (e.g. &#39;2016-01-01&#39;). If specified, then only accounts whose &#39;lastSuccessfulUpdate&#39; is equal to or later than the given date will be regarded. | [optional]
 **max_last_successful_update** | **string**| Upper bound for a account&#39;s last successful update date, in the format &#39;YYYY-MM-DD&#39; (e.g. &#39;2016-01-01&#39;). If specified, then only accounts whose &#39;lastSuccessfulUpdate&#39; is equal to or earlier than the given date will be regarded. | [optional]
 **min_balance** | **float**| If specified, then only accounts whose balance is equal to or greater than the given balance will be regarded. Can contain a positive or negative number with at most two decimal places. Examples: -300.12, or 90.95 | [optional]
 **max_balance** | **float**| If specified, then only accounts whose balance is equal to or less than the given balance will be regarded. Can contain a positive or negative number with at most two decimal places. Examples: -300.12, or 90.95 | [optional]

### Return type

[**\Swagger\Client\Model\AccountList**](../Model/AccountList.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getDailyBalances**
> \Swagger\Client\Model\DailyBalanceList getDailyBalances($account_ids, $start_date, $end_date, $with_projection, $page, $per_page, $order)

Get daily balances

Returns the user's daily balances for a given period and a set of specified accounts (or all accounts, if none are specified). The daily balances are calculated by finAPI and are based on the current balances of the regarded accounts.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\AccountsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$account_ids = array(56); // int[] | A comma-separated list of (non-security) account identifiers. If no accounts are specified, all (non-security) accounts of the user are regarded.
$start_date = "start_date_example"; // string | A string in the format 'YYYY-MM-DD'. Note that the requested date range [startDate..endDate] may not exceed 365 days. If startDate is not specified, it defaults to the endDate minus one month.
$end_date = "end_date_example"; // string | A string in the format 'YYYY-MM-DD'. Note that the requested date range [startDate..endDate] may not exceed 365 days. If endDate is not specified, it defaults to today's date.
$with_projection = true; // bool | Whether finAPI should project the first and last actually existing balance of an account into the past and future. When passing 'true', then the result will always contain a daily balance for every day of the entire requested date range, even for days before the first actually existing balance, resp. after the last actually existing balance. Those days will have the same balance as the day of the first actual balance, resp. last actual balance, i.e. the first/last balance will be infinitely projected into the past/the future. When passing 'false', then the result will contain daily balances only from the day on where the first actual balance exists for any of the regarded accounts, and only up to the day where the last actual balance exists for any of the regarded accounts. Note that when in this case there are no actual balances within the requested date range, then an empty array will be returned. Default value for this parameter is 'true'.
$page = 1; // int | Result page that you want to retrieve.
$per_page = 20; // int | Maximum number of records per page. Can be at most 500. NOTE: Due to its validation and visualization, the swagger frontend might show very low performance, or even crashes, when a service responds with a lot of data. It is recommended to use a HTTP client like Postman or DHC instead of our swagger frontend for service calls with large page sizes.
$order = array("order_example"); // string[] | Determines the order of the results. You can order the results by 'date', 'balance', 'income' or 'spending'. The default order for this service is 'date,asc'. You can also order by multiple properties. In that case the order of the parameters passed is important. Example: '/accounts/dailyBalances?order=date,desc&order=balance,asc' will return daily balances ordered by 'date' (descending), where items with the same 'date' are ordered by 'balance' (ascending). The general format is: 'property[,asc|desc]', with 'asc' being the default value. Please note that ordering by multiple fields is not supported in our swagger frontend, but you can test this feature with any HTTP tool of your choice (e.g. postman or DHC).

try {
    $result = $apiInstance->getDailyBalances($account_ids, $start_date, $end_date, $with_projection, $page, $per_page, $order);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountsApi->getDailyBalances: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **account_ids** | [**int[]**](../Model/int.md)| A comma-separated list of (non-security) account identifiers. If no accounts are specified, all (non-security) accounts of the user are regarded. | [optional]
 **start_date** | **string**| A string in the format &#39;YYYY-MM-DD&#39;. Note that the requested date range [startDate..endDate] may not exceed 365 days. If startDate is not specified, it defaults to the endDate minus one month. | [optional]
 **end_date** | **string**| A string in the format &#39;YYYY-MM-DD&#39;. Note that the requested date range [startDate..endDate] may not exceed 365 days. If endDate is not specified, it defaults to today&#39;s date. | [optional]
 **with_projection** | **bool**| Whether finAPI should project the first and last actually existing balance of an account into the past and future. When passing &#39;true&#39;, then the result will always contain a daily balance for every day of the entire requested date range, even for days before the first actually existing balance, resp. after the last actually existing balance. Those days will have the same balance as the day of the first actual balance, resp. last actual balance, i.e. the first/last balance will be infinitely projected into the past/the future. When passing &#39;false&#39;, then the result will contain daily balances only from the day on where the first actual balance exists for any of the regarded accounts, and only up to the day where the last actual balance exists for any of the regarded accounts. Note that when in this case there are no actual balances within the requested date range, then an empty array will be returned. Default value for this parameter is &#39;true&#39;. | [optional] [default to true]
 **page** | **int**| Result page that you want to retrieve. | [optional] [default to 1]
 **per_page** | **int**| Maximum number of records per page. Can be at most 500. NOTE: Due to its validation and visualization, the swagger frontend might show very low performance, or even crashes, when a service responds with a lot of data. It is recommended to use a HTTP client like Postman or DHC instead of our swagger frontend for service calls with large page sizes. | [optional] [default to 20]
 **order** | [**string[]**](../Model/string.md)| Determines the order of the results. You can order the results by &#39;date&#39;, &#39;balance&#39;, &#39;income&#39; or &#39;spending&#39;. The default order for this service is &#39;date,asc&#39;. You can also order by multiple properties. In that case the order of the parameters passed is important. Example: &#39;/accounts/dailyBalances?order&#x3D;date,desc&amp;order&#x3D;balance,asc&#39; will return daily balances ordered by &#39;date&#39; (descending), where items with the same &#39;date&#39; are ordered by &#39;balance&#39; (ascending). The general format is: &#39;property[,asc|desc]&#39;, with &#39;asc&#39; being the default value. Please note that ordering by multiple fields is not supported in our swagger frontend, but you can test this feature with any HTTP tool of your choice (e.g. postman or DHC). | [optional]

### Return type

[**\Swagger\Client\Model\DailyBalanceList**](../Model/DailyBalanceList.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getMultipleAccounts**
> \Swagger\Client\Model\AccountList getMultipleAccounts($ids)

Get multiple accounts

Get a list of multiple bank accounts of the user that is authorized by the access_token. Must pass the accounts' identifiers and the user's access_token. Accounts whose identifiers do not exist or do not relate to the authorized user will not be contained in the result (If this applies to all of the given identifiers, then the result will be an empty list). WARNING: This service is deprecated and will be removed at some point. If you want to get multiple accounts, please instead use the service 'Get and search all accounts' and pass a comma-separated list of identifiers as a parameter 'ids'.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\AccountsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$ids = array(56); // int[] | Comma-separated list of identifiers of requested accounts

try {
    $result = $apiInstance->getMultipleAccounts($ids);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountsApi->getMultipleAccounts: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ids** | [**int[]**](../Model/int.md)| Comma-separated list of identifiers of requested accounts |

### Return type

[**\Swagger\Client\Model\AccountList**](../Model/AccountList.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **requestSepaDirectDebit**
> \Swagger\Client\Model\DirectDebitOrderingResponse requestSepaDirectDebit($body)

Request SEPA Direct Debit

Submit a SEPA direct debit order for one or multiple direct debits. Returns an instruction from the bank server that can be displayed to the user (e.g. \"Enter TAN\"), typically in the language of the bank's country. The order remains valid for execution for only a couple of minutes (the exact validity period depends on the bank). For executing the order, use the /executeSepaDirectDebit service after calling this service. Note that when the order is not executed within the validity period, the bank might take note of that and - if happening too often - ultimately lock the user's online banking access. If there already exists a previously submitted but not yet executed SEPA order for this account (either another direct debit order, or a money transfer order), then that order will be discarded and replaced with the new order that is being created with this service call.<br/><br/>Notes:<br/>&bull; When using a two-step-procedure with flag 'implicitExecute' = true, then this service will immediately execute the direct debit. The response will not contain any challenge message and you won't be required to make a subsequent call to /executeSepaDirectDebit.<br/><br/>NOTE: Depending on your license, this service may respond with HTTP code 451, containing an error message with a identifier of web form in it. In addition to that the response will also have included a 'Location' header, which contains the URL to the web form. In this case, you must forward your user to finAPI's web form. For a detailed explanation of the Web Form Flow, please refer to this article: <a href='https://finapi.zendesk.com/hc/en-us/articles/360002596391' target='_blank'>finAPI's Web Form Flow</a><br/><br/>Note that this service only works when your client has payments enabled (see client configuration).<br/><br/>DEPRECATED: This service will be removed at some point. Please refer to the 'Payments' section of the API instead.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\AccountsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\RequestSepaDirectDebitParams(); // \Swagger\Client\Model\RequestSepaDirectDebitParams | Parameters for a SEPA direct debit request

try {
    $result = $apiInstance->requestSepaDirectDebit($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountsApi->requestSepaDirectDebit: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\RequestSepaDirectDebitParams**](../Model/RequestSepaDirectDebitParams.md)| Parameters for a SEPA direct debit request |

### Return type

[**\Swagger\Client\Model\DirectDebitOrderingResponse**](../Model/DirectDebitOrderingResponse.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **requestSepaMoneyTransfer**
> \Swagger\Client\Model\MoneyTransferOrderingResponse requestSepaMoneyTransfer($body)

Request SEPA Money Transfer

Submit a SEPA money transfer order for either a single or a collective money transfer. Returns an instruction from the bank server that can be displayed to the user (e.g. \"Enter TAN\"), typically in the language of the bank's country. The order remains valid for execution for only a couple of minutes (the exact validity period depends on the bank). For executing the order, use the /executeSepaMoneyTransfer service after calling this service. Note that when the order is not executed within the validity period, the bank might take note of that and - if happening too often - ultimately lock the user's online banking access. If there already exists a previously submitted but not yet executed SEPA order for this account (either another money transfer order, or a direct debit order), then that order will be discarded and replaced with the new order that is being created with this service call.<br/><br/>Notes:<br/>&bull; Some banks may require a multi-step authentication, in which case the service will respond with HTTP code 510 and an error message containing a challenge for the user from the bank. You must display the challenge message to the user, and then retry the service call, passing the user's answer to the bank's challenge in the 'multiStepAuthentication.challengeResponse' field.<br/>&bull; When using a two-step-procedure with flag 'implicitExecute' = true, then this service will immediately execute the money transfer. The response will not contain any challenge message and you won't be required to make a subsequent call to /executeSepaMoneyTransfer.<br/><br/>NOTE: Depending on your license, this service may respond with HTTP code 451, containing an error message with a identifier of web form in it. In addition to that the response will also have included a 'Location' header, which contains the URL to the web form. In this case, you must forward your user to finAPI's web form. For a detailed explanation of the Web Form Flow, please refer to this article: <a href='https://finapi.zendesk.com/hc/en-us/articles/360002596391' target='_blank'>finAPI's Web Form Flow</a><br/><br/>Note that this service only works when your client has payments enabled (see client configuration).<br/><br/>DEPRECATED: This service will be removed at some point. Please refer to the 'Payments' section of the API instead.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\AccountsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\RequestSepaMoneyTransferParams(); // \Swagger\Client\Model\RequestSepaMoneyTransferParams | Parameters for a SEPA money transfer request

try {
    $result = $apiInstance->requestSepaMoneyTransfer($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountsApi->requestSepaMoneyTransfer: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\RequestSepaMoneyTransferParams**](../Model/RequestSepaMoneyTransferParams.md)| Parameters for a SEPA money transfer request |

### Return type

[**\Swagger\Client\Model\MoneyTransferOrderingResponse**](../Model/MoneyTransferOrderingResponse.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

