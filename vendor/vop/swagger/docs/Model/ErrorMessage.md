# ErrorMessage

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**errors** | [**\Swagger\Client\Model\ErrorDetails[]**](ErrorDetails.md) | List of errors | 
**date** | **string** | Server date of when the error(s) occurred, in the format YYYY-MM-DD HH:MM:SS.SSS | 
**request_id** | **string** | ID of the request that caused this error. This is either what you have passed for the header &#39;X-REQUEST-ID&#39;, or an auto-generated ID in case you didn&#39;t pass any value for that header. | [optional] 
**endpoint** | **string** | The service endpoint that was called | 
**auth_context** | **string** | Information about the authorization context of the service call | 
**bank** | **string** | BLZ and name (in format \&quot;&lt;BLZ&gt; - &lt;name&gt;\&quot;) of a bank that was used for the original request | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


