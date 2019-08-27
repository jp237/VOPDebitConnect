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

class DC_DataTypes
{
    public $BIRTHDAY_TABLE = 'userbilling';
    public $SELECT_OFFEN = '';
    public $PickwarePaymentInformation = false;
    public $joinPickwarePayment = ' ';

    public function __construct()
    {
        // GET BIRTHDAY TABLE
        $check_birthday = DC()->db->singleResult('SELECT count(version) as checkval from s_schema_version where version = 730');
        if ($check_birthday['checkval'] == '1') {
            $this->BIRTHDAY_TABLE = 'kunde';
        }
        $selectPickwarePayment = ' ';
        if ($this->PickwarePaymentEnabled()) {
            $this->PickwarePaymentInformation = true;
            $selectPickwarePayment = "+IFNULL(pickware.sumPayment,'0.00')";
            $this->joinPickwarePayment = 'LEFT OUTER JOIN  ( SELECT SUM(amount) as sumPayment,orderId as pkOrder from s_plugin_viison_bank_transfer_matching_booking group by orderId) pickware on pickware.pkOrder = ';
        }
        if ((DC()->settings->currentSetting->gutschriften) != null && DC()->settings->currentSetting->gutschriften > 0) {
            $this->SELECT_OFFEN = " CAST(ROUND((CAST(ROUND(`invoice_amount`,2) as DECIMAL(12,2))
									-CAST(ROUND(IFNULL(zahlung.fWert,'0.00')" . $selectPickwarePayment . ",2)  AS DECIMAL(12,2))
									-CAST(ROUND(IFNULL(gutschrift.amount,'0.00'),2)  AS DECIMAL(12,2))
									),2) AS DECIMAL(12,2)) ";
        } else {
            $this->SELECT_OFFEN = " CAST(ROUND((CAST(ROUND(`invoice_amount`,2) as DECIMAL(12,2))
								-CAST(ROUND(IFNULL(zahlung.fWert,'0.00'),2)  AS DECIMAL(12,2))
							),2) AS DECIMAL(12,2)) ";
        }
        // APPEND STORNORECHNUNG // NEGATIV VALUE
    }

    public function setStatus()
    {
        return DC()->settings->currentSetting->shopwareapibenutzen > 0 ? true : false;
    }

    public function getCountryISO()
    {
        return DC()->db->getSQLResults('SELECT countryname, iso3 from s_core_countries');
    }

    public function BoniGatewayAdresses($pkCustomer)
    {
        $query = "SELECT IFNULL(userbilling.company,billing.company) as company,CASE  IFNULL(userbilling.salutation,billing.salutation) WHEN 'mr' THEN 1 else 2 end as salutation,IFNULL(userbilling.firstname,billing.firstname) as firstname,IFNULL(userbilling.lastname,billing.lastname) as lastname,IFNULL(userbilling.street,billing.street) as street,IFNULL(userbilling.zipcode,billing.zipcode) as zipcode,IFNULL(userbilling.city,billing.city) as city,IFNULL(land.iso3,'DEU') as country,IFNULL(DATE_FORMAT(" . $this->BIRTHDAY_TABLE . ".birthday,'%d.%m.%Y'),'00.00.0000') as DateOfBirth from s_user kunde 
				  LEFT JOIN s_order_billingaddress userbilling on userbilling.userID = kunde.id 
				  LEFT JOIN s_user_billingaddress billing on kunde.id = billing.userID
				  LEFT JOIN s_core_countries land on land.id = userbilling.countryID  where kunde.id = " . (int) $pkCustomer . ' ';

        return DC()->db->getSQLResults($query, false);
    }

    public function BoniGatewayBlacklist($pkOrder)
    {
        $query = "SELECT IFNULL(userbilling.company,billing.company) as company,CASE  IFNULL(userbilling.salutation,billing.salutation) WHEN 'mr' THEN 1 else 2 end as salutation,IFNULL(userbilling.firstname,billing.firstname) as firstname,IFNULL(userbilling.lastname,billing.lastname) as lastname,IFNULL(userbilling.street,billing.street) as street,IFNULL(userbilling.zipcode,billing.zipcode) as zipcode,IFNULL(userbilling.city,billing.city) as city,IFNULL(land.iso3,'DEU') as country,IFNULL(DATE_FORMAT(" . $this->BIRTHDAY_TABLE . ".birthday,'%d.%m.%Y'),'00.00.0000') as DateOfBirth from s_user kunde 
				  LEFT JOIN s_order_billingaddress userbilling on userbilling.userID = kunde.id 
				  LEFT JOIN s_user_billingaddress billing on kunde.id = billing.userID
				  LEFT JOIN s_core_countries land on land.id = userbilling.countryID  where userbilling.orderID = " . (int) $pkOrder . ' LIMIT 1';

        return DC()->db->singleResult($query, false);
    }

    public function BoniGatewayHistory($pkCustomer)
    {
        if ((int) $pkCustomer > 0) {
            return DC()->db->getSQLResults('select dc_gatewaylog.* from dc_gatewaylog left join s_user on s_user.id = dc_gatewaylog.pkCustomer where dc_gatewaylog.pkCustomer = ' . (int) $pkCustomer . ' order by logid desc', false);
        }
    }

    public function getShopList()
    {
        $query = "select name,s_core_shops.id,IFNULL(dc_meta.shopID,0) as settings from s_core_shops left outer join dc_meta on dc_meta.shopID = s_core_shops.id and dc_meta.art = 'mainsettings' order by s_core_shops.id = " . (int) DC()->settings->selectedShop . '  DESC';

        return DC()->db->getSQLResults($query);
    }

    public function getAuftragposQuery($pkOrder)
    {
        /*
    4;Auftrag;788;10.04.2012 00:00:00;0;0;N;1350;;;;Siegbald;Otto;Am Zuckerhut. 50;39457;Siegen;Deutschland;;;jens.perzewski@inkasso-vop.de;;;;;;N;689;139,859962;0;0;;
    4;Bankrücklastkosten;689;11.11.2016 00:00:00;0;0;N;1350;;;;Siegbald;Otto;Am Zuckerhut. 50;39457;Siegen;Deutschland;;;jens.perzewski@inkasso-vop.de;;;;;;N;689;23,000000;0;0;;
    4;Korrektur;689;23.05.2017 15:10:22;0;0;N;1350;;;;Siegbald;Otto;Am Zuckerhut. 50;39457;Siegen;Deutschland;;;jens.perzewski@inkasso-vop.de;;;;;;N;689;-31,080000;22;GS-50001;;
    4;Mahnkosten;689;23.05.2017 15:16:11;0;0;N;1350;;;;Siegbald;Otto;Am Zuckerhut. 50;39457;Siegen;Deutschland;;;jens.perzewski@inkasso-vop.de;;;;;;N;689;3,330000;0;0;;
    4;Rechnung;689;21.01.2015 00:00:00;0;0;N;1350;;;;Siegbald;Otto;Am Zuckerhut. 50;39457;Siegen;Deutschland;;;jens.perzewski@inkasso-vop.de;;;;;;N;689;139,859962;0;0;;
    4;Zahlung;689;23.05.2017 15:08:08;0;0;N;1350;;;;Siegbald;Otto;Am Zuckerhut. 50;39457;Siegen;Deutschland;;;jens.perzewski@inkasso-vop.de;;;;;;N;689;-13,000000;1750;0;;
    */
        // AUFTRAGZEILE
        $query = "SELECT _order.subshopID,'Auftrag',_order.ordernumber,DATE_FORMAT(ordertime,'%d.%m.%Y 00:00:00'),0 as blank1,
                '0' as blank2,'N',kunde.customernumber, billing.company, case billing.salutation when 'ms' then 'Frau' else 'Herr' end,'' as blank4,billing.firstname, billing.lastname, billing.street, billing.zipcode, billing.city, land.countryname ,billing.phone,'' as blank5,kunde.email ,'' as blank6
                ,'' as blank7,DATE_FORMAT(" . $this->BIRTHDAY_TABLE . ".birthday,'%d.%d.%Y'),0 as blank8,'' as blank9,'N' as blank10,_order.id,REPLACE(CAST(invoice_amount as DECIMAL(12,2)),'.',','),0 as blank11,0 as blank12
                FROM `s_order` _order  
                LEFT JOIN s_order_billingaddress billing ON _order.id = billing.orderID 
                LEFT JOIN s_user kunde on billing.userID = kunde.id 
                LEFT JOIN s_user_billingaddress userbilling ON kunde.id = userbilling.userID 
                LEFT JOIN s_core_countries land on land.id = billing.countryID 
                where _order.`id` = " . (int) $pkOrder;

        // RECHNUNGSZEILE
        $query .= "  UNION ALL SELECT _order.subshopID,'Rechnung',rechnung.docID,DATE_FORMAT(rechnung.date,'%d.%m.%Y 00:00:00'),0 as blank1,
                '0' as blank2,'N' as blank3,kunde.customernumber, billing.company, case billing.salutation when 'ms' then 'Frau' else 'Herr' end,'' as blank4,billing.firstname, billing.lastname, billing.street, billing.zipcode, billing.city, land.countryname ,billing.phone,'' as blank5,kunde.email ,'' as blank6
                ,'' as blank7,DATE_FORMAT(" . $this->BIRTHDAY_TABLE . ".birthday,'%d.%d.%Y'),0 as blank8,'' as blank9,'N' as blank10,_order.id,REPLACE(CAST(rechnung.amount as DECIMAL(12,2)),'.',','),0 as blank11,0 as blank12
                FROM `s_order` _order  
                LEFT JOIN s_order_billingaddress billing ON _order.id = billing.orderID 
                LEFT JOIN s_user kunde on billing.userID = kunde.id 
                LEFT JOIN s_core_countries land on land.id = billing.countryID 
                LEFT JOIN s_user_billingaddress userbilling ON kunde.id = userbilling.userID 
                INNER JOIN s_order_documents rechnung on rechnung.orderID = _order.id and rechnung.type = 1 
                where _order.`id` = " . (int) $pkOrder;
        //GUTSCHRIFT

        $query .= "  UNION ALL SELECT _order.subshopID,'Korrektur',korrektur.docID,DATE_FORMAT(korrektur.date,'%d.%m.%Y 00:00:00'),0 as blank1,
                '0' as blank2,'N' as blank3,kunde.customernumber, billing.company, case billing.salutation when 'ms' then 'Frau' else 'Herr' end,'' as blank4,billing.firstname, billing.lastname, billing.street, billing.zipcode, billing.city, land.countryname ,billing.phone,'' as blank5,kunde.email ,'' as blank6
                ,'' as blank7,DATE_FORMAT(" . $this->BIRTHDAY_TABLE . ".birthday,'%d.%d.%Y'),0 as blank8,'' as blank9,'N' as blank10,_order.id,REPLACE(CAST(korrektur.amount as DECIMAL(12,2)),'.',','),0 as blank11,korrektur.docID as blank12
                FROM `s_order` _order  
                LEFT JOIN s_order_billingaddress billing ON _order.id = billing.orderID 
                LEFT JOIN s_user kunde on billing.userID = kunde.id 
                LEFT JOIN s_core_countries land on land.id = billing.countryID 
                LEFT JOIN s_user_billingaddress userbilling ON kunde.id = userbilling.userID 
                INNER JOIN s_order_documents korrektur on korrektur.orderID = _order.id and korrektur.type = 3 
                where _order.`id` = " . (int) $pkOrder;
        //ZAHLUNG
        //ZAHLUNGSEINGANG
        $query .= " UNION ALL SELECT _order.subshopID, 'Zahlung', zahlung.kUmsatz, DATE_FORMAT( umsatz.dBuchung, '%d.%m.%Y 00:00:00' ) , 0 as blank1, '0' as blank2, 'N' as blank3, kunde.customernumber, billing.company,
                CASE billing.salutation			WHEN 'ms'			THEN 'Frau'			ELSE 'Herr'			END , '' as blank4, billing.firstname, billing.lastname, billing.street, billing.zipcode, 
                billing.city, land.countryname, billing.phone, '' as blank5, kunde.email, '' as blank6, '' as blank7, DATE_FORMAT( " . $this->BIRTHDAY_TABLE . ".birthday, '%d.%d.%Y' ) , 0 as blank8, '' as blank9, 'N' as blank10, zahlung.pkOrder, CONCAT('-',REPLACE( CAST( SUM( zahlung.fWert ) AS DECIMAL( 12, 2 ) ) , '.', ',' )) , 0 as blank11, 0 as blank12
                FROM `dc_tzahlung` zahlung
                LEFT JOIN `s_order` _order ON _order.id = zahlung.pkOrder 
                LEFT JOIN s_order_billingaddress billing ON _order.id = billing.orderID 
                LEFT JOIN s_user kunde ON billing.userID = kunde.id 
                LEFT JOIN s_core_countries land ON land.id = billing.countryID 
                LEFT JOIN s_user_billingaddress userbilling ON kunde.id = userbilling.userID 
                INNER JOIN dc_umsatz umsatz ON zahlung.kUmsatz = umsatz.kUmsatz and umsatz.nType = 0  
                WHERE _order.`id` =" . (int) $pkOrder . ' GROUP BY umsatz.kUmsatz,_order.subshopID,zahlung.kUmsatz,umsatz.dBuchung	,billing.customernumber, billing.company,billing.salutation	,billing.firstname, billing.lastname, billing.street, billing.zipcode,
                billing.city, land.countryname, billing.phone,kunde.email,' . $this->BIRTHDAY_TABLE . '.birthday,zahlung.pkOrder
                HAVING zahlung.pkOrder =' . (int) $pkOrder;

        if ($this->PickwarePaymentInformation) {
            $query .= " UNION ALL SELECT _order.subshopID, 'Zahlung', zahlung.id*-1, DATE_FORMAT( zahlung.creationDate, '%d.%m.%Y 00:00:00' ) , 0 as blank1, '0' as blank2, 'N' as blank3, kunde.customernumber, billing.company,
                CASE billing.salutation			WHEN 'ms'			THEN 'Frau'			ELSE 'Herr'			END , '' as blank4, billing.firstname, billing.lastname, billing.street, billing.zipcode, 
                billing.city, land.countryname, billing.phone, '' as blank5, kunde.email, '' as blank6, '' as blank7, DATE_FORMAT( " . $this->BIRTHDAY_TABLE . ".birthday, '%d.%d.%Y' ) , 0 as blank8, '' as blank9, 'N' as blank10, zahlung.orderId, CONCAT('-',REPLACE( CAST(  ROUND(zahlung.amount,2)  AS DECIMAL( 12, 2 ) ) , '.', ',' )) , 0 as blank11, 0 as blank12
                FROM `s_plugin_viison_bank_transfer_matching_booking` zahlung
                LEFT JOIN `s_order` _order ON _order.id = zahlung.orderId 
                LEFT JOIN s_order_billingaddress billing ON _order.id = billing.orderID 
                LEFT JOIN s_user kunde ON billing.userID = kunde.id 
                LEFT JOIN s_core_countries land ON land.id = billing.countryID 
                LEFT JOIN s_user_billingaddress userbilling ON kunde.id = userbilling.userID 
                
                WHERE _order.`id` =" . (int) $pkOrder . ' GROUP BY _order.subshopID,zahlung.id,zahlung.creationDate	,billing.customernumber, billing.company,billing.salutation	,billing.firstname, billing.lastname, billing.street, billing.zipcode,
                billing.city, land.countryname, billing.phone,kunde.email,' . $this->BIRTHDAY_TABLE . '.birthday,zahlung.orderId
                HAVING zahlung.orderId =' . (int) $pkOrder;
        }
        //ZAHLUNGSAUSGANG
        $query .= " UNION ALL SELECT _order.subshopID, 'Zahlung', zahlung.kUmsatz, DATE_FORMAT( umsatz.dBuchung, '%d.%m.%Y 00:00:00' ) , 0 as blank1, '0' as blank2, 'N' as blank3, kunde.customernumber, billing.company,
                CASE billing.salutation			WHEN 'ms'			THEN 'Frau'			ELSE 'Herr'			END , '' as blank4, billing.firstname, billing.lastname, billing.street, billing.zipcode, 
                billing.city, land.countryname, billing.phone, '' as blank5, kunde.email, '' as blank6, '' as blank7, DATE_FORMAT( " . $this->BIRTHDAY_TABLE . ".birthday, '%d.%d.%Y' ) , 0 as blank8, '' as blank9, 'N' as blank10, zahlung.pkOrder, REPLACE( CAST( SUM( zahlung.fWert*-1 ) AS DECIMAL( 12, 2 ) ) , '.', ',' ) , 0 as blank11, 0 as blank12
                FROM `dc_tzahlung` zahlung
                LEFT JOIN `s_order` _order ON _order.id = zahlung.pkOrder 
                LEFT JOIN s_order_billingaddress billing ON _order.id = billing.orderID 
                LEFT JOIN s_user kunde ON billing.userID = kunde.id 
                LEFT JOIN s_core_countries land ON land.id = billing.countryID 
                LEFT JOIN s_user_billingaddress userbilling ON kunde.id = userbilling.userID 
                INNER JOIN dc_umsatz umsatz ON zahlung.kUmsatz = umsatz.kUmsatz and umsatz.nType = 1 
                WHERE zahlung.nType != 8 and  zahlung.nType != 1 and  _order.`id` =" . (int) $pkOrder . ' 
                GROUP BY umsatz.kUmsatz,_order.subshopID,zahlung.kUmsatz,umsatz.dBuchung	,billing.customernumber, billing.company,billing.salutation	,billing.firstname, billing.lastname, billing.street, billing.zipcode,
                billing.city, land.countryname, billing.phone,kunde.email,' . $this->BIRTHDAY_TABLE . '.birthday,zahlung.pkOrder
                HAVING  zahlung.pkOrder =' . (int) $pkOrder;

        $retcsv = '';
        $checksum = 0;
        $rows = DC()->db->getSQLResults($query);
        $found_invoice = false;
        $data = [];
        if ($rows[1]['Auftrag'] != 'Rechnung') {
            // BEI B2B VORKASSE KLONE AUFTRAGSZEILE MIT KOMMENTAR ALS RECHNUNGSZEILE
            $data[0] = $rows[0];
            $clonedOrderRow = $rows[0];
            $clonedOrderRow['Auftrag'] = 'Rechnung';
            $clonedOrderRow['ordernumber'] .= '//Vorkasse';
            $data[1] = $clonedOrderRow;
            for ($i = 2, $iMax = count($rows); $i <= $iMax; ++$i) {
                $data[$i] = $rows[$i - 1];
            }
        } else {
            $data = $rows;
        }

        foreach ($data as $res) {
            if (count($res) > 2) {
                ++$checksum;
                $retcsv .= implode(';', str_replace(';', '', $res)) . "\r\n";
            }
        }
        $retval['csv'] = $retcsv;

        $retval['document'] = $this->submitDocumentsVOP($pkOrder, true) > 0 ? 'True' : 'False';
        $retval['checksum'] = $checksum;

        return $retval;
    }

    /** @return string */
    public function createNewOrderXML($pkOrder, $orderDataArray, $type = 'Mahnservice')
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->encoding = 'UTF-8';
        $order = array_keys($orderDataArray[0]);
        $orderDate = new DateTime($orderDataArray[0][$order[4]]);
        $invoice = array_keys($orderDataArray[1]);
        $rootElement = $dom->createElement('vopAuftrag');
        $rootElement->appendChild($dom->createElement('vopVersion', '1.0'));
        $rootElement->appendChild($dom->createElement('orderType', $type));
        $rootElement->appendChild($dom->createElement('productType', 'Shopware'));
        $rootElement->appendChild($dom->createElement('internalId', $pkOrder));
        $rootElement->appendChild($dom->createElement('ordernumber', $orderDataArray[0][$order[2]]));
        $rootElement->appendChild($dom->createElement('ordervalue', str_replace(',', '.', $orderDataArray[0][$order[27]])));
        $rootElement->appendChild($dom->createElement('orderdate', $orderDate->format('Y-m-d H:i:s')));
        $rootElement->appendChild($dom->createElement('paymentType', null));
        if ($invoice[1] == 'Rechnung') {
            $invoiceDate = new DateTime($orderDataArray[1][$invoice[2]]);
            $rootElement->appendChild($dom->createElement('invoicenumber', $orderDataArray[0]['ordernumber']));
            $rootElement->appendChild($dom->createElement('invoicedate', $invoiceDate->format('Y-m-d H:i:s')));
        } else {
            $rootElement->appendChild($dom->createElement('invoicenumber', null));
            $rootElement->appendChild($dom->createElement('invoicedate', null));
        }
        $rootElement->appendChild($dom->createElement('dateReminded', null));
        // ADDING CLIENTINFORMATION.. MAIN CLIENT IS SET BY AUTH DATA ( V.O.P )

        foreach ($orderDataArray as $row) {
            $keys = array_keys($row);
            if ($row[$keys[2]] == 'Auftrag' || $row[$keys[2]] == 'Rechnung') {
                continue;
            }
            //if($row[$keys[2]] == "Korrektur")
        }
        $correction = $dom->createElement('correction');
        $correction->appendChild($dom->createElement('number', null));
        $correction->appendChild($dom->createElement('date', null));
        $correction->appendChild($dom->createElement('value', null));
        $rootElement->appendChild($correction);
        $orderInformation = $dom->createElement('orderDetails');
        $rootElement->appendChild($orderInformation);
        $clientInformation = $dom->createElement('clientInformation');

        $client = $dom->createElement('client');

        $clientInformation->appendChild($client);

        $rootElement->appendChild($clientInformation);

        // ADDING DEBTORINFORMATIONS , LOOP
        $debtorInformation = $dom->createElement('debtorInformation');

        $airline = $details['airlineContactInformation']['mainAddress']; // @todo fix undefined variable $details

        $debtor = $dom->createElement('debtor');
        $debtor->appendChild($dom->createElement('firstname', $orderDataArray[0][$order[11]]));
        $debtor->appendChild($dom->createElement('lastname', $orderDataArray[0][$order[12]]));
        $debtor->appendChild($dom->createElement('company', ($orderDataArray[0][$order[10]])));
        $debtor->appendChild($dom->createElement('dateofbirth', $orderDataArray[0][$order[22]]));
        $debtor->appendChild($dom->createElement('street', $orderDataArray[0][$order[13]]));
        $debtor->appendChild($dom->createElement('zipcode', $orderDataArray[0][$order[14]]));
        $debtor->appendChild($dom->createElement('city', $orderDataArray[0][$order[15]]));
        $debtor->appendChild($dom->createElement('country', $orderDataArray[0][$order[16]]));
        $debtor->appendChild($dom->createElement('email', $orderDataArray[0][$order[19]]));
        $debtor->appendChild($dom->createElement('telphone', $orderDataArray[0][$order[17]]));
        $debtor->appendChild($dom->createElement('fax', null));
        $debtor->appendChild($dom->createElement('customernumber', $orderDataArray[0][$order[7]]));

        $debtorInformation->appendChild($debtor);
        // ADDING PROCESSAUTHORIZATION ADDRESS AS SECOND DEBTOR
        $airline = $details['airlineContactInformation']['processAuthorized'];

        $debtor = $dom->createElement('debtor');
        $debtor->appendChild($dom->createElement('firstname', null));
        $debtor->appendChild($dom->createElement('lastname', null));
        $debtor->appendChild($dom->createElement('company', ($details['problem']['flight']['airline']['name'])));
        $debtor->appendChild($dom->createElement('dateofbirth', null));
        $debtor->appendChild($dom->createElement('street', $airline['street']));
        $debtor->appendChild($dom->createElement('zipcode', $airline['zipcode']));
        $debtor->appendChild($dom->createElement('city', $airline['city']));
        $debtor->appendChild($dom->createElement('country', $airline['countryiso']));
        $debtor->appendChild($dom->createElement('telphone', null));
        $debtor->appendChild($dom->createElement('fax', $airline['fax']));

        $debtorInformation->appendChild($debtor);

        $rootElement->appendChild($debtorInformation);

        // ADDING ADDITIONALCOSTS...
        $additionalCosts = $dom->createElement('additionalCosts');
        $costs = null;

        foreach ($costs as $cost) {
            $additionalCost = $dom->createElement('additionalCost');
            $additionalCost->appendChild($dom->createElement('date', date('Y-m-d')));
            $additionalCost->appendChild($dom->createElement('type', 'Mahnkosten'));
            $additionalCost->appendChild($dom->createElement('value', $cost['fValue']));

            $additionalCosts->appendChild($additionalCost);
        }

        $rootElement->appendChild($additionalCosts);
        // ADDING PAYMENTINFORMATIONS
        $paymentInformation = $dom->createElement('paymentInformation');
        //$payments = $this->getFullPaymentDetails($problemId);
        if ($details['user_complaint']['alreadyReceivedCompensation'] > 0) {
            $payments[] = [
                'dateBooked' => date('Y-m-d'),
                'idPayment' => 0,
                'value' => number_format($details['user_complaint']['compensationAmountReceivedAlready'], 2, '.', ''),
            ];
        }
        foreach ($payments as $dbPayment) {
            $payment = $dom->createElement('payment');
            $payment->appendChild($dom->createElement('date', $dbPayment['dateBooked']));
            $payment->appendChild($dom->createElement('type', 'Zahlung'));
            $payment->appendChild($dom->createElement('value', $dbPayment['fValue']));
            $payment->appendChild($dom->createElement('id', $dbPayment['idPayment']));
            $paymentInformation->appendChild($payment);
        }
        $rootElement->appendChild($paymentInformation);
        $TextInformation = [];
        $TextInformation[] = ['date' => '',
            'type' => 'Text',
            'value' => $details['case']['countPassenger'] . ' Fluggäste ',
        ];
        $TextInformation[] = [
            'date' => $details['problem']['flight']['departureDate'],
            'type' => 'Text',
            'value' => 'Von ' . $details['problem']['flight']['origin']['cityNameDe'] . ' nach ' . $details['problem']['flight']['destination']['cityNameDe'],
        ];
        $TextInformation[] = [
            'date' => $details['problem']['flight']['departureDate'],
            'type' => 'Text',
            'value' => $details['problem']['problemDescription'] . '@ ' . $details['problem']['flight']['number'],
        ];

        // ADDING ADDITIONAL TEXT INFORMATIONS
        $additionalInformations = $dom->createElement('additionalInformations');
        foreach ($TextInformation as $infoText) {
            $additionalInformation = $dom->createElement('additionalInformation');
            $additionalInformation->appendChild($dom->createElement('date', $infoText['date']));
            $additionalInformation->appendChild($dom->createElement('type', $infoText['type']));
            $additionalInformation->appendChild($dom->createElement('value', $infoText['value']));
            $additionalInformations->appendChild($additionalInformation);
        }
        $rootElement->appendChild($additionalInformations);

        $dom->appendChild($rootElement);
        $xml = $dom->saveXML();

        return $xml;
    }

    public function array_insert(&$array, $position, $insert)
    {
        if (is_int($position)) {
            array_splice($array, $position, 0, $insert);
        } else {
            $pos = array_search($position, array_keys($array));
            $array = array_merge(
                array_slice($array, 0, $pos),
                $insert,
                array_slice($array, $pos)
            );
        }
    }

    public function getVOPAuftrag($pkOrder)
    {
        $query = 'SELECT kunde.email as cMail,billing.phone as cTel,land.iso3 as cLand,billing.salutation as cAnrede,billing.company as cFirma, billing.firstname as cVorname,billing.lastname as cNachname,billing.street as cStrasse,billing.zipcode as cPLZ,billing.city as cOrt,rechnung.docID as cRechnungsNr,CAST(_order.invoice_amount AS DECIMAL(12,2)) as fWert,gutschrift.docID as cGutschriftNr,CAST(IFNULL(zahlung.fWert,0) AS DECIMAL(12,2)) as fZahlung,_order.ordernumber as cAuftragsNr from s_order _order left join s_order_billingaddress billing on billing.orderID = _order.id LEFT JOIN s_order_documents rechnung on rechnung.orderID = _order.id and rechnung.type = 1
                  LEFT JOIN s_order_documents gutschrift on gutschrift.orderID = _order.id and gutschrift.type = 3 
                  LEFT JOIN s_core_countries land on land.id = billing.countryID 
                  LEFT JOIN s_user kunde on billing.userID = kunde.id 
                  LEFT OUTER JOIN ( SELECT SUM(fWert) as fWert,pkOrder  FROM `dc_tzahlung` where nType != 8 and  dc_tzahlung.nType != 1  group by pkOrder ) zahlung on zahlung.pkOrder = _order.id where _order.id = ' . (int) $pkOrder;

        return DC()->db->getSQLResults($query);
    }

    public function assignTemplateVars($pkOrder = 0)
    {
        if ($pkOrder == 0) {
            $select = DC()->db->singleResult('SELECT id from s_order order by id desc LIMIT 1');

            $pkOrder = $select['id'];
        }

        if ($pkOrder == 0) {
            return;
        }
        DC()->smarty->assign('Zahlungen', DC()->hbci->getZahlungenTemplate($pkOrder));
        DC()->smarty->assign('Bestellung', $this->getZEVars($pkOrder));
        DC()->smarty->assign('Artikel', $this->getArtikelPos($pkOrder));
        DC()->smarty->assign('Rechnungsadresse',
        DC()->db->singleResult('SELECT `company` as Firma,`salutation` as Anrede ,`firstname` as Vorname,`lastname` as Nachname,`street` as Strasse,`zipcode`as PLZ,`city`as Ort,`phone`as Telefon FROM `s_order_billingaddress` WHERE orderID = ' . (int) $pkOrder));
        DC()->smarty->assign('Lieferadresse',
            DC()->db->singleResult('SELECT `company` as Firma,`salutation` as Anrede ,`firstname` as Vorname,`lastname` as Nachname,`street` as Strasse,`zipcode`as PLZ,`city`as Ort,`phone`as Telefon FROM `s_order_shippingaddress` WHERE orderID = ' . (int) $pkOrder));
    }

    public function getOrderEmail($pkOrder)
    {
        $query = DC()->db->singleResult('select s_user.email from s_order left join s_user on s_order.userID = s_user.id where s_order.id = ' . (int) $pkOrder);

        return $query['email'];
    }

    public function getBelege($limitStart, $limitEnd, $order, $filter, $fieldModes)
    {
        $dataType['order'] = ['PK' => [false, 'rechnung.pkOrder', 'Key', false],
                                    'cRechtext' => [true, 'rechnung.cRechtext', 'Art', true],
                                    'tstamp' => [true, 'tstamp', 'Datum', false],
                                    'nRechNr' => [true, 'rechnung.nRechNr', 'RechnungsNr', true],
                                    'nRechjahr' => [true, 'rechnung.nRechJahr', 'RechnungsJahr', true],
                                    'ordernumber' => [true, '_order.ordernumber', 'AuftragsNummer', true],
                                    'cTransaktion' => [true, 'rechnung.cTransaktion', 'Transaktion', false],
                                    'dGebucht' => [true, 'dGebucht', 'Verbucht am', false],
                                    'fZahlbetrag' => [true, 'rechnung.fZahlbetrag', 'Auszahlung', false],
                                    'id' => [false, 'rechnung.id', 'Auszahlung', false],
                                    ];

        $query = "select rechnung.*,_order.ordernumber,DATE_FORMAT(rechnung.dErstellt,'%d.%m.%Y') as tstamp,DATE_FORMAT(rechnung.dGebucht,'%d.%m.%Y') as dGebucht, CASE  when bDocument IS NOT NULL THEN pkOrder else 0   END as PDF  from dc_rechnung rechnung 
			left join s_order _order on _order.id = rechnung.pkOrder 
			where 1=1 ";

        if (count($filter) > 0) {
            foreach ($filter as $key => $value) {
                if (strlen($value) > 0) {
                    $query .= " AND $key LIKE '%" . DC()->db->dbEscape($value) . "%' ";
                }
            }
        }

        if (is_array($fieldModes)) {
            $values = 0;
            foreach ($fieldModes as $key => $value) {
                if ($key != 'none' && $key != '' && strlen($value) > 0) {
                    $query .= $values == 0 ? ' HAVING ' . $key . " = '" . DC()->db->dbEscape($value) . "'" : ' AND ' . $key . " = '" . DC()->db->dbEscape($value) . "'";
                    ++$values;
                }
            }
        }
        //------------------------------------------
        if (count($order) > 0) {
            $query .= ' ORDER BY ' . $order['column'] . ' ' . $order['direction'];
        }
        $countquery = count(DC()->db->getSQLResults($query));

        $dataType['count'] = $countquery;

        $query .= " LIMIT $limitStart , $limitEnd ";
        $dataType['query'] = $query;

        return $dataType;
    }

    public function getLogBuch($limitStart, $limitEnd, $order, $filter, $fieldModes)
    {
        $dataType['order'] = ['PK' => [false, 'log.id', 'Key', false],
                                    'art' => [true, 'log.art', 'Art', true],
                                    'tstamp' => [true, 'log.tstamp', 'Datum', false],
                                    'logdata' => [true, 'log.logdata', 'Logbucheintrag', true],
                                    'ordernumber' => [true, '_order.ordernumber', 'Auftragsnummer', true],
                                    'errormsg' => [false, 'log.errormsg', 'errormsg', false], ];

        $query = "SELECT log.id , log.art , DATE_FORMAT(log.tstamp,'%d.%m.%Y %H:%i:%s') as tstamp,log.logdata,log.errormsg,IFNULL(_order.ordernumber,'Systemmeldung') as ordernumber from dc_log as log left outer join 
			s_order _order on _order.id = log.pkOrder where 1=1 ";
        if (count($filter) > 0) {
            foreach ($filter as $key => $value) {
                if (strlen($value) > 0) {
                    $query .= " AND $key LIKE '%" . DC()->db->dbEscape($value) . "%' ";
                }
            }
        }

        if (is_array($fieldModes)) {
            $values = 0;
            foreach ($fieldModes as $key => $value) {
                if ($key != 'none' && $key != '' && strlen($value) > 0) {
                    $query .= $values == 0 ? ' HAVING ' . $key . " = '" . DC()->db->dbEscape($value) . "'" : ' AND ' . $key . " = '" . DC()->db->dbEscape($value) . "'";
                    ++$values;
                }
            }
        }
        //------------------------------------------
        if (count($order) > 0) {
            $query .= ' ORDER BY ' . $order['column'] . ' ' . $order['direction'];
        }
        $countquery = count(DC()->db->getSQLResults($query));

        $dataType['count'] = $countquery;

        $query .= " LIMIT $limitStart , $limitEnd ";
        $dataType['query'] = $query;

        return $dataType;
    }

    public function getShopCompanyData()
    {
        $shopID = (int) DC()->getShopId();
        $url = DC()->db->singleResult('SELECT host from s_core_shops where id = ' . $shopID);

        return ['company' => $this->getShopwareConfValue(893, $shopID),
                 'mail' => $this->getShopwareConfValue(674, $shopID),
                 'host' => $url['host'], ];
    }

    public function PickwarePaymentEnabled()
    {
        return DC()->db->tableExists('viison_bank_transfer_matching_booking', 's_plugin_');
    }

    public function getShopwareConfValue($element_id, $shop_id)
    {
        $rs = DC()->db->singleResult('select `value` from s_core_config_values where element_id = ' . (int) $element_id . ' AND shop_id = ' . (int) $shop_id);
        if (isset($rs['value'])) {
            return unserialize($rs['value']);
        }

        return '';
    }

    public function getArtikelPos($pkOrder)
    {
        return DC()->db->getSQLResults((
        'SELECT * from s_order_details where orderID = ' . $pkOrder
    ));
    }

    public function getZEVars($pkOrder)
    {
        $selectPickwarePayment = '';
        $joinPickwarePayment = '';
        if ($this->PickwarePaymentInformation) {
            $selectPickwarePayment = '+IFNULL(pickware.sumPayment,0)';
            $joinPickwarePayment = $this->joinPickwarePayment . ' ' . $pkOrder;
        }
        $query = ' SELECT s_order.id,ordernumber,CAST(invoice_amount as DECIMAL(12,2)) as betrag,IFNULL(s_user.customernumber,s_order_billingaddress.customernumber) as KundenNr,s_order_billingaddress.lastname,s_order_billingaddress.firstname , rechnung.docID as RechnungsNr,rechnung.amount as Rechnungsbetrag,CAST(gutschrift.amount AS DECIMAL(12,2)) as Gutschriftbetrag,gutschrift.docID as GutschriftNr, ' . $this->SELECT_OFFEN . " as offen
				,rechnung.id as attachment,s_order_billingaddress.salutation as anrede , DATE_FORMAT(rechnung.date,'%d.%m.%Y') as Rechnungsdatum ,ordernumber as AuftragsNr,CAST(IFNULL(zahlung.fWert,0)" . $selectPickwarePayment . " as DECIMAL(12,2)) as Bezahlt, DATE_FORMAT(s_order.ordertime,'%d.%m.%Y') as Auftragdatum  
				, s_order.userID as pkCustomer , s_core_paymentmeans.description as ZahlartName,zahlungsstatus.description as Zahlungsstatus , s_core_customergroups.id as KundenGruppeId , s_core_customergroups.description as KundenGruppeName,s_order_billingaddress.company as Firma
								FROM s_order 
								
								left outer join s_order_documents rechnung on rechnung.orderID = s_order.id and rechnung.type = 1
								left outer join s_order_documents gutschrift on gutschrift.orderID = s_order.id and gutschrift.type = 3 
								left outer join s_order_documents storno on storno.orderID = s_order.id and storno.type = 4 
								LEFT OUTER JOIN ( SELECT SUM(fWert) as fWert,pkOrder  FROM `dc_tzahlung`  where nType != 1  group by pkOrder ) zahlung on zahlung.pkOrder = s_order.id 
								" . $joinPickwarePayment . '
								left join s_order_billingaddress on s_order.userID = s_order_billingaddress.userID and s_order_billingaddress.orderID = s_order.id
								LEFT JOIN s_core_paymentmeans ON s_core_paymentmeans.id = s_order.paymentID 
								LEFT JOIN s_core_states zahlungsstatus on zahlungsstatus.id = s_order.cleared 
							  	LEFT JOIN s_user  on s_user.id = s_order.userID  
								LEFT JOIN s_core_customergroups on s_core_customergroups.groupkey = s_user.customergroup 
								 where s_order.id = ' . (int) $pkOrder;
        $output = DC()->db->singleResult($query);
        $docID = $output['attachment'];
        if ($output['anrede'] == 'ms') {
            $output['anrede'] = 'Sehr geehrte Frau';
        } else {
            $output['anrede'] = 'Sehr geehrter Herr';
        }
        if ($docID > 0) {
            $output['attachment'] = $this->getInvoiceDocument($docID);
        }

        return $output;
    }

    public function submitDocumentsVOP($pkOrder, $dbUpload = false)
    {
        $ret = 0;
        try {
            $soap = DC()->API->mahnwesen();
            foreach (DC()->db->getSQLResults('SELECT ID,hash from s_order_documents where orderID = ' . (int) $pkOrder) as $key) {
                $request = [];
                $docId = $key['ID'];
                $user = DC()->settings->registration['vopUser'];
                $pass = md5(DC()->settings->registration['vopToken']);
                $path = __DIR__ . '/../../../../../../../../../files/documents/';

                if (file_exists($path . $key['hash'] . '.pdf')) {
                    $request['pdfDocument'] = base64_encode(file_get_contents($path . $key['hash'] . '.pdf'));
                } else {
                    $request = DC()->API->shopware->get('documents/' . $docId);
                    $request = $request['data'];
                }

                if (strlen($request['pdfDocument']) > 10) {
                    $res = $soap->newAllDoc($user, $pass, '1', $docId . '-shopware', '0', '0', '0', $request['pdfDocument'], $pkOrder, '0');
                    if ($res->status == 'OK') {
                        DC()->Log('Dokument', 'Dokument ' . $docId . ' übermittelt', 0);
                        ++$ret;
                    }
                }
            }
        } catch (Exception $e) {
            DC()->smarty->assign('API_ERROR', $e->getMessage());
            DC()->Log('API_ERROR', $e->getMessage(), 10);
        }

        return $ret;
    }

    public function getCustomerGroups()
    {
        return DC()->db->getSQLResults('SELECT id, description,groupkey from s_core_customergroups');
    }

    public function getInvoiceDocument($docId, $dbUpload = false)
    {
        try {
            $rs = DC()->db->singleResult('SELECT ID,hash from s_order_documents where ID = ' . (int) $docId);

            $path = __DIR__ . '/../../../../../../../../../files/documents/';
            $folderFile = $path . $rs['hash'] . '.pdf';

            if (file_exists($folderFile)) {
                $request['pdfDocument'] = base64_encode(file_get_contents($folderFile));
            }

            if (strlen($request['pdfDocument']) > 10) {
                return $request['pdfDocument'];
            }

            return false;
        } catch (Exception $e) {
            DC()->smarty->assign('API_ERROR', $e->getMessage());
            DC()->Log('API_ERROR', $e->getMessage(), 10);

            return false;
        }
    }

    public function getShippingMethods()
    {
        return DC()->db->getSQLResults('SELECT * from s_premium_dispatch');
    }

    public function getPaymentMethods()
    {
        return DC()->db->getSQLResults('select *  FROM `s_core_paymentmeans` ');
    }

    public function getPaymentStatus()
    {
        return DC()->db->getSQLResults("select * from s_core_states where `group` = 'payment'");
    }

    public function getOrderStatus()
    {
        return DC()->db->getSQLResults("select * from s_core_states where `group` = 'state'");
    }

    public function getUserLogin($username, $password)
    {
        if ((DC()->hasvalue('sessid')) && (DC()->hasvalue('usr'))) {
            $sessions = DC()->db->getSQLResults('SELECT * from s_core_sessions_backend ');
            foreach ($sessions as $session) {
                if ($session['id'] == DC()->get('sessid')) {
                    return (int) DC()->get('usr');
                }
            }
            DC()->smarty->assign('loginerror', 'Abgelaufene Backendsitzung');
        } else {
            $query = 'SELECT id , username, password,encoder from s_core_auth ';
            $users = DC()->db->getSQLResults($query);
            $options = ['cost' => 10];
            foreach ($users as $user) {
                if (strlen($username) > 0 && strlen($password) > 0) {
                    if ($username == $user['username']) {
                        if ($user['encoder'] == 'bcrypt') {
                            if (password_verify($password, $user['password'])) {
                                return (int) $user['id'];
                            }
                        }
                    }
                }
            }
            DC()->smarty->assign('loginerror', 'Falscher Benutzer/Passwort');

            return 0;
        }
    }

    public function removePayment($kUmsatz)
    {
        $rs = DC()->db->getSQLResults('SELECT * from dc_tzahlung where (nType = 0 or nType = 5 )  and kUmsatz = ' . (int) $kUmsatz);
        foreach ($rs as $row) {
            if ($row['orderStatusHistory'] != null || $row['paymentStatusHistory'] != null) {
                if (!$this->changeOrder($row['pkOrder'], null, $row['paymentStatusHistory'], null, '', null, true)) {
                    return false;
                }
            }
        }
        DC()->db->dbQuery('DELETE FROM dc_tzahlung where kUmsatz = ' . (int) $kUmsatz);
        DC()->Log('Zahlungsabgleich', 'Zahlung entfernt', 0);

        return true;
    }

    public function getZahlungsabgleichBestellungen($limitSubshop = false)
    {
        $minDate = new DateTime(date('d.m.Y'));
        //$minDate->modify("- 6month");
        $minDate->modify('- 36months');
        $payedStatus = ' AND (s_order.cleared != ' . DC()->settings->currentHBCI['statusbezahlt'] . ' OR dc_status.pkOrder > 0 )';
        $matching_ignore_paymentstate = DC()->getConf('matching_ignore_paymentstate', '0', true);
        $joinPickware = strlen($this->joinPickwarePayment) > 5 ? $this->joinPickwarePayment . ' s_order.id ' : '';
        if ($matching_ignore_paymentstate > 0) {
            $payedStatus = ' AND 1=1 ';
        }

        $query = 'SELECT s_order.id,ordernumber,CAST(ROUND(invoice_amount,2) as DECIMAL(12,2)) as betrag,s_order_billingaddress.customernumber as KundenNr,s_order_billingaddress.lastname,s_order_billingaddress.firstname , rechnung.docID as RechnungsNr,rechnung.amount as Rechnungsbetrag,gutschrift.amount as Gutschriftbetrag,gutschrift.docID as GutschriftNr
								,' . $this->SELECT_OFFEN . ' as offen
								,s_order.ordertime ,s_order.paymentID,s_order.status as orderstatus,s_order.cleared as paymentstatus,count(dc_status.id) as vopstatus,dc_auftrag.VOPStatus as nVOPStatus FROM s_order 
								left outer join s_order_documents rechnung on rechnung.orderID = s_order.id and rechnung.type = 1
								left outer join s_order_documents gutschrift on gutschrift.orderID = s_order.id and gutschrift.type = 3
								left outer join s_order_documents storno on storno.orderID = s_order.id and storno.type = 4 
								' . $joinPickware . "
								left join s_order_billingaddress on s_order.userID = s_order_billingaddress.userID and s_order_billingaddress.orderID = s_order.id 
								LEFT OUTER JOIN ( SELECT SUM(fWert) as fWert,pkOrder  FROM `dc_tzahlung` where  nType != 8 and nType != 1   group by pkOrder ) zahlung on zahlung.pkOrder = s_order.id 
								LEFT OUTER JOIN dc_status on dc_status.pkOrder = s_order.id 
								LEFT OUTER JOIN dc_auftrag on dc_auftrag.pkOrder = s_order.id 
								where s_order.ordernumber > 0 AND s_order.ordertime >= '" . $minDate->format('Y-m-d H:i:s') . "' " . $payedStatus . '';

        if ($limitSubshop) {
            $query .= ' and s_order.subshopID = ' . DC()->settings->selectedShop;
        }

        $query .= '  group by s_order.id ,s_order.ordernumber , s_order.invoice_amount,s_order_billingaddress.customernumber,s_order_billingaddress.lastname,s_order_billingaddress.firstname,rechnung.docID,rechnung.amount,gutschrift.amount,gutschrift.docID,zahlung.fWert';
        $query .= ' ,storno.amount,s_order.ordertime,s_order.paymentID,s_order.status,s_order.cleared';

        $rs = DC()->db->getSQLResults($query);

        $bestellung = [];
        foreach ($rs as $bestellung) {
            $bestellungen[$bestellung['id']] = $bestellung;
        }
        DC()->hbci->bestellungen = $bestellungen;
    }

    public function getOPListe($limitStart, $limitEnd, $order, $filter, $fieldModes)
    {
        $joinPickware = strlen($this->joinPickwarePayment) > 5 ? $this->joinPickwarePayment . ' _order.id ' : '';

        $query = 'SELECT _order.`id` , `ordernumber` , CAST(ROUND(`invoice_amount`,2) AS DECIMAL(12,2)) as invoice_amount,' . $this->SELECT_OFFEN . " as fWert, `ordertime` , billing.salutation, billing.company, billing.customernumber, billing.firstname, billing.lastname, billing.street, billing.zipcode, billing.city, billing.phone, land.iso3
					,rechnung.docID  , DATE_FORMAT(_order.ordertime,'%d.%m.%Y') as datum ,zahlart.description as zahlartname,states.description as zahlstatus,kundengruppe.description as kundengruppename
					,IFNULL(dc_mahnstop.id,0) as mahnstop,CAST(ROUND(IFNULL(zahlung.fWert,'0.00'),2) AS DECIMAL(12,2)) as zahlungseingang ,
					CASE IFNULL(auftrag.VOPStatus,0) when '0' THEN '---' WHEN '39' THEN 'ZE Versendet' WHEN '55' THEN  'MA Bearbeitung' WHEN '59' THEN 'MA Erledigt' WHEN '95' THEN 'INK Bearbeitung' WHEN '99' THEN 'INK Erledigt'
					WHEN '100' THEN  'Papierkorb' WHEN '1000' THEN 'Geloescht' ELSE '' END as _statusVOP
					FROM `s_order` _order
					LEFT JOIN s_order_billingaddress billing ON _order.id = billing.orderID 
					LEFT OUTER JOIN ( SELECT max(VOPStatus) as VOPStatus,pkOrder,dtSend,trash from dc_auftrag group by pkOrder,dtSend,trash) auftrag on auftrag.pkOrder = _order.id 
					LEFT OUTER JOIN ( SELECT SUM(fWert) as fWert,pkOrder  FROM `dc_tzahlung` where  nType != 8  and nType != 1    group by pkOrder ) zahlung on zahlung.pkOrder = _order.id 
					LEFT OUTER JOIN s_order_documents rechnung on rechnung.orderID = _order.id and rechnung.type = 1 
					left outer join s_order_documents gutschrift on gutschrift .orderID = _order.id and gutschrift.type = 3 
					left outer join s_order_documents storno on storno.orderID = _order.id and storno.type = 4 
					LEFT JOIN s_core_countries land on land.id = billing.countryID  ";
        $query .= ' LEFT  JOIN s_core_paymentmeans zahlart on _order.paymentID = zahlart.id ';
        $query .= ' LEFT  JOIN s_core_states states on _order.cleared = states.id ';
        $query .= ' LEFT JOIN s_user  _user on _user.id = _order.userID ';
        $query .= $joinPickware;
        $query .= ' LEFT OUTER JOIN s_core_customergroups  kundengruppe on kundengruppe.groupkey = _user.customergroup ';
        $query .= ' LEFT OUTER JOIN dc_mahnstop on dc_mahnstop.id =  (SELECT id from dc_mahnstop where dc_mahnstop.pk = _user.id and nType = 1 OR dc_mahnstop.pk = _order.id and nType = 0 LIMIT 1 )';
        $query .= ' where  _order.ordernumber > 0 AND  (auftrag.trash = 0 or auftrag.trash IS NULL ) and _order.subshopID =  ' . (int) DC()->settings->selectedShop;

        if (count($filter) > 0) {
            foreach ($filter as $key => $value) {
                if (strlen($value) > 0) {
                    $query .= " AND $key LIKE '%" . DC()->db->dbEscape($value) . "%' ";
                }
            }
        }
        $query .= ' AND ' . $this->SELECT_OFFEN . ' > 0 ';

        if (is_array($fieldModes)) {
            $values = 0;
            foreach ($fieldModes as $key => $value) {
                if ($key != 'none' && $key != '' && strlen($value) > 0) {
                    $query .= $values == 0 ? ' HAVING ' . $key . " = '" . DC()->db->dbEscape($value) . "'" : ' AND ' . $key . " = '" . DC()->db->dbEscape($value) . "'";
                    ++$values;
                }
            }
        }

        //------------------------------------------

        if (count($order) > 0) {
            $query .= ' ORDER BY ' . $order['column'] . ' ' . $order['direction'];
        }

        $countquery = count(DC()->db->getSQLResults($query));

        $query .= " LIMIT $limitStart , $limitEnd ";
        $filterVOP = [
                     ['id' => 0, 'description' => '---'],
                     ['id' => 39, 'description' => 'ZE Versendet'],
                     ['id' => 55, 'description' => 'MA Bearbeitung'],
                     ['id' => 59, 'description' => 'MA Erledigt'],
                     ['id' => 95, 'description' => 'INK Bearbeitung'],
                     ['id' => 99, 'description' => 'INK Erledigt'],
                     ['id' => 100, 'description' => 'Papierkorb'],
                     ['id' => 1000, 'description' => 'Geloescht'],
                    ];

        $output = ['order' => [
                                        'PK' => [false, '_order.id', 'Key', false],
                                        'AuftragsNr' => [true, '_order.ordernumber', 'Auftragsnr', true],
                                        'RechnungsNr' => [true, 'rechnung.docID', 'RechnungsNr', true],
                                        'Betrag' => [true, '_order.invoice_amount', 'Betrag', false],
                                        'Offen' => [true, 'fWert', 'Offen', false],
                                        'Zahlung' => [true, 'zahlungseingang', 'Zahlung', false],
                                        'Datum' => [true, 'datum', 'Datum', false],
                                        'Anrede' => [false, 'billing.salutation', 'Anrede', false],
                                        'Firma' => [true, 'billing.company', 'Firma', true],
                                        'KundenNr' => [true, 'billing.customernumber', 'KundenNr', true],
                                        'Vorname' => [true, 'billing.firstname', 'Vorname', true],
                                        'Nachname' => [true, 'billing.lastname', 'Nachname', true],
                                        'Kundengruppe' => [true, 'kundengruppename', 'Kundengruppe', false, 'arrayFilter' => $this->getCustomerGroups()],
                                        'Zahlart' => [true, 'zahlartname', 'Zahlart', false, 'arrayFilter' => $this->getPaymentMethods()],
                                        'Zahlstatus' => [true, 'zahlstatus', 'Zahlstatus', false],
                                        'Land' => [false, 'land.iso3', 'Land', false],
                                        'mahnstop' => [false, 'mahnstop', 'mahnstop', false],
                                        'sumGesamt' => [false, 'invoice_amount', 'Betrag', false],
                                        'sumOffen' => [false, 'fWert', 'Offen', false],
                                        'StatusVOP' => [true, '_statusVOP', 'StatusVOP', false, 'arrayFilter' => $filterVOP],
                                    ], 'query' => $query, 'count' => $countquery];

        return $output;
    }

    public function getAuftragList($filter, $order, $nVOPStatus, $limit, $fieldModes = [])
    {
        $joinPickware = strlen($this->joinPickwarePayment) > 5 ? $this->joinPickwarePayment . ' s_order.id ' : '';
        $b2bshop = DC()->settings->currentSetting->mahnwesenvorkasse == 'on' ? true : false;
        $paymentID = DC()->settings->currentPayments;
        $vorkasse = DC()->settings->currentVorkasse;
        $nullvalueVOPOffen = $nVOPStatus == 39 || $nVOPStatus == 100 ? '0.00' : 'auftrag.fWert-auftrag.fZahlung';
        $query = "select auftrag.pkOrder as orderId,auftrag.*,CASE status.fGesamt WHEN '0.00' THEN -1.00 ELSE IFNULL( FLOOR( IFNULL( status.fOffen, '0.00' ) / IFNULL( status.fGesamt, '0.00' ) *100 ) , '-1.00' )END as percentvalue,
							IFNULL(status.fOffen," . $nullvalueVOPOffen . ") as OffenVOP,DATE_FORMAT(auftrag.dtSend,'%d.%m.%Y') as datum,zahlart.description as zahlartname,states.description as zahlstatus,kundengruppe.description as kundengruppename,s_order.ordernumber
					  		," . $this->SELECT_OFFEN . ' as fWert , s_order.invoice_amount  
							from dc_auftrag auftrag  LEFT OUTER JOIN dc_status status on status.pkOrder = auftrag.pkOrder  ';
        $query .= ' LEFT JOIN s_order  on auftrag.pkOrder = s_order.id ';
        $query .= ' LEFT  JOIN s_core_paymentmeans zahlart on s_order.paymentID = zahlart.id ';
        $query .= ' LEFT  JOIN s_core_states states on s_order.cleared = states.id ';
        $query .= ' LEFT JOIN s_user  _user on _user.id = s_order.userID ';
        $query .= ' LEFT OUTER JOIN ( SELECT SUM(fWert) as fWert,pkOrder  FROM `dc_tzahlung` where  nType != 8  and nType != 1  group by pkOrder ) zahlung on zahlung.pkOrder = s_order.id  ';
        $query .= '	left outer join s_order_documents gutschrift on gutschrift.orderID = s_order.id and gutschrift.type = 3 ';
        $query .= '	left outer join s_order_documents storno on storno.orderID = s_order.id and storno.type = 4 ';
        $query .= ' LEFT OUTER JOIN s_core_customergroups  kundengruppe on kundengruppe.groupkey = _user.customergroup ';
        $query .= $joinPickware;
        $query .= ' where  s_order.ordernumber > 0 AND auftrag.trash = 0 and auftrag.VOPStatus = ' . (int) $nVOPStatus . '  AND auftrag.subshopID =  ' . (int) DC()->settings->selectedShop;
        foreach ($filter as $key => $value) {
            if (strlen($value) > 0) {
                $query .= " AND $key LIKE '%" . DC()->db->dbEscape($value) . "%' ";
            }
        }

        if ($nVOPStatus == 39) {
            // ZAHLUNGSERINNERUNG VERSENDET BEZAHLTE AUSBLENDEN
            $query .= ' AND ' . $this->SELECT_OFFEN . ' > 0 ';
        }

        if (is_array($fieldModes)) {
            $values = 0;
            foreach ($fieldModes as $key => $value) {
                if ($key != 'none' && $key != '' && strlen($value) > 0) {
                    $query .= $values == 0 ? ' HAVING ' . $key . " = '" . DC()->db->dbEscape($value) . "'" : ' AND ' . $key . " = '" . DC()->db->dbEscape($value) . "'";
                    ++$values;
                }
            }
        }

        // DONT MODIFY HERE
        $query .= ' ORDER BY ' . $order['column'] . ' ' . $order['direction'];
        $countquery = count(DC()->db->getSQLResults($query));
        $limitStart = (int) 0 + (int) $limit[0];
        $limitEnd = (int) $limit[1];
        $query .= " LIMIT $limitStart , $limitEnd ";

        $output = ['order' => [
                                        'PK' => [false, 'auftrag.pkOrder', 'Key', false],
                                        'Progressbarvalue' => [false, 'status.percentvalue', 'progressbar', false],
                                        'Betrag' => [true, 's_order.invoice_amount', 'Betrag', false],
                                        'Offen' => [$nVOPStatus == 39 || $nVOPStatus == 100 ? true : false, 'fWert', 'Offen', false],
                                        'offenVOP' => [$nVOPStatus == 39 || $nVOPStatus == 100 ? false : true, 'OffenVOP', 'Offen (VOP)', false],
                                        'RechnungNr' => [true, 'auftrag.cRechnungsNr', 'RechnungsNr', true],
                                        'AuftragsNr' => [true, 's_order.ordernumber', 'AuftragsNr', true],
                                        'Datum' => [true, 'datum', 'Datum', false],
                                        'Anrede' => [false, 'auftrag.cAnrede', 'Anrede', false],
                                        'Firma' => [true, 'auftrag.cFirma', 'Firma', true],
                                        'Vorname' => [true, 'auftrag.cVorname', 'Vorname', true],
                                        'Nachname' => [true, 'auftrag.cNachname', 'Nachname', true],
                                        'Kundengruppe' => [
                                                        true, 'kundengruppename', 'Kundengruppe', false, 'arrayFilter' => $this->getCustomerGroups(),
                                                        ],
                                        'Zahlart' => [
                                                    true, 'zahlartname', 'Zahlart', false, 'arrayFilter' => $this->getPaymentMethods(),
                                                ],
                                        'Zahlstatus' => [true, 'zahlstatus', 'Zahlstatus', false],
                                        'Land' => [false, 'auftrag.cLand', 'Land', true],
                                        'sumGesamt' => [false, 'invoice_amount', 'Betrag', false],
                                        'sumOffen' => [false, 'OffenVOP', 'Offen', false],
                                        ],
                    'query' => $query, 'count' => $countquery, ];

        return $output;
    }

    public function testApi()
    {
        try {
            $request = DC()->API->shopware->get('orders?limit=1');

            if ($request['success']) {
                DC()->smarty->assign('apiteststatus', "<b style='color:green'>API-Zugriff vorhanden</b>");
            } else {
                DC()->smarty->assign('apiteststatus', "<b style='color:red'>API-Zugriff NICHT vorhanden</b>");
            }
            //apiteststatus
        } catch (Exception $e) {
            DC()->smarty->assign('API_ERROR', $e->getMessage());
            DC()->smarty->assign('apiteststatus', "<b style='color:red'>API-Zugriff NICHT vorhanden</b>");
        }
    }

    public function changeOrder($pkOrder, $payment_date, $paymentstatus, $orderstatus, $comment = '', $partialpaymentstatus = null, $remove = false)
    {
        try {
            if (!$this->setStatus()) {
                DC()->Log('Status', 'Zahlungsstatus wird nicht gesetzt ( Einstellung )', 1, $pkOrder);

                return true;
            }
            $params = [];
            if (isset($payment_date) && $paymentstatus > 0 && DC()->settings->currentHBCI['setpaymentdate'] > 0) {
                $date = new DateTime($payment_date);
                $date = $date->format(DateTime::ATOM);
                $params['clearedDate'] = $date;
            } elseif ($remove && DC()->settings->currentHBCI['setpaymentdate'] > 0) {
                $params['clearedDate'] = null;
            }

            if ($paymentstatus != null && $paymentstatus != 'null') {
                $params['paymentStatusId'] = $paymentstatus;
            } elseif ($partialpaymentstatus != null && $partialpaymentstatus != 'null') {
                $params['paymentStatusId'] = $partialpaymentstatus;
            }

            if ($orderstatus != null && $orderstatus != 'null') {
                $params['orderStatusId'] = $orderstatus;
            }
            if (strlen($comment) > 0) {
                $params['comment'] = $comment;
            }
            if (count($params) < 1) {
                return true;
            }

            $request = DC()->API->shopware->put('orders/' . $pkOrder, $params);

            if (!$request['success']) {
                return false;
            }

            DC()->Log('Status', 'Zahlungsstatus gesetzt', 0, $pkOrder);
            if ($orderstatus > 0) {
                DC()->Log('Status', 'Bestellstatus gesetzt', 0, $pkOrder);
            }

            return true;
        } catch (Exception $e) {
            DC()->smarty->assign('API_ERROR', $e->getMessage());
            DC()->Log('API_ERROR', $e->getMessage(), 10, $pkOrder);

            return false;
        }
    }

    public function getIdentNumber($shopID)
    {
        $getMandref = 'SELECT `value` from s_core_config_values where element_id = 945 and shop_id = ' . (int) $shopID;
        $rs = DC()->db->singleResult($getMandref);
        $value = unserialize($rs['value']);

        return $value;
    }

    public function createDTA($pkOrder)
    {
        // ordernumber,invoicenumber,customernumber,amount,firstname,lastname,company,iban,bic,bankname
        $query = 'select s_order.ordernumber,s_order_documents.docID as invoicenumber,s_user.customernumber , CAST(ROUND(SUM(s_order.invoice_amount),2) as DECIMAL (12,2)) as amount 
	 		   ,s_order_billingaddress.firstname,s_order_billingaddress.lastname,s_order_billingaddress.company
			   ,s_core_payment_data.iban,s_core_payment_data.bic,s_core_payment_data.bankname
	 			from s_order ';
        $query .= ' LEFT OUTER JOIN s_order_documents on s_order_documents.orderID = s_order.id and `type` = 1  ';
        $query .= 'LEFT JOIN s_user on s_user.id = s_order.userID ';
        $query .= ' LEFT JOIN s_core_payment_data on s_core_payment_data.user_id = s_order.userID  ';
        $query .= ' LEFT JOIN s_order_billingaddress on s_order_billingaddress.orderID = s_order.id ';
        $query .= ' where s_order.id = ' . $pkOrder;

        return DC()->db->singleResult($query);
    }

    public function createSynch($pkOrder)
    {
    }

    public function getAuftragDetail($pkOrder)
    {
        $query = "SELECT docID as cNr ,CAST(`amount` as DECIMAL ( 12,2)) as fWert,`date` as datum,'Rechnung' as cArt,
			  s_order_documents.ID as kDetail," . $pkOrder . ' as pkOrder  from s_order_documents where `type` =  1 and orderID = ' . (int) $pkOrder;
        //STORNORECHNUNG
        $query .= " UNION ALL SELECT docID cNr ,CAST((`amount`*-1) as DECIMAL ( 12,2)) as fWert,`date` as datum,'Korrektur' as cArt,
				s_order_documents.ID as kDetail," . $pkOrder . ' as pkOrder  from s_order_documents where `type` =  4 and orderID = ' . (int) $pkOrder;
        // GUTSCHRIFT
        $query .= " UNION ALL SELECT docID cNr ,CAST(`amount` as DECIMAL ( 12,2)) as fWert,`date` as datum,'Korrektur' as cArt, s_order_documents.ID as kDetail," . $pkOrder . ' as pkOrder 
				 from s_order_documents where `type` =  3 and orderID = ' . (int) $pkOrder;
        // ZAHLUNGSEINGANG
        $query .= " 	UNION ALL SELECT dc_tzahlung.kUmsatz as cNr ,CAST(ROUND(SUM(dc_tzahlung.fWert),2) as DECIMAL (12,2)) as fWert,dc_umsatz.dBuchung as datum,'Zahlung' as cArt, dc_tzahlung.kUmsatz as kDetail," . $pkOrder . ' as pkOrder  
				FROM `dc_tzahlung` LEFT JOIN dc_umsatz on dc_umsatz.kUmsatz = dc_tzahlung.kUmsatz WHERE dc_umsatz.nType = 0 AND `pkOrder` = ' . (int) $pkOrder . '  
				group by dc_tzahlung.kUmsatz,dc_umsatz.dBuchung ';

        if ($this->PickwarePaymentInformation) {
            $query .= " UNION ALL SELECT id*-1 as cNr,CAST(ROUND(amount,2) as DECIMAL(12,2)) as fWert,creationDate as datum,'Zahlung' as cArt,id*-1 as kDetail," . $pkOrder . ' as pkOrder 
	             FROM `s_plugin_viison_bank_transfer_matching_booking` WHERE orderId = ' . $pkOrder;
        }
        // ZAHLUNGSAUSGANG
        $query .= " UNION ALL SELECT dc_tzahlung.kUmsatz as cNr ,CAST(ROUND(SUM(dc_tzahlung.fWert),2) AS DECIMAL(12,2)) as fWert,dc_umsatz.dBuchung as datum,'Zahlung' as cArt, dc_tzahlung.kUmsatz as kDetail ," . $pkOrder . ' as pkOrder FROM `dc_tzahlung`
			  LEFT JOIN dc_umsatz on dc_umsatz.kUmsatz = dc_tzahlung.kUmsatz WHERE dc_umsatz.nType = 1 AND  dc_tzahlung.nType != 8  and  dc_tzahlung.nType != 1 and `pkOrder` = ' . (int) $pkOrder . '  
			  group by dc_tzahlung.kUmsatz,dc_umsatz.dBuchung ';

        //echo $query;

        return DC()->db->getSQLResults($query);
    }

    public function getDTAList($limitStart, $limitEnd, $order, $filter, $fieldModes)
    {
        // RESET MAHNSTOP
        $limitStart = (int) $limitStart;
        $zahlungseingang = "CAST(ROUND(IFNULL(zahlung.fWert,'0.00'),2) AS DECIMAL(12,2))";
        $joinPickware = strlen($this->joinPickwarePayment) > 5 ? $this->joinPickwarePayment . ' _order.id ' : '';
        $sepa = count(DC()->settings->currentSEPA) > 0 ? DC()->settings->currentSEPA : [-1];
        $query = 'SELECT _order.`id` , `ordernumber` , CAST(ROUND(`invoice_amount`,2) AS DECIMAL(12,2)) as invoice_amount,' . $this->SELECT_OFFEN . " as fWert, `ordertime` , billing.salutation, billing.company, billing.customernumber, billing.firstname, billing.lastname, billing.street, billing.zipcode, billing.city, billing.phone, land.iso3
					,rechnung.docID  , DATE_FORMAT(_order.ordertime,'%d.%m.%Y') as datum ,zahlart.description as zahlartname,states.description as zahlstatus,kundengruppe.description as kundengruppename
					," . $zahlungseingang . ' as zahlungseingang 
					FROM `s_order` _order
					LEFT JOIN s_order_billingaddress billing ON _order.id = billing.orderID 
					LEFT OUTER JOIN ( SELECT SUM(fWert) as fWert,pkOrder  FROM `dc_tzahlung` where  nType != 8 and  nType != 1   group by pkOrder ) zahlung on zahlung.pkOrder = _order.id 
					LEFT OUTER JOIN s_order_documents rechnung on rechnung.orderID = _order.id and rechnung.type = 1 
					left outer join s_order_documents gutschrift on gutschrift.orderID = _order.id and gutschrift.type = 3 
					left outer join s_order_documents storno on storno.orderID = _order.id and storno.type = 4 
					LEFT JOIN s_core_countries land on land.id = billing.countryID  ';
        $query .= ' LEFT  JOIN s_core_paymentmeans zahlart on _order.paymentID = zahlart.id ';
        $query .= $joinPickware;
        $query .= ' LEFT  JOIN s_core_states states on _order.cleared = states.id ';
        $query .= ' LEFT JOIN s_user  _user on _user.id = _order.userID ';

        $query .= ' LEFT OUTER JOIN s_core_customergroups  kundengruppe on kundengruppe.groupkey = _user.customergroup ';
        $query .= ' LEFT OUTER JOIN dc_dtacreatelog on dc_dtacreatelog.nType = 1 and dc_dtacreatelog.pkOrder = _order.id ';
        $query .= ' where ' . $zahlungseingang . " = '0.00' AND zahlart.id IN (" . implode(',', $sepa) . ') AND  _order.ordernumber > 0 and IFNULL(dc_dtacreatelog.pkOrder,0) = 0 ';
        $query .= ' and _order.cleared in (17) ';

        if (count($filter) > 0) {
            foreach ($filter as $key => $value) {
                if (strlen($value) > 0) {
                    $query .= " AND $key LIKE '%" . DC()->db->dbEscape($value) . "%' ";
                }
            }
        }
        $query .= ' AND ' . $this->SELECT_OFFEN . ' > 0 ';

        if (is_array($fieldModes)) {
            $values = 0;
            foreach ($fieldModes as $key => $value) {
                if ($key != 'none' && $key != '' && strlen($value) > 0) {
                    $query .= $values == 0 ? ' HAVING ' . $key . " = '" . DC()->db->dbEscape($value) . "'" : ' AND ' . $key . " = '" . DC()->db->dbEscape($value) . "'";
                    ++$values;
                }
            }
        }

        //------------------------------------------

        if (count($order) > 0) {
            $query .= ' ORDER BY ' . $order['column'] . ' ' . $order['direction'];
        }

        $countquery = count(DC()->db->getSQLResults($query));

        $query .= " LIMIT $limitStart , $limitEnd ";

        $output = ['order' => [
                                        'PK' => [false, '_order.id', 'Key', false],
                                        'AuftragsNr' => [true, '_order.ordernumber', 'Auftragsnr', true],
                                        'RechnungsNr' => [true, 'rechnung.docID', 'RechnungsNr', true],
                                        'Betrag' => [true, '_order.invoice_amount', 'Betrag', false],
                                        'Datum' => [true, 'datum', 'Datum', false],
                                        'Anrede' => [false, 'billing.salutation', 'Anrede', false],
                                        'Firma' => [true, 'billing.company', 'Firma', true],
                                        'KundenNr' => [true, 'billing.customernumber', 'KundenNr', true],
                                        'Vorname' => [true, 'billing.firstname', 'Vorname', true],
                                        'Nachname' => [true, 'billing.lastname', 'Nachname', true],
                                        'Kundengruppe' => [true, 'kundengruppename', 'Kundengruppe', false, 'arrayFilter' => $this->getCustomerGroups()],
                                        'Zahlart' => [true, 'zahlartname', 'Zahlart', false],
                                        'Zahlstatus' => [true, 'zahlstatus', 'Zahlstatus', false],
                                        'Land' => [false, 'land.iso3', 'Land', false],
                                        'sumGesamt' => [false, 'invoice_amount', 'Betrag', false],
                                        'sumOffen' => [false, 'fWert', 'Offen', false],
                                    ], 'query' => $query, 'count' => $countquery];

        return $output;
    }

    public function getOPOSList($sort, $filter, $status, $order, $nVOPStatus, $frist, $limit, $kundengruppe = [], $minBetrag = 0, $fieldModes = [], $cronjob = false, $withoutStates = [])
    {
        // RESET MAHNSTOP
        DC()->db->dbQuery(' DELETE from dc_mahnstop where resetDate IS NOT NULL and resetDate <= NOW()');
        $mahnstopSettings = DC()->settings->mahnstopCustomerGroup;
        if (count($mahnstopSettings) > 0) {
            $SELECT_MAHNSTOP = ', CASE WHEN kundengruppe.id in (' . implode(',', $mahnstopSettings) . ') THEN 1 ELSE IFNULL(dc_mahnstop.id,0) END as mahnstop ';
        } else {
            $SELECT_MAHNSTOP = ',IFNULL(dc_mahnstop.id,0) as mahnstop ';
        }
        $status = count($status) > 0 ? $status : [-100];
        $b2bshop = DC()->settings->currentSetting->mahnwesenvorkasse == 'on' ? true : false;
        $paymentID = count(DC()->settings->currentPayments) > 0 ? DC()->settings->currentPayments : [-1];
        $vorkasse = count(DC()->settings->currentVorkasse) > 0 ? DC()->settings->currentVorkasse : [-1];
        $sepa = count(DC()->settings->currentSEPA) > 0 ? DC()->settings->currentSEPA : [-1];
        $joinPickware = strlen($this->joinPickwarePayment) > 5 ? $this->joinPickwarePayment . ' _order.id ' : '';
        $query = 'SELECT _order.`id` , `ordernumber` , CAST(ROUND(`invoice_amount`,2) AS DECIMAL(12,2)) as invoice_amount,' . $this->SELECT_OFFEN . " as fWert, `ordertime` , billing.salutation, billing.company, billing.customernumber, billing.firstname, billing.lastname, billing.street, billing.zipcode, billing.city, billing.phone, land.iso3
					,rechnung.docID  , DATE_FORMAT(IFNULL(auftrag.dtSend,_order.ordertime),'%d.%m.%Y') as datum ,zahlart.description as zahlartname,states.description as zahlstatus,kundengruppe.description as kundengruppename " .
                    $SELECT_MAHNSTOP . "	,sumZahlungen.paymentCount 	,date_format(versand.date,	'%d.%m.%Y')	 as versanddatum,versandstatus.description
					FROM `s_order` _order
					LEFT JOIN s_order_billingaddress billing ON _order.id = billing.orderID 
					LEFT OUTER JOIN ( SELECT max(VOPStatus) as VOPStatus,pkOrder,dtSend,trash from dc_auftrag group by pkOrder,dtSend,trash) auftrag on auftrag.pkOrder = _order.id 
					LEFT OUTER JOIN ( SELECT SUM(fWert) as fWert,pkOrder  FROM `dc_tzahlung` where  dc_tzahlung.nType != 8  and  dc_tzahlung.nType != 1  group by pkOrder ) zahlung on zahlung.pkOrder = _order.id 
					LEFT OUTER JOIN ( SELECT COUNT(fWert) as paymentCount,pkOrder  FROM `dc_tzahlung` where  dc_tzahlung.nType != 8 and  dc_tzahlung.nType != 1  group by pkOrder ) sumZahlungen on sumZahlungen.pkOrder = _order.id 
					LEFT OUTER JOIN s_order_documents rechnung on rechnung.orderID = _order.id and rechnung.type = 1 
					left outer join s_order_documents gutschrift on gutschrift.orderID = _order.id and gutschrift.type = 3 
					left outer join s_order_documents storno on storno.orderID = _order.id and storno.type = 4 
					LEFT OUTER JOIN s_core_states versandstatus on versandstatus.id = _order.status 
					LEFT OUTER JOIN ( SELECT orderID,MIN(change_date) as date from s_order_history where order_status_id in(" . implode(',', DC()->settings->shipping->states) . ') group by orderID ) versand on versand.orderID = _order.id 
					LEFT JOIN s_core_countries land on land.id = billing.countryID  ';
        $query .= ' LEFT  JOIN s_core_paymentmeans zahlart on _order.paymentID = zahlart.id ';
        $query .= $joinPickware;
        $query .= ' LEFT  JOIN s_core_states states on _order.cleared = states.id ';
        $query .= ' LEFT JOIN s_user  _user on _user.id = _order.userID ';
        $query .= ' LEFT OUTER JOIN s_core_customergroups  kundengruppe on kundengruppe.groupkey = _user.customergroup ';
        $query .= ' LEFT OUTER JOIN dc_mahnstop on dc_mahnstop.id =  (SELECT id from dc_mahnstop where dc_mahnstop.pk = _user.id and nType = 1 OR dc_mahnstop.pk = _order.id and nType = 0 LIMIT 1 )';
        $query .= ' where  _order.ordernumber > 0 and  (auftrag.trash = 0 or auftrag.trash IS NULL ) and _order.subshopID =  ' . (int) DC()->settings->selectedShop;

        if (count($filter) > 0) {
            foreach ($filter as $key => $value) {
                if (strlen($value) > 0) {
                    $query .= " AND $key LIKE '%" . DC()->db->dbEscape($value) . "%' ";
                }
            }
        }

        if (count($kundengruppe) > 0) {
            $query .= ' AND kundengruppe.id NOT IN (' . implode(',', $kundengruppe) . ' ) ';
        }

        $query .= ' AND ' . $this->SELECT_OFFEN . ' > 0 ';

        $query .= ' AND ( ';
        // ZAHLART RECHNUNG, RECHNUNG MUSS VORHANDEN SEIN
        $query .= ' (rechnung.id IS NOT NULL AND _order.paymentID in ( ' . implode(',', $paymentID) . ' ) ';
        if (DC()->settings->shipping->overrideInvoice) {
            $query .= ' and versand.date < DATE_SUB(NOW(),INTERVAL ' . (int) $frist . ' DAY) ';
        }
        $query .= ' ) ';
        // VORKASSEAUFTRÄGE
        $query .= ' OR ( _order.paymentID in ( ' . implode(',', $vorkasse) . ' ) and LENGTH(billing.company) > 0 ) ';
        // SEPA MUSS EINEN EINTRAG IN TZAHLUNG HABEN
        $query .= ' OR ( sumZahlungen.paymentCount >= 1 AND  _order.paymentID in ( ' . implode(',', $sepa) . ' )) 		  
							    ) ';

        // WENN KEIN B2BShop ist dann schließe vorkasse wieder aus
        if ($nVOPStatus >= 39 && !$b2bshop) {
            $query .= ' AND _order.paymentID NOT IN ( ' . implode(',', $vorkasse) . ' )';
        }

        $clearedstate = '';
        if ($status) {
            if ($nVOPStatus == 0) {
                $wherevopstatus = '<= 0';
            } elseif ($nVOPStatus == 39) {
                $wherevopstatus = '<= 39 ';
            } elseif ($nVOPStatus == 90) {
                $wherevopstatus = '< 55 ';
            }
            $statuscode = is_array($status) ? implode(',', $status) : $status;

            if ($nVOPStatus > 0) {
                $query .= " AND (  (  auftrag.VOPStatus $wherevopstatus ))  AND IFNULL(auftrag.dtSend,IFNULL(rechnung.date,_order.ordertime)) < DATE_SUB(NOW(),INTERVAL " . (int) $frist . ' DAY) ';
            } else {
                $query .= " AND ( cleared in ( $statuscode ) AND ( auftrag.VOPStatus IS NULL OR auftrag.VOPStatus $wherevopstatus ))  AND IFNULL(auftrag.dtSend,IFNULL(rechnung.date,_order.ordertime)) < DATE_SUB(NOW(),INTERVAL " . (int) $frist . ' DAY) ';
            }
        }

        $query .= ' AND ' . $this->SELECT_OFFEN . " > $minBetrag ";

        if ($cronjob && count($withoutStates) > 0) {
            //cronjob filter status ausschluss
            $query .= ' AND cleared NOT IN (' . implode(',', $withoutStates) . ' ) ';
        }

        if ($cronjob) {
            $query .= ' AND IFNULL(dc_mahnstop.id,0) = 0 ';
        }

        if (is_array($fieldModes)) {
            $values = 0;
            foreach ($fieldModes as $key => $value) {
                if ($key != 'none' && $key != '' && strlen($value) > 0) {
                    $query .= $values == 0 ? ' HAVING ' . $key . " = '" . DC()->db->dbEscape($value) . "'" : ' AND ' . $key . " = '" . DC()->db->dbEscape($value) . "'";
                    ++$values;
                }
            }
        }

        $query .= ' and (_order.status = 0 or _order.status IN (' . implode(',', DC()->settings->shipping->states) . ') ) ';

        //------------------------------------------

        if (count($order) > 0) {
            $query .= ' ORDER BY ' . $order['column'] . ' ' . $order['direction'];
        }

        $countquery = count(DC()->db->getSQLResults($query));
        $limitStart = (int) 0 + (int) $limit[0];
        $limitEnd = (int) $limit[1];
        if (count($limit) > 0) {
            $query .= " LIMIT $limitStart , $limitEnd ";
        }

        $output = ['order' => [
                                        'PK' => [false, '_order.id', 'Key', false],
                                        'AuftragsNr' => [true, '_order.ordernumber', 'Auftragsnr', true],
                                        'RechnungsNr' => [true, 'rechnung.docID', 'RechnungsNr', true],
                                        'Betrag' => [true, '_order.invoice_amount', 'Betrag', false],
                                        'Offen' => [true, 'fWert', 'Offen', false],
                                        'Datum' => [true, 'datum', 'Datum', false],
                                        'Anrede' => [false, 'billing.salutation', 'Anrede', false],
                                        'Firma' => [true, 'billing.company', 'Firma', true],
                                        'KundenNr' => [true, 'billing.customernumber', 'KundenNr', true],
                                        'Vorname' => [true, 'billing.firstname', 'Vorname', true],
                                        'Nachname' => [true, 'billing.lastname', 'Nachname', true],
                                        'Kundengruppe' => [true, 'kundengruppename', 'Kundengruppe', false, 'arrayFilter' => $this->getCustomerGroups()],
                                        'Zahlart' => [true, 'zahlartname', 'Zahlart', false, 'arrayFilter' => $this->getPaymentMethods()],
                                        'Zahlstatus' => [true, 'zahlstatus', 'Zahlstatus', false],
                                        'Land' => [false, 'land.iso3', 'Land', false],
                                        'mahnstop' => [false, 'mahnstop', 'mahnstop', false],
                                        'sumGesamt' => [false, 'invoice_amount', 'Betrag', false],
                                        'sumOffen' => [false, 'fWert', 'Offen', false],
                                         'bestellstatus' => [true, 'versandstatus.description', 'Bestellstatus', false],
                                         'versanddate' => [true, 'versanddatum', 'Versanddatum', false],
                                    ], 'query' => $query, 'count' => $countquery];

        return $output;
    }

    public function getStandardValues()
    {
        $values = [];
        $values['states'] = [9, 10, 11, 13, 14, 15, 16, 17];
        $values['shipping'] = ['overrideInvoices' => 0, 'states' => [7]];
        $values['payment'] = [4];
        $values['vorkasse'] = [5];
        $values['sepa'] = [2, 6];
        $values['hbci'] = ['statusbezahlt' => 12,
                            'teilzahlung' => 11,
                            'orderstatus' => 1,
                            'setpaymentdate' => 1,
                            'bestaetigung' => 0,
                            'bankruecklast' => 17,
                            'betreff' => 'Zahlungseingang bei Ihr-Onlineshop', ];

        $values['mainsettings'] = ['shopwareapibenutzen' => 0,
                                    'gutschriften' => 1,
                                    'mahnwesenvorkasse' => 'on',
                                    'statusZE' => 13,
                                    'fristZE' => 14,
                                    'zeArt' => 1,
                                    'smtpbetreff' => 'Zahlungserinnerung Ihr-Onlineshop',
                                    'smtpabsender' => '',
                                    'smtpkopie' => '',
                                    'statusMA' => '14',
                                    'fristMA' => '7',
                                    'statusIN' => '16',
                                    'blackliste' => 0, ];

        foreach ($values as $key => $value) {
            DC()->getConf($key, json_encode($value));
        }
    }
}
