# Swagger\Client\NotificationRulesApi

All URIs are relative to *https://localhost*

Method | HTTP request | Description
------------- | ------------- | -------------
[**createNotificationRule**](NotificationRulesApi.md#createNotificationRule) | **POST** /api/v1/notificationRules | Create a new notification rule
[**deleteAllNotificationRules**](NotificationRulesApi.md#deleteAllNotificationRules) | **DELETE** /api/v1/notificationRules | Delete all notification rules
[**deleteNotificationRule**](NotificationRulesApi.md#deleteNotificationRule) | **DELETE** /api/v1/notificationRules/{id} | Delete a notification rule
[**getAndSearchAllNotificationRules**](NotificationRulesApi.md#getAndSearchAllNotificationRules) | **GET** /api/v1/notificationRules | Get and search all notification rules
[**getNotificationRule**](NotificationRulesApi.md#getNotificationRule) | **GET** /api/v1/notificationRules/{id} | Get a notification rule


# **createNotificationRule**
> \Swagger\Client\Model\NotificationRule createNotificationRule($body)

Create a new notification rule

Create a new notification rule for a specific user. Must pass the user's access_token.<br/><br/>Setting up notification rules for a user allows your client application to get notified about changes in the user's data, e.g. when new transactions were downloaded, an account's balance has changed, or the user's banking credentials are no longer correct. Note that currently, this feature is implemented only for finAPI's automatic batch update, i.e. notification rules are only relevant when the user has activated the automatic updates (and when the automatic batch update is activated in general for your client).<br/><br/>There are different kinds of notification rules. The kind of a rule is depicted by the 'triggerEvent'. The trigger event specifies what data you have to pass when creating a rule (specifically, the contents of the 'params' field), on which events finAPI will send notifications to your client application, as well as what data is contained in those notifications. The specifics of the different trigger events are documented in the following article on our Dev Portal: <a href='https://finapi.zendesk.com/hc/en-us/articles/232324608-How-to-create-notification-rules-and-receive-notifications' target='_blank'>How to create notification rules and receive notifications</a>

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\NotificationRulesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\NotificationRuleParams(); // \Swagger\Client\Model\NotificationRuleParams | Notification rule parameters

try {
    $result = $apiInstance->createNotificationRule($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling NotificationRulesApi->createNotificationRule: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\NotificationRuleParams**](../Model/NotificationRuleParams.md)| Notification rule parameters |

### Return type

[**\Swagger\Client\Model\NotificationRule**](../Model/NotificationRule.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteAllNotificationRules**
> \Swagger\Client\Model\IdentifierList deleteAllNotificationRules()

Delete all notification rules

Delete all notification rules of the user that is authorized by the access_token. Must pass the user's access_token.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\NotificationRulesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);

try {
    $result = $apiInstance->deleteAllNotificationRules();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling NotificationRulesApi->deleteAllNotificationRules: ', $e->getMessage(), PHP_EOL;
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

# **deleteNotificationRule**
> deleteNotificationRule($id)

Delete a notification rule

Delete a single notification rule of the user that is authorized by the access_token. Must pass the notification rule's identifier and the user's access_token.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\NotificationRulesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 789; // int | Identifier of the notification rule to delete

try {
    $apiInstance->deleteNotificationRule($id);
} catch (Exception $e) {
    echo 'Exception when calling NotificationRulesApi->deleteNotificationRule: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Identifier of the notification rule to delete |

### Return type

void (empty response body)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getAndSearchAllNotificationRules**
> \Swagger\Client\Model\NotificationRuleList getAndSearchAllNotificationRules($ids, $trigger_event, $include_details)

Get and search all notification rules

Get notification rules of the user that is authorized by the access_token. Must pass the user's access_token. You can set optional search criteria to get only those notification rules that you are interested in. If you do not specify any search criteria, then this service functions as a 'get all' service.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\NotificationRulesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$ids = array(56); // int[] | A comma-separated list of notification rule identifiers. If specified, then only notification rules whose identifier match any of the given identifiers will be regarded. The maximum number of identifiers is 1000.
$trigger_event = "trigger_event_example"; // string | If specified, then only notification rules with given trigger event will be regarded.
$include_details = true; // bool | If specified, then only notification rules that include or not include details will be regarded.

try {
    $result = $apiInstance->getAndSearchAllNotificationRules($ids, $trigger_event, $include_details);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling NotificationRulesApi->getAndSearchAllNotificationRules: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ids** | [**int[]**](../Model/int.md)| A comma-separated list of notification rule identifiers. If specified, then only notification rules whose identifier match any of the given identifiers will be regarded. The maximum number of identifiers is 1000. | [optional]
 **trigger_event** | **string**| If specified, then only notification rules with given trigger event will be regarded. | [optional]
 **include_details** | **bool**| If specified, then only notification rules that include or not include details will be regarded. | [optional]

### Return type

[**\Swagger\Client\Model\NotificationRuleList**](../Model/NotificationRuleList.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getNotificationRule**
> \Swagger\Client\Model\NotificationRule getNotificationRule($id)

Get a notification rule

Get a single notification rule of the user that is authorized by the access_token. Must pass the notification rule's identifier and the user's access_token.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: finapi_auth
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new Swagger\Client\Api\NotificationRulesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 789; // int | Identifier of requested notification rule

try {
    $result = $apiInstance->getNotificationRule($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling NotificationRulesApi->getNotificationRule: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Identifier of requested notification rule |

### Return type

[**\Swagger\Client\Model\NotificationRule**](../Model/NotificationRule.md)

### Authorization

[finapi_auth](../../README.md#finapi_auth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

