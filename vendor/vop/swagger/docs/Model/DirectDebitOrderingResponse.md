# DirectDebitOrderingResponse

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**success_message** | **string** | Technical message from the bank server, confirming the success of the request. Typically, you would not want to present this message to the user. Note that this field may not be set. However if it is not set, it does not necessarily mean that there was an error in processing the request. | [optional] 
**warn_message** | **string** | In some cases, a bank server may accept the requested order, but return a warn message. This message may be of technical nature, but could also be of interest to the user. | [optional] 
**payment_id** | **int** | Payment identifier. Can be used to retrieve the status of the payment (see &#39;Get payments&#39; service). | 
**challenge_message** | **string** | Message from the bank server containing information or instructions on how to retrieve the TAN that is needed to execute the requested order. This message should be presented to the user. Note that some bank servers may limit the message to just the most crucial information, e.g. the message may contain just a single number that depicts the target TAN number on a user&#39;s TAN list. You may want to parse the challenge message for such cases and extend it with more detailed information before showing it to the user. | [optional] 
**answer_field_label** | **string** | Suggestion from the bank server on how you can label your input field where the user must enter his TAN. A typical value that many bank servers give is &#39;TAN-Nummer&#39;. | [optional] 
**tan_list_number** | **string** | In case that the bank server has instructed the user to look up a TAN from a TAN list, this field may contain the identification number of the TAN list. However in most cases, this field is only set (i.e. not null) when the user has multiple active TAN lists. | [optional] 
**optical_data** | **string** | In case that the bank server has instructed the user to scan a flicker code, then this field will contain the raw data for the flicker animation as a BASE-64 string. Otherwise, this field will be not set (i.e. null). For more information on how to process the flicker code data, please address the &lt;a href&#x3D;&#39;https://finapi.zendesk.com&#39; target&#x3D;&#39;_blank&#39;&gt;finAPI Developer Portal&lt;/a&gt;. | [optional] 
**photo_tan_mime_type** | **string** | In case that the &#39;photoTanData&#39; field is set (i.e. not null), this field contains the MIME type to use for interpreting the photo data (e.g.: &#39;image/png&#39;) | [optional] 
**photo_tan_data** | **string** | In case that the bank server has instructed the user to scan a photo (or more generally speaking, any kind of QR-code-like data), then this field will contain the raw data of the photo as a BASE-64 string. Otherwise, this field will be not set (i.e. null). For more information on how to process the photo data, please address the &lt;a href&#x3D;&#39;https://finapi.zendesk.com&#39; target&#x3D;&#39;_blank&#39;&gt;finAPI Developer Portal&lt;/a&gt;. | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


