<?php

class BuchungsPos
{
    public $zugeordnet;
    public $pkOrder;
    public $fWert;
    public $fWertOrig;
    public $RechnungsNr;
    public $AuftragNr;
    public $RechnungsBetrag;
    public $Offen;
    public $Zahlbetrag = '0.00';
    public $Ueberzahlung = '0.00';
    public $skonto = '0.00';
    public $mahnkosten = '0.00';
    public $richtung;
    public $bankruecklastkosten = '0.00';
    public $bankruecklast = '0.00';
    public $erstattung = '0.00';
    public $gutschrift = '0.00';
    public $verbucht = false;
    public $bestellung;
    public $steuererstattung = '0.00';

    public $matchedfirma = null;
    public $matchedfirstname = null;
    public $matchedlastname = null;
    public $matchedrechnungsnr = null;
    public $matchedauftragsnr = null;
    public $matchedbetrag = null;
    public $matchedskonto = null;
    public $matchedkundennr = null;
    public $matchedvalue = 0;
    public $vopUmsatz = false;
    public $vopBeleg = null;

    public function __construct($pkOrder, $zugeordnet, $umsatz, $bestellung, $matchBetrag, $matchedvalue, $vopUmsatz = false, $beleg = null)
    {
        $zahlung = number_format(str_replace('-', '', $umsatz['fWert']), 2, '.', '');
        $this->richtung = preg_match('/-/', $umsatz['fWert']) ? '-' : '+';
        $this->fWert = number_format($zahlung, 2, '.', '');
        $this->Offen = number_format($bestellung['offen'], 2, '.', '');
        $this->Zahlbetrag = number_format($matchBetrag['fWert'], 2, '.', '');
        if ($this->richtung == '+' && $this->Offen < 0 && !$vopUmsatz) {
            $this->Zahlbetrag = '0.00';
        }
        if ($matchBetrag['mahngeb'] > 0 || $matchBetrag['mahngeb'] < 0) {
            $this->mahnkosten = number_format($matchBetrag['mahngeb'], 2, '.', '');
        }
        if ($matchBetrag['skonto'] > 0) {
            $this->skonto = number_format($matchBetrag['skonto'], 2, '.', '');
        }
        if ($matchBetrag['bankruecklast'] > 0) {
            $this->bankruecklast = number_format($matchBetrag['bankruecklast'], 2, '.', '');
        }
        if ($matchBetrag['bankruecklastkosten'] > 0) {
            $this->bankruecklastkosten = number_format($matchBetrag['bankruecklastkosten'], 2, '.', '');
        }
        if ($matchBetrag['gutschrift'] > 0) {
            $this->gutschrift = number_format($matchBetrag['gutschrift'], 2, '.', '');
        }
        if ($matchBetrag['erstattung'] > 0) {
            $this->erstattung = number_format($matchBetrag['erstattung'], 2, '.', '');
        }
        if ($matchBetrag['steuererstattung'] > 0) {
            $this->steuererstattung = number_format($matchBetrag['steuererstattung'], 2, '.', '');
        }
        $this->fWertOrig = number_format($this->fWert, 2, '.', '');
        $this->pkOrder = $pkOrder;
        $this->zugeordnet = $zugeordnet;
        $this->bestellung = $bestellung;
        $this->matchedvalue = $matchedvalue;
        if ($beleg != null) {
            $this->beleg = $beleg;
        }
        $this->vopUmsatz = $vopUmsatz;
    }
}