<h5>HBCI-Profilverwaltung</h5>
<div class='box-group'>
<table class='full'>

<tr><td colspan="2"><h5>HBCI-Profilverwaltung</h5></td></tr>
<tbody>
{if $profiles|@count == 0}
<tr><td colspan="2"><b>Keine Profile eingerichtet.</b></td></tr>
{else}
<tr><td style='border-right:1px solid #f2f2f2;width:300px;min-height:500px;' valign="top">
{foreach from=$profiles item=profil}
<a onclick='showLoader();' class="btn btn-info btn-sm" href='VOPDebitConnect?switchTo=hbciProfiles&getProfileId={$profil->id}'>{$profil->profileName}</a><br />
{/foreach}
</td><td valign="top">
{if $smarty.get.getProfileId>0}
<form method="post">
<input type='hidden' name='updateProfile' />
<h4>Zugangsdaten</h4>
<table class='full'>
    <td width="24%">Bankleitzahl</td>
    <td width="76%"><input type='text' class='form-control'  name='profile[blz]' required value='{$selected->profileData->blz}'></td>
  </tr>
  
   <tr>
    <td>HBCI-URL</td>
    <td><input type='text' class='form-control'  name='profile[url]' required value='{$selected->profileData->url}'></td>
  </tr>
     <tr>
    <td>HBCI-Benutzer</td>
    <td><input type='text' class='form-control'  name='profile[alias]' required value='{$selected->profileData->alias}'></td>
  </tr>
  <tr>
    <td>HBCI-PIN</td>
    <td><input type='password' class="form-control" name='profile[pin]' required value='{$selected->profileData->pin}'></td>
  </tr>
  {if $konten|@count > 0}
    <tr><td colspan="2"><br /><h4>Konten</h4> </td></tr>
    <tr><td colspan="2">
        <table width="100%">
        <tr>
        	<td>IBAN</td>
            <td style='padding-left:10px'>BIC</td>
            <td style='padding-left:10px'>DTA-Verwendungszweck vorranstellen</td>
            <td style='padding-left:10px'>Kontoinhaber (DTA)</td>
            <td style='padding-left:10px'>Aktion</td>
            {foreach from=$konten item=konto}
            	<input type='hidden' name='profile[konto][{$konto.IBAN}][IBAN]' value='{$konto.IBAN}' />
            	<input type='hidden' name='profile[konto][{$konto.IBAN}][BIC]' value='{$konto.BIC}' />
            <tr>
                <td>{$konto.IBAN}</td>
                <td><span style="padding-left:10px">{$konto.BIC}</span></td>
                <td style='padding-left:10px'>  <input type='text' class='form-control' name='profile[konto][{$konto.IBAN}][VWZ]' value='{$konto.VWZ}' /></td>
               	<td style='padding-left:10px'>  <input type='text' class='form-control' name='profile[konto][{$konto.IBAN}][OWNER]' value='{$konto.OWNER}' /></td>
                <td style='padding-left:10px'>
                    <select class="form-control" name='profile[konto][{$konto.IBAN}][enabled]'>
                        <option {if $konto.enabled == 0} selected {/if} value='0'>Nicht verwenden</option>
                        <option  {if $konto.enabled == 1} selected {/if}value='1'>Verwenden</option>
                    </select>
                </td>
            </tr>
            {/foreach}
        </table>
     </td></tr>
    {/if}
    {if isset($HBCI_FAULT)} <tr><td colspan="2"><div class='errormsg'> <b>HBCI-Fehler :</b><br />{$HBCI_FAULT} </div></td></tr>{/if}
  <tr><td colspan="2"><input type="submit" class="btn btn-success"  value="Profil {$selected->profileName} Speichern" /></td></tr>
</table>

</form>

{else} <b> Kein Profil ausgewählt</b>
{/if}
</td></tr>
{/if}
</tbody>
    <tr><td><b>Profil erstellen</b></td><td><form method="post"><input type='hidden' name='newProfile' /><div class="col-sm-3"><input type='text' class="form-control" required placeholder="Bitte Profilnamen eingeben" name='ProfileName' /></div><div class="col-sm-1"><input style='margin-left:30px' type="submit" class='btn btn-success' value='Neues Profil erstellen'/></div></form>
</td></tr>
</table></div>