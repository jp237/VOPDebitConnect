CREATE TABLE IF NOT EXISTS `dc_auftrag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pkOrder` int(11) NOT NULL,
  `VOPStatus` int(11) NOT NULL,
  `oldVOPStatus` int(11) NOT NULL,
  `dtSend` datetime NOT NULL,
  `trash` int(11) NOT NULL,
  `cAnrede` varchar(128) NOT NULL,
  `cFirma` varchar(128) NOT NULL,
  `cVorname` varchar(128) NOT NULL,
  `cNachname` varchar(128) NOT NULL,
  `cStrasse` varchar(128) NOT NULL,
  `cPLZ` varchar(128) NOT NULL,
  `cOrt` varchar(128) NOT NULL,
  `cLand` varchar(128) NOT NULL,
  `cTel` varchar(128) NOT NULL,
  `cMail` varchar(128) NOT NULL,
  `cRechnungsNr` varchar(128) DEFAULT NULL,
  `cAuftragsNr` varchar(32) NOT NULL,
  `fWert` decimal(12,2) NOT NULL,
  `fZahlung` decimal(12,1) NOT NULL,
  `subshopID` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `dc_AuftragDetail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pkOrder` int(11) NOT NULL,
  `tstamp` date NOT NULL,
  `cArt` varchar(32) NOT NULL,
  `fWert` decimal(12,2) NOT NULL,
  `cNr` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `dc_firma` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopID` int(11) NOT NULL,
  `vopUser` varchar(32) NOT NULL,
  `vopToken` varchar(32) NOT NULL,
  `activated` int(11) NOT NULL,
  `registerJson` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `dc_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pkOrder` int(11) NOT NULL DEFAULT '0',
  `tstamp` datetime NOT NULL,
  `kUser` int(11) DEFAULT NULL,
  `shopID` int(11) NOT NULL,
  `art` varchar(32) NOT NULL,
  `logdata` text NOT NULL,
  `errormsg` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `dc_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopID` int(11) NOT NULL,
  `art` varchar(32) NOT NULL,
  `datavalue` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `dc_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pkOrder` int(11) NOT NULL,
  `fOffen` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fGesamt` decimal(12,2) NOT NULL DEFAULT '0.00',
  `nMandart` int(11) NOT NULL DEFAULT '0',
  `nTZV` int(11) NOT NULL DEFAULT '0',
  `nStatus` int(11) NOT NULL DEFAULT '0',
  `nTituliert` int(11) NOT NULL DEFAULT '0',
  `nErledigt` int(11) NOT NULL DEFAULT '0',
  `nAdresse` int(11) NOT NULL DEFAULT '0',
  `nAdresseDat` int(11) NOT NULL DEFAULT '0',
  `lastLea` int(11) NOT NULL DEFAULT '0',
  `lastLeaBack` int(11) NOT NULL DEFAULT '0',
  `orderhash` varchar(32) DEFAULT NULL,
  `lastSync` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pkOrder` (`pkOrder`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `dc_tZahlung` (
  `kZahlung` int(11) NOT NULL AUTO_INCREMENT,
  `kUmsatz` int(11) NOT NULL,
  `nType` int(11) NOT NULL,
  `fWert` decimal(10,2) NOT NULL,
  `pkOrder` int(11) NOT NULL,
  `cSKR` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`kZahlung`),
  UNIQUE KEY `kUmsatz` (`kUmsatz`,`nType`,`fWert`,`pkOrder`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `dc_Umsatz` (
  `kUmsatz` int(11) NOT NULL AUTO_INCREMENT,
  `kShop` int(11) NOT NULL,
  `IdUmsatz` varchar(32) NOT NULL,
  `IdKonto` varchar(32) NOT NULL,
  `dBuchung` datetime NOT NULL,
  `cName` varchar(1024) NOT NULL,
  `cVzweck` varchar(2048) NOT NULL,
  `fWert` varchar(12) NOT NULL,
  `nVerbucht` int(11) NOT NULL,
  `nNichtverbuchen` int(11) NOT NULL,
  `dAbgleich` datetime NOT NULL,
  `kUser` int(11) NOT NULL,
  `nType` int(1) NOT NULL,
  PRIMARY KEY (`kUmsatz`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;