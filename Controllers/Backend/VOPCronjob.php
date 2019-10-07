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

use Shopware\Components\CSRFWhitelistAware;

/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Shopware_Controllers_Backend_VOPCronjob extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    public function indexAction()
    {

        $this->get('plugin_manager')->Controller()->ViewRenderer()->setNoRender();
        $cronTask = $this->get('vopdebitconnect.runcronjob');
        echo $cronTask->getCronjobTask($this->View());
    }

    public function getWhitelistedCSRFActions()
    {
        return ['index'];
    }
}
