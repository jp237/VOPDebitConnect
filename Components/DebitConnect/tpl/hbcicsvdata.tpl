<form target='_parent' action='VOPDebitConnect?switchTo=zahlungsabgleich' method="post">
<select required name="CSVFile"><option value=''>Bitte auswählen</option>
{foreach from=$hbci_csv_list item=csv}
<option value='{$csv}'>{$csv}</option>
{/foreach}
</select>
<input type="submit" name="GetCSV" value='CSV Importieren' />
</form>
