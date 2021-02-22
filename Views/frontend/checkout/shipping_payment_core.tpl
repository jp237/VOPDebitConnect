{extends file="parent:frontend/checkout/shipping_payment_core.tpl"}
{block name='frontend_checkout_shipping_payment_core_payment_fields'}

    {if $secure_checkout_payment || $abweichend_msg}

        <div style='margin-bottom:10px' class='alert is--error is--rounded'>
            <div class="alert--content">{$alertmsg_payment} {$abweichend_msg}</div>
        </div>
    {/if}
    {$smarty.block.parent}
{/block}

{block name='frontend_checkout_shipping_payment_core_shipping_fields'}
    {if $alertmsg_shipping}
        <div style='margin-bottom:10px' class='alert is--error is--rounded'>
            <div class="alert--content">{$alertmsg_shipping}</div>
        </div>
    {/if}
    {$smarty.block.parent}
{/block}
