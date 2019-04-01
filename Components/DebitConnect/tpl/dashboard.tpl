{literal}
<style type="text/css">
.errormsg{
display:none;
}</style>
{/literal}
{if isset($cronjob_log)}

    <div class="box-group list">
    <table cellpadding="10" cellspacing="10">
        <tr><td colspan="2"><h4 style="color:#9e1616">Cronjob Protokoll {$smarty.get.currentLog} {if $CRONJOB_ERROR_MSG}  !!! {$CRONJOB_ERROR_MSG} !!! {/if}</h4></td> </tr>
        <tr>
            <td valign="top">
                {foreach from=$cronjob_log key=key item=logEntry}
                    <a href="VOPDebitConnect?currentLog={$key}">{$key}</a><br>
                {/foreach}
            </td>
            <td valign="top">

                {if isset($currentLog)}
                <table cellspacing="10" cellpadding="10" >
                    <tr ><td>Aktion</td><td>Beschreibung</td><td>Uhrzeit</td><td>Fehler</td><td>BestellNr</td><td></td></tr>
                   {foreach from=$currentLog|array_reverse item=entry}
                       <tr {if $entry.bIserror>0}style="color:red" {else}{/if}><td>{$entry.cStep}</td><td>{$entry.cResult}</td><td>{$entry.dAction|date_format:"%H:%M:%S"}</td><td>{if $entry.bIserror>0}Ja{else}Nein{/if}</td><td>{$entry.ordernumber}</td><td><textarea style="display: none;">{$entry.jResult}</textarea></td></tr>
                       {if $entry.bIserror>0}<tr><td colspan="5">{$entry.jResult}</td> </tr>{/if}
                   {/foreach}
                </table>
                {/if}
            </td>
        </tr>
    </table>
    </div>
    {elseif isset($CRONJOB_ERROR_MSG)}
    <h4 style="color:#9e1616">Cronjob Protokoll {$smarty.get.currentLog} {if $CRONJOB_ERROR_MSG}  !!! {$CRONJOB_ERROR_MSG} !!! {/if}</h4>
{/if}
<div style="padding-top:200px" align="center">
<img style='border:1px solid #c6c1c1' src='/engine/Shopware/Plugins/Community/Backend/VOPDebitConnect/Views/backend/_resources/img/splashscreen.png' />
</div>