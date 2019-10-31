# Transaction

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Transaction identifier | 
**parent_id** | **int** | Parent transaction identifier | [optional] 
**account_id** | **int** | Account identifier | 
**value_date** | **string** | Value date in the format &#39;YYYY-MM-DD HH:MM:SS.SSS&#39; (german time). | 
**bank_booking_date** | **string** | Bank booking date in the format &#39;YYYY-MM-DD HH:MM:SS.SSS&#39; (german time). | 
**finapi_booking_date** | **string** | finAPI Booking date in the format &#39;YYYY-MM-DD HH:MM:SS.SSS&#39; (german time). NOTE: In some cases, banks may deliver transactions that are booked in future, but already included in the current account balance. To keep the account balance consistent with the set of transactions, such \&quot;future transactions\&quot; will be imported with their finapiBookingDate set to the current date (i.e.: date of import). The finapiBookingDate will automatically get adjusted towards the bankBookingDate each time the associated bank account is updated. Example: A transaction is imported on July, 3rd, with a bank reported booking date of July, 6th. The transaction will be imported with its finapiBookingDate set to July, 3rd. Then, on July 4th, the associated account is updated. During this update, the transaction&#39;s finapiBookingDate will be automatically adjusted to July 4th. This adjustment of the finapiBookingDate takes place on each update until the bank account is updated on July 6th or later, in which case the transaction&#39;s finapiBookingDate will be adjusted to its final value, July 6th.&lt;br/&gt; The finapiBookingDate is the date that is used by the finAPI PFM services. E.g. when you calculate the spendings of an account for the current month, and have a transaction with finapiBookingDate in the current month but bankBookingDate at the beginning of the next month, then this transaction is included in the calculations (as the bank has this transaction&#39;s amount included in the current account balance as well). | 
**amount** | **float** | Transaction amount | 
**purpose** | **string** | Transaction purpose. Maximum length: 2000 | [optional] 
**counterpart_name** | **string** | Counterpart name. Maximum length: 80 | [optional] 
**counterpart_account_number** | **string** | Counterpart account number | [optional] 
**counterpart_iban** | **string** | Counterpart IBAN | [optional] 
**counterpart_blz** | **string** | Counterpart BLZ | [optional] 
**counterpart_bic** | **string** | Counterpart BIC | [optional] 
**counterpart_bank_name** | **string** | Counterpart Bank name | [optional] 
**counterpart_mandate_reference** | **string** | The mandate reference of the counterpart | [optional] 
**counterpart_customer_reference** | **string** | The customer reference of the counterpart | [optional] 
**counterpart_creditor_id** | **string** | The creditor ID of the counterpart. Exists only for SEPA direct debit transactions (\&quot;Lastschrift\&quot;). | [optional] 
**counterpart_debitor_id** | **string** | The originator&#39;s identification code. Exists only for SEPA money transfer transactions (\&quot;Überweisung\&quot;). | [optional] 
**type** | **string** | Transaction type, according to the bank. If set, this will contain a German term that you can display to the user. Some examples of common values are: \&quot;Lastschrift\&quot;, \&quot;Auslands&amp;uuml;berweisung\&quot;, \&quot;Geb&amp;uuml;hren\&quot;, \&quot;Zinsen\&quot;. The maximum possible length of this field is 255 characters. | [optional] 
**type_code_zka** | **string** | ZKA business transaction code which relates to the transaction&#39;s type. Possible values range from 1 through 999. If no information about the ZKA type code is available, then this field will be null. | [optional] 
**type_code_swift** | **string** | SWIFT transaction type code. If no information about the SWIFT code is available, then this field will be null. | [optional] 
**sepa_purpose_code** | **string** | SEPA purpose code, according to ISO 20022 | [optional] 
**primanota** | **string** | Transaction primanota (bank side identification number) | [optional] 
**category** | [**\Swagger\Client\Model\Category**](Category.md) | Transaction category, if any is assigned. Note: Recently imported transactions that have currently no category assigned might still get categorized by the background categorization process. To check the status of the background categorization, see GET /bankConnections. Manual category assignments to a transaction will remove the transaction from the background categorization process (i.e. the background categorization process will never overwrite a manual category assignment). | [optional] 
**labels** | [**\Swagger\Client\Model\Label[]**](Label.md) | Array of assigned labels | [optional] 
**is_potential_duplicate** | **bool** | While finAPI uses a well-elaborated algorithm for uniquely identifying transactions, there is still the possibility that during an account update, a transaction that was imported previously may be imported a second time as a new transaction. For example, this can happen if some transaction data changes on the bank server side. However, finAPI also includes an algorithm of identifying such \&quot;potential duplicate\&quot; transactions. If this field is set to true, it means that finAPI detected a similar transaction that might actually be the same. It is recommended to communicate this information to the end user, and give him an option to delete the transaction in case he confirms that it really is a duplicate. | 
**is_adjusting_entry** | **bool** | Indicating whether this transaction is an adjusting entry (&#39;Zwischensaldo&#39;).&lt;br/&gt;&lt;br/&gt;Adjusting entries do not originate from the bank, but are added by finAPI during an account update when the bank reported account balance does not add up to the set of transactions that finAPI receives for the account. In this case, the adjusting entry will fix the deviation between the balance and the received transactions so that both adds up again.&lt;br/&gt;&lt;br/&gt;Possible causes for such deviations are:&lt;br/&gt;- Inconsistencies in how the bank calculates the balance, for instance when not yet booked transactions are already included in the balance, but not included in the set of transactions&lt;br/&gt;- Gaps in the transaction history that finAPI receives, for instance because the account has not been updated for a while and older transactions are no longer available | 
**is_new** | **bool** | Indicating whether this transaction is &#39;new&#39; or not. Any newly imported transaction will have this flag initially set to true. How you use this field is up to your interpretation. For example, you might want to set it to false once a user has clicked on/seen the transaction. You can change this flag to &#39;false&#39; with the PATCH method. | 
**import_date** | **string** | Date of transaction import, in the format &#39;YYYY-MM-DD HH:MM:SS.SSS&#39; (german time). | 
**children** | **int[]** | Sub-transactions identifiers (if this transaction is split) | [optional] 
**paypal_data** | [**\Swagger\Client\Model\PaypalTransactionData**](PaypalTransactionData.md) | Additional, PayPal-specific transaction data. This field is only set for transactions that belong to an account of the &#39;PayPal&#39; bank (BLZ &#39;PAYPAL&#39;).&lt;br/&gt;NOTE: This field is deprecated as the bank with blz &#39;PAYPAL&#39; is no longer supported. Do not use this field, as it will be removed at some point. | [optional] 
**end_to_end_reference** | **string** | End-To-End reference | [optional] 
**compensation_amount** | **float** | Compensation Amount. Sum of reimbursement of out-of-pocket expenses plus processing brokerage in case of a national return / refund debit as well as an optional interest equalisation. Exists predominantly for SEPA direct debit returns. | [optional] 
**original_amount** | **float** | Original Amount of the original direct debit. Exists predominantly for SEPA direct debit returns. | [optional] 
**different_debitor** | **string** | Payer&#39;s/debtor&#39;s reference party (in the case of a credit transfer) or payee&#39;s/creditor&#39;s reference party (in the case of a direct debit) | [optional] 
**different_creditor** | **string** | Payee&#39;s/creditor&#39;s reference party (in the case of a credit transfer) or payer&#39;s/debtor&#39;s reference party (in the case of a direct debit) | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


