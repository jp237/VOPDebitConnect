  <select  name='konto'>
  <option value=''>Bitte Konto auswählen</option>
  {foreach from=$profiles item=profile}
    	{foreach from=$profile->profileData->konto item=konto}
 			{if $konto->enabled == 1}<option value='{$profile->id};{$konto->IBAN}'>{$konto->IBAN} ({$profile->profileName})</option>{/if}
  		{/foreach}
  {/foreach}
  </select><input type="submit" value='DTA-Erstellen' name='createDTA' class='button' />