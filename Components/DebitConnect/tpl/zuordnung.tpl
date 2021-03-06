{if $differenz != 0}
    <div class="alert alert-warning">Sie haben {$differenz} nicht zugeordnet. Sie müssen den Umsatz komplett zuordnen um ihn Verbuchen zu können.</div>
{/if}
<form method="post">
  <input type="hidden" name="submitaction" value='setVerbuchen'>
  <div class='box-group'>
  <table width='100%' class='full'><tr><td colspan="4"><h5>Manuelle Zuordnung des Umsatzes vom {$umsatz.datum}</h5></td></tr>
  <tr>
   <td width="21%">Name</td><td colspan="3">{$umsatz.cName}</td>
   </tr>
    <tr>
   <td>Verwendungszweck</td><td colspan="3">{$umsatz.cVzweck}</td>
   </tr>
   <tr> <td width="21%">Betrag</td>
      <td width="79%">{$umsatz.fWert}</td></tr>
   <td>Buchungspositionen</td>
   <td><b>{if $umsatz.nType == 1}-{/if}{$umsatz.zugeordnetvalue.value}</b></td>
   </tr>
   <tr><td>Differenz</td><td><b {if $umsatz.zugeordnetvalue.class} class='{$umsatz.zugeordnetvalue.class}' {/if}>{$differenz}</b></td></tr>
<tr><td colspan="6">{if $umsatz.zugeordnetvalue.action}
    <input id="setVerbuchen" onchange='showLoader();this.form.submit()'type="checkbox" {if $verbuchen}  checked {/if} name='verbuchen' />
    <label for="setVerbuchen"> Umsatz verbuchen</label>{/if}</tr>


</table>
</div>
</form>

<form method="post">
<input type="hidden" name="submitaction">
<div class='box-group'>
<table class='full'><tr ><td colspan="4"><h5>Gewählte Bestellung</h5></td></tr>
<tr>
  <td width="59">Ähnliche</td><td colspan="2">
<select style='width:600px' class='getSort form-control' onchange='showLoader();this.form.submit()' name="changeselected">
{foreach from=$buchungsPos item=pos}
<option class='sort' sortValue='{$pos->matchedvalue}'  value='{$pos->pkOrder}'>{$pos->bestellung.ordernumber} {$pos->bestellung.RechnungsNr}  {$pos->bestellung.firstname} {$pos->bestellung.lastname} ({$pos->matchedvalue})</option>
{/foreach}
</select></td><td colspan="3">{if $selectedBestellung > 0 and $buchungsPos[$selectedBestellung]->zugeordnet == false}
    <input class='btn btn-success' type="submit" value='Hinzufügen' name="add">{/if}
        <a class='btn btn-info fancyboxfullscreen' data-fancybox-href='VOPDebitConnect?switchTo=zasuche&fancy=1&transaction={$smarty.get.transaction}&limit=open&vwz={$umsatz.cVzweck}'>Suche (Offene Bestellungen )</a>
        <a class='btn btn-info fancyboxfullscreen' data-fancybox-href='VOPDebitConnect?switchTo=zasuche&fancy=1&transaction={$smarty.get.transaction}&limit=all&vwz={$umsatz.cVzweck}'>Suche (Alle Bestellungen )</a>
{if $dtaList}
<a class='btn btn-info fancyboxfullscreen' data-fancybox-href='VOPDebitConnect?switchTo=zasuche&fancy=1&transaction={$smarty.get.transaction}&limit=open&dta=1&vwz={$umsatz.cVzweck}'>Suche (Lastschriften)</a>
{/if}
        {$buchungsPos[$selectedBestellung]->bestellung.VOPStatus}
    </td></tr>
    {if $buchungsPos[$selectedBestellung]->bestellung.nVOPStatus > 39}
        <tr> <td colspan="4">
                <div class="alert alert-danger"><b>Achtung </b>
                    {if $buchungsPos[$selectedBestellung]->bestellung.nVOPStatus == 39}
                        Zahlungserinnerung versendet.
                    {elseif $buchungsPos[$selectedBestellung]->bestellung.nVOPStatus == 55}
                        Mahnung in Bearbeitung
                    {elseif $buchungsPos[$selectedBestellung]->bestellung.nVOPStatus == 59}
                        Mahnung abgeschlossen
                    {elseif $buchungsPos[$selectedBestellung]->bestellung.nVOPStatus == 95}
                        Inkasso in Bearbeitung
                    {elseif $buchungsPos[$selectedBestellung]->bestellung.nVOPStatus == 99}
                        Inkasso abgeschlossen
                    {/if}
                </div>
            </td></tr>
    {/if}
  <tr>
   <td>Vorname</td><td width="663"><input  class='form-control {if $buchungsPos[$selectedBestellung]->matchedfirstname} success{/if}' type='text' value='{$buchungsPos[$selectedBestellung]->bestellung.firstname}' name='name3'></td>
   <td width="67">Name</td><td width="827"><input class='form-control  {if $buchungsPos[$selectedBestellung]->matchedlastname} success{/if}'  type='text' name='name' value='{$buchungsPos[$selectedBestellung]->bestellung.lastname}'></td></tr>
   </tr>
   <tr>
   <td>Firma</td><td><input  class='form-control {if $buchungsPos[$selectedBestellung]->matchedfirma} success{/if}' type='text' value='{$buchungsPos[$selectedBestellung]->bestellung.firma}' name='name2' /></td>
       <td>Kunde</td><td><input class='form-control  {if $buchungsPos[$selectedBestellung]->matchedkundennr} success{/if}'  type='text' name='name4' value='{$buchungsPos[$selectedBestellung]->bestellung.KundenNr}'></td>
   </tr>
   <tr>
   <td>Auftrag</td><td><input class='form-control  {if $buchungsPos[$selectedBestellung]->matchedauftragsnr} success{/if}'  type='text' name='name5' value='{$buchungsPos[$selectedBestellung]->bestellung.ordernumber}'></td>
  <td>Rechnung</td><td><input class='form-control  {if $buchungsPos[$selectedBestellung]->matchedrechnungsnr} success{/if}'  type='text' name='name5' value='{$buchungsPos[$selectedBestellung]->bestellung.RechnungsNr}' ></td></tr>
  </tr>
	<tr><td>Betrag</td><td><input class='form-control' type="text" name='_betrag' value='{$buchungsPos[$selectedBestellung]->bestellung.betrag}' /></td>
 <td>Offen</td><td><input class='form-control  {if $buchungsPos[$selectedBestellung]->matchedbetrag} success{/if}'  type='text' name='name5' value='{$buchungsPos[$selectedBestellung]->bestellung.offen}' ></td></tr>
</table>
</form>
</div>
<div class='box-group'>

<table  class='full'>
<tr><td colspan="8"><h5>Zugeordnete Buchungspositionen</h5></td></tr>
{if $umsatz.nType==0}
<tr><td><strong>RechnungsNr</strong></td>
<td><strong>AuftragsNr</strong></td>
<td><strong>Offen</strong></td>
<td ><strong>Zahlbetrag</strong></td>
<td><strong>Mahn/Inkassokosten</strong></td>
<td ><strong>Überzahlung</strong></td>
<td><strong>Skonto</strong></td>
<td><strong>Aktion</strong></td></tr>

{foreach from=$buchungsPos item=position}
{if $position->zugeordnet == true}
<form method="post">
<input type="hidden" name="pkOrder" value='{$position->pkOrder}'>
<input type="hidden" name="submitaction" value='change'>
  <tr>
    <td><input type="text" name="Rechnr"  value="{$position->bestellung.RechnungsNr}"></td>
    <td><input type="text" name="auftragnr" value="{$position->bestellung.ordernumber}"></td>
    <td><input type="text" name="offen"    value="{$position->bestellung.offen}"></td>
    <td><input type="text" name="zahlbetrag"  class='autosubmitnumber form-control' required value="{$position->Zahlbetrag}"></td>
    <td><input type="text" name="mahnkosten"  class='autosubmitnumber form-control' required value="{$position->mahnkosten}"></td>
    <td><input type="text" name="Ueberzahlung"  class='autosubmitnumber form-control' required value="{$position->Ueberzahlung}"></td>
    <td><input type="text" name="skonto"  class='autosubmitnumber form-control' required value="{$position->skonto}"></td>
    <td><input type='submit' class='btn btn-danger' value='Löschen' name='delete'><input class='btn btn-success' type="submit"  value='Ändern' name="change"></td>
  </tr>
</form>
  {/if}
  {/foreach}
  {else if $umsatz.nType == 1}
<tr><td><strong>RechnungsNr</strong></td>
<td><strong>AuftragsNr</strong></td>
<td><strong>Offen / Betrag</strong></td>
<td><strong>Bankrückbelastung</strong></td>
<td><strong>Bankrücklastkosten</strong></td>
<td><strong>Gutschrift</strong></td>
<td><strong>Sonst. Erstattung</strong></td>
<td><strong>Aktion</strong></td></tr>

{foreach from=$buchungsPos item=position}
{if $position->zugeordnet == true}
<form method="post">
<input type="hidden" name="pkOrder" value='{$position->pkOrder}'>
<input type="hidden" name="submitaction" value='change'>
  <tr>
    <td><input type="text" name="Rechnr"  value="{$position->bestellung.RechnungsNr}"></td>
    <td><input type="text" name="auftragnr" value="{$position->bestellung.ordernumber}"></td>
    <td><input type="text" name="offen"    value="{$position->bestellung.offen} / {$position->bestellung.betrag}"></td>
    <td><input type="text" name="bankruecklast"  class='autosubmitnumber' required value="{$position->bankruecklast}"></td>
    <td><input type="text" name="bankruecklastkosten"  class='autosubmitnumber' required value="{$position->bankruecklastkosten}"></td>
    <td><input type="text" name="gutschrift"  class='autosubmitnumber' required value="{$position->gutschrift}"></td>
    <td><input type="text" name="erstattung"  class='autosubmitnumber' required value="{$position->erstattung}"></td>
    <td><input type='submit' class='button' value='Löschen' name='delete'><input class='button' type="submit"  value='Ändern' name="change"></td>
  </tr>
</form>
  {/if}
 {/foreach}
{/if}
</table>
</div>

<p><a class="button" href='VOPDebitConnect?switchTo=zahlungsabgleich'>Zurück</a></p>
<script>
var $wrapper = $('.getSort');

$wrapper.find('.sort').sort(function(a, b) {
    return +b.getAttribute('sortValue') - +a.getAttribute('sortValue');
})
.appendTo($wrapper);
 {if $selectedBestellung}
 $('.getSort').val({$selectedBestellung});
 {/if}
</script>
{if isset($smarty.get.debug)}
<pre>{$buchungsPos[$selectedBestellung]|print_r}</pre>
{/if}

