<?php
require dirname(__FILE__)."/php-sepa-xml/autoload.php";

use Digitick\Sepa\TransferFile\Factory\TransferFileFacadeFactory;
use Digitick\Sepa\PaymentInformation;

class DTA{
	
	var $xmlFile;
	
	function __construct($var1 , $var2,$konto,$companyName,$ident_number,$transactionName,$payment_name){
		$this->xmlFile = TransferFileFacadeFactory::createDirectDebit($var1, $var2);
		$this->xmlFile->addPaymentInfo($payment_name, array(
					'id'                    => $payment_name,
					'creditorName'          => $companyName,
					'creditorAccountIBAN'   => $konto->IBAN,
					'creditorAgentBIC'      => $konto->BIC,
					'seqType'               => PaymentInformation::S_ONEOFF,
					'creditorId'            => $ident_number
				));
	}
}