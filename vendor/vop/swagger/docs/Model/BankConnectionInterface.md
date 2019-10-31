# BankConnectionInterface

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**interface** | **string** | Bank interface. Possible values:&lt;br&gt;&lt;br&gt;&amp;bull; &lt;code&gt;FINTS_SERVER&lt;/code&gt; - means that finAPI will download data via the bank&#39;s FinTS interface.&lt;br&gt;&amp;bull; &lt;code&gt;WEB_SCRAPER&lt;/code&gt; - means that finAPI will parse data from the bank&#39;s online banking website.&lt;br&gt;&amp;bull; &lt;code&gt;XS2A&lt;/code&gt; - means that finAPI will download data via the bank&#39;s XS2A interface.&lt;br&gt; | 
**login_credentials** | [**\Swagger\Client\Model\LoginCredentialResource[]**](LoginCredentialResource.md) | Login fields for this interface, in the order that we suggest to show them to the user. | [optional] 
**default_two_step_procedure_id** | **string** | The default two-step-procedure for this interface. Must match one of the available &#39;procedureId&#39;s from the &#39;twoStepProcedures&#39; list. When this field is set, then finAPI will automatically try to select the procedure wherever applicable. Note that the list of available procedures of a bank connection may change as a result of an update of the connection, and if this field references a procedure that is no longer available after an update, finAPI will automatically clear the default procedure (set it to null). | [optional] 
**two_step_procedures** | [**\Swagger\Client\Model\TwoStepProcedure[]**](TwoStepProcedure.md) | Available two-step-procedures in this interface, used for submitting a money transfer or direct debit request (see /accounts/requestSepaMoneyTransfer or /requestSepaDirectDebit),or for multi-step-authentication during bank connection import or update. The available two-step-procedures mya be re-evaluated each time this bank connection is updated (/bankConnections/update). This means that this list may change as a result of an update. | [optional] 
**ais_consent** | [**\Swagger\Client\Model\BankConsent**](BankConsent.md) | If this field is set, it means that this interface is handing out a consent to finAPI in exchange for the login credentials. finAPI needs to use this consent to get access to the account list and account data (i.e. Account Information Services, AIS). If this field is not set, it means that this interface does not use such consents. | [optional] 
**last_manual_update** | [**\Swagger\Client\Model\UpdateResult**](UpdateResult.md) | Result of the last manual update of the associated bank connection using this interface. If no manual update has ever been done so far with this interface, then this field will not be set. | [optional] 
**last_auto_update** | [**\Swagger\Client\Model\UpdateResult**](UpdateResult.md) | Result of the last auto update of the associated bank connection using this interface (ran by finAPI&#39;s automatic batch update process). If no auto update has ever been done so far with this interface, then this field will not be set. | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


