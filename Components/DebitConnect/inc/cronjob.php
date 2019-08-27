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

class DebitConnect_Cronjob
{
    public $logEntry;
    public $mailer;

    public function Log($step, $result, $json, $isError = 0, $pkOrder = 0)
    {
        $entry = new stdClass();
        $entry->bIserror = $isError;
        $entry->dAction = date('Y-m-d H:i:s');
        $entry->cStep = $step;
        $entry->pkOrder = $pkOrder;
        $entry->cResult = $result;
        if ($json != null) {
            $entry->jResult = json_encode($json);
        }
        DC()->db->dbInsert('dc_cronlog', $entry);
    }

    public function doTasks($jsonOutput = false)
    {
        $this->logEntry = '';
        DC()->db->dbQuery('delete from dc_cronlog where dAction < DATE_SUB(NOW(),INTERVAL 1 WEEK) ');

        try {
            if (DC()->getUpdate()) {
                $this->logEntry = 'Update Required';

                return;
            }
            ini_set('display_error', false);
            if ($jsonOutput) {
                ini_set('display_error', false);
                header('Content-Type: application/json');
            }
            $status = [];
            $syncLast = true;
            $entry = new stdClass();

            foreach (DC()->db->getSQLResults('SELECT shopID from dc_firma where activated = 1') as $shop) {
                $firma = $shop['shopID'];
                DC()->getSettings($shop['shopID']);
                DC()->getSyncList(true);
                $copySynclist = DC()->syncList;
                $status['syncdata']['count'] = count($copySynclist);
                foreach ($copySynclist as $syncitem) {
                    $syncResult = DC()->doSync();
                    $status['syncdata'][$syncitem['pkOrderAuftrag']] = $syncResult;
                    $this->Log('Sync', 'Auftrag Synchronisiert', $syncResult, $syncResult['error'] ? 1 : 0, $syncitem['pkOrderAuftrag']);
                }

                $cronjobLimit = DC()->getConf('hbci_cron_limit', 30, true);

                if ($_SERVER['REMOTE_ADDR'] == '62.225.158.106' || DC()->hasvalue('debug')) {
                    ini_set('display_errors', true);
                    //echo "debug";
                }
                $hbciProfiles = DC()->settings->hbciProfiles;
                $firstRun = new DateTime(date('Y-m-d H:i:s'));
                $firstRun->modify('-3 hour');
                $lastRun = DC()->getConf('cronjobHBCI', $firstRun->format('Y-m-d H:i:s'), true);
                $diff = strtotime(date('Y-m-d H:i:s')) - strtotime($lastRun);
                $TimeNewRequest = 10800;

                if (count($hbciProfiles) > 0 && DC()->settings->cronjob->zahlungsabgleich > 0) {
                    if ($diff >= $TimeNewRequest) {
                        // JUST CALL EVERY 3 HOURS THE BANK REQUEST
                        foreach ($hbciProfiles as $profile) {
                            // KONTENABRUF
                            try {
                                foreach ($profile->profileData->konto as $konto) {
                                    if ($konto->enabled == 1) {
                                        $from = new DateTime(date('d.m.Y'));
                                        $from->modify('-7 day');
                                        $to = new DateTime(date('d.m.Y'));
                                        DC()->hbci->abrufUmsatz($profile->id, $konto->IBAN, $from, $to);
                                        $status[$firma]['imported'] = $status[$firma]['imported'] + DC()->hbci->imported;
                                    }
                                }
                            } catch (Exception $e) {
                                DC()->Log('HBCI', $e->getMessage(), 10, 0);
                            }
                        }
                        DC()->setConf('cronjobHBCI', date('Y-m-d H:i:s'), true);
                    }
                    DC()->hbci->flushdata();
                    DC()->hbci->UmsaetzeFromDB(true);

                    DC()->dataTypes->getZahlungsabgleichBestellungen();
                    DC()->hbci->getMatching(0, true);

                    DC()->hbci->writeBackUmsatz();

                    // GET COUNT ALL REMAINING
                    DC()->hbci->UmsaetzeFromDB();
                    $status[$firma]['entrys'] = DC()->hbci->entrys;
                    $status[$firma]['fullPayed'] = DC()->hbci->payed;
                    $status[$firma]['countpos'] = DC()->hbci->verbucht;
                    if (DC()->hbci->verbucht > 0) {
                        $syncLast = false;
                    }
                    $status[$firma]['sumcount'] = DC()->hbci->verbuchtsum;
                }
                $oposList = DC()->dataTypes->getOPOSList(false,null,DC()->settings->currentStates,null,0
                                                                                    ,DC()->settings->currentSetting->fristZE,null
                                                                                    ,DC()->settings->cronjob->ze->kundengruppe,DC()->settings->cronjob->ze->minvalue,
                                                                                    [],
                                                                                    true,
                                                                                    DC()->settings->cronjob->ze->withoutstate);

                $oposData = DC()->db->getSQLResults($oposList['query']);
                $status[$firma]['zahlungserinnerung']['list'] = count($oposData);
                if (!DC()->settings->cronjob->ze->active > 0) {
                    $status[$firma]['zahlungserinnerung']['notactive'] = true;
                } else {
                    if (count($oposData) > 0) {
                        foreach ($oposData as $row) {
                            foreach ($row as $key => $pkOrder) {
                                if (DC()->sendZahlungserinnerung($pkOrder, false)) {
                                    $status[$firma]['zahlungserinnerung']['send'][] = $pkOrder;
                                }
                                // BREAK JUST TO GET THE FIRST COLUMN
                                break;
                            }
                        }
                    }
                }

                $oposList = DC()->dataTypes->getOPOSList(false, null, DC()->settings->currentSetting->statusZE, null, 39, DC()->settings->currentSetting->fristMA, null, DC()->settings->cronjob->ma->kundengruppe, DC()->settings->cronjob->ma->minvalue, [], true, null);
                $oposData = DC()->db->getSQLResults($oposList['query']);
                $status[$firma]['mahnung']['list'] = count($oposData);
                if (!DC()->settings->cronjob->ma->active > 0) {
                    $status[$firma]['mahnung']['notactive'] = true;
                } else {
                    if (count($oposData) > 0) {
                        foreach ($oposData as $row) {
                            foreach ($row as $key => $pkOrder) {
                                if (DC()->sendMahnung($pkOrder)) {
                                    $status[$firma]['mahnung']['send'][] = $pkOrder;
                                }
                                // BREAK JUST TO GET THE FIRST COLUMN
                                break;
                            }
                        }
                    }
                }
            }

            $this->logEntry = 'Cronjob Running ' . date('d.m.Y H:i:s');
            $this->Log('End', $this->logEntry, $status);
            $status['status'] = 'ok';
        } catch (Exception $e) {
            $this->Log('End', print_r($e->getMessage()), $status, 1);
            $status = ['error' => $e->getMessage()];
            $this->logEntry = 'Error:' . $e->getMessage() . ' ' . date('d.m.Y H:i:s');
        }
        if ($jsonOutput) {
            echo json_encode($status);
            exit;
        }
    }
}
