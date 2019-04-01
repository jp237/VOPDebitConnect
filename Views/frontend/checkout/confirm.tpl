{extends file="parent:frontend/checkout/confirm.tpl"}

{block name='frontend_checkout_confirm_product_table'}
{if $schufa_idcheck_required || $identcheck_failed || $dpag_idcheck_required }
<br />
<link rel="stylesheet" type="text/css" href="{link file='frontend/_resources/css/gateway.css'}"/>
    {if $IDENT_FAILED || $POSTID_REQUEST}
    <div class='eap_container'><div class='eap_note'><strong>{if $jtl_eap_identcheck_required} {$jtl_eap_identcheck_required} {else}{$identcheck_failed_headline}{/if}</strong></div>
    {if $QBIT_FAILED}
    <div class='qbit_failed'>{$identcheck_failed_msg}
    {if $identcheck_qbit_output}<div class='qbit_output'><em>{$identcheck_qbit_dataerror_msg} :</em><strong> {$identcheck_qbit_dataerror}</strong></div>
    {/if}</div>
    {/if}
    {if $POSTID_REQUEST}
    </form>
    <form method="post" {if $postid_api_url} action='{$postid_api_url}' {/if}>
    <input type="hidden" name="cmd" id='eap_cmd' value='requestIdentCheck' />
    <div class='postIdent'>{if $postident_notice_highcart} <br /><strong>{$postident_notice_highcart}<br /></strong>{/if}<br />{$postident_notice_identcheck}{$postident_notice_agecheck}
    {if $postident_ausweisen}<div class>{$postident_ausweisen}<br /><button border="0" type="button" class="eap_continue_postident_ausweisen" onclick="document.getElementById('eap_cmd').value='idcard2Request';this.form.submit();"></button></div>{/if}
    {if $postident_register}<div class>{$postident_register}<br /><button border="0" type="button" class="eap_continue_postident_register" onclick="window.open('https://postident.deutschepost.de/nutzerportal/register')"></button></div>{/if}
    {if $postident_identify}<div class>{$postident_identify}<br /><button border="0" type="button" class="eap_continue_postident_identifizieren" onclick="document.getElementById('eap_cmd').value='idcard2Request';this.form.submit();"></button></div>{/if}
    </div>
    </form>
    {/if}
    </div>

    {else}
    </form>
    <form method="post" >
    <input type="hidden" name="cmd" value='requestIdentCheck' />
    <div class="eap_container">{$identcheck_notice}
    <div class='eap_geb'>{$geb_text}  <input type="text"  name="eap_geburtstag" required value="{$eap_bonigateway_birthday}" id="birthday" placeholder="z.b. 23.06.1991" class="birthday eap_geburtstag"></div>
 
    <div class='buttons'>{if !$POSTID_REQUEST}<button class="btn is--primary is--large " type="submit">{$btn_submit}</button>{/if}
    </div>
    </div>
    </form>
    {/if}
{else}
{$smarty.block.parent}
{/if}
{/block}


{block name='frontend_checkout_confirm_confirm_table_actions'}
{if !$schufa_idcheck_required && !$identcheck_failed && !$dpag_idcheck_required }
{$smarty.block.parent}
{/if}
{/block}


{block name='frontend_checkout_confirm_tos_panel'}
{if !$schufa_idcheck_required && !$identcheck_failed && !$dpag_idcheck_required }
{$smarty.block.parent}
{/if}
{/block}

