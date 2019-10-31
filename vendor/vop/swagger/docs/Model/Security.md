# Security

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identifier. Note: Whenever a security account is being updated, its security positions will be internally re-created, meaning that the identifier of a security position might change over time. | 
**account_id** | **int** | Security account identifier | 
**name** | **string** | Name | [optional] 
**isin** | **string** | ISIN | [optional] 
**wkn** | **string** | WKN | [optional] 
**quote** | **float** | Quote | [optional] 
**quote_currency** | **string** | Currency of quote | [optional] 
**quote_type** | **string** | Type of quote. &#39;PERC&#39; if quote is a percentage value, &#39;ACTU&#39; if quote is the actual amount | [optional] 
**quote_date** | **string** | Quote date in the format &#39;YYYY-MM-DD HH:MM:SS.SSS&#39; (german time). | [optional] 
**quantity_nominal** | **float** | Value of quantity or nominal | [optional] 
**quantity_nominal_type** | **string** | Type of quantity or nominal value. &#39;UNIT&#39; if value is a quantity, &#39;FAMT&#39; if value is the nominal amount | [optional] 
**market_value** | **float** | Market value | [optional] 
**market_value_currency** | **string** | Currency of market value | [optional] 
**entry_quote** | **float** | Entry quote | [optional] 
**entry_quote_currency** | **string** | Currency of entry quote | [optional] 
**profit_or_loss** | **float** | Current profit or loss | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


