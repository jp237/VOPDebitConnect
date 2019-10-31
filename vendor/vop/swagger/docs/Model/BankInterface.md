# BankInterface

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**interface** | **string** | Bank interface. Possible values:&lt;br&gt;&lt;br&gt;&amp;bull; &lt;code&gt;FINTS_SERVER&lt;/code&gt; - means that finAPI will download data via the bank&#39;s FinTS server.&lt;br&gt;&amp;bull; &lt;code&gt;WEB_SCRAPER&lt;/code&gt; - means that finAPI will parse data from the bank&#39;s online banking website.&lt;br&gt;&amp;bull; &lt;code&gt;XS2A&lt;/code&gt; - means that finAPI will download data via the bank&#39;s XS2A interface.&lt;br&gt; | 
**tpp_authentication_group** | [**\Swagger\Client\Model\TppAuthenticationGroup**](TppAuthenticationGroup.md) | TPP Authentication Group which the bank interface is connected to | [optional] 
**login_credentials** | [**\Swagger\Client\Model\BankInterfaceLoginField[]**](BankInterfaceLoginField.md) | Login credentials fields which should be shown to the user. | 
**properties** | **string[]** | Set of interface properties/specifics. Possible values:&lt;br&gt;&lt;br&gt;&amp;bull; &lt;code&gt;REDIRECT_APPROACH&lt;/code&gt; - means that the interface uses a redirect approach when authorizing the user. It requires you to pass the &#39;redirectUrl&#39; field in all services which define the field.&lt;br/&gt;&lt;br/&gt;&amp;bull; &lt;code&gt;DECOUPLED_APPROACH&lt;/code&gt; - means that the interface uses a decoupled approach when authorizing the user.&lt;br/&gt;&lt;br/&gt;&amp;bull; &lt;code&gt;DETAILED_CONSENT&lt;/code&gt; - means that the interface requires a list of account references when authorizing the user. It requires you to pass the &#39;accountReferences&#39; field in all services which define the field.&lt;br/&gt;&lt;br/&gt;Note that this set can be empty, if the interface does not have any specific properties. | [optional] 
**login_hint** | **string** | Login hint. Contains a German message for the user that explains what kind of credentials are expected.&lt;br/&gt;&lt;br/&gt;Please note that it is essential to always show the login hint to the user if there is one, as the credentials that finAPI requires for the bank might be different to the credentials that the user knows from his online banking.&lt;br/&gt;&lt;br/&gt;Also note that the contents of this field should always be interpreted as HTML, as the text might contain HTML tags for highlighted words, paragraphs, etc. | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


