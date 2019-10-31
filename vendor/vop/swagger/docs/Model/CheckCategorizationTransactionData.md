# CheckCategorizationTransactionData

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**transaction_id** | **string** | Identifier of transaction. This can be any arbitrary string that will be passed back in the response so that you can map the results to the given transactions. Note that the identifier must be unique within the given list of transactions. | 
**account_type_id** | **int** | Identifier of account type.&lt;br/&gt;&lt;br/&gt;1 &#x3D; Checking,&lt;br/&gt;2 &#x3D; Savings,&lt;br/&gt;3 &#x3D; CreditCard,&lt;br/&gt;4 &#x3D; Security,&lt;br/&gt;5 &#x3D; Loan,&lt;br/&gt;6 &#x3D; Pocket (DEPRECATED; will not be returned for any account unless this type has explicitly been set via PATCH),&lt;br/&gt;7 &#x3D; Membership,&lt;br/&gt;8 &#x3D; Bausparen&lt;br/&gt;&lt;br/&gt; | 
**amount** | **float** | Amount | 
**purpose** | **string** | Purpose. Any symbols are allowed. Maximum length is 2000. Default value: null. | [optional] 
**counterpart** | **string** | Counterpart. Any symbols are allowed. Maximum length is 80. Default value: null. | [optional] 
**counterpart_iban** | **string** | Counterpart IBAN. Default value: null. | [optional] 
**counterpart_account_number** | **string** | Counterpart account number. Default value: null. | [optional] 
**counterpart_blz** | **string** | Counterpart BLZ. Default value: null. | [optional] 
**counterpart_bic** | **string** | Counterpart BIC. Default value: null. | [optional] 
**mc_code** | **string** | Merchant category code (for credit card transactions only). May only contain up to 4 digits. Default value: null. | [optional] 
**type_code_zka** | **string** | ZKA business transaction code which relates to the transaction&#39;s type (Number from 0 through 999). Default value: null. | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


