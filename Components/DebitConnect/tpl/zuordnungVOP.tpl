{if $differenz != 0}
    <div class="alert alert-warning">Sie haben {$differenz} nicht zugeordnet. Sie müssen den Umsatz komplett zuordnen um ihn Verbuchen zu können.</div>
{/if}
{if $SUM_MISSMATCH}
    <div class="alert alert-danger">Diese Steuerdatei passt nicht zu dem Umsatz {$SUM_MISSMATCH} EUR</div>
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
    <input  onchange='showLoader();this.form.submit()'type="checkbox" {if $verbuchen}  checked {/if} name='verbuchen' />
    Umsatz verbuchen{/if}</tr>

</table>
</div>
</form>

<form enctype="multipart/form-data" method="post">
<input type="hidden" name="submitSteuerDatei">
<div class='box-group'>
<table class='full'>
<tr><td colspan="8"><h5>Upload Steuerdatei</h5></td></tr>
<tr><td>Hier können Sie Ihre Steuerdatei, die Sie von V.O.P erhalten haben Hochladen</td><td><input type="file" name='steuerdatei' required  /></td><td><input type="submit" name="upload" class="btn btn-success" value="Steuerdatei Hochladen" /></td></tr>

</table>
</div>
</form>

<div class='box-group'>

<table  class='full'>
<tr><td colspan="8"><h5>Zugeordnete Buchungspositionen</h5></td></tr>
<tr><td><strong>RechnungsNr</strong></td>
<td><strong>AuftragsNr</strong></td>
<td><strong>Offen</strong></td>
<td ><strong>Zahlbetrag</strong></td>
<td><strong>Mahn/Inkassokosten</strong></td>
<td ><strong>Steuererstattung</strong></td>
{foreach from=$buchungsPos item=position}
{if $position->zugeordnet == true}
  <tr>
    <td><input type="text" name="Rechnr"  readonly="readonly" class="form-control" value="{$position->bestellung.RechnungsNr}"></td>
    <td><input type="text" name="auftragnr" readonly="readonly" class="form-control"  value="{$position->bestellung.ordernumber}"></td>
    <td><input type="text" name="offen"  readonly="readonly" class="form-control"   value="{$position->bestellung.offen}"></td>
    <td><input type="text" name="zahlbetrag"  readonly="readonly" class='autosubmitnumber form-control' required value="{$position->Zahlbetrag}"></td>
    <td><input type="text" name="mahnkosten" readonly="readonly" class='autosubmitnumber  form-control' required value="{$position->mahnkosten}"></td>
    <td><input type="text" name="steuererstattung" readonly="readonly" class='autosubmitnumber form-control' required value="{$position->steuererstattung}"></td>
  </tr>
  {/if}
  {/foreach}

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
