<form method="post">

<input type="hidden" name="contextcmd" />
<input type="hidden" name="HBCIAction">
<div class='box-group'>
<table width="100%">
<tr><td><h4>{$Umsatzcounter} Umsätze in der Datenbank vorhanden</h4></td><td align="right" colspan="6">
        <div style='float:right;'>{if $hbci_csv_list}
                <a class='fancybox button' href='#' data-fancybox-href='VOPDebitConnect?switchTo=CSVData&fancy=1'>CSV</a>{/if}
            {if $zaActive}
                <a class='btn btn-info'  href='VOPDebitConnect?switchTo=zahlungsabgleich&updateFinApi'>FINAPI-Update</a>
                <a class='fancybox btn btn-info' href='#' data-fancybox-href='VOPDebitConnect?switchTo=hbcirequest&fancy=1'>FINAPI-Abruf</a>
            {/if}
            <a class='btn btn-info fancyboxreload' data-fancybox-href="VOPDebitConnect?switchTo=HBCIMatching&fancy=1" >Abgleichen</a>
            <a class='btn btn-success fancyboxreload' data-fancybox-href="VOPDebitConnect?switchTo=HBCIPayments&fancy=1" >Zahlungen Buchen</a>
            {if $matches|count>0}
            <input  class='btn btn-danger' type="submit" value='Zurücksetzen' name="resetMatches">
            {/if}
            <input  class='btn btn-danger' type="submit" value='Nicht Verbuchen' name="HBCIDelete">
        </div>
</td></tr>
<tr><td colspan="7"></td></tr>
</table>
<table width='100%' class='auftragtable'>
<thead>
<tr>
	<td width="1%"><input class="checkall" name="selectAll" type="checkbox" /></td>
    <td width="7%">Datum</td><td width="7%">Umsatz</td><td width='14%'>Zugeordnet</td><td width="14%">Name</td>
    <td width="51%">Verwendungszweck</td><td width="6%">Aktion</td>
</tr>
</thead>
<tbody>
{foreach from=$umsatzData item=umsatz}
<tr id='{$umsatz.kUmsatz}' class='{if $matches[$umsatz.kUmsatz].verbuchen} successfont {elseif $matches[$umsatz.kUmsatz].sum > $umsatz.fWert && $umsatz.nType == 0} errorfont {else if ($matches[$umsatz.kUmsatz].sum >0 && $matches[$umsatz.kUmsatz].sum < $umsatz.fWert) || ($umsatz.nType == 1 && $matches[$umsatz.kUmsatz].sum >0)} orangefont{/if}'>
    <td><input type="checkbox" name="selected[]" value="{$umsatz.kUmsatz}" /></td>
    <td>{$umsatz.datum}</td>
    <td>{$umsatz.fWert}</td>
    <td>{$matches[$umsatz.kUmsatz].sum}</td>
    <td>{$umsatz.cName}</td>
    <td>{$umsatz.cVzweck}</td>
    <td><a name='{$umsatz.kUmsatz}'></a>
        <a class='btn btn-info btn-sm' onclick='showLoader();' href='VOPDebitConnect?switchTo=zakontrolle&transaction={$umsatz.kUmsatz}'>Kontrolle</a></td>
</tr>
  {/foreach}
</tbody>
</table>
</div>
</form>
