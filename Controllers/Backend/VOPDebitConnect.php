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
class Shopware_Controllers_Backend_VOPDebitConnect extends Enlight_Controller_Action implements CSRFWhitelistAware
{





    public function indexAction()
    {
		

		
		$csrfToken = $this->container->get('BackendSession')->offsetGet('X-CSRF-Token');
        $this->View()->assign([ 'csrfToken' => $csrfToken ]);
		$smarty = $this->View();
		try
		{
			// TRY AUTOLOGIN
			
			$auth = (Shopware()->Container()->get('Auth'));
			$identity = $auth->getIdentity();


		if($identity->id){
			$this->View()->assign('_session' , $identity->sessionID);
			$this->View()->assign( '_user' , $identity->id);
			
			require_once(dirname(__FILE__)."/../../Components/DebitConnect/inc/DebitConnectCore.php");
			$cfg =  new \DebitConnectCore($this->View());

			if(is_object(unserialize(Shopware()->BackendSession()->{$cfg->sessName}))) {
                $cfg = unserialize(Shopware()->BackendSession()->{$cfg->sessName});
            }
			
			$cfg->request = $this->Request();
			$cfg->init($smarty);
			
			
			if(!$cfg->checkInstallation())
			{
				$this->View()->assign(["nomenu" => true]);
				DC()->setSession();
				return;
			}
			$cfg->loginData['logged_in'] = true;
			$cfg->user = $identity->id;

			$usr = array();
			
		
			 if(($cfg->hasvalue('ajaxwritepayments'))){
				$usr['logged_in'] = $cfg->checkLogin();
				header('Content-Type: application/json');
				if($usr['logged_in']){
					$status['state'] = DC()->hbci->writeBackUmsatz(true);
				}else $status['state'] = "sessionerror";
				echo json_encode($status);
				
				DC()->setSession();
				exit();
			}else if(($cfg->hasvalue('export')))
			{

					$usr['logged_in'] = $cfg->checkLogin();
					$export = DC()->Export;
					
					header("Content-type: application/octet-stream");
					header("Content-Disposition: attachment; filename=\"DebitConnect-Export".$export->headLine.".csv\"");
					$exportData = "";
					foreach($export->csv as $row)
					{
						$exportData.= implode(";",$row)."\r\n";
					}
					
				  echo $exportData;
				  exit();
			}else if(($cfg->hasvalue('downloadDTA'))){
					$usr['logged_in'] = $cfg->checkLogin();
					
					$rs = DC()->db->singleResult("SELECT * from dc_dtacreatelog where id = ".(int)$cfg->get('downloadDTA'));
					$update = new stdClass();
					$update->dDownload = date("Y-m-d");
					DC()->db->dbUpdate("dc_dtacreatelog",$update,"id = ".(int)$cfg->get('downloadDTA'));
					header("Content-type: application/octet-stream");
					header("Content-Disposition: attachment; filename=\"dta-".$rs["cTransaktion"].".xml\"");
					
					echo DebitConnectCore::decrypt($rs["dtaFile"]);;
					DC()->setSession();
					exit();
			}else if (($cfg->hasvalue('ajaxmatching'))){
				$usr['logged_in'] = $cfg->checkLogin();
				header('Content-Type: application/json');
				if($usr['logged_in'])
				{
					if(DC()->hbci->getMatching($cfg->get('ajaxmatching')))
					{
						$status["done"] = true;
					}
					else $status["done"] = false;
				}
				else $status['state'] = "sessionerror";
			
				echo json_encode($status);
				DC()->setSession();
				exit();
				
				
			}else if(($cfg->hasvalue('syncList'))){
				$usr['logged_in'] = $cfg->checkLogin();
				header('Content-Type: application/json');
				DC()->getSyncList();
				$outputData["order"] = DC()->syncList[0]["cRechnungsNr"];
				echo json_encode($outputData);
				DC()->setSession();
				exit();
			}
			else if (($cfg->hasvalue('ajaxsync'))){
				$usr['logged_in'] = $cfg->checkLogin();
				header('Content-Type: application/json');
				$outputData["order"] = DC()->syncList[0]["cAuftragsNr"];
				$outputData["invoice"] = strlen(DC()->syncList[0]["cRechnungsNr"])>0 ? DC()->syncList[0]["cRechnungsNr"] : "" ;
				$syncErg = DC()->doSync();
				$outputData["res"] = $syncErg['syncText'];
				echo json_encode($outputData);
				DC()->setSession();
				exit();
			}else{


				$usr['logged_in'] = $cfg->checkLogin();


				if($usr['logged_in']==true && $cfg->user > 0 ) {
				$smarty->assign("shopList",$cfg->shopList);


				if(@$cfg->hasvalue('fancy')){
					$this->View()->assign(["nomenu" => true]);
					$smarty->assign("DebitConnectOutput",$cfg->fetchFancy($cfg->get('switchTo'),$smarty));
				}else{
                     $smarty->assign("DebitConnectOutput", $cfg->fetchTemplate($cfg->current_page,$smarty));
				}
				
			}else{
			
					$smarty->assign("version",DebitConnectCore::$DC_VERSION);
					try
					{
						$soap = new SoapClient(DebitConnectCore::$SOAP,array(  'encoding' => 'UTF-8', 'cache_wsdl' => WSDL_CACHE_NONE,'trace' => 1));
						$handshake = $soap->handshake();
						$handshake = $handshake->status;
					}catch(Exception $e)
					{

					}
					$smarty->assign("handshake",$handshake);
					$this->View()->assign(["nomenu" => true]);
					$smarty->assign("DebitConnectOutput",$smarty->fetch(dirname(__FILE__)."/../../Components/DebitConnect/tpl/login.tpl"));
			}
			
			
				print $cfg->smarty->fetch(dirname(__FILE__)."/../../Components/DebitConnect/tpl/error.tpl");
          
	
			}
            DC()->setSession();
            $this->View()->addTemplateDir(__DIR__ . '/../../Views/v_o_p_debit_connect/');

        }
	
		}catch(Exception $e){

		}


		
    }

    public function createSubWindowAction()
    {
    }

    public function getWhitelistedCSRFActions()
    {
        return ['index'];
    }
}
