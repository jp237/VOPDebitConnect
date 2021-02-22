  <div class="col-sm-8"></div>
  <div class="col-sm-3"><select class="form-control small" name='konto'>
    <option value=''>Bitte Konto auswählen</option>
    {foreach from=$profiles->bankAccounts item="bank"}
          {foreach from=$bank key=key item="iban"}
              <option value='{$iban}'>{$iban}</option>
          {/foreach}
    {/foreach}
    </select>
  </div>
  <div class="col-sm-1">
    <input type="submit" value='DTA-Erstellen' name='createDTA' class='btn btn-success' />
  </div>