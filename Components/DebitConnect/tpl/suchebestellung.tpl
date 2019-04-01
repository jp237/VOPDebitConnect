{if $smarty.get.dta == 1}
<form method="post">
<table width='100%' class='auftragtable'>
<thead>
<tr><td width="5%"></td><td width="26%">Datum</td><td width="31%">Anzahl Zahlungen</td><td width="38%">IdTransaktion</td></tr>
</thead>
{foreach from=$dtaList item=_dta}

<tr><td><input required type='radio' name='selectedDTA' value='{$_dta.idTransaktion}' /></td><td>{$_dta.dateCreated}</td><td>{$_dta.nAnzahl}</td><td>{$_dta.idTransaktion}</td></tr>
{/foreach}
</table>
<input  class='button' type="submit" name="submitDTA" value='Hinzufügen' />
</form>
{else}
<form id='searchform' method="post">
<table width="100%">
<input type="hidden" name="suchebestellung">
<tr><td><input type="text" class='full' id='appendsearchfield' required name="searchfield" value='{$smarty.post.searchfield}' placeholder='Bitte Suchparameter eingeben'></td><td><input class='button'  type="submit" name="Suchen"></td></tr>
<tr>
  <td>Verwendungszweck : 
  {assign var="keywords" value=" "|explode:{$smarty.get.vwz}}
    {foreach from=$keywords item=keyword}
        {if ($keyword|trim)|strlen >2}
             <a onclick="$('#appendsearchfield').val('{$keyword}');$('#searchform').submit();" class='button' href='#'>{$keyword|trim}</a>
        {/if}
    {/foreach}
 </td>
  <td>&nbsp;</td>
</tr>
</table>
</form>
{/if}
<b>Ihre Suchergebnisse:</b>

<table class='auftragtable'>
<thead>
<tr><td>Aktion</td><td>Vorname</td><td>Nachname</td><td>Betrag</td><td>Offen</td><td>Rechnungsnummer</td><td>Auftragsnummer</td><td>Kundennummer</td><td>BestellDatum</td></tr></thead>
<form target='_parent' action='VOPDebitConnect?switchTo=zakontrolle&transaction={$smarty.get.transaction}' method="post">
{if $smarty.post.selectedDTA}<input type='hidden' name='selectedDTA' value='{$smarty.post.selectedDTA}' />{/if}
{foreach from=$searchres item=bestellung}
<tr><td><input type="checkbox" {if $smarty.post.selectedDTA} checked="checked" {/if} value="{$bestellung.id}" name="addbestellung[]" /></td><td>{$bestellung.firstname}</td><td>{$bestellung.lastname}</td><td>{$bestellung.betrag}<td>{$bestellung.offen}</td><td>{$bestellung.RechnungsNr}</td><td>{$bestellung.ordernumber}</td><td>{$bestellung.KundenNr}</td><td>{$bestellung.ordertime}</td></tr>
{/foreach}
</table><br />
<input  class='button' type='submit' name='submitbtn' value='Hinzufügen'>
</form>
