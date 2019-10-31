# Swagger\Client\BankConnectionsApi

All URIs are relative to *https://localhost*

Method | HTTP request | Description
------------- | ------------- | -------------
[**connectInterface**](BankConnectionsApi.md#connectInterface) | **POST** /api/v1/bankConnections/connectInterface | Connect a new interface
[**deleteAllBankConnections**](BankConnectionsApi.md#deleteAllBankConnections) | **DELETE** /api/v1/bankConnections | Delete all bank connections
[**deleteBankConnection**](BankConnectionsApi.md#deleteBankConnection) | **DELETE** /api/v1/bankConnections/{id} | Delete a bank connection
[**editBankConnection**](BankConnectionsApi.md#editBankConnection) | **PATCH** /api/v1/bankConnections/{id} | Edit a bank connection
[**getAllBankConnections**](BankConnectionsApi.md#getAllBankConnections) | **GET** /api/v1/bankConnections | Get all bank connections
[**getBankConnection**](BankConnectionsApi.md#getBankConnection) | **GET** /api/v1/bankConnections/{id} | Get a bank connection
[**getMultipleBankConnections**](BankConnectionsApi.md#getMultipleBankConnections) | **GET** /api/v1/bankConnections/{ids} | Get multiple bank connections
[**importBankConnection**](BankConnectionsApi.md#importBankConnection) | **POST** /api/v1/bankConnections/import | Import a new bank connection
[**removeInterface**](BankConnectionsApi.md#removeInterface) | **POST** /api/v1/bankConnections/removeInterface | Remove an interface
[**updateBankConnection**](BankConnectionsApi.md#updateBankConnection) | **POST** /api/v1/bankConnections/update | Update a bank connection


# **connectInterface**
> \Swagger\Client\Model\BankConnection connectInterface($body)

Connect a new interface

Connects new interface to an existing bank connection for a specific user. Must pass the connection credentials and the user's access_token. All bank accounts will be downloaded and imported with their current balances, transactions and supported two-step-procedures (note that the amount of available transactions may vary between banks, e.g. some banks deliver all transactions from the past year, others only deliver the transactions from the past three months). The balance and transactions download process runs asynchronously, so this service may return before all balances and transactions have been imported. Also, all downloaded transactions will be categorized by a separate background process that runs asynchronously too. To check the status of the balance and transactions download process as well as the background categorization process, see the status flags that are returned by the GET /bankConnections/<id> service.<br/><br/>NOTE: Depending on your license, this service may respond with HTTP code 451, containing an error message with a identifier of web form in it. In addition to that the response will also have included a 'Location' header, which contains the URL to the web form. In this case, you must forward your user to finAPI's web form. For a detailed explanation of the Web Form Flow, please refer to this article: <a href='https://finapi.zendesk.com/hc/en-us/articles/360002596391' target='_blank'>finAPI's Web Form Flow</a>

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\BankConnectionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\ConnectInterfaceParams(); // \Swagger\Client\Model\ConnectInterfaceParams | Connect interface parameters

try {
    $result = $apiInstance->connectInterface($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BankConnectionsApi->connectInterface: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\ConnectInterfaceParams**](../Model/ConnectInterfaceParams.md)| Connect interface parameters |

### Return type

[**\Swagger\Client\Model\BankConnection**](../Model/BankConnection.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteAllBankConnections**
> \Swagger\Client\Model\IdentifierList deleteAllBankConnections()

Delete all bank connections

Delete all bank connections of the user that is authorized by the access_token. Must pass the user's access_token.<br/><br/>Notes: <br/>- All notification rules that are connected to any specific bank connection will get deleted as well. <br/>- If at least one bank connection is busy (currently in the process of import, update, or transactions categorization), then this service will perform no action at all.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\BankConnectionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);

try {
    $result = $apiInstance->deleteAllBankConnections();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BankConnectionsApi->deleteAllBankConnections: ', $e->getMessage(), PHP_EOL;
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

# **deleteBankConnection**
> deleteBankConnection($id)

Delete a bank connection

Delete a single bank connection of the user that is authorized by the access_token, including all of its accounts and their transactions and balance data. Must pass the connection's identifier and the user's access_token.<br/><br/>Notes: <br/>- All notification rules that are connected to the bank connection will get adjusted so that they no longer have this connection listed. Notification rules that are connected to just this bank connection (and no other connection) will get deleted altogether. <br/>- A bank connection cannot get deleted while it is in the process of import, update, or transactions categorization.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\BankConnectionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 789; // int | Identifier of the bank connection to delete

try {
    $apiInstance->deleteBankConnection($id);
} catch (Exception $e) {
    echo 'Exception when calling BankConnectionsApi->deleteBankConnection: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Identifier of the bank connection to delete |

### Return type

void (empty response body)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **editBankConnection**
> \Swagger\Client\Model\BankConnection editBankConnection($id, $body)

Edit a bank connection

Edit bank connection data. Must pass the connection's identifier and the user's access_token.<br/><br/>Note that a bank connection's credentials cannot be changed while it is in the process of import, update, or transactions categorization.<br/><br/>NOTE: Depending on your license, this service may respond with HTTP code 451, containing an error message with a identifier of web form in it. In addition to that the response will also have included a 'Location' header, which contains the URL to the web form. In this case, you must forward your user to finAPI's web form. For a detailed explanation of the Web Form Flow, please refer to this article: <a href='https://finapi.zendesk.com/hc/en-us/articles/360002596391' target='_blank'>finAPI's Web Form Flow</a>

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\BankConnectionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 789; // int | Identifier of the bank connection to change the parameters for
$body = new \Swagger\Client\Model\EditBankConnectionParams(); // \Swagger\Client\Model\EditBankConnectionParams | New bank connection parameters

try {
    $result = $apiInstance->editBankConnection($id, $body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BankConnectionsApi->editBankConnection: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Identifier of the bank connection to change the parameters for |
 **body** | [**\Swagger\Client\Model\EditBankConnectionParams**](../Model/EditBankConnectionParams.md)| New bank connection parameters |

### Return type

[**\Swagger\Client\Model\BankConnection**](../Model/BankConnection.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getAllBankConnections**
> \Swagger\Client\Model\BankConnectionList getAllBankConnections($ids)

Get all bank connections

Get bank connections of the user that is authorized by the access_token. Must pass the user's access_token. You can set optional search criteria to get only those bank connections that you are interested in. If you do not specify any search criteria, then this service functions as a 'get all' service.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\BankConnectionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$ids = array(56); // int[] | A comma-separated list of bank connection identifiers. If specified, then only bank connections whose identifier match any of the given identifiers will be regarded. The maximum number of identifiers is 1000.

try {
    $result = $apiInstance->getAllBankConnections($ids);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BankConnectionsApi->getAllBankConnections: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ids** | [**int[]**](../Model/int.md)| A comma-separated list of bank connection identifiers. If specified, then only bank connections whose identifier match any of the given identifiers will be regarded. The maximum number of identifiers is 1000. | [optional]

### Return type

[**\Swagger\Client\Model\BankConnectionList**](../Model/BankConnectionList.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getBankConnection**
> \Swagger\Client\Model\BankConnection getBankConnection($id)

Get a bank connection

Get a single bank connection of the user that is authorized by the access_token. Must pass the connection's identifier and the user's access_token.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\BankConnectionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 789; // int | Identifier of requested bank connection

try {
    $result = $apiInstance->getBankConnection($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BankConnectionsApi->getBankConnection: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Identifier of requested bank connection |

### Return type

[**\Swagger\Client\Model\BankConnection**](../Model/BankConnection.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getMultipleBankConnections**
> \Swagger\Client\Model\BankConnectionList getMultipleBankConnections($ids)

Get multiple bank connections

Get a list of multiple bank connections of the user that is authorized by the access_token. Must pass the connections' identifiers and the user's access_token. Connections whose identifiers do not exist or do not relate to the authorized user will not be contained in the result (If this applies to all of the given identifiers, then the result will be an empty list). WARNING: This service is deprecated and will be removed at some point. If you want to get multiple bank connections, please instead use the service 'Get all bank connections' and pass a comma-separated list of identifiers as a parameter 'ids'.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\BankConnectionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$ids = array(56); // int[] | Comma-separated list of identifiers of requested bank connections

try {
    $result = $apiInstance->getMultipleBankConnections($ids);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BankConnectionsApi->getMultipleBankConnections: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ids** | [**int[]**](../Model/int.md)| Comma-separated list of identifiers of requested bank connections |

### Return type

[**\Swagger\Client\Model\BankConnectionList**](../Model/BankConnectionList.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **importBankConnection**
> \Swagger\Client\Model\BankConnection importBankConnection($body)

Import a new bank connection

Imports a new bank connection for a specific user. Must pass the connection credentials and the user's access_token. All bank accounts will be downloaded and imported with their current balances, transactions and supported two-step-procedures (note that the amount of available transactions may vary between banks, e.g. some banks deliver all transactions from the past year, others only deliver the transactions from the past three months). The balance and transactions download process runs asynchronously, so this service may return before all balances and transactions have been imported. Also, all downloaded transactions will be categorized by a separate background process that runs asynchronously too. To check the status of the balance and transactions download process as well as the background categorization process, see the status flags that are returned by the GET /bankConnections/<id> service.<br/><br/>You can also import a \"demo connection\" which contains a single bank account with some pre-defined transactions. To import the demo connection, you need to pass the identifier of the \"finAPI Test Bank\". In case of demo connection import, any other fields besides the bank identifier can remain unset. The bankingUserId, bankingCustomerId, bankingPin, and storeSecrets fields will be stored if you pass them, however they will not be regarded when updating the demo connection (in other words: It doesn't matter what credentials you choose for the demo connection). Note however that if you want to import the demo connection multiple times for the same user, you must use a different bankingUserId and/or bankingCustomerId for each of the imports. Also note that the skipPositionsDownload flag is ignored for the demo bank connection, i.e. when importing the demo bank connection, you will always get the transactions for its account. You can enable multi-step authentication for the demo bank connection by setting the bank connection name to \"MSA\".<br/><br/><b>For a more in-depth understanding of the import process, please also read this article on our Dev Portal: <a href='https://finapi.zendesk.com/hc/en-us/articles/115000296607-Import-Update-of-Bank-Connections-Accounts' target='_blank'>Import & Update of Bank Connections / Accounts</a></b><br/><br/>NOTE: Depending on your license, this service may respond with HTTP code 451, containing an error message with a identifier of web form in it. In addition to that the response will also have included a 'Location' header, which contains the URL to the web form. In this case, you must forward your user to finAPI's web form. For a detailed explanation of the Web Form Flow, please refer to this article: <a href='https://finapi.zendesk.com/hc/en-us/articles/360002596391' target='_blank'>finAPI's Web Form Flow</a><br/><br/><b>Attention:</b> Due to changes on the bank's side we have been forced to limit the maxDaysForDownload field to 89 days. Now any import or update of a bank connection will only fetch the last three months of transactions per account, regardless if maxDaysForDownload is defined or not. We're working of fixing this behaviour by implementing the multi step authentication workflow for FinTS.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\BankConnectionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\ImportBankConnectionParams(); // \Swagger\Client\Model\ImportBankConnectionParams | Import bank connection parameters

try {
    $result = $apiInstance->importBankConnection($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BankConnectionsApi->importBankConnection: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\ImportBankConnectionParams**](../Model/ImportBankConnectionParams.md)| Import bank connection parameters |

### Return type

[**\Swagger\Client\Model\BankConnection**](../Model/BankConnection.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **removeInterface**
> removeInterface($body)

Remove an interface

Remove an interface from bank connection and from all associated accounts in the bank connection. Notes: <br/>- An interface cannot get deleted while it is in the process of import or update.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\BankConnectionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\RemoveInterfaceParams(); // \Swagger\Client\Model\RemoveInterfaceParams | Remove interface parameters

try {
    $apiInstance->removeInterface($body);
} catch (Exception $e) {
    echo 'Exception when calling BankConnectionsApi->removeInterface: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\RemoveInterfaceParams**](../Model/RemoveInterfaceParams.md)| Remove interface parameters |

### Return type

void (empty response body)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateBankConnection**
> \Swagger\Client\Model\BankConnection updateBankConnection($body)

Update a bank connection

Update an existing bank connection of the user that is authorized by the access_token. Downloads and imports the current account balances and new transactions. Must pass the connection's identifier and the user's access_token. For more information about the processes of authentication, data download and transactions categorization, see POST /bankConnections/import. Note that supported two-step-procedures are updated as well. It may unset the current default two-step-procedure of the given bank connection (but only if this procedure is not supported anymore by the bank). You can also update the \"demo connection\" (in this case, the fields 'bankingPin', 'importNewAccounts', and 'skipPositionsDownload' will be ignored).<br/><br/>Note that you cannot trigger an update of a bank connection as long as there is still a previously triggered update running.<br/><br/><b>For a more in-depth understanding of the update process, please also read this article on our Dev Portal: <a href='https://finapi.zendesk.com/hc/en-us/articles/115000296607-Import-Update-of-Bank-Connections-Accounts' target='_blank'>Import & Update of Bank Connections / Accounts</a></b><br/><br/>NOTE: Depending on your license, this service may respond with HTTP code 451, containing an error message with a identifier of web form in it. In addition to that the response will also have included a 'Location' header, which contains the URL to the web form. In this case, you must forward your user to finAPI's web form. For a detailed explanation of the Web Form Flow, please refer to this article: <a href='https://finapi.zendesk.com/hc/en-us/articles/360002596391' target='_blank'>finAPI's Web Form Flow</a><br/><br/><b>Attention:</b> Due to changes on the bank's side we have been forced to limit the maxDaysForDownload field to 89 days. Now any import or update of a bank connection will only fetch the last three months of transactions per account, regardless if maxDaysForDownload is defined or not. We're working of fixing this behaviour by implementing the multi step authentication workflow for FinTS.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\BankConnectionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\UpdateBankConnectionParams(); // \Swagger\Client\Model\UpdateBankConnectionParams | Update bank connection parameters

try {
    $result = $apiInstance->updateBankConnection($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BankConnectionsApi->updateBankConnection: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\UpdateBankConnectionParams**](../Model/UpdateBankConnectionParams.md)| Update bank connection parameters |

### Return type

[**\Swagger\Client\Model\BankConnection**](../Model/BankConnection.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

