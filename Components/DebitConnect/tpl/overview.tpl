{if $paymentscount > 0}
<div class='box-group'>
<table class='full'>
<tr><td colspan="4"><h5>Zahlungen</h5></td></tr>
{foreach from=$payments item=umsatz}
  	{foreach from=$umsatz.pos item=zahlung}
    <tr>
        <td width="245">
            <div class='tooltip'>{$umsatz.IdKonto}
                <span class='tooltiptext'>{$umsatz.cVzweck}</span>
            </div>
        </td>
        <td width="98">{$umsatz.date}</td>
        <td width="109">{$zahlung.nType}</td>
        <td width="106">{$zahlung.fWert}</td>
    </tr>
    {/foreach}
  {/foreach}
</table>
</div>
{/if}
<div class='box-group'>
<table class='full'>
 <tr> <td colspan="3"><h5>Gesamtübersicht</h5></td></tr>
  <tr><td width="0"></td><td width="331"><div align="right">Auftragsbetrag</div></td><td width="545"><div align="center">{$orderData.betrag}</div></td>
  {if $orderData.Gutschriftbetrag>0}
   </tr><tr><td></td><td><div align="right">Abz&uuml;glich Gutschrift : {$orderData.GutschriftNr}</div></td><td><div align="center">-{$orderData.Gutschriftbetrag}</div></td>
  {/if}
  {if $payedMahngeb != 0}
  </tr><tr><td></td><td><div align="right">Gezahlte Mahngebühren</div></td><td><div align="center">{$payedMahngeb}</div></td>
  {/if}
  </tr><tr><td></td><td><div align="right">Summe Zahlungen</div></td><td><div align="center">{$orderData.Bezahlt}</div></td>
  </tr><tr><td></td><td><div align="right"><strong>Offen</strong></div></td><td><div align="center"><strong>{$orderData.offen}</strong></div></td></tr>
  {if $fOffenVOP && $fGesamtVOP>0}
  <tr><td></td><td><div align="right">Gesamt Forderungshöhe V.O.P </div></td><td><div align="center">{$fGesamtVOP}</div></td></tr>
  <tr><td></td><td><div align="right"><strong>Offen bei V.O.P</strong></div></td><td><div align="center"><strong>{$fOffenVOP}</strong></div></td></tr>
  {/if}
</table>
</div>
<div class='box-group'>
<table class='full'>
<tr><td><h5>Mahnwesen : {$mahnwesenstatus}</h5></td></tr>
</table>
{if $mahnstopCustomerGroup}{$mahnstopCustomerGroup}
{elseif $setmahnstop}

{assign var="customer" value=$mahnstop[1]}{assign var="mahnstoporder" value=$mahnstop[0]}

    <table>
        <tr>
            <td>Typ</td>
            <td>Bis</td>
            <td>Kommentar</td>
            <td>Aktion</td>
        </tr>
        <form method="post">
            <input type="hidden" name="pkOrder" value='{$orderData.id}' />
            <input type="hidden" name="changeMahnstop"  />
            <input type="hidden" name="pkCustomer" value='{$orderData.pkCustomer}' />
        <tr>
            <td>Kunde</td>
            <td><input type="text" class='datepickerzahlung form-control' value='{$customer.resetDate}' name='bis' /> </td>
            <td><input type="text" class="form-control" name="cCommentary" value="{$customer.cCommentary}"> </td>
            <td><input type="submit" class='btn btn-primary' name="{if $customer.nType == 1}removeMahnstopCustomer{else}addMahnstopCustomer{/if}" value='{if $customer.nType == 1}Mahnstop aufheben{else}Mahnstop setzen{/if}' /></td>
        </tr>
        </form>
        <form method="post">
            <input type="hidden" name="pkOrder" value='{$orderData.id}' />
            <input type="hidden" name="changeMahnstop"  />
        <tr>
            <td>Auftrag</td>
            <td><input type="text" class='datepickerzahlung form-control' value='{$mahnstoporder.resetDate}' name='bis' /> </td>
            <td><input type="text" class="form-control" name="cCommentary" value="{$mahnstoporder.cCommentary}"></td>
            <td><input type="submit" class='btn btn-primary' name="{if  $mahnstoporder.id > 0}removeMahnstopOrder{else}addMahnstopOrder{/if}" value='{if  $mahnstoporder.id > 0}Mahnstop aufheben{else}Mahnstop setzen{/if}' /></td>
        </tr>
        </form>
    </table>

{else}
Mahnstop ist nicht mehr möglich, bitte wenden Sie sich an Ihren Sachbearbeiter
{/if}
</td></tr></table>
</div>
