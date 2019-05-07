{$listview.header}
{$listview.table}

<div class='box-group list'>
<table class='full'><tr><td><h4>Download SEPA-Dateien</h4></td></tr></table>

<table class='auftragtable'>
<thead>
<tr><th align="left">ID</th><th align="left">Transaktion</th><th align="left">Verwendungszweck</th><th align="left">Iban</th><th align="left">Datum</th><th align="left">Anzahl</th><th align="left">Summe</th><th align="left">Archiv</th><th align="left">Download</th></tr>
</thead>
<tbody>
{foreach from=$dtaList item=transaction}
<tr>
   <td style="padding-left:0px">{$transaction.idTransaktion}</td>
    <td style="padding-left:0px">{$transaction.cTransaktion}</td>
    <td style="padding-left:0px">{$transaction.cVzweck}</td>
    <td style="padding-left:0px">{$transaction.idKonto}</td>
    <td style="padding-left:0px">{$transaction.dateCreated|date_format:"%d.%m.%Y"}</td>
    <td style="padding-left:0px">{$transaction.nAnzahl}</td>
    <td style="padding-left:0px">{$transaction.fSumme}</td>
    <td style="padding-left:0px">{$transaction.dDownload|date_format:"%d.%m.%Y"}</td>
    <td style="padding-left:0px"><a class='btn btn-info' href='VOPDebitConnect?downloadDTA={$transaction.id}'>Download</a></td>
</tr>
{/foreach}
</tbody>
</table>
</div>
