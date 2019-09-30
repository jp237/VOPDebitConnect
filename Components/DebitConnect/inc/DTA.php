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

use Digitick\Sepa\PaymentInformation;
use Digitick\Sepa\TransferFile\Factory\TransferFileFacadeFactory;

class DTA
{
    public $xmlFile;

    public function __construct($var1, $var2, $konto, $companyName, $ident_number, $transactionName, $payment_name,$iban,$bic)
    {
        $this->xmlFile = TransferFileFacadeFactory::createDirectDebit($var1, $var2);


        $this->xmlFile->addPaymentInfo($payment_name, [
                    'id' => $payment_name,
                    'creditorName' => $companyName,
                    'creditorAccountIBAN' => $iban,
                    'creditorAgentBIC' => $bic,
                    'seqType' => PaymentInformation::S_ONEOFF,
                    'creditorId' => $ident_number,
                ]);
    }
}
