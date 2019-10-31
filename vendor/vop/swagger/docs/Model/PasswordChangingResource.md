# PasswordChangingResource

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**user_id** | **string** | User identifier | 
**user_email** | **string** | User&#39;s email, encrypted. Decrypt with your data decryption key. If the user has no email set, then this field will be null. | [optional] 
**password_change_token** | **string** | Encrypted password change token. Decrypt this token with your data decryption key, and pass the decrypted token to the /users/executePasswordChange service in order to set a new password for the user. | 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


