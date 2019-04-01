{$gateway_exception}
<div class='box-group'>
{if $gatewaylogin}
<h5>Manuelle Bonitätsprüfung</h5>
<form id='requestParams' method="post">
<input id='addrChange' type="hidden" name="changeAdress" value='' />
<table class="full">
<tr><td width="50%">
    <table class='full'>
    {if $gateway_invoice_address}
        <tr>
            <td width="250">Letzte Rechnungsadresse</td><td><select onchange="document.getElementById('addrChange').value='true';this.form.submit()" class='full' name='lastAdresses'>
                {foreach key=arrItem from=$gateway_invoice_address item=adress}
                {if $smarty.post.lastAdresses == $arrItem && $smarty.post.changeAdress == 1 || !$smarty.post.lastAdresses && $arrItem == 0}
                	{assign var="adress_selected" value=$adress}
                {/if}
                <option value='{$arrItem}'>{$adress.company} {$adress.firstname} {$adress.lastname} {$adress.street}</option>
                {/foreach}
                </select>
            </td>
        </tr>
      {/if}
        <tr>
            <td width="250px">Prüfungsart auswählen</td>
            <td colspan="5">
                <select onchange='showLoader();this.form.submit()' class='full' name='request_art'>
                <option {if $smarty.post.request_art=='B2C'} selected {/if} value='B2C'>Privatpersonen ( Ampel )</option>
                <option {if $smarty.post.request_art=='Kaskade'} selected {/if} value='Kaskade'>Kurzauskunft Direkt - Kaskade( Ampel )</option>
                <option {if $smarty.post.request_art=='Kompakt'} selected {/if} value='Kompakt'>Kompaktauskunft ( PDF )</option>
                <option {if $smarty.post.request_art=='Vollauskunft'} selected {/if} value='Vollauskunft'>Vollauskunft ( PDF )</option>
            </select>
            </td>
        </tr>
         {if  $smarty.post.request_art!='Kompakt' && $smarty.post.request_art != 'Vollauskunft'}
         <tr>
            <td>Projekt auswählen</td>
            <td colspan="5">
                <select class='full' required name='request_project'>
                <option value="">Bitte Auswählen/ Konfigurieren</option>
                {if !$smarty.post.request_art OR $smarty.post.request_art == 'B2C'}
                    {foreach from=$projecte_b2c item=projekt}
                    <option  {if $smarty.post.request_project == $projekt->userprojectid} selected {/if} value='{$projekt->userprojectid}'>{$projekt->bezeichnung}</option>
                    {/foreach}
                {else}
                    {foreach from=$projecte_b2b item=projekt}
                    <option   {if $smarty.post.request_project == $projekt->userprojectid} selected {/if}   value='{$projekt->userprojectid}'>{$projekt->bezeichnung}</option>
                    {/foreach}
                {/if}
            </select>
            </td>
        </tr>
        {/if}
         {if  $smarty.post.request_art!='Kompakt' && $smarty.post.request_art != 'Vollauskunft'}
        <tr>
            <td>Vorname</td>
            <td width="548"><input class='full'  type="text" name='firstname' value='{if $smarty.post.firstname}{$smarty.post.firstname}{else}{$adress_selected.firstname}{/if}' /></td>
        </tr>
        <tr>
            <td width="115">Nachname</td>
            <td width="548"><input class='full'  type="text" name="lastname" value='{if $smarty.post.firstname}{$smarty.post.lastname}{else}{$adress_selected.lastname}{/if}'/></td>
        </tr>
        <tr>
            <td width="115">Geburtstag</td>
            <td width="548"><input class='full'  type="text" name="DateOfBirth" value='{if $smarty.post.DateOfBirth}{$smarty.post.DateOfBirth}{else}{$adress_selected.DateOfBirth}{/if}'/></td>
        </tr>
        {/if}
       {if  $smarty.post.request_art=='Kaskade'}
            <tr>
                <td colspan="2"><input type="checkbox" name='kaskade_inhaber' value='{if $smarty.post.kaskade_inhaber} checked{/if}' />Person ist der Inhaber?</td>
            </tr>
        {/if}
        {if  $smarty.post.request_art!='B2C'}
       <tr>
            <td width="115">Firma</td>
            <td width="548"><input class='full'  type="text" name="company" value='{if $smarty.post.company}{$smarty.post.company}{else}{$adress_selected.company}{/if}' /></td>
        </tr>
        {/if}
         {if  $smarty.post.request_art=='B2C'}
        <tr> 
        	<td width="115">Geschlecht</td>
            <td width="548"><select  class='full'  name="salutation"><option value='1'>Männlich</option><option value="2">Weiblich</option></select></td>
       </tr>
       {/if}
       <tr>
            <td width="115">Strasse</td>
            <td width="548"><input class='full'  type="text" name="street" value='{if $smarty.post.street}{$smarty.post.street}{else}{$adress_selected.street}{/if}' /></td>
        </tr>
        <tr>
            <td width="115">PLZ</td>
            <td width="548"><input class='full'  type="text" name="zipcode" value='{if $smarty.post.zipcode}{$smarty.post.zipcode}{else}{$adress_selected.zipcode}{/if}' /></td>
        </tr>
        <tr>
            <td width="115">Ort</td>
            <td width="548"><input class='full'  type="text" name="city" value='{if $smarty.post.city}{$smarty.post.city}{else}{$adress_selected.city}{/if}' /></td>
        </tr>
        <tr>
            <td width="115">Land</td>
            <td width="548">
            <select class='full' required name='country'>
            {foreach from=$countries item=country}
            <option value='{$country.iso3}'>{$country.countryname}</option>
            {/foreach}
            </select></td>
        </tr>{if $smarty.post.request_art == 'Kompakt' || $smarty.post.request_art == 'Vollauskunft'}
        <tr>
        <td>Kennziffer</td>
        <td>
         <select required name="kennziffer">
        	<option value=''>Bitte auswählen</option>
            {foreach from=$kennziffern item=kennziffer}
             <option {if $smarty.post.kennziffer == $kennziffer->id} selected {/if} value='{$kennziffer->id}'>{$kennziffer->kennziffer}</option>
            {/foreach}
        	
          </select></td>
        </tr>
        <tr><td>Berechtigtes Intresse</td><td>
        <select required name="intresse">
        	<option value=''>Bitte auswählen</option>
        	<option {if $smarty.post.intresse == 'X1' } selected  {/if} value='X1'>Kreditanfrage</option>
            <option {if $smarty.post.intresse == 'X2' } selected  {/if} value='X2'>Geschäftsanbahnung</option>
            <option {if $smarty.post.intresse == 'X3' } selected {/if}  value='X3' >Bonit&auml;tspr&uuml;fung</option>
            <option {if $smarty.post.intresse == 'X4' } selected {/if}  value='X4' >Forderung</option></select>
            </td></tr>
        {else}
       <tr><td colspan="2"><input type="checkbox" required {if $smarty.post.intresse} checked {/if} name="intresse" />Berechtigtes Interesse / Telefonische Einwilligung{/if}</td></tr>
        <tr><td colspan="2">{if !$GatewayList} <input type="submit" onclick='showLoader();'  name='getRequestBoniGateway' class="button" {if $smarty.post.request_art == 'Kompakt' || $smarty.post.request_art == 'Vollauskunft'} value='Trefferliste abrufen' {else} value='Bonitätsprüfung einholen' {/if} />{/if}<input type="button" class="button" onclick="showLoader();rldPage();" value="Formular zurücksetzen" name='Formular Zurücksetzen' /></td></tr>
    </table>

</td><td style='padding-left:25px' valign="top">
 {if $GatewayList}
  <table><tr><td colspan="2"><h4>Trefferliste</h4>Bitte wählen Sie eine Firma aus der nachfolgenden Liste</td></tr>
  {foreach from=$GatewayList item=treffer}
 <tr> <td><input type="radio" name='request_id' required value='{$treffer->id}' /></td><td>{$treffer->name} {$treffer->strasse} {$treffer->plz} {$treffer->ort}</td></tr>
  {/foreach}
  {if $GatewayList} <tr><td colspan="2"><input type="submit" onclick='showLoader();' name='getRequestBoniGateway' class="button" value='Auskunft abrufen'/></td></tr>{/if}
  </table>
    {else if $GatewayResult && $GatewayResult->bPDF == null}<h4>Ergebnis der Bonitätsprüfung</h4>
<table>
<tr><td>Person/Firma</td><td><input type="text" class='full'  name='erg_personfirma' value='{$GatewayResult->Nachname} {$GatewayResult->Vorname}' /></td></tr>
<tr><td>Strasse</td><td><input type="text" class='full'  name='erg_strasse' value='{$GatewayResult->Strasse}' /></td></tr>
<tr><td>PLZ</td><td><input type="text"  class='full' name='erg_plz' value='{$GatewayResult->PLZ}' /></td></tr>
<tr><td>Ort</td><td><input type="text" class='full'  name='erg_Ort' value='{$GatewayResult->Ort}' /></td></tr>
<tr><td>Scorewert</td><td><input type="text"  class='full' name='erg_Ort' value='{$GatewayResult->Scorewert}' /></td></tr>
<tr><td>Scorebereich</td><td><input type="text"  class='full' name='erg_Ort' value='{$GatewayResult->Scorebereich}' /></td></tr>
<tr><td colspan="2"><b>{$GatewayResult->filterText}</b></td></tr>
<tr><td>Ergebnis </td><td>{if $GatewayResult->secure_payment}<img src='/engine/Shopware/Plugins/Community/Backend/VOPDebitConnect/Views/backend/_resources/img/rot.png' />{else}<img src='/engine/Shopware/Plugins/Community/Backend/VOPDebitConnect/Views/backend/_resources/img/gruen.PNG' />{/if}</td></tr>


</table>
	{else if $GatewayResult->bPDF }
  <embed src='data:application/pdf;base64,{$GatewayResult->bPDF}' width='100%' height='300px' alt='pdf' pluginspage='http://www.adobe.com/products/acrobat/readstep2.html' type='application/pdf'>
    {else}
    Bitte Anfrage durchführen
    {/if}</td></tr></table>
</form>
{if $gateway_history}
<h5>Protokoll</h5>
<table class="auftragtable">
<thead><tr><th>Datum</th><th>Zahlungsart ( Warenkorbhöhe )</th><th>Art</th><th>Ergebnis</th><th>Score</th></tr></thead>
<tbody>
{foreach from=$gateway_history item=historyEntry}
<tr><th>{$historyEntry.tstamp}</th><th>{$historyEntry.zahlungsart} ({$historyEntry.warenkorb})</th><th>{$historyEntry.cArt} {$historyEntry.responseText}</th><th>{if $historyEntry.bPDF} <a download='Boniaetesauskunft.pdf' href='data:application/pdf;base64,{$historyEntry.bPDF}' title='Download pdf document' />Download PDF</a>{else}{if $historyEntry.ergebnis == 1}<img width='25px' height='25px' src='/engine/Shopware/Plugins/Community/Backend/VOPDebitConnect/Views/backend/_resources/img/rot.png' />{else} <img  width='25px' height='25px' src='/engine/Shopware/Plugins/Community/Backend/VOPDebitConnect/Views/backend/_resources/img/gruen.PNG' />{/if}{/if}</th><th>{$historyEntry.scoreInfo}</th></tr>
{/foreach}
</tbody>
</table>
{/if}
{else}
<form method="post">
<input type="hidden" name="requestLogin" value='1'/>

<table>
<tr><td colspan="2"><h5>Login EAP-BoniGateway</h5></td></tr>
<tr><td colspan="2">Für die Nutzung benötigen Sie einen Vertrag mit der Schufa Holding AG.<br />
Wir beraten Sie gerne.<br />
Hinterlegen Sie ihre Schufa-Kennziffer/n in Ihrem persöhnlichem <a href="https://gateway.eaponline.de" target="_new">Administrationsbereich</a></td></tr>
<tr><td>Benutzername</td><td><input type="text" required name="gatewaylogin" /></td></tr>
<tr><td>Passwort</td><td><input type="password" required name="gatewaypass" /></td></tr>
<tr>
  <td><img src="/engine/Shopware/Plugins/Community/Backend/VOPDebitConnect/Views/backend/_resources/img/schufa.PNG" /></td>
  <td><input type="submit" onclick='showLoader();'  name='Login' value='Im BoniGateway anmelden' class='button'/></td></tr></table>
</form>

{/if}
</div>
<script type="text/javascript">
function rldPage(){
this.location = this.location.href;
}</script>
