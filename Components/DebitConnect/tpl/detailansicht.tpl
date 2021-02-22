<a href='VOPDebitConnect?switchTo={$smarty.get.back}' class='btn btn-danger'>Zurück zur Übersicht</a> <a class='btn btn-info' onclick="$('.nachrichtsb').css('display','block');">Nachricht an Sachbearbeiter</a>
<div class='nachrichtsb' style='display:none;'>
<h4>Nachricht an Sachbearbeiter</h4>
<form method="post">
<input type="text" class='form-control' required name="nachrichtsb"><input type="submit" class='btn btn-info' name="insertmsg" value='{$schuldner->sachb} Kontaktieren'>
</form>
</div>
<div class="row">
    <div class="col-sm-4">
        <div class='box-group'>
        <table class="full">
        <thead>
            <tr>
             <th colspan="2"><h5>Schuldnerdaten {$schuldner->az}</h5></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2">{$schuldner->vorname} {$schuldner->name}</td>
            </tr>
            <tr>
              <td colspan="2">{$schuldner->name2}</td>
            </tr>
            <tr>
              <td colspan="2">{$schuldner->strasse}</td>
            </tr>
            <tr><td colspan="2">{$schuldner->plzneu} {$schuldner->ortu}</td></tr>
            <tr>
                <td colspan="2">Telefon: {$schuldner->telefon} Telefax: {$schuldner->telefax}</td>
            </tr>
            <tr><td colspan="2">Mobil: {$schuldner->mobil} E-Mail: {$schuldner->mail}</td></tr>
            <tr> <td colspan="2">Abgabe EV:  {$vbdaten->evdatum}</td></tr>
        <tr><td colspan="2">Titelinformationen : {$vbdaten->text}</td></tr>
        </tbody>
        </table></div>
    </div>
    <div class="col-sm-8">
        <div class='box-group'>
    <table width='100%'>
    <thead><th colspan="4"><h5>Forderungskonto</h5></th></thead>
    <tbody>
      <tr>
        <td></td>
        <td>Soll</td>
        <td>Haben</td>
        <td>Saldo</td>
      </tr>
      <tr>
        <td>Hauptforderung</td>
        <td>{$fkto.hauptforderung.soll}</td>
        <td>{$fkto.hauptforderung.haben}</td>
        <td>{$fkto.hauptforderung.saldo}</td>
      </tr>
      <tr>
        <td>Zinsen</td>
        <td>{$fkto.zinsen.soll}</td>
        <td>{$fkto.zinsen.haben}</td>
        <td>{$fkto.zinsen.saldo}</td>
      </tr>
      <tr>
        <td>RA/Gerichtskosten</td>
        <td>{$fkto.ra.soll}</td>
        <td>{$fkto.ra.haben}</td>
        <td>{$fkto.ra.saldo}</td>
      </tr>
      <tr>
        <td>Kosten</td>
        <td>{$fkto.kosten.soll}</td>
        <td>{$fkto.kosten.haben}</td>
        <td>{$fkto.kosten.saldo}</td>
      </tr>
      <tr>
        <td>Salden</td>
        <td><strong>{$fkto.salden.soll}</strong></td>
        <td><strong>{$fkto.salden.haben}</strong></td>
        <td><strong>{$fkto.salden.saldo}</strong></td>
      </tr>
    </table>
    </div>
    </div>
</div>
<div class='box-group'>
<table width='100%'>
<thead><th colspan="3"><h5>Aktenlebenslauf</h5></th></thead>
 
     
     {foreach from=$lea item=leaitem}
     <tr>
        <td>{if $leaitem->Doc != ''}<a style='cursor:pointer' class='fancyboxfullscreen' data-fancybox-href='VOPDebitConnect?switchTo=leadoc&fancy=1&doctype=pdf&doc={$leaitem->Doc}'><img src='/engine/Shopware/Plugins/Community/Backend/VOPDebitConnect/Views/backend/_resources/img/file-pdf.png'></a>{/if}</td>
        <td><strong>{$leaitem->beginn}</strong></td>
        <td><strong>{$leaitem->login} - {$leaitem->taetigkeit}</strong></td>
  </tr>
      <tr>
        <td></td>
        <td>{$leaitem->beginnzeit}</td>
        <td>{$leaitem->kommentar}</td>
      </tr>
      <tr> <td colspan="3" id='hr'></td></tr>
      {/foreach}
    </table>
</div>