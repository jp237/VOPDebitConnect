{extends file="parent:frontend/register/index.tpl"}
{block name='frontend_register_index_form_submit'}
    {include file='frontend/validation/validator.tpl'}
    <input type="hidden" id='eap_invoice_address_postdirekt_type' name="eap_invoice_address_postdirekt_type" value="0">
    <input type="hidden" id='eap_shipping_address_postdirekt_type'  name="eap_shipping_address_postdirekt_type" value="0">

    {$smarty.block.parent}
{/block}