
<div class="container">

    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#status">Status</a></li>
        <li ><a data-toggle="tab" href="#config">Grundeinstellungen</a></li>
        <li><a data-toggle="tab" href="#boni">Bonitätsprüfung</a></li>
        <li><a data-toggle="tab" href="#ident">Alters/Identitätsprüfung / Adressvalidierung</a></li>
        <li><a data-toggle="tab" href="#lang">Sprachvariabeln</a></li>
    </ul>

    <div class="tab-content">

        <div class="tab-pane fade in active" id="status">

            <div class="box-group">
                <h5>Status</h5>
                {if $eap_state == 'NOLOGIN'}
                <div class="alert alert-danger">Das BoniGateway ist nicht einsatzbereit (Login)</div>
                {elseif $eap_state=='NOPROJECT'}
                <div class="alert alert-danger">Das BoniGateway ist nicht einsatzbereit (Keine Projekte definiert)</div>
                {elseif $eap_state=='COMERR'}
                <div class="alert alert-danger">Das BoniGateway ist nicht einsatzbereit (Kommunikationsfehler <a href='mailto:support@eaponline.de'>Support</a></div>
                {elseif $eap_state=='LOGIN'}
                <div class="alert alert-success">Das BoniGateway ist mit den folgenden Projekten einsatzbereit</div>
                {/if}
                <div class="row">
                    <div class="col-sm-6"><b>Projekte Privatpersonen</b></div>
                    <div class="col-sm-6"><b>Projekte Firmen</b></div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        {foreach from=$eap_projekte item=projekte}
                            <div class="col-sm-12">{$projekte->bezeichnung}</div>
                        {/foreach}
                    </div>
                    <div class="col-sm-6">
                        {foreach from=$eap_projekteb2b item=projekteb2b}
                        <div class="col-sm-12">{$projekteb2b->bezeichnung}</div>
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade in " id="config">

            <div class='box-group' style="border:none;">
                <h5>Grundeinstellungen</h5>

                <form method="post">
                    <input type="hidden" name="tab" value="config" />
                    <table class='full'>

                        <tr><td colspan="2"> </td></tr>
                        <tr><td align="left" width='35%'>Benutzername</td><td><input type="text" class='form-control' required name="gateway[username]" value='{$gateway.username}' /></td></tr>
                        <tr><td>Passwort</td><td><input type="password"  class='form-control'  required name="gateway[passwd]"  value='{$gateway.passwd}' /></td></tr>
                        <tr><td>Protokollierung</td><td><select required  class='form-control' name="gateway[log]"><option {if $gateway.log == '1'} selected {/if} value='1'>Ja</option><option  {if $gateway.log == '0'} selected {/if}  value='0'>Nein</option></select></td></tr>
                        <tr><td>Fehlerbenachrichtung per Mail</td><td><input  class='form-control' type="text"   value='{$gateway.logmail}' name="gateway[logmail]" /></td></tr>

                        <tr><td colspan="2"><br />
                                <input type='submit' class='btn btn-primary'  value='Konfiguration Speichern' /></td></tr>
                    </table>
                </form>
            </div>
        </div>
        <div class="tab-pane fade"  id="boni">
            <div class='box-group'>
                <form method="post">
                    <input type="hidden" name="tab" value="boni" />

                    <table  class='full' >
                        <tr><td colspan="2"><h5>Bonitätsprüfung</h5></td></tr>
                        <tr><td align="left" width='35%'>Anfrage Privatpersonen</td>
                            <td><select  required  class='form-control' name='gateway[request_b2c]'><option {if $gateway.request_b2c == '0'} selected {/if} value='0'>Prüfen</option><option <option {if $gateway.request_b2c == '1'} selected {/if} value='1'>Sperren</option><option {if $gateway.request_b2c == '2'} selected {/if} value='2'>Freigeben</option></select></td>
                        </tr>
                        <tr><td>Anfrage Firmen</td>
                            <td><select  required  class='form-control' name='gateway[request_b2b]'><option <option {if $gateway.request_b2b == '0'} selected {/if} value='0'>Prüfen</option><option <option {if $gateway.request_b2b == '1'} selected {/if} value='1'>Sperren</option><option {if $gateway.request_b2b == '2'} selected {/if} value='2'>Freigeben</option></select></td>
                        </tr>
                        <tr><td>Geburtsdatum abfragen *B2C</td>
                            <td><select  required  class='form-control' name='gateway[b2c_birthday]'><option <option {if $gateway.b2c_birthday == '0'} selected {/if} value='0'>Nicht abfragen</option><option  {if $gateway.b2c_birthday == '1'} selected {/if}  value='1'>Abfragen</option></select></td>
                        </tr>
                        <tr><td>Anfrage Ausland</td>
                            <td><select  required  class='form-control' name='gateway[request_nonde]'><option <option {if $gateway.request_nonde == '0'} selected {/if} value='0'>Prüfen</option><option  {if $gateway.request_nonde == '1'} selected {/if}  value='1'>Sperren</option><option  {if $gateway.request_nonde == '2'} selected {/if}  value='2'>Freigeben</option></select></td>
                        </tr>
                        <tr><td>Verhalten im Fehlerfall</td>
                            <td><select required  class='form-control'  name='gateway[exceptionhandle]'><option  {if $gateway.exceptionhandle == '0'} selected {/if} value='0'>Zahlungsart(en) sperren</option><option  {if $gateway.exceptionhandle == '1'} selected {/if} value='1'>Zahlungsart(en) freigeben</option></select></td>
                        </tr>
                        <tr><td>Abweichende Lieferadresse sperren</td>
                            <td><select  required  class='form-control' name='gateway[deviant]'><option  {if $gateway.deviant == '0'} selected {/if} value='0'>Nein</option><option  {if $gateway.deviant == '1'} selected {/if}  value='1'>Ja</option></select></td>
                        </tr>
                        </tr>
                        <tr><td>Max. Änderungen am Warenkorb</td>
                            <td>
                                <select  required  class='form-control' name='gateway[cardprotection]'>
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
                                    <select  required class='form-control'  name='gateway[boni_payments][{$payment.id}]'>
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
                                    <select  required class='form-control'  name='gateway[boni_customergroup][{$customergroup.id}]'>
                                        <option {if $customergroup.conf_value == '0'} selected {/if} value="0">Nicht ausschließen</option>
                                        <option  {if $customergroup.conf_value == '1'} selected {/if} value='1'>Ausschließen</option>
                                    </select>
                                </td></tr>
                        {/foreach}
                        </tbody>
                    </table>
                    <br />
                    <input type='submit' class='btn btn-primary'  value='Konfiguration Speichern' />
                </form>
            </div>
        </div>
        <div class="tab-pane fade" id="ident">
            <form method="post">
                <input type="hidden" name="tab" value="ident" />
                <div class='box-group'>

                    <table class='full' >
                        <tr><td colspan="2"><h5>Einstellungen Alters-/Identitätsprüfung / Adressvalidierung</h5></td></tr>
                        <tr><td align="left" width='35%'>Altersprüfung verwenden</td>
                            <td><select  required class='form-control'  name='gateway[ident]'><option  {if $gateway.ident == '0'} selected {/if} value='0'>Nein</option><option {if $gateway.ident == '1'} selected {/if}  value='1'>QBIT</option></select></td>
                        </tr>
                        <tr><td>Art der Abfrage</td>
                            <td><select  required class='form-control'  name='gateway[ident_art]'><option {if $gateway.ident_art == '0'} selected {/if} value='0'>Immer</option><option {if $gateway.ident_art == '1'} selected {/if} value='1'>Artikelattribut</option></select></td>
                        </tr>
                        <tr><td>Neuprüfung bei Adressänderung</td>
                            <td><select  required class='form-control'  name='gateway[ident_recheck_address]'><option {if $gateway.ident_recheck_address == '0'} selected {/if} value='0'>Nein</option><option  {if $gateway.ident_recheck_address == '1'} selected {/if} value='1'>Ja</option></select></td>
                        </tr>
                        <tr><td>Artikelattribut</td>
                            <td><input  class='form-control' type='text' value='{$gateway.ident_attribute}' name='gateway[ident_attribute]'></td>
                    </table>

                    <table class='full' >
                        <tr><td colspan="2"><b>Versandarten</b></td></tr>
                        <tbody>
                        {foreach from=$shipping item=ship}
                            <tr><td td width='35%'>{$ship.name}</td><td>
                                    <select  required class='form-control'  name='gateway[ident_shipping][{$ship.id}]'><option {if $ship.conf_value == '0'} selected {/if} value="0">Nicht Sperren</option><option {if $ship.conf_value == '1'} selected {/if} value='1'>Sperren</option></select></td></tr>
                        {/foreach}
                        </tbody>
                    </table>

                    <table class='full' >
                        <tr><td colspan="2"><b>Kundengruppen</b></td></tr>
                        <tbody>
                        {foreach from=$customergroups.ident item=customergroup}
                            <tr><td td width='35%'>{$customergroup.description}</td><td>
                                    <select  required  class='form-control'  name='gateway[ident_customergroup][{$customergroup.id}]'>
                                        <option {if $customergroup.conf_value == '0'} selected {/if} value="0">Nicht ausschließen</option>
                                        <option  {if $customergroup.conf_value == '1'} selected {/if} value='1'>Ausschließen</option>
                                    </select>
                                </td></tr>
                        {/foreach}
                        <tr><td>Verschiebe in Kundengruppe</td><td> <select required  class='form-control'  name='gateway[ident_moveto]'>
                                    <option {if $gateway.ident_moveto} selected {/if} value="dontmove">Nicht verschieben</option>
                                    {foreach from=$customergroups.ident item=customergroup}
                                        <option  {if $gateway.ident_moveto == $customergroup.groupkey} selected {/if} value='{$customergroup.groupkey}'>{$customergroup.description}</option>
                                    {/foreach}
                                </select></td></tr>
                        <tr><td><b>Adressvalidierung ( Postdirekt )</b></td>
                            <td><select  required class='form-control'  name='gateway[postdirekt]'><option {if $gateway.postdirekt == '0'} selected {/if} value='0'>nicht verwenden</option><option {if $gateway.postdirekt == '1'} selected {/if} value='1'>Postdirekt verwenden</option></select></td>
                        </tr>
                        <tr><td>Adressvalidierung (Neukunde)</td><td>
                                <select  required  class='form-control' name='gateway[adressvalidierung_enabled]'>
                                    <option {if $gateway.adressvalidierung_enabled == '0'} selected {/if} value="0">Nicht verwenden</option>
                                    <option {if $gateway.adressvalidierung_enabled == '1'} selected {/if} value="1">Postdirekt verwenden</option>
                                </select></td></tr>
                        <tr><td>Personendaten korrigieren</td><td>
                                <select  required  class='form-control' name='gateway[adressvalidierung_personendaten]'>
                                    <option {if $gateway.adressvalidierung_personendaten == '0'} selected {/if} value="0">Nicht korrigieren</option>
                                    <option {if $gateway.adressvalidierung_personendaten == '1'} selected {/if} value="1">Korrigieren</option>
                                </select></td></tr>
                        </tbody>
                    </table>
                    <br />
                    <input type='submit' class='btn btn-primary'  value='Konfiguration Speichern' />
            </form>
        </div>
        </div>
        <div class="tab-pane fade" id="lang">
            <form method="post">
                <input type="hidden" name="tab" value="lang" />
                <div class='box-group'>
                    <table  class='full' >
                        <tr><td colspan="2"><h5>Einstellungen Sprachvariablen</h5></td></tr>
                        {foreach from=$lang item=sprache}
                            <tr><td colspan="2"><b title='{$sprache.key}' style="padding-top:10px">{$sprache.comment}</b></td></tr>
                            <tr>
                                <td colspan="2"><input  type='text' class='form-control' required name='lang[{$sprache.key}]' value='{$sprache.value}'/></td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                    <br />
                    <input type='submit' class='btn btn-primary'  value='Konfiguration Speichern' />
            </form>
        </div>
    </div>
</div>

