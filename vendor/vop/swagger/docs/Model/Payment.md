# Payment

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Payment identifier | 
**account_id** | **int** | Identifier of the account to which this payment relates | 
**type** | **string** | Payment type | 
**amount** | **float** | Total money amount of the payment order(s), as absolute value | 
**order_count** | **int** | Total count of orders included in this payment | 
**status** | **string** | Current payment status:&lt;br/&gt; &amp;bull; OPEN: means that this payment has been created in finAPI, but not yet submitted to the bank.&lt;br/&gt; &amp;bull; PENDING: means that this payment has been requested at the bank, but not yet executed.&lt;br/&gt; &amp;bull; SUCCESSFUL: means that this payment has been successfully executed.&lt;br/&gt; &amp;bull; NOT_SUCCESSFUL: means that this payment could not be executed successfully.&lt;br/&gt; &amp;bull; DISCARDED: means that this payment was discarded, either because another payment was requested for the same account before this payment was executed and the bank does not support this, or because the bank has rejected the payment even before the execution. | 
**bank_message** | **string** | Contains the bank&#39;s response to the execution of this payment. This field is not set until the payment gets executed. Note that even after the execution of the payment, the field might still not be set, if the bank did not send any response message. | [optional] 
**request_date** | **string** | Time of when this payment was requested, in the format &#39;YYYY-MM-DD HH:MM:SS.SSS&#39; (german time) | [optional] 
**execution_date** | **string** | Time of when this payment was executed, in the format &#39;YYYY-MM-DD HH:MM:SS.SSS&#39; (german time) | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


