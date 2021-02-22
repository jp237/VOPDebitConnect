{literal}
<style type="text/css">
.valign-top{
vertical-align:top;
}
</style>
{/literal}
{if $setting_art == "reg"}
<form method="post" name="settings">
<input type='hidden' name='userregistration'>
<div class='box-group'>
<table>
  <td colspan="2"><h5>Registrierung</h5></td></tr>
  <tr><td width="9%">Firma</td><td width="91%"><input type='text' class='form-control'  required value='{if !$firma}{$CompanyData.company}{else if $firma}{$firma}{/if}' name='reg[firma]'></td></tr>
    <tr><td>Unternehmer</td><td><input type='text' class='form-control'  value='{$unternehmer}' name='reg[unternehmer]'></td></tr>
  <tr><td>Strasse</td><td colspan="3"><input type='text' class='form-control'  required  value='{$strasse}' name='reg[strasse]'></td></tr>
  <tr><td>PLZ</td><td><input type='text' class='form-control'  required  value='{$plz}' name='reg[plz]'></td></tr>
    <tr><td>Ort</td><td><input type='text' class='form-control'   value='{$ort}'  name='reg[ort]'></td></tr>
  <tr><td>Land</td><td colspan="3"><input type='text' class='form-control'   value='{$land}'  required name='reg[land]'></td></tr>
  <tr><td>Tel</td><td><input type='text' class='form-control'  required  value='{$tel}' name='reg[tel]'></td></tr>
    <tr><td>Fax</td><td><input type='text' class='form-control'   value='{$fax}' name='reg[fax]'></td></tr>
  <tr><td>E-Mail</td><td colspan="3"><input type='text' class='form-control'   value='{if !$email}{$CompanyData.mail}{else if $email}{$email}{/if}' required name='reg[email]'></td></tr>
  <tr><td>USTID</td><td><input type='text' class='form-control'   value='{$ustid}' required name='reg[ustid]'></td></tr>
    <tr><td>Vorsteuerabzugsberechtigt</td><td><select class='form-control' name='reg[vorsteuer]' required>
      <option value=''>Bitte auswählen</option>
      <option {if $vorsteuer == 'True'} selected {/if} value='True'>Vorsteuerabzugsberechtigt</option>
      <option {if $vorsteuer == 'False'} selected {/if} value='False'>Nicht Vorsteuerabzugsberechtigt</option>
  </select></td>
    </tr>
  <tr>
    <td>EAPID</td><td>{$vopUser}</td>
   </tr><tr> <td>Aktivierungskey</td><td><input type='text' class='form-control'  {if $activated == 1}   required    {/if} {if $not_registered}   disabled    {/if} value = '{if $activated == 2}{$vopToken}{/if}' name='reg[key]'></td>
    </tr>
     {if $not_registered}
     <tr><td colspan="4"><input type="checkbox" name='agb' required  /><a target='_new' href='https://www.inkasso-vop.de/agbs/Rahmenvereinbarung.pdf'>Bestätigung der Rahmenvereinbarung</a></td></tr>
   <tr><td colspan="4"><input type="submit" class="btn btn-success" value='Registrieren' name="register"></td></tr>
    {/if}
    {if $activated == 1}
   <tr><td colspan="4"><input type="submit" class="btn btn-success" value='Aktivieren' name="activate"></td></tr>
   {/if}
</table>
</div>
</form>
{else if $setting_art == "frist"}
{literal}
<script>$(document).ready(function(){showEditor();});</script>
{/literal}
<form method="post">
<input type="hidden" name="updatesettings">
<div class='box-group'>
    <table width='100%'>
    <tr>
      <td colspan="2"><h5>Systemeinstellungen</h5></td>
      <td></td></tr>
     <tr><td width="200px">Shopware API URL</td><td width="1481"><input  type='text' class='form-control'   value='{$shopwareapiurl}' name='settings[shopwareapiurl]'></td></tr>
     <tr><td>Shopware API User</td><td width="1481"><input  type='text' class='form-control'  value='{$shopwareapiuser}' name='settings[shopwareapiuser]'></td></tr>
     <tr><td>Shopware API Key</td><td><input  type='password' class='form-control'  value='{$shopwareapikey}' name='settings[shopwareapikey]'></td></tr>
    <tr>         <td>API Verwenden</td><td>
            <select  class='form-control' required name='settings[shopwareapibenutzen]'>
      <option  {if $shopwareapibenutzen == 0} selected {/if}value='0'>Keinen Status über API Setzen</option>
      <option {if $shopwareapibenutzen == 1} selected {/if} value='1'>Status über API setzen</option>
      </select></td>
      </tr>
     {if $shopwareapibenutzen == 1} <tr><td>API-Status</td><td>{$apiteststatus}</td></tr>{/if}
       <tr><td>Gutschriften behandeln als</td><td>
  <select required  class="form-control" name="settings[gutschriften]" />
  <option {if $gutschriften==0} selected {/if} value='0'>Neue Rechnung</option>
  <option  {if $gutschriften==1} selected {/if} value='1'>Abzug der Positionen</option>
  </select>
  </td>
  </tr>
  <tr><td>B2B-Shop</td><td><input type="checkbox" {if $mahnwesenvorkasse} checked  {/if} name="settings[mahnwesenvorkasse]" />
  B2B-Shop ( Volles Mahnwesen bei Vorkasse )</td></tr>
      </table>

  </div>
<div class='box-group'>
  <table>
  <tr><td colspan="2"><h5>Zahlungsarten und Zahlungsstatus</h5></td></tr>
  <tr>
  <td width="19%" colspan="2" id='headline' valign="top"><strong>Zahlungsarten Vorkasse</strong> </td>
  </tr>
  <tr><td colspan="4">{foreach from=$vorkassepayments item=vorkasse}
  <div style='width:400px;float:left;padding-right:50px'><input type="checkbox" value='{$vorkasse.id}' name='vorkasse[]' {if $vorkasse.dc_config == 1} checked {/if}>
  {$vorkasse.description} </div>  
  {/foreach}</td></tr>
      <tr>
  <td width="19%" id='headline' valign="top"><strong>Zahlungsarten  Rechnungskauf</strong></td>
  </tr>
  <tr><td colspan="4">{foreach from=$payments item=payment}
  <div style='width:400px;float:left;padding-right:50px'><input type="checkbox" value='{$payment.id}' name='payment[]' {if $payment.dc_config == 1} checked {/if}>
  {$payment.description|truncate:40} </div>  
  {/foreach}</td></tr>
  
  <tr>
  <td width="19%" id='headline' valign="top"><strong>Zahlungsarten  Lastschrift</strong></td>
  </tr>
  <tr><td colspan="4">{foreach from=$sepapayments item=payment}
  <div style='width:400px;float:left;padding-right:50px'><input type="checkbox" value='{$payment.id}' name='sepa[]' {if $payment.dc_config == 1} checked {/if}>
  {$payment.description|truncate:40} </div>  
  {/foreach}</td></tr>
  
     <tr><td id='headline' colspan="4"><strong>Filter : Zahlungsstatus</strong></td></tr>
     <tr><td colspan="4">
  {foreach from=$states item=state}
  <div style='width:400px;float:left;padding-right:50px'> <input type="checkbox" value='{$state.id}' name='states[]' {if $state.dc_config == 1} checked {/if}>
  {$state.description|truncate:40}   </div>
  {/foreach}
  </td></tr>
  </table></div>
  
  <div class='box-group'>
  <table class='full' >
  <tr><td colspan="2"><h5>Mahnwesen</h5></td></tr>

   <tr><td id="headline" colspan="2"><strong>Versandstatus Komplett versendet</strong></td> </tr>

      <tr><td colspan="4">{foreach from=$orderstates item=shipping}
                  <div style='width:400px;float:left;padding-right:50px'><input type="checkbox" value='{$shipping.id}' name='shipping[states][]' {if $shipping.dc_config == 1} checked {/if}>
                      {$shipping.description|truncate:40} </div>
              {/foreach}</td></tr>

      <tr><td colspan="2"><strong>Berechnungsdatum Mahnwesen ( Rechnungskauf ) </strong></td> </tr>
      <tr><td>Versanddatum Benutzen</td>
          <td>
              <select name="shipping[overrideInvoice]" class="form-control">
                  <option {if $settingsShipping->overrideInvoice == 0} selected {/if} value="0">Nein</option>
                  <option {if $settingsShipping->overrideInvoice == 1} selected {/if}  value="1">Ja</option>
              </select>
          </td></tr>
   <tr>
    <td  id='headline' ><strong>Zahlungserinnerung</strong></td>
    </tr>
    <tr><td>Status nach Versand</td><td width="81%">
    <select class='form-control' required name="settings[statusZE]">
    <option value=''>Bitte Auswählen</option>
    {foreach from=$states item=state}
	<option {if $state.id == $statusZE} selected {/if} value='{$state.id}'>{$state.description}</option>
  {/foreach}
    </select></td></tr>
    <tr>
    	<td>Frist nach Rechnungsstellung</td><td><select class='form-control' required name="settings[fristZE]">
          <option value=''>Bitte auswählen</option>
          {for $counter=1 to 31}
    	<option {if $fristZE == $counter} selected {/if}value='{$counter}'>{$counter} Tag/e</option>
		{/for}
        </select>
        </td>
    </tr>
        <tr>
    	<td>Art</td><td><select  onchange="showEditor();" class='zeArt form-control' required name="settings[zeArt]">
          <option value=''>Bitte auswählen</option>
    	<option {if $zeArt == 1} selected {/if} value='1'>Eigener Mailversand</option>
        </select>
        </td>
    </tr>

   <tr class='editorhide'><td>Betreff</td><td><input class='form-control'  type="text" value='{$smtpbetreff}' name="settings[smtpbetreff]" /></td></tr>
    <tr class='editorhide'><td>Absender</td><td><input class='form-control' type="text" value='{$smtpabsender}' name="settings[smtpabsender]" /></td></tr>
    <tr class='editorhide'><td>Kopie</td><td><input class='form-control'  type="text" value='{$smtpkopie}'  name="settings[smtpkopie]" /></td></tr>
   	<tr class='editorhide'>
    <td colspan="1"></td><td><a href='#' class='fancyboxfullscreen btn btn-info' data-fancybox-href='VOPDebitConnect?switchTo=template&noncss=1&fancy=1&art=zetpl'>Template anpassen</a></td>
    </tr>  
        <tr>
    	<td id='headline' ><strong>Mahnservice</strong></td><td></td></tr>
 <tr><td>Gebühr</td><td><input type='text'   class='maskednumber form-control' disabled name='settings[mahngeb]' value='7.50'/>
            <tr><td>Status nach Versand</td><td>
                <select class='form-control' required name="settings[statusMA]">
                <option value=''>Bitte Auswählen</option>
                {foreach from=$states item=state}
                <option {if $state.id == $statusMA} selected {/if} value='{$state.id}'>{$state.description}</option>
              	{/foreach}
                </select>
    		</td></tr>
    	<tr><td>Frist nach Zahlungserinnerung</td><td><select class='form-control' required name="settings[fristMA]">
        <option value=''>Bitte auswählen</option>
        {for $counter=1 to 31}
    	<option {if $fristMA == $counter} selected {/if}value='{$counter}'>{$counter} Tag/e</option>
		{/for}
        </select>
        </td>
    </tr>  
    <tr><td id='headline'  colspan="2"><strong>Inkasso</strong></td></tr>
    <tr><td>Status nach Versand</td><td>
                <select class='form-control' required name="settings[statusIN]">
                <option value=''>Bitte Auswählen</option>
                {foreach from=$states item=state}
                <option {if $state.id == $statusIN} selected {/if} value='{$state.id}'>{$state.description}</option>
              	{/foreach}
                </select>
    		</td>
 </tr>
 <tr><td id='headline' colspan="4"><strong>Mahnstop : Kundengruppen</strong></td></tr>
     <tr><td colspan="4">
  {foreach from=$mahnstopCustomerGroup item=customergroup}
  <div style='width:400px;float:left;padding-right:50px'> <input type="checkbox" value='{$customergroup.id}' name='mahnstop[]' {if $customergroup.dc_config == 1} checked {/if}>
  {$customergroup.description|truncate:40}   </div>
  {/foreach}
  </td></tr>
 <tr><td id='headline' colspan="2"><strong>Blackliste BoniGateway B2C</strong></td></tr>
 <tr><td>Setzen ab:</td><td>
 <select class="form-control" name="settings[blackliste]">
 	<option {if $blackliste == '0'} selected {/if} value="0">Nie</option>
    <option {if $blackliste == '1'} selected  {/if} value="1">Mahnung</option>
    <option  {if $blackliste == '2'} selected  {/if}  value="2">Inkasso</option>
 </select>
 </td></tr>
</table>
</div>
 
<input type="submit" class="btn btn-success" value="Ändern">
</form>
{else if $setting_art == "hbci"}
<form method="post">
<input type="hidden" name="updatehbci">
<div class='box-group'>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td colspan="2"><h5>HBCI-Einstellungen Zahlungsabgleich</h5></td></tr>
         <tr>
         <td width="30%"><strong>{$zugangsprofile} Zugangsprofile</strong></td>
         <td width="70%"><a class='btn btn-info' href='VOPDebitConnect?switchTo=hbciProfiles'>Profile verwalten</a></td></tr>
     <tr>
       <td><strong>Status</strong></td>
       <td></td></tr>
         <tr><td>Bezahlstatus Komplett Bezahlt</td><td>
    <select class='form-control' required name="hbci[statusbezahlt]">
    <option value=''>Bitte Auswählen</option>
    <option {if 'null' == $hbci.statusbezahlt} selected {/if} value='null'>Nicht setzen</option>
    {foreach from=$states item=state}
	<option {if $state.id == $hbci.statusbezahlt} selected {/if} value='{$state.id}'>{$state.description}</option>
  {/foreach}
    </select></td></tr>
         <tr><td>Bezahlstatus Teilzahlung</td><td>
    <select class='form-control' required name="hbci[teilzahlung]">
    <option value=''>Bitte Auswählen</option>
     <option {if 'null' == $hbci.teilzahlung} selected {/if} value='null'>Nicht setzen</option>
    {foreach from=$states item=state}
	<option {if $state.id == $hbci.teilzahlung} selected {/if} value='{$state.id}'>{$state.description}</option>
  {/foreach}
    </select></td></tr>
         <tr><td>Bezahlstatus Bankrücklast</td><td>
    <select class='form-control' required name="hbci[bankruecklast]">
    <option value=''>Bitte Auswählen</option>
     <option {if 'null' == $hbci.bankruecklast} selected {/if} value='null'>Nicht setzen</option>
    {foreach from=$states item=state}
	<option {if $state.id == $hbci.bankruecklast} selected {/if} value='{$state.id}'>{$state.description}</option>
  {/foreach}
    </select></td></tr>
     <tr><td>Bestellstatus Vorkasse - Komplett Bezahlt</td><td>
    <select class='form-control' required name="hbci[orderstatus]">
    <option value=''>Bitte Auswählen</option>
     <option {if 'null' == $hbci.orderstatus} selected {/if} value='null'>Nicht setzen</option>
    {foreach from=$orderstates item=state}
	<option {if $state.id == $hbci.orderstatus} selected {/if} value='{$state.id}'>{$state.description}</option>
  {/foreach}
    </select></td></tr>
         <tr><td>Zahlungsdatum setzen - Komplett Bezahlt</td><td>
    <select class='form-control' required name="hbci[setpaymentdate]">
    <option value=''>Bitte Auswählen</option>
     <option {if '0' == $hbci.setpaymentdate} selected {/if} value='0'>Nicht setzen</option>
   	<option {if '1' == $hbci.setpaymentdate} selected {/if} value='1'>Datum  setzen</option>
    </select></td></tr>
     <tr>
       <td><strong>Zahlungsbestätigung</strong></td>
       <td></td></tr>
    <tr>
    <td>Zahlungsbestätigung</td>
    <td><select class='form-control' name='hbci[bestaetigung]'>
    	<option  {if $hbci.bestaetigung == 0 }selected {/if} value='0'>Nicht versenden</option>
        <option  {if $hbci.bestaetigung == 1 }selected {/if} value='1'>ab Teilzahlung versenden</option>
        <option  {if $hbci.bestaetigung == 2 }selected {/if} value='2'>Komplett bezahlt versenden</option>
        </select></td>

  </tr>
      <tr>
    <td>Betreff</td>
    <td><input type="text" class='form-control' value='{$hbci.betreff}' name='hbci[betreff]'></td>
  </tr>
   <tr>
    <td>Absender</td>
    <td><input type="text" class='form-control' value='{$hbci.absender}' name='hbci[absender]'></td>
  </tr>
    <tr><td id='headline' colspan="4"><strong>Kundengruppen ausschliessen</strong></td></tr>
    <tr><td colspan="4">
            {foreach from=$hbcicustomergroup item=customergroup}
                <div style='width:400px;float:left;padding-right:50px'> <input type="checkbox" value='{$customergroup.id}' name='hbci_confirmation[]' {if $customergroup.dc_config == 1} checked {/if}>
                    {$customergroup.description|truncate:40}   </div>
            {/foreach}
        </td></tr>
  <tr>
  <td colspan="1"></td><td><a href='#' class='fancyboxfullscreen btn btn-info' data-fancybox-href='VOPDebitConnect?switchTo=template&noncss=1&fancy=1&art=zatpl'>Template anpassen</a></td>
  </tr>
    <tr><td>Zahlungsausgänge</td><td>
            <select name="hbci[zahlungsausgang]" class="form-control">
                <option {if $hbci.zahlungsausgang == 0} selected {/if} value="0">Anzeigen</option>
                <option <option {if $hbci.zahlungsausgang == 1} selected {/if} value="1">Nicht Anzeigen</option></select></td></tr>
  <tr><td colspan="2"><input type="submit" class='button' value="Speichern" /></td></tr>
  
</table>
</div>
</form>

<div class='box-group'>
<table>
<tr><td colspan="2"><h5>Globale Einstellung Umsatzblackliste</h5></td></tr>
<tr><td>Art</td><td>Enthält Textmuster</td><td>Aktion</td></tr>
{foreach key=idEntry from=$blackliste item=blacklist}
<form method="post">
<input type="hidden" name="deleteBlacklist" />
<input type='hidden' name='deleteId' value='{$idEntry}' />
<tr><td>{if $blacklist->art == 0 } Name {elseif $blacklist->art == 1} Verwendungszweck {/if}</td><td>{$blacklist->cString}</td><td><input type='submit' name='del' class='btn btn-danger' value='Löschen' /></td></tr>
</form>
{/foreach}
<form method="post">
<input type="hidden" name="setblacklist" />
<tr><td colspan="2">Neuer Eintrag:</td></tr>
<tr><td><select  required name='blacklist[art]'><option  value=''>Bitte auswählen</option><option value='0'>Name</option><option value='1'>Verwendungszweck</option></select></td><td><input type='text' name='blacklist[cString]' /><input type="submit" name="newEntry" value='Neuen Eintrag hinzufügen' class='btn btn-success' /></td></tr>
</form>
</table>
</div>

<form method="post">
<input type="hidden" name="setmatching" />
<div class="box-group">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td colspan="2"><h5>Globale Einstellung Matching</h5></td></tr>
	<tr><td width="30%">Matching - Eindeutig</td><td width="86%">
    <select class='form-control' required name="eindeutig">
    <option {if $eindeutig == 30}selected{/if} value='30'>30 Punkte</option>
    <option {if $eindeutig == 40}selected{/if} value='40'>40 Punkte</option>
    <option {if $eindeutig == 50}selected{/if} value='50'>50 Punkte</option>
    <option {if $eindeutig == 60}selected{/if} value='60'>60 Punkte</option>
    <option {if $eindeutig == 70}selected{/if} value='70'>70 Punkte</option>
    </select>
    </td></tr>
    <tr><td>Matching - Ähnlich</td><td>
    <select class='form-control' required name="aehnlich">
    <option {if $aehnlich == 10}selected{/if} value='10'>10 Punkte</option>
    <option  {if $aehnlich == 20}selected{/if} value='20'>20 Punkte</option>
    </select>
    </td></tr>
    <input type="hidden" name='matching_threads' value='1' />
    <tr>
    <td>Matching - Zahlstatus Ignorieren</td>
    <td>
    <select class='form-control' required name="matching_ignore_paymentstate">
    <option value=''>Bitte auswählen</option>
    <option {if $matching_ignore_paymentstate == 1}selected{/if} value='1'>Ja</option>
    <option  {if $matching_ignore_paymentstate == 0}selected{/if} value='0'>Nein</option>
    </select>
    </td>
    </tr>
    <tr><td>RegEx</td><td>
    <table class='regextable'>
    <tr><td>RegEx-Replace</td><td>Ersetzen mit</td><td>Kommentar</td><td>Löschen</td> <td></td></tr>
    <tbody>
    {foreach from=$regex item=reg}
    <tr><td><input type="text" readonly name="regex[replace][]" value='{$reg[0]}' /></td><td><input readonly type="text" name="regex[with][]" value='{$reg[1]}' /></td><td><input  readonly type="text" name="regex[comment][]" value='{$reg[2]}' /></td><td><input type="button" class='button' onclick="$(this).closest('tr').remove();" value="Entfernen" /></td></tr>
  	{/foreach}
    <tr class='append'></tr>
    </tbody>
    <tr><td colspan="5">Neue Regeln hinzufügen</td></tr>
      <tr>
        <td><strong>/</strong><input type="text" name="newRegex[replace]"  /><strong>/i</strong></td>
        <td><input style='margin-left:10px;margin-right:10px' type="text" name="newRegex[replacewith]"  /></td>
        <td><input  style='margin-left:10px;margin-right:10px' type="text" name="newRegex[comment]" placeholder="Kommentar" /></td>
        <td><input  style='margin-left:10px;margin-right:10px' type="text" name="newRegex[test]" placeholder="Teststring"/><input  style='margin-left:10px;margin-right:10px' type="text" name="newRegex[compare]" placeholder="Vergleiche"/></td>
        <td><input type="button" class='button' onclick='testregex()' value='Testen' /><input type="button" onclick='saveregex()' name='Hinzufügen' value='Hinzufügen' class='button' /></td></tr>
       <td id='regextestvalue' colspan="5">Vorschau:<br /><input type="text" class='full' name="newRegex[erg]" /></td></tr>
      </table>
    </td></tr>
    <tr><td colspan="2"><input type="submit" class="btn btn-success" name="savematching" value='Speichern' />
</table>
</div>
</form>
{literal}
<script>

function saveregex()
{
	if(!$("input[name='newRegex[replace]']").val() && !$("input[name='newRegex[replacewith]']").val()  )
	{
		alert('Bitte Daten eingeben');
		return;
	}
	$('.append').after("<tr><td><input type=\"text\" name=\"regex[replace][]\" value='"+$("input[name='newRegex[replace]']").val()+"' /></td><td><input type=\"text\" name=\"regex[with][]\" value='"+$("input[name='newRegex[replacewith]']").val()+"' /></td><td><input type=\"text\" name=\"regex[comment][]\" value='"+$("input[name='newRegex[comment]']").val()+"' /></td><td><input type=\"button\" class='button' onclick=\"$(this).closest('tr').remove();\" value=\"Entfernen\" /></td></tr>");
	$("input[name='newRegex[replace]']").val("");
	$("input[name='newRegex[replacewith]']").val("");
	$("input[name='newRegex[erg]']").val("");
	$("input[name='newRegex[comment]']").val("");
	$("input[name='newRegex[compare]']").val("");
	$("input[name='newRegex[test]']").val("");
	$("input[name='newRegex[erg]']").removeClass("success").removeClass("error");
}
function testregex()
{

			var API = "inc/regex.php?replace="+$("input[name='newRegex[replace]']").val()+"&with="+$("input[name='newRegex[replacewith]']").val()+"&ref="+$("input[name='newRegex[test]']").val()+"&compare="+$("input[name='newRegex[compare]']").val();
			var rsapi = $.getJSON( API, function(json_data) {
				console.log(JSON.stringify(json_data));
			$("input[name='newRegex[erg]']").val(json_data.replaced);
				if(json_data.matched)
				{ 
					$("input[name='newRegex[erg]']").removeClass("error").addClass("success");
				}
				else
				{
					$("input[name='newRegex[erg]']").removeClass("success").addClass("error");
				}
			});
}
</script>
{/literal}

<form method="post">
<input type="hidden" name="SKRSkonto" />
<div class='box-group'>
<table class='full'>
<tr><td colspan="4"><h5>Sachkontenrahmen & Skonto</h5></td></tr>
<tr><td  id='headline'  colspan="4"><b>Zahlungsarten</b></td></tr>
<tr><td>Zahlungsart</td><td>Sachkontorahmen</td><td>Skonto in %</td><td>Zeitraum</td></tr>
{foreach from=$payments item=zahlungsart}
<tr>
<td>{$zahlungsart.description}</td>
<td>

<input type='text' class='form-control'  name='skrpayment[{$zahlungsart.id}]' value='{foreach from=$skr.skr_payment key=objkey item=skrvalue}{if $objkey==$zahlungsart.id}{$skrvalue}{/if}{/foreach}'>
</td>
<td>
<input type='text' name='skonto[{$zahlungsart.id}]' class='form-control maskednumber' value='{foreach from=$skr.skonto key=objkey item=skrvalue}{if $objkey==$zahlungsart.id}{$skrvalue}{/if}{/foreach}'/>
</td>
<td>
<select class='form-control' name='zeitraum[{$zahlungsart.id}]'>
    <option value='0'>Bitte auswählen </option>
        {for $counter=1 to 31}

    	<option  {foreach from=$skr.zeitraum key=objkey item=skrvalue}{if $objkey==$zahlungsart.id && $skrvalue == $counter} selected {break} {/if}{/foreach} value='{$counter}'>{$counter} Tag/e</option>
		{/for}
</select>
</td>
</tr>
{/foreach}
<tr><td  id='headline'  colspan="4"><b>Sachkontorahmen Buchungspositionen</b></td></tr>
<tr><td>Mahnkosten</td><td><input type='text' class='form-control'  name='skr[mahnkosten]' value='{$skr.skr_buchungpos->mahnkosten}'/></td>
<td>Überzahlung</td>
<td><input type='text' class='form-control'  name='skr[ueberzahlung]' value='{$skr.skr_buchungpos->ueberzahlung}'/></td></tr>
<tr>
  <td>Erstattung</td>
  <td><input type='text' class='form-control'  name='skr[erstattung]' value='{$skr.skr_buchungpos->erstattung}'/></td>
  <td>Bankrücklastkosten</td>
  <td><input type='text' class='form-control'  name='skr[bankruecklastkosten]' value='{$skr.skr_buchungpos->bankruecklastkosten}'/></td>
</tr>
<tr>
  <td>Skontoausgleich</td>
  <td><input type='text' class='form-control'  name='skr[skontoausgleich]' value='{$skr.skr_buchungpos->skontoausgleich}'/></td>
  <td>Gutschrift</td>
  <td><input type='text' class='form-control'  name='skr[gutschrift]' value='{$skr.skr_buchungpos->gutschrift}'/></td>
</tr>
<tr><td colspan="4"><input type='submit' class='btn btn-success' name='saveSKR' value='Speichern'/></td></tr>
</table>
</div>
</form>
{else if $setting_art == "template"}
{literal}
<script>
$(document).ready(function(){
tinymce.init({

  selector: 'form textarea',
  height: 500,
  cleanup:false,
    convert_urls:true,
    relative_urls:false,
    remove_script_host:false,
    verify_html:false,
    extended_valid_elements : '*[*]',
  theme: 'modern',
   setup: function(editor) {
	      editor.addButton('Bestellung', {
      type: 'menubutton',
      text: 'Bestellung',
      icon: false,
      menu: [{
        text: 'AuftragsNr',
		type:'menubutton',
        onclick: function() {
          editor.insertContent('{$Bestellung.AuftragsNr}');
        }
      }, {
        text: 'RechnungsNr',
        onclick: function() {
          editor.insertContent('{$Bestellung.RechnungsNr}');
        }
      }, {
        text: 'Auftragdatum',
        onclick: function() {
          editor.insertContent('{$Bestellung.Auftragdatum}');
        }
      }, {
        text: 'KundenNr',
        onclick: function() {
          editor.insertContent('{$Bestellung.KundenNr}');
        }
      }
	  , {
        text: 'KundengruppeName',
        onclick: function() {
          editor.insertContent('{$Bestellung.KundenGruppeName}');
        }
      }
	  , {
        text: 'KundengruppeId',
        onclick: function() {
          editor.insertContent('{$Bestellung.KundenGruppeId}');
        }
		
      },
	  
	   {
        text: 'RechnungsDatum',
        onclick: function() {
          editor.insertContent('{$Bestellung.Rechnungsdatum}');
        }
      },{
        text: 'Betrag',
        onclick: function() {
          editor.insertContent('{$Bestellung.betrag}');
        }
      }, {
        text: 'Offen',
        onclick: function() {
          editor.insertContent('{$Bestellung.offen}');
        }
      }, {
        text: 'Zahlungsart',
        onclick: function() {
          editor.insertContent('{$Bestellung.ZahlartName}');
        }
      }, {
        text: 'Zahlungsstatus',
        onclick: function() {
          editor.insertContent('{$Bestellung.Zahlungsstatus}');
        }
      }, {
        text: 'Bezahlt',
        onclick: function() {
          editor.insertContent('{$Bestellung.Bezahlt}');
        },
		
		
		
      }
	  ]
    });
	   //-----------------------------
	    editor.addButton('Lieferadresse', {
      type: 'menubutton',
      text: 'Lieferadresse',
      icon: false,
      menu: [{
        text: 'Firma',
		type:'menubutton',
        onclick: function() {
          editor.insertContent('{$Lieferadresse.Firma}');
        }
      }, {
        text: 'Anrede',
        onclick: function() {
          editor.insertContent('{$Lieferadresse.Anrede}');
        }
      },{
        text: 'Nachname',
        onclick: function() {
          editor.insertContent('{$Lieferadresse.Nachname}');
        }
      },{
        text: 'Vorname',
        onclick: function() {
          editor.insertContent('{$Lieferadresse.Vorname}');
        }
      }, {
        text: 'Strasse',
        onclick: function() {
          editor.insertContent('{$Lieferadresse.Strasse}');
        }
      }, {
        text: 'PLZ',
        onclick: function() {
          editor.insertContent('{$Lieferadresse.PLZ}');
        }
      }, {
        text: 'Ort',
        onclick: function() {
          editor.insertContent('{$Lieferadresse.Ort}');
        }
      }, {
        text: 'Telefon',
        onclick: function() {
          editor.insertContent('{$Lieferadresse.Telefon}');
        }
      }, {
        text: 'Titel',
        onclick: function() {
          editor.insertContent('{$Lieferadresse.Titel}');
        }
      }
	  
	  
	  ]
    });
	   //--------------------
	  
    editor.addButton('Rechnungsadresse', {
      type: 'menubutton',
      text: 'Rechnungsadresse',
      icon: false,
      menu: [{
        text: 'Firma',
		type:'menubutton',
        onclick: function() {
          editor.insertContent('{$Rechnungsadresse.Firma}');
        }
      }, {
        text: 'Anrede',
        onclick: function() {
          editor.insertContent('{$Rechnungsadresse.Anrede}');
        }
      },{
        text: 'Nachname',
        onclick: function() {
          editor.insertContent('{$Rechnungsadresse.Nachname}');
        }
      },{
        text: 'Vorname',
        onclick: function() {
          editor.insertContent('{$Rechnungsadresse.Vorname}');
        }
      }, {
        text: 'Strasse',
        onclick: function() {
          editor.insertContent('{$Rechnungsadresse.Strasse}');
        }
      }, {
        text: 'PLZ',
        onclick: function() {
          editor.insertContent('{$Rechnungsadresse.PLZ}');
        }
      }, {
        text: 'Ort',
        onclick: function() {
          editor.insertContent('{$Rechnungsadresse.Ort}');
        }
      }, {
        text: 'Telefon',
        onclick: function() {
          editor.insertContent('{$Rechnungsadresse.Telefon}');
        }
      }, {
        text: 'Titel',
        onclick: function() {
          editor.insertContent('as{$Rechnungsadresse.Titel}');
        }
      }
	  
	  
	  ]
    });
  }
,
  plugins: [
    'fullpage advlist lists link image charmap  hr anchor pagebreak',
    'searchreplace wordcount visualblocks visualchars code fullscreen',
    'insertdatetime media nonbreaking save table directionality',
    'emoticons template paste textcolor colorpicker textpattern imagetools  toc help'
  ],
  toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
  toolbar2: ' forecolor backcolor  | Rechnungsadresse Lieferadresse Bestellung',
  image_advtab: true,
  templates: [
    { title: 'Test template 1', content: 'Test 1' },
    { title: 'Test template 2', content: 'Test 2' }
  ],
  content_css: [
    '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
    '//www.tinymce.com/css/codepen.min.css'
  ]
 });
 $('#htmleditor').show();
});

 </script>
{/literal}
<form method="post">
<input type="hidden" name="updatetemplate">
{if $tpl_saved}<div class='alert alert-success'>Änderung erfolgreich gespeichert</div>{/if}
<input type="submit" class="button" name="save" value='Template Ändern'/>
<textarea id='htmleditor'  name="tpl">{$tpl}</textarea>
</form>
{else if $setting_art == "cronjob"}
<form method="post">
<input type="hidden" name="updatecronjob">
<div class='box-group'>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td colspan="2"><h5>Cronjob</h5></td></tr>
  <tr>
    <td colspan="2"><h4>Zahlungserinnerung</h4></td>
  </tr>
  <tr>
    <td width="30%">Aktiv</td>
    <td width="82%"><select class='form-control' required="required" name='cronjob[ze][active]'>
      <option {if $cronjob->ze->active == 0} selected="selected" {/if} value='0'>Nicht automatisieren</option>
      <option  {if $cronjob->ze->active == 1} selected="selected" {/if}  value='1'>Automatisieren</option>
    </select></td>
  </tr>
    <tr>
    <td width="18%">Mindestbetrag</td>
    <td width="82%"><input type="text" class="maskednumber form-control" name="cronjob[ze][minvalue]" value='{$cronjob->ze->minvalue}' /></td>
  </tr>
      <tr>
    <td  class='valign-top'  width="18%">Kundengruppen ausschliessen</td>
    <td width="82%">
    {foreach from=$kundengruppe_ze item=kundengruppe}
  <div style='width:400px;float:left;padding-right:50px'><input type="checkbox" value='{$kundengruppe.id}' name='cronjob[ze][kundengruppe][]' {if $kundengruppe.dc_config == 1} checked {/if}>
  {$kundengruppe.description|truncate:40} </div>  
  {/foreach}</td>
  </tr>
    <tr>
    <td  class='valign-top'  width="18%">Zahlungsstatus ausschliessen</td>
    <td width="82%">
   {foreach from=$states item=state}{if $state.dc_config == 1}
  <div style='width:400px;float:left;padding-right:50px'><input type="checkbox" value='{$state.id}' name='cronjob[ze][withoutstate][]' {if !empty($cronjob->ze->withoutstate) && in_array($state.id,$cronjob->ze->withoutstate)} checked {/if} />{$state.description|truncate:40} 
   </div>
   {/if}
  {/foreach}
  </td>
  </tr>  
    <tr>
    <td colspan="2"><h4>Mahnservice</h4></td>
  </tr>
  <tr>
    <td width="18%">Aktiv</td>
    <td width="82%"><select  class='form-control' required="required" name='cronjob[ma][active]'>
      <option {if $cronjob->ma->active == 0} selected="selected" {/if} value='0'>Nicht automatisieren</option>
      <option  {if $cronjob->ma->active == 1} selected="selected" {/if}  value='1'>Automatisieren</option>
    </select></td>
  </tr>
    <tr>
    <td width="18%">Mindestbetrag</td>
    <td width="82%"><input type="text"  class="maskednumber form-control" name="cronjob[ma][minvalue]" value='{$cronjob->ma->minvalue}' /></td>
  </tr>
      <tr>
    <td class='valign-top'  width="18%">Kundengruppen ausschliessen</td>
    <td width="82%">
      {foreach from=$kundengruppe_ma item=kundengruppe}
  <div style='width:400px;float:left;padding-right:50px'><input type="checkbox" value='{$kundengruppe.id}' name='cronjob[ma][kundengruppe][]' {if $kundengruppe.dc_config == 1} checked {/if}>
  {$kundengruppe.description|truncate:40} </div>  
  {/foreach}</td>
  </tr>
  
    <tr>
    <td colspan="2"><h4>Zahlungsabgleich</h4></td>
  </tr>
  <tr>
    <td width="18%">Aktiv</td>
    <td width="82%"><select  class='form-control' required name='cronjob[zahlungsabgleich]'>
    <option {if $cronjob->zahlungsabgleich == 0} selected {/if} value='0'>Nicht automatisieren</option>
    <option  {if $cronjob->zahlungsabgleich == 1} selected {/if}  value='1'>Automatisieren</option>
    </select>
    </td>
  </tr>
 </table>
 </div>
 <input type="submit" class='btn btn-success' value='Speichern' name="Speichern" />
</form>
{/if}
