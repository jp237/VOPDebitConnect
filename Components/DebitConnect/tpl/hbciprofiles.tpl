<style type="text/css">
    .top10{
        padding-top:10px;
    }
</style>
<div class="container">
    <ul class="nav nav-tabs">
        <li class="{if !$bankLogin}active{/if}"><a data-toggle="tab" href="#home">Zugangsdaten</a></li>
        {if $bankLogin}
        <li class="active"><a data-toggle="tab" href="#bankaccounts">Bankverbindungen</a></li>
        {/if}
        {if $webForms|count > 0}
            <li><a data-toggle="tab" href="#webforms">Webforms</a></li>
        {/if}
    </ul>

    <form method="post">
        <div class="box-group">
            <div class="tab-content">
                <div id="webforms" class="tab-pane fade">
                    <h4>Webforms</h4>
                    <p>Aufgrund PSD-2 wird Ihre Aktion benötigt</p>
                    <div class="row">
                        <div class="col-sm-4">Funktion</div>
                        <div class="col-sm-4">Datum</div>
                        <div class="col-sm-4">Bestätigen</div>
                    </div>
                    {foreach from=$webForms key=key item="webForm"}
                        <div class="row">
                            <div class="col-sm-4">{$webForm->function}</div>
                            <div class="col-sm-4">{$webForm->dateTime|date_format:"%d.%m.%y %H:%m"}</div>
                            <div class="col-sm-4"><a target="_blank" href="VOPDebitConnect?webForm={$key}">Bestätigen</a></div>
                        </div>
                    {/foreach}
                </div>
                <div id="home" class="tab-pane fade {if !$bankLogin}  in active{/if}">
                    <h4>Ihre Zugangsdaten</h4>
                   <p>Bitte hierlegen Sie hier Ihre Zugangsdaten die Sie von V.O.P erhalten haben.<p></p>
                    <div class="row top10">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" placeholder="Benutzername" name="profile[client_id]" value="{$profile->client_id}">
                        </div>
                    </div>
                    <div class="row top10">
                        <div class="col-sm-12">
                            <input type="password" class="form-control" placeholder="Passwort" name="profile[client_secret]" value="{$profile->client_secret}">
                        </div>
                    </div>
                     <div class="row top10">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" placeholder="finAPI Url" name="profile[url]" value="{$profile->url}">
                        </div>
                    </div>

                    <div class="row top10">
                        <div class="col-sm-12"><input type="submit" name="saveLoginCredentials" class="btn btn-success" value="Zugangsdaten speichern"> </div>
                    </div>
                </div>
                {if $bankLogin}
                <div id="bankaccounts" style="min-height:768px" class="tab-pane fade {if $bankLogin}in active{/if}">
                    <div class="row top10">

                        <div class="col-sm-12">
                            <h4 class="top10">Neue Bankverbindung hinzufügen </h4>
                            <div class="col-sm-3">
                                Bankverbindung suchen
                            </div>
                            <div class="col-sm-7">
                                <input type="text" name="searchBank" class="form-control" value="">
                            </div>
                                <div class="col-sm-2"><input type="submit" name="searchBankData" class="btn btn-primary">
                            </div>
                        </div>
                    </div>
                    {if $smarty.post.searchBank && $searchResultBankAccounts|count == 0}
                        <div class="alert alert-danger top10">Keine Bank gefunden.</div>
                        {else if $searchResultBankAccounts|count>0}
                        <div class="row top10">
                            <div class="col-sm-4">Name</div>
                            <div class="col-sm-4">Ort</div>
                            <div class="col-sm-2">BIC</div>
                            <div class="col-sm-2">Aktion</div>
                        </div>
                        <div style="max-height:300px;overflow:scroll">
                            {foreach from=$searchResultBankAccounts item=bankData}
                                <input type="hidden" name="accountName[{$bankData.id}]" value="{$bankData.name}">
                                <div class="row">
                                    <div class="col-sm-4">{$bankData.name}</div>
                                    <div class="col-sm-4">{$bankData.city}</div>
                                    <div class="col-sm-2">{$bankData.bic}</div>

                                    <div class="col-sm-2"><input type="submit" name="addBankAccount[{$bankData.id}]" value="Konto hinzufügen" class="btn btn-primary"> </div>
                                </div>

                            {/foreach}
                        </div>
                    {/if}

                    {if $currentAccounts|count == 0}
                        <div class="alert alert-info top10">Sie haben derzeit noch keine Bank hinzugefügt</div>
                        {else}
                        {foreach from=$currentAccounts key=key item=accountData}
                            <h4 class="top10">Ihre Banken ( {$currentAccounts|count} ) {$accountData.bic}</h4>
                            <div class="panel-group top10" id="accordion">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#account{$key}">
                                                {$accountData.name}</a>
                                        </h4>
                                    </div>


                                    <div id="account{$key}" class="panel-collapse collapse in">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-2">Name</div>
                                                <div class="col-sm-3">IBAN</div>
                                                <div class="col-sm-2">Letztes Update</div>
                                                <div class="col-sm-2">Saldo</div>
                                                <div class="col-sm-2">Verwenden</div>
                                            </div>
                                            {foreach from=$accountData.accounts item="account"}
                                               <div class="row">
                                                    <div class="col-sm-2">{$account.account_name}</div>
                                                    <div class="col-sm-3">{$account.iban}</div>
                                                    <div class="col-sm-2">{$account.update|date_format:"%d.%m.%Y %H:%m"}</div>
                                                    <div class="col-sm-2">{$account.balance}</div>
                                                    <div class="col-sm-2"><input type="checkbox" {if array_key_exists($account.id,$profile->bankAccounts[$key])} checked {/if} name="profile[accounts][{$account.id}]" value="{$account.iban}">  </div>
                                                </div>

                                            {/foreach}
                                            <h4>DTA-Informationen</h4>
                                            {foreach from=$accountData.accounts item="account"}
                                                <input type="hidden" name="profile[dtaInformation][{$account.id}][iban]" value="{$account.iban}">
                                                <input type="hidden" name="profile[dtaInformation][{$account.id}][bic]" value="{$accountData.bic}">
                                                <div class="row">
                                                    <div class="col-sm-4">{$account.iban}</div>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control" placeholder="Kontoinhaber" name="profile[dtaInformation][{$account.id}][owner]" value="{$profile->dtaInformation[{$account.id}].owner}">
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control" placeholder="Verwendungszweck z.B Ihr Einkauf bei..." name="profile[dtaInformation][{$account.id}][usagePrepend]" value="{$profile->dtaInformation[{$account.id}].usagePrepend}">
                                                    </div>
                                                </div>

                                            {/foreach}
                                            <div class="row top10">
                                            <div class="col-sm-3">
                                                <input type="submit" name="saveAccount[{$key}]" value="Kontoeinstellungen speichern" class="btn btn-success ">
                                            </div> <div class="col-sm-3">
                                                <input type="submit" name="deleteAccount[{$accountData.id}]" value="Bankverbindung löschen" class="btn btn-danger ">
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    {/if}

                </div>
                {/if}
            </div>
        </div>
    </form>
</div>
