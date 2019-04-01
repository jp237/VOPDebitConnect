<?php

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Shopware\Components\CSRFWhitelistAware;
use Shopware\Models\Article\Repository as ArticleRepo;
use Shopware\Models\Article\SupplierRepository;
use Shopware\Models\Emotion\Repository as EmotionRepo;
use Shopware\Models\Form\Repository as FormRepo;

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
		Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
		$cronTask = Shopware()->Container()->get("vopdebitconnect.runcronjob");
		echo $cronTask->getCronjobTask($this->View());
    }
	

    public function getWhitelistedCSRFActions()
    {
        return ['index'];
    }
}
