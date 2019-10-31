<?php
/**
 * EAP-DebitConnect (shopware Edition)
 *
 * V.O.P GmbH & Co. KG
 * Hauptstraße 62
 * 56745 Bell
 * Telefon: +49 (2652) 529-0
 * Telefax: +49 (2652) 529-129
 * E-Mail: info@eaponline.de
 * USt-IdNr.: DE 261 538 563
 * Pers. Haft. Gesellschafter:
 * V.O.P Verwaltungs GmbH, HRB 21231, Koblenz
 * Geschäftsführer: Thomas Pütz
 * Handelsregister HRA20499, Koblenz
 */

use Fhp\FinTs;
use Fhp\Model\StatementOfAccount\Statement;
use Fhp\Model\StatementOfAccount\Transaction;

class HBCI_MODULE
{
    /** @var FinTs */
    public $hbci;
    public $umsaetze;
    public $matches;
    public $bestellungen;
    public $imported = 0;
    public $payed = [];
    public $verbucht = 0;
    public $verbuchtsum = 0;
    public $halfPayed = [];
    public $entrys = 0;
    public $skontoSKR = [];
    public $selectedProfile;
    public $matchedDTA = null;

    /** @var \Psr\Log\VOPLogger */
    public $logger;

    public function __construct()
    {
        $this->logger = new \Psr\Log\VOPLogger();
    }

    public function setHBCIData($url, $blz, $alias, $pin)
    {

        $this->logger = new \Psr\Log\VOPLogger();

        $this->hbci = new FinTs($url, 443, $blz, $alias, $pin,$this->logger);
    }


    public function initProfileById($id)
    {
        foreach (DC()->settings->hbciProfiles as $profileObject) {
            if ($profileObject->id == $id) {
                $this->setHBCIProfile($profileObject);
            }
        }
    }

    public function setHBCIProfile($profileObject)
    {
        if (is_object($profileObject)) {
            $this->hbci = new FinTS($profileObject->profileData->url, 443, $profileObject->profileData->blz, $profileObject->profileData->alias, $profileObject->profileData->pin,$this->logger);
            $this->selectedProfile = $profileObject;
        } else {
            $this->hbci = null;
            $this->selectedProfile = null;
        }
    }

    public function getnTypeBuchungPos($input)
    {
        $values = [0 => 'Rechnungsbetrag', 1 => 'Mahnkosten', 2 => 'Skonto', 3 => 'Überzahlung', 4 => 'Erstattung', 5 => 'Bankrücklastkosten', 7 => 'Bankrückbelastung', 8 => 'Gutschrift'];

        return $values[$input];
    }

    public function getVWZ($iban)
    {
        if (count($this->selectedProfile->profileData->konto) > 0) {
            foreach ($this->selectedProfile->profileData->konto as $kontoItem) {
                if ($kontoItem->IBAN == $iban) {
                    return $kontoItem->VWZ;
                }
            }
        }

        return '';
    }

    public function getOwner($iban)
    {
        if (count($this->selectedProfile->profileData->konto) > 0) {
            foreach ($this->selectedProfile->profileData->konto as $kontoItem) {
                if ($kontoItem->IBAN == $iban) {
                    return $kontoItem->OWNER;
                }
            }
        }

        return '';
    }

    public function IBANActive($iban)
    {
        if (count($this->selectedProfile->profileData->konto) > 0) {
            foreach ($this->selectedProfile->profileData->konto as $kontoItem) {
                if ($kontoItem->enabled == 1 && $kontoItem->IBAN == $iban) {
                    return true;
                }
            }
        }

        return false;
    }

    public function checkPaymentMahngebuehr($pkOrder)
    {
        $mahngeb = 0.00;
        $rs = DC()->db->singleResult('SELECT SUM(fWert) mahngeb from dc_tzahlung where nType = 1 and pkOrder = ' . (int) $pkOrder);

        return number_format($rs['mahngeb'], 2, '.', '');
    }

    public function getPaymentDetail($pkOrder = 0)
    {
        if ($pkOrder > 0) {
            $query = 'SELECT dc_tzahlung.fWert , dc_tzahlung.nType,IdKonto,cVzweck,dc_tzahlung.kUmsatz,DATE(dc_umsatz.dBuchung) as dateBuchung from dc_tzahlung left join dc_umsatz on dc_tzahlung.kUmsatz = dc_umsatz.kUmsatz where dc_tzahlung.nType != 1 AND dc_tzahlung.pkOrder = ' . (int) $pkOrder . ' ';
        }

        $rs = DC()->db->getSQLResults($query);
        $ret = [];
        if (count($rs) > 0) {
            foreach ($rs as $row) {
                $dateBuchung = new DateTime($row['dateBuchung']);
                //$row["fWert"] = str_replace($row["fWert"],".",",");
                $row['nType'] = $this->getnTypeBuchungPos($row['nType']);
                $ret[$row['kUmsatz']]['pos'][] = $row;
                $ret[$row['kUmsatz']]['date'] = $dateBuchung->format('d.m.Y');
                $ret[$row['kUmsatz']]['cVzweck'] = $row['cVzweck'];
                $ret[$row['kUmsatz']]['IdKonto'] = $row['IdKonto'];
                $ret[$row['kUmsatz']]['kUmsatz'] = $row['kUmsatz'];
            }
        }

        if (DC()->dataTypes->PickwarePaymentEnabled()) {
            // IMPORTING PICKWARE ENTRYS
            $rs = DC()->db->getSQLResults('SELECT *,amount as fWert from s_plugin_viison_bank_transfer_matching_booking where orderId = ' . $pkOrder);

            foreach ($rs as $row) {
                $dateBuchung = new DateTime($row['creationDate']);
                $row['nType'] = $this->getnTypeBuchungPos($row['nType']);
                $ret[$row['kUmsatz']]['pos'][] = $row;
                $ret[$row['kUmsatz']]['date'] = $dateBuchung->format('d.m.Y');
                $ret[$row['kUmsatz']]['cVzweck'] = 'Pickware Buchung ' . $row['type'] . ' ' . $row['comment'];
                $ret[$row['kUmsatz']]['IdKonto'] = 'Pickware';
                $ret[$row['kUmsatz']]['kUmsatz'] = $row['id'] * -1;
            }
        }

        return $ret;
    }

    public function removeUmsatz($kUmsatz)
    {
        $update = new stdClass();
        $update->dAbgleich = null;
        $update->nNichtverbuchen = 0;
        $update->nVerbucht = 0;
        if (DC()->dataTypes->removePayment($kUmsatz)) {
            DC()->db->dbUpdate('dc_umsatz', $update, 'kUmsatz = ' . (int) $kUmsatz);
            $updateBeleg = new stdClass();
            $updateBeleg->dGebucht = null;
            $updateBeleg->kUmsatz = null;
            DC()->db->dbUpdate('dc_rechnung', $updateBeleg, 'kUmsatz = ' . (int) $kUmsatz);
            $updateDTA = new stdClass();
            $updateDTA->kUmsatz = 0;
            DC()->db->dbUpdate('dc_dtacreatelog', $updateDTA, 'kUmsatz = ' . (int) $kUmsatz);
        }
    }

    public function getCountPaymentsReady()
    {
        $count = 0;
        if (count($this->matches) > 0) {
            foreach ($this->matches as $kUmsatz => $match) {
                if ($match['verbuchen'] && !$match['verbucht']) {
                    ++$count;
                }
            }
        }

        return $count;
    }

    public function matchDTA($kUmsatz, $idDta)
    {
        if ($this->matchedDTA == null) {
            $this->matchedDTA = [];
        }
        $this->matchedDTA[$kUmsatz] = $idDta;
    }

    public function writeBackUmsatz($ajaxwriteback = false)
    {
        if (count($this->matches) > 0) {
            foreach ($this->matches as $kUmsatz => $match) {
                if ($match['verbuchen'] && !$match['verbucht']) {
                    $umsatz = $this->umsaetze[$kUmsatz];
                    $dbBuchung = [];
                    $verbucht = false;
                    $belegeArray = [];
                    foreach ($match['pos'] as $buchungsPos) {
                        if ($buchungsPos->zugeordnet) {
                            if ($buchungsPos->bestellung['vopstatus'] == 1) {
                                $updateSyncTab = new stdClass();
                                $updateSyncTab->lastSync = 0;
                                DC()->db->dbUpdate('dc_status', $updateSyncTab, 'pkOrder = ' . (int) $buchungsPos->pkOrder);
                            }

                            if ($buchungsPos->vopUmsatz && $buchungsPos->beleg != null) {
                                $belegeArray[] = $buchungsPos->beleg;
                            }

                            if ($buchungsPos->Zahlbetrag > 0 || ($buchungsPos->vopUmsatz && $buchungsPos->Zahlbetrag < 0)) {
                                $buchung = new stdClass();
                                $buchung->kUmsatz = $kUmsatz;
                                $buchung->pkOrder = $buchungsPos->pkOrder;
                                $buchung->nType = 0;
                                $buchung->fWert = $buchungsPos->Zahlbetrag;
                                try {
                                    $buchung->cSKR = DC()->settings->SKRSkonto['skr_payment']->{$buchungsPos->bestellung['paymentID']};
                                } catch (Exception $e) {
                                }

                                $dbBuchung[] = $buchung;
                            }
                            if ($buchungsPos->Ueberzahlung > 0) {
                                $buchung = new stdClass();
                                $buchung->kUmsatz = $kUmsatz;
                                $buchung->pkOrder = $buchungsPos->pkOrder;
                                try {
                                    $buchung->cSKR = DC()->settings->SKRSkonto['skr_buchungpos']->ueberzahlung;
                                } catch (Exception $e) {
                                }
                                $buchung->nType = 3;
                                $buchung->fWert = $buchungsPos->Ueberzahlung;
                                $dbBuchung[] = $buchung;
                            }
                            if ($buchungsPos->skonto > 0) {
                                $buchung = new stdClass();
                                $buchung->kUmsatz = $kUmsatz;
                                $buchung->pkOrder = $buchungsPos->pkOrder;
                                try {
                                    $buchung->cSKR = DC()->settings->SKRSkonto['skr_buchungpos']->skontoausgleich;
                                } catch (Exception $e) {
                                }
                                $buchung->nType = 2;
                                $buchung->fWert = $buchungsPos->skonto;
                                $dbBuchung[] = $buchung;
                            }
                            if ($buchungsPos->mahnkosten > 0 || ($buchungsPos->vopUmsatz && $buchungsPos->mahnkosten < 0)) {
                                $buchung = new stdClass();
                                $buchung->kUmsatz = $kUmsatz;
                                $buchung->pkOrder = $buchungsPos->pkOrder;
                                try {
                                    $buchung->cSKR = DC()->settings->SKRSkonto['skr_buchungpos']->mahnkosten;
                                } catch (Exception $e) {
                                }
                                $buchung->nType = 1;
                                $buchung->fWert = $buchungsPos->mahnkosten;
                                $dbBuchung[] = $buchung;
                            }
                            if ($buchungsPos->bankruecklastkosten > 0) {
                                $buchung = new stdClass();
                                $buchung->kUmsatz = $kUmsatz;
                                $buchung->pkOrder = $buchungsPos->pkOrder;
                                try {
                                    $buchung->cSKR = DC()->settings->SKRSkonto['skr_buchungpos']->bankruecklastkosten;
                                } catch (Exception $e) {
                                }
                                $buchung->nType = 5;
                                $buchung->fWert = '-' . $buchungsPos->bankruecklastkosten;
                                $dbBuchung[] = $buchung;
                            }
                            if ($buchungsPos->bankruecklast > 0) {
                                $buchung = new stdClass();
                                $buchung->kUmsatz = $kUmsatz;
                                $buchung->pkOrder = $buchungsPos->pkOrder;
                                try {
                                } catch (Exception $e) {
                                }
                                $buchung->nType = 7;
                                $buchung->fWert = '-' . $buchungsPos->bankruecklast;
                                $dbBuchung[] = $buchung;
                            }
                            if ($buchungsPos->erstattung > 0) {
                                $buchung = new stdClass();
                                $buchung->kUmsatz = $kUmsatz;
                                $buchung->pkOrder = $buchungsPos->pkOrder;
                                try {
                                    $buchung->cSKR = DC()->settings->SKRSkonto['skr_buchungpos']->erstattung;
                                } catch (Exception $e) {
                                }
                                $buchung->nType = 4;
                                $buchung->fWert = '-' . $buchungsPos->erstattung;
                                $dbBuchung[] = $buchung;
                            }
                            if ($buchungsPos->gutschrift > 0) {
                                $buchung = new stdClass();
                                $buchung->kUmsatz = $kUmsatz;
                                $buchung->pkOrder = $buchungsPos->pkOrder;
                                try {
                                    $buchung->cSKR = DC()->settings->SKRSkonto['skr_buchungpos']->gutschrift;
                                } catch (Exception $e) {
                                }
                                $buchung->nType = 8;
                                $buchung->fWert = '-' . $buchungsPos->gutschrift;
                                $dbBuchung[] = $buchung;
                            }
                        }
                    }
                    if (count($dbBuchung) == 0) {
                        return 'finish';
                    }

                    $verbucht = DC()->db->dbInsertList('dc_tzahlung', $dbBuchung, false);
                    if ($verbucht) {
                        if (!$this->checkKomplettBezahlt($umsatz)) {
                            DC()->Log('Zahlungsabgleich', 'Status konnte nicht gesetzt werden');
                            DC()->hbci->matches[$kUmsatz] = null;
                            DC()->db->dbQuery('DELETE from dc_tzahlung where kUmsatz = ' . (int) $kUmsatz);

                            return 'finish';
                        }

                        ++$this->verbucht;
                        $this->verbuchtsum = $this->verbuchtsum + $umsatz['fWert'];
                        $updateUmsatz = new stdClass();
                        $updateUmsatz->nVerbucht = 1;
                        $updateUmsatz->dAbgleich = date('Y-m-d H:i:s');
                        if (DC()->db->dbUpdate('dc_umsatz', $updateUmsatz, 'kUmsatz = ' . (int) $kUmsatz)) {
                            $matchedDTA = $this->matchedDTA[$kUmsatz] == null ? 0 : $this->matchedDTA[$kUmsatz];

                            if ($matchedDTA > 0) {
                                $updateDTA = new stdClass();
                                $updateDTA->kUmsatz = (int) $kUmsatz;
                                if (DC()->db->dbUpdate('dc_dtacreatelog', $updateDTA, 'idTransaktion = ' . $matchedDTA, false)) {
                                    $this->matchedDTA[$kUmsatz] = null;
                                }
                            }

                            $this->matches[$kUmsatz]['verbucht'] = true;
                            $logText = $umsatz['nType'] == '0' ? 'Zahlungseingang' : 'Zahlungsausgang';
                            $logText .= ' ' . $umsatz['cName'] . ' ' . $umsatz['fWert'] . ' EUR';
                            DC()->Log('Zahlungsabgleich', $logText);
                            DC()->hbci->matches[$kUmsatz] = null;
                            if (count($belegeArray) > 0) {
                                // ZUORDNEN DES UMSATZES ZU BELEG
                                foreach ($belegeArray as $beleg) {
                                    $updateBeleg = new stdClass();
                                    $updateBeleg->kUmsatz = $kUmsatz;
                                    $updateBeleg->dGebucht = date('Y-m-d');
                                    DC()->db->dbUpdate('dc_rechnung', $updateBeleg, 'nRechJahr = ' . (int) $beleg['RechJahr'] . ' AND nRechNr = ' . (int) $beleg['RechNr'], false);
                                }
                            }
                        } else {
                            DC()->db->dbQuery('DELETE from dc_tzahlung where kUmsatz = ' . (int) $kUmsatz);
                        }
                    } else {
                        $errormsg = DC()->db->lastError;
                        DC()->Log('InsertBuchungList', 'SQL_ERROR :' . $errormsg, 10);
                        DC()->hbci->matches[$kUmsatz] = null;

                        return 'finish';
                    }// IF VERBUCHT
                    //
                    if ($ajaxwriteback) {
                        return 'next';
                    }
                }
            }
        }

        $this->flushdata();
        if ($ajaxwriteback) {
            return 'finish';
        }
    }

    public function resetKreditLimit($value, $ordernumber, $pkOrder)
    {
        try {
            $usr = DC()->db->singleResult("select `datavalue` from dc_gatewaymeta where art = 'username' and shopID = " . DC()->getShopId());
            $pwd = DC()->db->singleResult("select `datavalue` from dc_gatewaymeta where art = 'passwd' and shopID = " . DC()->getShopId());
            $username = $usr['datavalue'];
            $passwd = md5($pwd['datavalue']);
            if (strlen($username) > 0 && strlen($passwd) == 32 && $passwd != 'd41d8cd98f00b204e9800998ecf8427e') {
                $value = ceil($value);
                $soap = new SoapClient('https://webservice.eaponline.de/webservice.php?wsdl', ['encoding' => 'UTF-8', 'cache_wsdl' => WSDL_CACHE_NONE, 'trace' => 1]);
                $res = $soap->resetKreditLimit($username, $passwd, $ordernumber, $value);
                if ($res) {
                    DC()->Log('Kreditlimit', 'Kreditlimit aktualisiert ' . (int) $value . ' EUR', 0, (int) $pkOrder);
                }
            }
        } catch (Exception $e) {
            DC()->View('API_ERROR', $e);
        }
    }

    public function checkKomplettBezahlt($umsatz)
    {
        foreach (@$this->matches[$umsatz['kUmsatz']]['pos'] as $key => $match) {
            $checkvalue = 0;
            if ($match->zugeordnet) {
                $checkvalue = $checkvalue + $match->Zahlbetrag + $match->mahnkosten + $match->Ueberzahlung + $match->skonto + $match->bankruecklast + $match->bankruecklastkosten + $match->erstattung + $match->gutschrift;
                $payment_submitted = false;
                $orderstatus = null;
                $partialpaymentstatus = null;
                $paymentstatus = null;
                $commentary = '';
                $orderStatusHistory = null;
                $paymentStatusHistory = null;
                $dBuchung = null;

                if ($checkvalue > 0 && $checkvalue < $match->bestellung['offen'] && $match->bestellung['offen'] > 0 && $umsatz['nType'] == 0) {
                    $partialpaymentstatus = DC()->settings->currentHBCI['teilzahlung'];
                    if (!DC()->dataTypes->changeOrder($match->pkOrder, $dBuchung, $paymentstatus, $orderstatus, $commentary, $partialpaymentstatus)) {
                        return false;
                    }

                    $this->writeBackPaymentHistory($umsatz, $match);

                    $this->resetKreditLimit($checkvalue, $match->bestellung['ordernumber'], $match->bestellung['id']);
                    if (!in_array($match->pkOrder, $this->halfPayed)) {
                        $this->halfPayed[] = $match->pkOrder;
                        DC()->sendZahlungseingang($match->pkOrder, 1);
                    }
                } elseif ($checkvalue > 0 && $checkvalue >= $match->bestellung['offen'] && $match->bestellung['offen'] > 0 && $umsatz['nType'] == 0) {
                    // ORDER BESTELLSTATUS BEI VORKASSE ÄNDERN
                    $dBuchung = $umsatz['dBuchung'];
                    $settingOrderStatus = DC()->settings->currentHBCI['orderstatus'];
                    $paymentstatus = DC()->settings->currentHBCI['statusbezahlt'];
                    if ($settingOrderStatus > 0 && in_array($match->bestellung['paymentID'], DC()->settings->currentVorkasse)) {
                        $orderstatus = $settingOrderStatus;
                    }

                    if (!DC()->dataTypes->changeOrder($match->pkOrder, $umsatz['dBuchung'], $paymentstatus, $orderstatus, $commentary, $partialpaymentstatus)) {
                        return false;
                    }

                    $this->writeBackPaymentHistory($umsatz, $match);

                    $this->resetKreditLimit($checkvalue, $match->bestellung['ordernumber'], $match->bestellung['id']);

                    if (!in_array($match->pkOrder, $this->payed)) {
                        $this->payed[] = $match->pkOrder;
                        DC()->sendZahlungseingang($match->pkOrder, 2);
                    }
                } elseif ($umsatz['nType'] == 1 && $match->bankruecklast > 0 && $match->bankruecklastkosten > 0) {
                    $paymentstatus = DC()->settings->currentHBCI['bankruecklast'];
                    if (!DC()->dataTypes->changeOrder($match->pkOrder, null, $paymentstatus, $orderstatus, $commentary, $partialpaymentstatus)) {
                        return false;
                    }

                    $this->writeBackPaymentHistory($umsatz, $match, 5);
                }
            }
        }

        return true;
    }

    public function writeBackPaymentHistory($umsatz, $match, $type = 0)
    {
        if (DC()->settings->currentSetting->shopwareapibenutzen > 0) {
            $update = new stdClass();
            $update->orderStatusHistory = $match->bestellung['orderstatus'];
            $update->paymentStatusHistory = $match->bestellung['paymentstatus'];

            return DC()->db->dbUpdate('dc_tzahlung', $update, 'kUmsatz = ' . (int) $umsatz['kUmsatz'] . ' AND nType = ' . (int) $type . ' AND pkOrder = ' . (int) $match->pkOrder);
        }

        return true;
    }

    public function flushdata()
    {
        $this->umsaetze = null;
        $this->matches = null;
        $this->bestellungen = null;
        $this->payed = [];
        $this->verbucht = 0;
        $this->verbuchtsum = 0;
        $this->halfPayed = [];
        $this->entrys = 0;
        $this->skontoSKR = [];
    }

    public function UmsaetzeFromDB($cronjobMode = false, $cronJobLimit = 30)
    {//cast(dBuchung AS DATE) DESC ,
        $withInterval = '';
        if ($cronjobMode) {
            //	$withInterval = " AND cronDate <= NOW() - INTERVAL 1 DAY ";
        }
        $type = '(nType = 0 or nType = 1 )';
        if (DC()->settings->currentHBCI['zahlungsausgang'] == 1) {
            $type = '(nType = 0)';
        }

        $rs = DC()->db->getSQLResults("SELECT kUmsatz,kShop,IdUmsatz,IdKonto,CAST(dBuchung AS DATE) as dBuchung,cName,cVzweck,cName,CAST(fWert as DECIMAL(12,2)) as fWert,nType,DATE_FORMAT(dBuchung,'%d.%m.%Y') as datum from dc_umsatz
		 where kShop = " . DC()->settings->selectedShop . ' and  nVerbucht = 0 and nNichtverbuchen = 0 and ' . $type . ' ' . $withInterval . ' order by  dBuchung DESC, kUmsatz ');

        $sortedUmsatz = [];
        foreach ($rs as $umsatz) {
            $sortedUmsatz[$umsatz['kUmsatz']] = $umsatz;
            if ($cronjobMode) {
                $dbUpdate = new stdClass();
                $dbUpdate->cronDate = date('Y-m-d H:i:s');
                DC()->db->dbUpdate('dc_umsatz', $dbUpdate, 'kUmsatz = ' . (int) $umsatz['kUmsatz'], false);
            }
        }

        $this->entrys = count($sortedUmsatz);
        $this->umsaetze = $sortedUmsatz;
    }

    public function getZahlungenTemplate($pkOrder)
    {
        return DC()->db->getSQLResults("SELECT CAST(SUM(dc_tzahlung.fWert) as DECIMAL (12,2)) as fWert,DATE_FORMAT(dc_umsatz.dBuchung,'%d.%m.%Y') as datum FROM `dc_tzahlung` LEFT JOIN dc_umsatz on dc_umsatz.kUmsatz = dc_tzahlung.kUmsatz WHERE `pkOrder` = " . (int) $pkOrder . '  group by dc_tzahlung.kUmsatz,dc_umsatz.dBuchung ');
    }

    public function regExPerson($ref, $compare, $punkte, $debug = false, $doublePointsMinStringLength = 0)
    {
        $debug = false; // @todo remove?
        $copy_ref_orgi = $ref;
        if (strlen($ref) < 3) {
            return 0;
        }
        if (strlen($compare) < 3) {
            return 0;
        }

        $matched = 0;
        $ref = strtoupper($ref);
        $compare = strtoupper($compare);
        if ($matched == 0 && $ref == $compare) {
            $matched = $punkte;
        }
        if ($matched == 0 && preg_match("/$compare/", $ref)) {
            $matched = $punkte;
        }
        if ($debug) {
            echo $ref . " <> $compare = $matched<br>";
        }
        $ref = $this->clearchars($ref);
        $compare = $this->clearchars($compare);
        if ($matched == 0 && $ref == $compare) {
            $matched = $punkte;
        }
        if ($matched == 0 && preg_match("/$compare/", $ref)) {
            $matched = $punkte;
        }
        if ($debug) {
            echo $ref . " <> $compare = $matched<br>";
        }
        $regex = DC()->regExList;
        if ($matched == 0 && count($regex) > 0) {
            foreach ($regex as $reg) {
                $copyref = $ref;
                $copyref = preg_replace('/' . $reg[0] . '/i', $reg[1], $copyref);

                if (preg_match("/$compare/", $copyref)) {
                    $matched = $punkte;
                }
                if ($debug) {
                    echo 'REGEX >>>> ' . $copyref . " << $compare = $matched <br>";
                }
            }
        }

        if ($matched > 0 & $doublePointsMinStringLength > 0 && strlen($compare) == $doublePointsMinStringLength) {
            $matched = $matched * 2;
        }

        return $matched;
    }

    public function clearchars($inputstring)
    {
        $inputstring = str_replace(' ', '', $inputstring);
        $inputstring = str_replace('-', '', $inputstring);
        $inputstring = str_replace('.', '', $inputstring);
        $inputstring = str_replace('_', '', $inputstring);
        $inputstring = str_replace('Ö', 'OE', $inputstring);
        $inputstring = str_replace('&', 'UND', $inputstring);
        $inputstring = str_replace('Ä', 'AE', $inputstring);
        $inputstring = str_replace('Ü', 'UE', $inputstring);

        return $inputstring;
    }

    public function regExAuftragData($ref, $compare, $debug = false)
    {
    }

    public function matchBetrag($umsatz, $bestellung, $punkte = 5)
    {
        $return = [];
        $return['fWert'] = '0.00';
        $skonto = false;
        if ($umsatz['nType'] == 1) {
            $fWertPositiv = number_format($umsatz['fWert'] * -1, 2, '.', '');

            if ($fWertPositiv == $bestellung['Gutschriftbetrag']) {
                $return['gutschrift'] = $fWertPositiv;
                $return['punkte'] = $punkte;
            } elseif ($bestellung['offen'] <= 0 && $fWertPositiv > $bestellung['betrag']) {
                $return['bankruecklast'] = number_format($bestellung['betrag'], 2, '.', '');
                $return['bankruecklastkosten'] = number_format($fWertPositiv - $bestellung['betrag'], 2, '.', '');
                $return['punkte'] = $punkte;
            }
        } elseif ($umsatz['nType'] == 0) {
            try {
                $skontovalue = DC()->settings->SKRSkonto['skonto']->{$bestellung['paymentID']};
                $skontozeitraum = DC()->settings->SKRSkonto['zeitraum']->{$bestellung['paymentID']};
                if ($skontovalue > 0 && $skontozeitraum > 0) {
                    $dtBestellung = new DateTime($bestellung['ordertime']);
                    $dtBestellung->modify('+' . $skontozeitraum . ' day');
                    $now = new DateTime(date('d.m.Y H:i:s'));
                    if ($dtBestellung >= $now) {
                        $skonto = true;
                        $skontobetrag = number_format(($bestellung['offen'] / 100) * $skontovalue, 2, '.', '');
                        $sumSkonto = number_format($bestellung['offen'] - $skontobetrag, 2, '.', '');
                    }
                }
            } catch (Exception $d) {
            }
            /*
            $this->SKRSkonto["skonto"]
            $this->SKRSkonto["zeitraum"] ;
            $this->SKRSkonto["skr_payment"]  ;
            $this->SKRSkonto["skr_buchungpos"]
            */
            if ($umsatz['fWert'] == $bestellung['offen']) {
                $return['punkte'] = $punkte;
                $return['fWert'] = $bestellung['offen'];
            } elseif ($umsatz['fWert'] == $bestellung['offen'] + DC()->settings->currentSetting->mahngeb) {
                $return['punkte'] = $punkte;
                $return['fWert'] = $bestellung['offen'];
                $return['mahngeb'] = DC()->settings->currentSetting->mahngeb;
            } elseif ($skonto && $sumSkonto == $umsatz['fWert']) {
                $return['punkte'] = $punkte;
                $return['fWert'] = $sumSkonto;
                $return['skonto'] = $skontobetrag;
            } elseif ($bestellung['offen'] < $umsatz['fWert']) {
                $return['fWert'] = $bestellung['offen'];
            } elseif ($bestellung['offen'] > $umsatz['fWert']) {
                $return['fWert'] = $umsatz['fWert'];
            }
        } // END IF NTYPE
        //ÜBERZAHLUN GEHT NICHT...
        return $return;
    }

    public function setUmsatzBlackliste($kUmsatz)
    {
        $update = new stdClass();
        $update->nType = 10;

        return DC()->db->dbUpdate('dc_umsatz', $update, 'kUmsatz = ' . (int) $kUmsatz);
    }

    public function addMatching($kUmsatz, $pkOrder)
    {
        $umsatz = $this->umsaetze[$kUmsatz];
        $bestellung = $this->bestellungen[$pkOrder];
        $betrag = $this->matchBetrag($umsatz, $bestellung);
        $this->matches[$umsatz['kUmsatz']]['pos'][$bestellung['id']] = new buchungsPos($bestellung['id'], true, $umsatz, $bestellung, $betrag, 100);
    }

    public function getMatching($ajaxSingleSync = 0, $cronJob = false)
    {
        DC()->dataTypes->getZahlungsabgleichBestellungen();
        $dateNow = new DateTime(date('Y-m-d'));
        $dateSixmonth = $dateNow->modify('- 6month');

        DC()->setIgnoreAbort();
        //DC()->hbci->matches = array();
        $eindeutig = DC()->getConf('matched', 30, true);
        $aehnlich = DC()->getConf('similar', 20, true);
        $count = 0;

        foreach (DC()->hbci->umsaetze as $umsatz) {
            ++$count;
            if ($ajaxSingleSync > 0 && $count < $ajaxSingleSync) {
                // SINGLE SYNC,
                continue;
            } elseif ($ajaxSingleSync > 0 && $count > $ajaxSingleSync) {
                break;
                //BREAK
            }

            if (count(DC()->settings->hbciBlacklist) > 0) {
                // CHECK BLACKLISTE
                $foundBlacklist = false;
                foreach (DC()->settings->hbciBlacklist as $blackListEntry) {
                    if ($blackListEntry->art == 0 && strpos(strtoupper($umsatz['cName']), strtoupper($blackListEntry->cString)) !== false) {
                        $foundBlacklist = true;
                    } elseif ($blackListEntry->art == 1 && strpos(strtoupper($umsatz['cVzweck']), strtoupper($blackListEntry->cString)) !== false) {
                        $foundBlacklist = true;
                    }
                }
                if ($foundBlacklist && $this->setUmsatzBlackliste($umsatz['kUmsatz'])) {
                    if ($cronJob) {
                        continue;
                    }
                    break;
                }
            }

            if ($cronJob && $umsatz['nType'] == 1) {
                // CRONJOB NUR ZAHLUNGSEINGÄGEN... VIELLEICHT SPÄTER
                continue;
            }

            if (strlen($umsatz['cName']) > 0 && strpos(strtoupper($umsatz['cName']), strtoupper('V.O.P')) !== false) {
                // VOP UMSÄTZE NUR MANUELLE BUCHUNG..
                continue;
            }

            $dateUmsatz = new DateTime($umsatz['dBuchung']);

            $matching = 0;
            if ($this->matches[$umsatz['kUmsatz']]['verbucht'] == true) {
                continue;
            }

            $selected = false;
            $this->matches[$umsatz['kUmsatz']]['selected'] = 0;

            foreach (@$this->bestellungen as $bestellung) {
                $dateBestellung = new DateTime(substr($bestellung['ordertime'], 0, 10));

                if ($dateUmsatz < $dateBestellung) {
                    continue;
                }

                if ($bestellung['offen'] <= 0 && $umsatz['nType'] == 0) {
                    continue;
                }

                $firma = 0;
                $nachname = 0;
                $vorname = 0;
                $rechnungnr = 0;
                $auftragnr = 0;
                $betrag = 0;
                $skonto = 0;

                $extraTreffergesamt = 0;
                $personExtrapunkte = 0;
                $matching = 0;
                // PERSONDATEN
                $nachname = $this->regExPerson($umsatz['cVzweck'] . $umsatz['cName'], $bestellung['lastname'], 10, $debugRegex); // @todo fix undefined $debugRegex
                $vorname = $this->regExPerson($umsatz['cVzweck'] . $umsatz['cName'], $bestellung['firstname'], 10, $debugRegex);
                $firma = $this->regExPerson($umsatz['cVzweck'] . $umsatz['cName'], $bestellung['firma'], 10, $debugRegex);
                $kundennr = $this->regExPerson($umsatz['cVzweck'], $bestellung['KundenNr'], 5, $debugRegex, 6);

                // RECHNUNGSDATEN
                $rechnungnr = $this->regExPerson($umsatz['cVzweck'], $bestellung['RechnungsNr'], 20, $debugRegex, 6);
                $auftragnr = $this->regExPerson($umsatz['cVzweck'], $bestellung['ordernumber'], 15, $debugRegex, 6);

                $betrag = $this->matchBetrag($umsatz, $bestellung);

                // ADD ALL MATCHES
                $matching = $matching + $rechnungnr + $auftragnr + $vorname + $nachname + $firma + $kundennr + $betrag['punkte'];

                if ($matching >= $eindeutig) {
                    $this->matches[$umsatz['kUmsatz']]['pos'][$bestellung['id']] = new buchungsPos($bestellung['id'], true, $umsatz, $bestellung, $betrag, $matching);

                    if (!$selected) {
                        $this->matches[$umsatz['kUmsatz']]['selected'] = $bestellung['id'];
                        $selected = true;
                    }
                } elseif ($matching >= $aehnlich) {
                    if (!$selected) {
                        $this->matches[$umsatz['kUmsatz']]['selected'] = $bestellung['id'];
                    }
                    $this->matches[$umsatz['kUmsatz']]['pos'][$bestellung['id']] = new buchungsPos($bestellung['id'], false, $umsatz, $bestellung, $betrag, $matching);
                }
                if ($matching >= $aehnlich) {
                    $this->matches[$umsatz['kUmsatz']]['pos'][$bestellung['id']]->matchedfirma = $firma > 0 ? true : null;
                    $this->matches[$umsatz['kUmsatz']]['pos'][$bestellung['id']]->matchedfirstname = $vorname > 0 ? true : null;
                    $this->matches[$umsatz['kUmsatz']]['pos'][$bestellung['id']]->matchedlastname = $nachname > 0 ? true : null;
                    $this->matches[$umsatz['kUmsatz']]['pos'][$bestellung['id']]->matchedrechnungsnr = $rechnungnr > 0 ? true : null;
                    $this->matches[$umsatz['kUmsatz']]['pos'][$bestellung['id']]->matchedauftragsnr = $auftragnr > 0 ? true : null;
                    $this->matches[$umsatz['kUmsatz']]['pos'][$bestellung['id']]->matchedbetrag = $betrag['punkte'] > 0 ? true : null;
                    $this->matches[$umsatz['kUmsatz']]['pos'][$bestellung['id']]->matchedskonto = $skonto > 0 ? true : null;
                    $this->matches[$umsatz['kUmsatz']]['pos'][$bestellung['id']]->matchedkundennr = $kundennr > 0 ? true : null;
                }
            }
            $sum = $this->returnBuchungsBetrag($umsatz);

            $this->setSumMatches($umsatz);
            if ($sum > 0 && $sum <= $umsatz['fWert'] && $umsatz['fWert'] - $sum == 0) {
                DC()->hbci->matches[$umsatz['kUmsatz']]['verbuchen'] = true;
            }
        }

        return true;
    }

    public function setSumMatches($umsatz)
    {
        $sum = $this->returnBuchungsBetrag($umsatz);
        $this->matches[$umsatz['kUmsatz']]['sum'] = $sum;
        $this->matches[$umsatz['kUmsatz']]['tomuch'] = $sum - $umsatz['fWert'] > 0 ? true : true; // @todo always true??
    }

    public function returnBuchungsBetrag($umsatz)
    {
        $val = 0.00;
        if (isset($this->matches[$umsatz['kUmsatz']]['pos'])) {
            foreach (@$this->matches[$umsatz['kUmsatz']]['pos'] as $key => $match) {
                if ($match->zugeordnet) {
                    $val = $val + $match->Zahlbetrag + $match->mahnkosten + $match->Ueberzahlung + $match->bankruecklast + $match->bankruecklastkosten + $match->erstattung + $match->gutschrift;
                }
            }
        }

        return number_format($val, 2, '.', '');
    }

    public function returnCountBuchungPos($umsatz)
    {
        $val = 0.00;
        $returnArray = ['value' => 0.00, 'class' => null, 'action' => false];

        if (count($this->matches[$umsatz['kUmsatz']]['pos']) > 0) {
            foreach (@$this->matches[$umsatz['kUmsatz']]['pos'] as $key => $match) {
                if ($match->zugeordnet) {
                    $returnArray['value'] = $returnArray['value'] + $match->Zahlbetrag + $match->mahnkosten + $match->Ueberzahlung + $match->bankruecklast + $match->bankruecklastkosten + $match->erstattung + $match->gutschrift;
                }
            }
        }

        $returnArray['value'] = number_format($returnArray['value'], 2, '.', '');
        if (strlen($umsatz['cName']) > 0 && strpos(strtoupper($umsatz['cName']), strtoupper('V.O.P')) !== false) {
            //DONOTHING..
        } elseif ($umsatz['fWert'] != $returnArray['value'] && $umsatz['nType'] == 0) {
            $this->matches[$umsatz['kUmsatz']]['verbuchen'] = false;
            if (number_format($umsatz['fWert'], 2, '.', '') < $returnArray['value']) {
                $returnArray['class'] = 'error';
            } else {
                $returnArray['class'] = 'orange';
            }
        } elseif (($returnArray['value'] != ($umsatz['fWert'] * -1) && $umsatz['nType'] == 1)) {
            $this->matches[$umsatz['kUmsatz']]['verbuchen'] = false;
            if (number_format(($umsatz['fWert'] * -1), 2, '.', '') < $returnArray['value']) {
                $returnArray['class'] = 'error';
            } else {
                $returnArray['class'] = 'orange';
            }
        } elseif (($returnArray['value'] == ($umsatz['fWert'] * -1) && $umsatz['nType'] == 1) || (($umsatz['fWert']) == $returnArray['value'] && $umsatz['nType'] == 0)) {
            $returnArray['class'] = 'success';
            $returnArray['action'] = true;
        }

        return $returnArray;
    }

    public function getCSVList()
    {


        $fileFolder = __DIR__ .'/../CSVImport/';
        $files = scandir($fileFolder);

        $file = [];
        foreach ($files as $filename) {
            if (strtoupper(substr($filename, -3)) == 'CSV') {
                if (!file_exists($fileFolder . $filename) || !is_readable($fileFolder . $filename)) {
                    continue;
                }

                $file[] = $filename;
            }
        }

        return $file;
    }

    public function abrufUmsatzCSV($delimiter = ';', $enclosure = '', $escape = '', $filename)
    {
        $fileFolder = __DIR__ .'/../CSVImport/';
        $fileFolderLog = __DIR__ .'/../CSVImport/Log/';
        //$files = scandir("./CSVImport/");

        if (strlen($filename) > 0) {
            if (strtoupper(substr($filename, -3)) == 'CSV') {
                if (!file_exists($fileFolder . $filename) || !is_readable($fileFolder . $filename)) {
                    return;
                }


                $header = null;
                $data = [];
                if (($handle = fopen($fileFolder . $filename, 'r')) !== false) {
                    while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                        if (!$header) {
                            $header = $row;
                        } else {
                            $_data = array_combine($header, $row);
                            $data[] = $_data;
                        }
                    }
                    fclose($handle);
                }
                $imported = 0;


                foreach ($data as $row) {
                    $insert = new stdClass();
                    $dt = new DateTime($row["dBuchung"]);

                    $insert->dBuchung = $dt->format("Y-m-d");

                    $insert->IdKonto = $row['IdKonto'];
                    $insert->fWert = number_format(str_replace(',', '', $row['fWert']), 2, '.', '');
                    $insert->cVzweck = $row['cVzweck'];
                    $insert->cName = $row['Name'];
                    $insert->kShop = $row['kShop'];
                    $insert->nType = $row['fWert'] > 0 ? 0 : 1;

                    $identity = md5($insert->cVzweck . $insert->cName . $insert->fWert . $insert->dBuchung . $insert->IdKonto);
                    $insert->IdUmsatz = ($identity);

                    if (strlen($insert->dBuchung) > 0) {
                        $checkValue = DC()->db->singleResult(" SELECT count(kUmsatz) as zaehler from dc_umsatz where IdUmsatz = '" . $insert->IdUmsatz . "' and IdKonto = '" . $insert->IdKonto . "'");
                        if ($checkValue['zaehler'] == '0') {
                            if (DC()->db->dbInsert('dc_umsatz', $insert, false)) {
                                ++$imported;
                            }
                        }
                    }
                }
                if ($imported > 0) {
                    rename($fileFolder . $filename, $fileFolderLog . $filename);
                }
            }
        }
    }

    public function abrufUmsatz($kontoId, $from, $to)
    {
        DC()->setIgnoreAbort();

       $profile = DC()->settings->getHBCIProfiles();


       $lastImport = DC()->getConf('lastImportFinapi',"");
        $fints = DC()->finTS($profile);
        if($fints->UpdateBankAccounts($profile,$lastImport)){
            DC()->setConf('lastImportFinapi',date("Y-m-d H:i:s"));
        }

       foreach($profile->bankAccounts as $bank){

           foreach($bank as $kontoItem => $iban){
               if($kontoItem == $kontoId){

                   $daytransActionList = $fints->getAllTransActionsByDate($kontoId,$from,$to);

                   foreach($daytransActionList as $transactionList){

                       if($transactionList == null ) continue;
                       foreach($transactionList->getTransactions() as $transaction){

                           $date = new DateTime($transaction->getBankBookingDate());
                           $date = $date->format("Y-m-d h:i:s");
                           $fWert = number_format($transaction->getAmount(), 2, '.', '');
                           $name = str_replace('@', '', $transaction->getCounterpartName());
                           $vwz = $transaction->getPurpose();

                           if(strlen($vwz) == 0) $vwz =  "--- Kein Verwendungszweck bekannt -- ";

                           $identity = md5($vwz . $name . $fWert . $date . $iban.$transaction->getId());
                           $checkValue = DC()->db->singleResult(" SELECT count(kUmsatz) as zaehler from dc_umsatz where IdUmsatz = '$identity' and IdKonto = '$iban'");

                           $vwz = strpos($vwz, 'ABWA+') !== false ? substr($vwz, 0, strpos($vwz, 'ABWA+')) : $vwz;
                           if ($checkValue['zaehler'] == '0') {
                               ++$this->imported;
                               $insert = new stdClass();
                               $insert->dBuchung = $date;
                               $insert->IdUmsatz = $identity;
                               $insert->IdKonto = $iban;
                               $insert->fWert = number_format(str_replace(',', '', $fWert), 2, '.', '');
                               $insert->cVzweck = $vwz;
                               $insert->cName = $name;
                               $insert->kShop = DC()->settings->selectedShop;
                               $insert->nType = $fWert > 0 ? 0 : 1;
                               DC()->db->dbInsert('dc_umsatz', $insert, false);
                           }

                       }
                   }
                   break;
               }
           }
       }

       /*
        try {
            $this->imported = 0;
            $ret = [];
            $konten = $this->hbci->getSEPAAccounts();

            foreach ($konten as $konto) {
                if ($konto->getIban() == $selectedKonto) {

                    $umsaetze = $this->hbci->getStatementOfAccount($konto, $from, $to);


                    if (DC()->hasvalue('debug')) {
                        print_r($this->logger);
                    }

                    foreach ($umsaetze->getStatements() as $statement) {
                        try {
                            foreach ($statement->getTransactions() as $transaction) {
                                $date = $transaction->getBookingDate()->format('Y-m-d');
                                $fWert = ($transaction->getCreditDebit() == Transaction::CD_DEBIT ? '-' : '') . number_format(floatval($transaction->getAmount()), 2, '.', '');
                                $name = str_replace('@', '', $transaction->getName());
                                $description = $transaction->getDescription1() . ' ' . $transaction->getDescription2();
                                $vwz = explode('SVWZ+', $description);
                                if (DC()->hasvalue('debug')) {
                                    print_r($transaction);
                                }

                                $vwz = explode('SVWZ+', $transaction->getDescription1() . ' ' . $transaction->getDescription2());
                                $vwz = explode('EREF+', $vwz[1]);
                                $vwz = str_replace('@', '', $vwz[0]);
                                if (strlen($vwz) == 0) {
                                    // PRINTING RAW DATA
                                    $vwz = $transaction->getDescription1() . ' ' . $transaction->getDescription2();
                                }

                                $vwz = str_replace('@', '', $vwz);
                                $identity = md5($vwz . $name . $fWert . $date . $selectedKonto);
                                $checkValue = DC()->db->singleResult(" SELECT count(kUmsatz) as zaehler from dc_umsatz where IdUmsatz = '$identity' and IdKonto = '$selectedKonto'");

                                $vwz = strpos($vwz, 'ABWA+') !== false ? substr($vwz, 0, strpos($vwz, 'ABWA+')) : $vwz;
                                if ($checkValue['zaehler'] == '0') {
                                    ++$this->imported;
                                    $insert = new stdClass();
                                    $insert->dBuchung = $date;
                                    $insert->IdUmsatz = $identity;
                                    $insert->IdKonto = $selectedKonto;
                                    $insert->fWert = number_format(str_replace(',', '', $fWert), 2, '.', '');
                                    $insert->cVzweck = $vwz;
                                    $insert->cName = $name;
                                    $insert->kShop = DC()->settings->selectedShop;
                                    $insert->nType = $fWert > 0 ? 0 : 1;
                                    DC()->db->dbInsert('dc_umsatz', $insert, false);
                                }
                            }
                        } catch (Exception $transactionException) {
                            //print_r($transactionException->getMessage());
                        }
                    }
                }
            }
        } catch (Exception $e) {
            DC()->View('ERROR_MSG', '' . $e->getMessage());
            DC()->Log('Umsatzimport', 'Abruf Fehlgeschlagen', $e->getMessage());
            if (DC()->cronJob != null) {
                DC()->cronJob->Log('Umsatzimport', $e->getMessage(), null, 1);
            }
        }
        */

        return $ret;
    }

    public function returnKonten($profileObject)
    {
        $this->setHBCIProfile($profileObject);
        $ret = [];
        $konten = $this->hbci->getSEPAAccounts();
        //DebitConnect_r($this->hbci);

        foreach ($konten as $konto) {
            $bic = $konto->getBic();
            $iban = $konto->getIban();

            $ret[] = ['BIC' => $bic,
                          'IBAN' => $iban,
                          'enabled' => $this->IBANActive($konto->getIban()) ? 1 : 0,
                          'VWZ' => $this->getVWZ($konto->getIban()),
                          'OWNER' => $this->getOwner($konto->getIban()),
                     ];
        }

        return $ret;
    }
}

class buchungsPos
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
