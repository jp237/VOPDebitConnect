  <div class="col-sm-8"></div>
  <div class="col-sm-3"><select class="form-control small" name='konto'>
    <option value=''>Bitte Konto auswählen</option>
    {foreach from=$profiles item=profile}
          {foreach from=$profile->profileData->konto item=konto}
              {if $konto->enabled == 1}<option value='{$profile->id};{$konto->IBAN}'>{$konto->IBAN} ({$profile->profileName})</option>{/if}
          {/foreach}
    {/foreach}
    </select>
  </div>
  <div class="col-sm-1">
    <input type="submit" value='DTA-Erstellen' name='createDTA' class='btn btn-success' />
  </div>