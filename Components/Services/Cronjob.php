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

namespace VOPDebitConnect\Components;

class Cronjob
{
    private $logger;

    public function __construct()
    {
        //  $this->logger = $logger;
    }

    public function getCronjobTask($View)
    {
        try {
            if ($View == null) {
                return 'Missing Smarty';
            }
            require_once __DIR__ . '/../../Components/DebitConnect/inc/DebitConnectCore.php';
            /** @var \DebitConnectCore $core */
            $core = new \DebitConnectCore(null);
            $core->init(null);
            $core->smarty = $View;
            $core->cronJob->doTasks();

            return $core->cronJob->logEntry;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
