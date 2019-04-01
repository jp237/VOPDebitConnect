<style type="text/css">
#tabs-nohdr { 
    padding: 0px; 
    background: none; 
    border-width: 0px; 
} 
#tabs-nohdr .ui-tabs-nav { 
    padding-left: 0px; 
    background: transparent; 
    border-width: 0px 0px 1px 0px; 
    -moz-border-radius: 0px; 
    -webkit-border-radius: 0px; 
    border-radius: 0px; 
} 
#tabs-nohdr .ui-tabs-panel { 
    background: #f5f3e5 url(http://code.jquery.com/ui/1.8.23/themes/south-street/images/ui-bg_highlight-hard_100_f5f3e5_1x100.png) repeat-x scroll 50% top; 
    border-width: 0px 1px 1px 1px; 
}
.tabheader{
background-color:white;
}
</style>
{literal}
<style type="text/css">
ul.eap_liste {
	list-style-type: none;
    padding: 0px;
}

li.aktiv_head {
	float:left;
	width:120px;
	padding: 2px;
	padding-bottom:5px;
	font-weight:bold;
	border-bottom:1px solid #c0c0c0;
}

li.name_head {
	float:left;
	width:250px;
	padding: 2px;
	padding-bottom:5px;
	font-weight:bold;
	border-bottom:1px solid #c0c0c0;
}

li.score_head {
	float:left;
	width:200px;
	padding: 2px;
	padding-bottom:5px;
	font-weight:bold;
	border-bottom:1px solid #c0c0c0;
}

li.aktion_head {
	float:none;
	padding: 2px;
	padding-bottom:5px;
	font-weight:bold;
	border-bottom:1px solid #c0c0c0;
}

li.aktiv {
	float:left;
	width:120px;
	padding: 2px;
}

li.name {
	float:left;
	width:250px;
	padding: 2px;
}

li.score {
	float:left;
	width:200px;
	padding: 2px;
}

li.aktion {
	float:none;
	padding: 2px;
	text-align:center;
	border-bottom:1px solid #c0c0c0;
}


-->
</style>

{/literal}
<div class="tabs">
<div  style='width:100%'>
  <ul>
    <li><a class='button' href="#config">Grundeinstellungen</a></li>
    <li ><a class='button' href="#boni">Bonitätsprüfung</a></li>
    <li><a  class='button'  href="#ident">Alters/Identitätsprüfung / Adressvalidierung</a></li>
    <li><a  class='button'  href="#lang">Sprachvariablen</a></li>
  </ul>
</div>
  <div id="config">
  <div class='box-group'>
 
  <form method="post">
  <input type="hidden" name="tab" value="config" />
   <table class='full'>
   <tr><td colspan="2"> <h5>Grundeinstellungen</h5></td></tr>
   <tr><td align="left" width='35%'>Benutzername</td><td><input type="text" class='full' required name="gateway[username]" value='{$gateway.username}' /></td></tr>
   <tr><td>Passwort</td><td><input type="password"  class='full'  required name="gateway[passwd]"  value='{$gateway.passwd}' /></td></tr>
   <tr><td>Protokollierung</td><td><select required  class='full' name="gateway[log]"><option {if $gateway.log == '1'} selected {/if} value='1'>Ja</option><option  {if $gateway.log == '0'} selected {/if}  value='0'>Nein</option></select></td></tr>
   <tr><td>Fehlerbenachrichtung per Mail</td><td><input  class='full' type="text"   value='{$gateway.logmail}' name="gateway[logmail]" /></td></tr>
    <tr><td>Adressvalidierung (Neukunde)</td><td>
            <select  required  class='full' name='gateway[adressvalidierung_enabled]'>
                <option {if $gateway.adressvalidierung_enabled == '0'} selected {/if} value="0">Nicht verwenden</option>
                <option {if $gateway.adressvalidierung_enabled == '1'} selected {/if} value="1">Postdirekt verwenden</option>
            </select></td></tr>
       <tr><td>Personendaten korrigieren</td><td>
               <select  required  class='full' name='gateway[adressvalidierung_personendaten]'>
                   <option {if $gateway.adressvalidierung_personendaten == '0'} selected {/if} value="0">Nicht korrigieren</option>
                   <option {if $gateway.adressvalidierung_personendaten == '1'} selected {/if} value="1">Korrigieren</option>
               </select></td></tr>
   <tr><td colspan="2"><br />
    <input type='submit' class='button'  value='Konfiguration Speichern' /></td></tr>
   </table>
<table style='margin-top:35px' width="100%" border="0">
  {if $eap_state == 'NOLOGIN'}
   <tr><td colspan="2" style='background-color:#F8898C'>Das BoniGateway ist nicht einsatzbereit (Login)</td></tr>
  {elseif $eap_state=='NOPROJECT'}
  <tr><td colspan="2" style='background-color:#F8898C'>Das BoniGateway ist nicht einsatzbereit (Keine Projekte definiert)</td></tr>
  {elseif $eap_state=='COMERR'}
   <tr><td colspan="2" style='background-color:#F8898C'>Das BoniGateway ist nicht einsatzbereit (Kommunikationsfehler <a href='mailto:support@eaponline.de'>Support</a>)</td></tr>
  {elseif $eap_state=='LOGIN'}
  <tr><td colspan="2" style='background-color:#CEFFAA'>Das BoniGateway ist mit den folgenden Projekten einsatzbereit</td></tr>
  <tr id='projekte'><td width='32%'><strong>Projekte Privatpersonen </strong>
    <table>
   {foreach from=$eap_projekte item=projekte}
  <tr><td colspan="2">{$projekte->bezeichnung}</td></tr>
   {/foreach}
  </table></td><td width="68%" valign="top"><strong>Projekte Firmen </strong>
    <table>
      {foreach from=$eap_projekteb2b item=projekteb2b}
  <tr><td colspan="2">{$projekteb2b->bezeichnung}</td></tr>
   {/foreach}
  {/if}             
  </table></td></tr></table>

   </form>
   </div>
  </div>
  <div id="boni">
  <div class='box-group'>
     <form method="post">
  <input type="hidden" name="tab" value="boni" />
  
   <table  class='full' >
   <tr><td colspan="2"><h5>Bonitätsprüfung</h5></td></tr>
   <tr><td align="left" width='35%'>Anfrage Privatpersonen</td>
   	<td><select  required  class='full' name='gateway[request_b2c]'><option {if $gateway.request_b2c == '0'} selected {/if} value='0'>Prüfen</option><option <option {if $gateway.request_b2c == '1'} selected {/if} value='1'>Sperren</option><option {if $gateway.request_b2c == '2'} selected {/if} value='2'>Freigeben</option></select></td>
   </tr>
      <tr><td>Anfrage Firmen</td>
   	<td><select  required  class='full' name='gateway[request_b2b]'><option <option {if $gateway.request_b2b == '0'} selected {/if} value='0'>Prüfen</option><option <option {if $gateway.request_b2b == '1'} selected {/if} value='1'>Sperren</option><option {if $gateway.request_b2b == '2'} selected {/if} value='2'>Freigeben</option></select></td>
   </tr>
       <tr><td>Geburtsdatum abfragen *B2C</td>
           <td><select  required  class='full' name='gateway[b2c_birthday]'><option <option {if $gateway.b2c_birthday == '0'} selected {/if} value='0'>Nicht abfragen</option><option  {if $gateway.b2c_birthday == '1'} selected {/if}  value='1'>Abfragen</option></select></td>
       </tr>
       <tr><td>Anfrage Ausland</td>
   	<td><select  required  class='full' name='gateway[request_nonde]'><option <option {if $gateway.request_nonde == '0'} selected {/if} value='0'>Prüfen</option><option  {if $gateway.request_nonde == '1'} selected {/if}  value='1'>Sperren</option><option  {if $gateway.request_nonde == '2'} selected {/if}  value='2'>Freigeben</option></select></td>
   </tr>
   <tr><td>Verhalten im Fehlerfall</td>
   	<td><select required  class='full'  name='gateway[exceptionhandle]'><option  {if $gateway.exceptionhandle == '0'} selected {/if} value='0'>Zahlungsart(en) sperren</option><option  {if $gateway.exceptionhandle == '1'} selected {/if} value='1'>Zahlungsart(en) freigeben</option></select></td>
   </tr>
     <tr><td>Abweichende Lieferadresse sperren</td>
   	<td><select  required  class='full' name='gateway[deviant]'><option  {if $gateway.deviant == '0'} selected {/if} value='0'>Nein</option><option  {if $gateway.deviant == '1'} selected {/if}  value='1'>Ja</option></select></td>
   </tr>
       </tr>
       <tr><td>Max. Änderungen am Warenkorb</td>
           <td>
               <select  required  class='full' name='gateway[cardprotection]'>
                   <option {if $gateway.cardprotection == '0'} selected {/if} value="0">Nicht verwenden</option>
                   <option {if $gateway.cardprotection == '1'} selected {/if} value="1">1 Änderung</option>
                   <option {if $gateway.cardprotection == '2'} selected {/if} value="2">2 Änderungen</option>
                   <option {if $gateway.cardprotection == '3'} selected {/if} value="3">3 Änderungen</option>
                   <option {if $gateway.cardprotection == '4'} selected {/if} value="4">4 Änderungen</option>
                   <option {if $gateway.cardprotection == '5'} selected {/if} value="5">5 Änderungen</option>
                   <option {if $gateway.cardprotection == '6'} selected {/if} value="6">6 Änderungen</option>
                   <option {if $gateway.cardprotection == '7'} selected {/if} value="7">7 Änderungen</option>
                   <option {if $gateway.cardprotection == '8'} selected {/if} value="8">8 Änderungen</option>
                   <option {if $gateway.cardprotection == '9'} selected {/if} value="9">9 Änderungen</option>
                   <option {if $gateway.cardprotection == '10'} selected {/if} value="10">10 Änderungen</option>
       </tr>
   </table>
 
   <table class='full' >
   <tr><td colspan="2"><b>Zahlungsarten</b></td></tr>
   <tbody>
   	{foreach from=$payments item=payment}
    

    <tr><td width='35%'>{$payment.description}</td>
    	<td>
            <select  required class='full'  name='gateway[boni_payments][{$payment.id}]'>
                <option {if $payment.conf_value == '0'} selected {/if} value="0">Nicht Prüfen</option>
                <option  {if $payment.conf_value == '1'} selected {/if} value='1'>Prüfen</option>
            </select>
        </td>
    </tr>
    {/foreach}
   </tbody>
   </table>
 
    <table class='full' >
   <tbody>
   <tr><td colspan="2"><b>Kundengruppen</b></td></tr>
   	{foreach from=$customergroups.boni item=customergroup}
    <tr><td width='35%'>{$customergroup.description}</td><td>
     <select  required class='full'  name='gateway[boni_customergroup][{$customergroup.id}]'>
                <option {if $customergroup.conf_value == '0'} selected {/if} value="0">Nicht ausschließen</option>
                <option  {if $customergroup.conf_value == '1'} selected {/if} value='1'>Ausschließen</option>
            </select>
    </td></tr>
    {/foreach}
   </tbody>
   </table>
   <br />
    <input type='submit' class='button'  value='Konfiguration Speichern' />
   </form> 
   </div>
   </div>
    <div id="ident">
   <form method="post">
  <input type="hidden" name="tab" value="ident" />
  <div class='box-group'>
 
    <table class='full' >
    <tr><td colspan="2"><h5>Einstellungen Alters-/Identitätsprüfung / Adressvalidierung</h5></td></tr>
   <tr><td align="left" width='35%'>Altersprüfung verwenden</td>
   	<td><select  required class='full'  name='gateway[ident]'><option  {if $gateway.ident == '0'} selected {/if} value='0'>Nein</option><option {if $gateway.ident == '1'} selected {/if}  value='1'>QBIT</option></select></td>
   </tr>
      <tr><td>Art der Abfrage</td>
   	<td><select  required class='full'  name='gateway[ident_art]'><option {if $gateway.ident_art == '0'} selected {/if} value='0'>Immer</option><option {if $gateway.ident_art == '1'} selected {/if} value='1'>Artikelattribut</option></select></td>
   </tr>
     <tr><td>Neuprüfung bei Adressänderung</td>
   	<td><select  required class='full'  name='gateway[ident_recheck_address]'><option {if $gateway.ident_recheck_address == '0'} selected {/if} value='0'>Nein</option><option  {if $gateway.ident_recheck_address == '1'} selected {/if} value='1'>Ja</option></select></td>
   </tr>
       <tr><td>Artikelattribut</td>
   	<td><input  class='full' type='text' value='{$gateway.ident_attribute}' name='gateway[ident_attribute]'></td>
     </table>
  
    <table class='full' >
    <tr><td colspan="2"><b>Versandarten</b></td></tr>
   <tbody>
   	{foreach from=$shipping item=ship}
    <tr><td td width='35%'>{$ship.name}</td><td>
    <select  required class='full'  name='gateway[ident_shipping][{$ship.id}]'><option {if $ship.conf_value == '0'} selected {/if} value="0">Nicht Sperren</option><option {if $ship.conf_value == '1'} selected {/if} value='1'>Sperren</option></select></td></tr>
    {/foreach}
   </tbody>
   </table>
   
    <table class='full' >
	<tr><td colspan="2"><b>Kundengruppen</b></td></tr>
    <tbody>
   	{foreach from=$customergroups.ident item=customergroup}
    <tr><td td width='35%'>{$customergroup.description}</td><td>
     <select  required  class='full'  name='gateway[ident_customergroup][{$customergroup.id}]'>
                <option {if $customergroup.conf_value == '0'} selected {/if} value="0">Nicht ausschließen</option>
                <option  {if $customergroup.conf_value == '1'} selected {/if} value='1'>Ausschließen</option>
            </select>
    </td></tr>
    {/foreach}
    <tr><td>Verschiebe in Kundengruppe</td><td> <select required  class='full'  name='gateway[ident_moveto]'>
    <option {if $gateway.ident_moveto} selected {/if} value="dontmove">Nicht verschieben</option>
    {foreach from=$customergroups.ident item=customergroup}
	<option  {if $gateway.ident_moveto == $customergroup.groupkey} selected {/if} value='{$customergroup.groupkey}'>{$customergroup.description}</option>
     {/foreach}
    </select></td></tr>
    <tr><td><b>Adressvalidierung ( Postdirekt )</b></td>
        <td><select  required class='full'  name='gateway[postdirekt]'><option {if $gateway.postdirekt == '0'} selected {/if} value='0'>nicht verwenden</option><option {if $gateway.postdirekt == '1'} selected {/if} value='1'>Postdirekt verwenden</option></select></td>
    </tr>
   </tbody>
   </table>
     <br />
    <input type='submit' class='button'  value='Konfiguration Speichern' />
    </form> 
    </div>
   </div>
    <div id="lang">
   <form method="post">
  <input type="hidden" name="tab" value="lang" />
  <div class='box-group'>
  
    <table  class='full' >
    <tr><td colspan="2"><h5>Einstellungen Sprachvariablen</h5></td></tr>
    {foreach from=$lang item=sprache}
    <tr><td colspan="2"><h6 title='{$sprache.key}' style="padding-top:10px">{$sprache.comment}</h6></td></tr>
   <tr>
   	<td colspan="2"><input  type='text' class='full' required name='lang[{$sprache.key}]' value='{$sprache.value}'/></td>
   </tr>
   	{/foreach}
   </tbody>
   </table>
    <br />
    <input type='submit' class='button'  value='Konfiguration Speichern' />
    </form> 
    </div>
   </div>
</div>

  <script>
  $( function() {
    $( ".tabs" ).tabs();
  } );
  
 </script>
  