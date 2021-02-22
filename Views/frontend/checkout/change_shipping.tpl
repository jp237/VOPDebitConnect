{extends file="parent:frontend/checkout/change_shipping.tpl"}
{block name='frontend_checkout_dispatch_shipping_input_radio'}
                        <div class="method--input">
                        {if in_array($dispatch.id,$disabled_shipping_methods)}
                        	**
                        {else}
                            <input type="radio" id="confirm_dispatch{$dispatch.id}" class="radio auto_submit" value="{$dispatch.id}" name="sDispatch"{if $dispatch.id eq $sDispatch.id} checked="checked"{/if} />
                        {/if}
                        </div>
{/block}


{block name='frontend_checkout_dispatch_shipping_input_label'}
                            <div class="method--label is--first" {if in_array($dispatch.id,$disabled_shipping_methods)}style='opacity:.6'{/if}>
                                <label class="method--name is--strong" for="confirm_dispatch{$dispatch.id}">{$dispatch.name}</label>
                            </div>
                        {/block}
 