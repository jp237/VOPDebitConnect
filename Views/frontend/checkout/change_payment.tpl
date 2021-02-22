{extends file="parent:frontend/checkout/change_payment.tpl"}
{* Radio Button *}
{block name='frontend_checkout_payment_fieldset_input_radio'}
        <div class="method--input paymentContainer{$payment_mean.id}">
            {if $secure_checkout_payment && in_array($payment_mean.id, $secure_payments_bonigateway)}
                **
            {else}
              <input type="radio" name="payment" class="payment_mean{$payment_mean.id} auto_submit" {if in_array($payment_mean.id, $secure_payments_bonigateway)}attr-checkpayment="1"{/if} attr-paymentdescription="{$payment_mean.description}" attr-paymentname="{$payment_mean.name}" value="{$payment_mean.id}" {if !in_array($payment_mean.name, $allowedPayments)}id="payment_mean{$payment_mean.id}"{/if}{if $payment_mean.id eq $sFormData.payment or (!$sFormData && !$smarty.foreach.register_payment_mean.index)} checked="checked"{/if} />
            {/if}
        </div>
{/block}

{* Method Name *}
{block name='frontend_checkout_payment_fieldset_input_label'}
        <div class="method--label is--first paymentContainer{$payment_mean.id}" {if  $secure_checkout_payment &&  in_array($payment_mean.id, $secure_payments_bonigateway)}style="opacity:.6"{/if}>
            <label class="method--name is--strong" for="payment_mean{$payment_mean.id}">{$payment_mean.description} </label>
        </div>
{/block}

{block name='frontend_checkout_payment_fieldset_template'}
    {$smarty.block.parent}
{if $sFormData.payment === $payment_mean.id && in_array($payment_mean.id, $secure_payments_bonigateway) && !$secure_checkout_payment}
{if $eap_request_type == 'B2C' && $b2c_birthday == '1' }
<div class='method--description'>
 <h5>{$headline_boni}</h5>{$jtl_eap_eingabe_notice}<br />
 <input type="input" name="eap_geburtstag"  id='eap_datepicker'  placeholder="dd.mm.yyyy" required  value='{$eap_bonigateway_birthday}'/>
</div>
{/if}
{/if} 
{/block}
