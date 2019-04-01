{extends file="parent:frontend/checkout/cart.tpl"}
{block name="frontend_checkout_cart_panel"}
{if $agecheck_warenkorb_msg}
<div style='margin-bottom:10px' class='alert is--error is--rounded'>
    <div class="alert--content"> {$agecheck_warenkorb_msg}</div>
</div>
{/if}
    {$smarty.block.parent}
{/block}