<div class='box-group first' style='height:400px'>
<form action="VOPDebitConnect?switchTo=zahlungsabgleich" autocomplete="off" target="_parent" method="post">
<table width="100%"><tr><td colspan="2"><h5>Umsatzabruf HBCI</h5></td></tr><tr><td>Konto</td><td><select class=' form-control'  name="selectedKonten">

{foreach from=$profiles->bankAccounts item="bank"}
    	{foreach from=$bank key=key item=konto}
 		<option value='{$key}'>{$konto}</option>
  		{/foreach}
{/foreach}
    
</select>
</td></tr>
  <tr>
    <td>Von</td>
    <td><input type="text"  autocomplete="off" name="von" value='{$smarty.post.von}' required="required" class='datepickerzahlung form-control' /></td>
  </tr>
  <tr>
    <td>Bis</td>
    <td><input type="text"  autocomplete="off" name="bis" value='{$smarty.post.bis}' required="required" class='datepickerzahlung  form-control' /></td>
  </tr>
  <tr>
    <td></td>
    <td><input  class='btn btn-success'  type="submit" name="requesthbci" value="Umsätze abrufen" /></td>
  </tr>
  <tr>
    <td colspan="2"><b>Hinweis</b><br>
    Einige Banken buchen zeitverzögert. Um einen effizienteren Zahlungsabgleich durchführen zu können ist es sinnvoll, Ihr HBCI Konto auf eine Direktverbuchung umstellen zu lassen.</td>
  </tr>
</table>
</form></div>
