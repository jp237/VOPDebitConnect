# NotificationRule

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Notification rule identifier | 
**trigger_event** | **string** | Trigger event type | 
**params** | **map[string,string]** | Additional parameters that are specific to the trigger event type. Please refer to the documentation for details. | [optional] 
**callback_handle** | **string** | The string that finAPI includes into the notifications that it sends based on this rule. | [optional] 
**include_details** | **bool** | Whether the notification messages that will be sent based on this rule contain encrypted detailed data or not | 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


