<?php

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
      
        try
        {
					
					if($View== null){ return "Missing Smarty";}
					require_once dirname(__FILE__)."/../../Components/DebitConnect/inc/DebitConnectCore.php";
					/** @var \DebitConnectCore $core */
					$core =  new \DebitConnectCore(null);
					$core->init(null);
					$core->smarty= $View;
					$core->cronJob->doTasks();
					return $core->cronJob->logEntry;
        }
				catch(Exception $e)
         {
						return $e->getMessage();
         }

    }

}