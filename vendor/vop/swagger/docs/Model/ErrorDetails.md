# ErrorDetails

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**message** | **string** | Error message | [optional] 
**code** | **string** | Error code. See the documentation of the individual services for details about what values may be returned. | 
**type** | **string** | Error type. BUSINESS errors depict German error messages for the user, e.g. from a bank server. TECHNICAL errors depict internal errors. | 
**multi_step_authentication** | [**\Swagger\Client\Model\MultiStepAuthenticationChallenge**](MultiStepAuthenticationChallenge.md) | This field is set when a multi-step authentication is required, i.e. when you need to repeat the original service call and provide additional data. The field contains information about what additional data is required. | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


