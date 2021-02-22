CREATE TABLE IF NOT EXISTS `dc_identcheck_log` (
  `kKunde` int(11) NOT NULL,
  `tstamp` datetime NOT NULL, 
  `handle` varchar(32) NOT NULL,
  `type` int(1) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `dc_gatewayLog` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `customer_vname` varchar(20) DEFAULT NULL,
  `customer_nname` varchar(20) DEFAULT NULL,
  `warenkorb` varchar(20) DEFAULT NULL,
  `zahlungsart` varchar(56) DEFAULT NULL,
  `pruefung` varchar(56) DEFAULT NULL,
  `ergebnis` varchar(56) DEFAULT NULL,
  `tstamp` varchar(56) DEFAULT NULL,
  `error` varchar(2048) DEFAULT NULL,
  `abschluss` varchar(56) DEFAULT NULL,
  `sessToken` varchar(32) DEFAULT NULL,
  `customer_firma` varchar(128) DEFAULT NULL,
  `cArt` varchar(32) NOT NULL,
  `responseCode` int(5) NOT NULL,
  `responseText` varchar(128) NOT NULL,
  `scoreInfo` varchar(128) NOT NULL,
  PRIMARY KEY (`logid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `dc_gatewaymeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopID` int(11) NOT NULL,
  `art` varchar(64) NOT NULL,
  `datavalue` text NOT NULL,
  `nType` int(1) NOT NULL DEFAULT '0',
  `comment` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `dc_hbciProfiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopID` int(11) NOT NULL,
  `profileName` varchar(32) NOT NULL,
  `profileData` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


INSERT INTO `dc_gatewaymeta` (`id`, `shopID`, `art`, `datavalue`, `nType`, `comment`) VALUES
(1, 0, 'postident_notice_identcheck', 'Sie können Ihre Identität einfach, sicher und schnell bestätigen lassen. Mit Hilfe der POSTID, einem Service der Deutschen Post.', 1, 'Hinweis keine Postid'),
(2, 0, 'postident_notice_agecheck', 'Sie können Ihr Alter einfach, sicher und schnell bestätigen lassen. Mit Hilfe der POSTID, einem Service der Deutschen Post.', 1, 'Hinweis keine Postid'),
(3, 0, 'postident_identify', 'Sie haben bereits eine POSTID? Dann nutzen Sie diese und setzen gleich Ihren Einkauf fort.', 1, 'Hinweis keine Postid'),
(4, 0, 'postident_register', 'Sie haben noch keine POSTID? Dann registrieren Sie sich jetzt kostenlos und kehren anschließend hierhin zurück.', 1, 'Hinweis keine Postid'),
(5, 0, 'postident_ausweisen', 'Sie haben bereits eine POSTID? Dann nutzen Sie diese und setzen gleich Ihren Einkauf fort.', 1, 'Hinweis jetzt Ausweisen'),
(6, 0, 'alertmsg_shipping', 'Aufgrund altersbeschränkter Artikel stehen diese Versandarten nicht zur Verfügung', 1, 'Hinweis das die Versandarten nicht zur Verfügung stehen'),
(7, 0, 'agecheck_warenkorb_msg', 'Sie haben altersbeschränkte Artikel in Ihrem Warenkorb', 1, 'Hinweis auf Altersverifizierung im Warenkorb'),
(8, 0, 'identcheck_qbit_dataerror_msg', 'Folgende eingegebenen Daten weichen vom Bestand ab', 1, 'Folgende eingegebenen Daten weichen vom Bestand ab'),
(9, 0, 'jtl_eap_identcheck_required', 'Identitätsprüfung wird benötigt', 1, 'Identitätsprüfung wird benötigt Überschrift'),
(10, 0, 'jtl_eap_identcheck_failed_msg', 'Sie haben die Möglichkeit Ihre Adresse zu korrigieren, andernfalls kontaktieren Sie bitte unseren Support', 1, 'Identitätsprüfung fehlgeschlagen Text'),
(11, 0, 'jtl_eap_identcheck_failed_headline', 'Identitätsprüfung fehlgeschlagen', 1, 'Identitätsprüfung fehlgeschlagen Überschrift'),
(12, 0, 'jtl_eap_identcheck_addrchange', 'Adresse korrigieren', 1, 'Adresse korrigieren'),
(13, 0, 'jtl_eap_identcheck_notice', 'Sie haben Artikel mit Altersbeschränkung in Ihrem Warenkorb. Um fortfahren zu können, müssen Sie ihr Alter bestätigen.', 1, 'Hinweis auf Altersüberprüfung'),
(14, 0, 'jtl_eap_abweichend_adresse', 'Zahlungsart bei abweichender Lieferadresse nicht möglich', 1, 'Lieferadresse abweichend'),
(15, 0, 'jtl_eap_fortfahren_text', 'Bitte klicken Sie auf Bestellung fortfahren', 1, 'Hinweis nach der FancyBox das der Kunde auf Bestellung fortfahren klicken soll'),
(16, 0, 'jtl_eap_geb_text', 'Geburtsdatum(*)', 1, 'Geburtsdatum'),
(17, 0, 'jtl_eap_tel_text', 'Telefonnummer(*)', 1, 'Telefon'),
(18, 0, 'jtl_eap_continuebutton', 'Mit Bestellung fortfahren', 1, 'Weiter mit Zahlart'),
(19, 0, 'jtl_eap_eingabe_notice', 'Um diese Zahlart nutzen zu können müssen Sie ihr Geburtsdatum eingeben', 1, 'Geburtsdatum Abfrage'),
(20, 0, 'jtl_eap_abbrucbbutton', 'Abbrechen', 1, 'Zahlart Abbrechen'),
(21, 0, 'jtl_eap_pruefung_removed_payment', 'Bitte wählen Sie eine alternative Zahlungsart.', 1, 'Hinweis das die gewünschte Zahlungsart für den Kunden nicht zur Verfügung steht - Zahlungsart wurde entfernt'),
(22, 0, 'jtl_eap_pruefung_vor_zahlung', 'Diese Zahlungsart ist leider nicht möglich.', 1, 'Hinweis das die gewünschte Zahlungsart für den Kunden nicht zur Verfügung steht - Zahlungsart wurde deaktiviert'),
(23, 0, 'postident_notice_highcart', 'Um diese Zahlart bei erhötem Warenkorb verwenden zu können, müssen Sie bitte Ihre Identität bestätigen.', 1, 'Hinweis auf Identitätsprüfüfung aufgrund des hohen Warenkorbes'),
(24, 0, 'EAP_COMPANY_NOTICE', 'Sie haben in Ihrer Rechnungsadresse <b>{$Kunde->cFirma}</b> als Firma angegeben<br>Bitte wählen Sie die Rechtsform aus', 1, 'Hinweis auf eine nicht erkannte Firmeneingabe'),
(25, 0, 'headline_boni', 'Geburtsdatum benötigt', 1, 'Hinweis auf Geburtsdatumeingabe bei Zahlartwahl'),
(26, 0, 'alertmsg_payment', '** Diese Zahlungsart ist leider nicht möglich', 1, 'Hinweis das die Zahlungsart abgelehnt wird');

CREATE TABLE IF NOT EXISTS `dc_dtaCreateLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idTransaktion` int(11) NOT NULL,
  `pkOrder` int(11) NOT NULL,
  `nType` int(11) NOT NULL,
  `dateCreated` date NOT NULL,
  `dtaFile` text,
  `cTransaktion` varchar(32) DEFAULT NULL,
  `idProfile` int(11) DEFAULT NULL,
  `idKonto` varchar(128) DEFAULT NULL,
  `shopID` int(11) NOT NULL,
  `nAnzahl` int(11) NOT NULL,
  `fSumme` decimal(10,2) NOT NULL,
  `dDownload` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;