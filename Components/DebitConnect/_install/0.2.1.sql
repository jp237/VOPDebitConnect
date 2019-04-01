CREATE TABLE IF NOT EXISTS `dc_Mahnstop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nType` int(11) NOT NULL,
  `pk` int(11) NOT NULL,
  `resetDate` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `dc_Rechnung` (
  `pkOrder` int(11) NOT NULL,
  `kLaufnr` int(11) NOT NULL,
  `nRechJahr` int(11) NOT NULL,
  `nRechNr` int(11) NOT NULL,
  `dErstellt` date NOT NULL,
  `fUst` decimal(12,2) DEFAULT NULL,
  `fSumme` decimal(12,2) DEFAULT NULL,
  `cRechtext` varchar(128) DEFAULT NULL,
  `cName1` varchar(128) DEFAULT NULL,
  `cName2` varchar(128) DEFAULT NULL,
  `cStrasse` varchar(128) DEFAULT NULL,
  `cPLZ` varchar(128) DEFAULT NULL,
  `cOrt` varchar(128) DEFAULT NULL,
  `bDocument` mediumblob,
  `fZEGL` decimal(12,2) DEFAULT NULL,
  `fZEVOP` decimal(12,2) DEFAULT NULL,
  `fVorschuss` decimal(12,2) DEFAULT NULL,
  `fZahlbetrag` decimal(12,2) DEFAULT NULL,
  `fAusgezahlt` decimal(12,2) DEFAULT NULL,
  `cTransaktion` varchar(32) DEFAULT NULL,
  `cKommentar` varchar(128) DEFAULT NULL,
  `cRichtung` varchar(1) DEFAULT NULL,
  `dGesehen` date DEFAULT NULL,
  `dGebucht` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `dc_RechPos` (
  `kLaufnr` int(11) NOT NULL,
  `nZNR` int(11) NOT NULL,
  `nArtzeile` int(11) NOT NULL,
  `fMingeb` decimal(12,2) DEFAULT NULL,
  `fMaxgeb` decimal(12,2) DEFAULT NULL,
  `cGebText` varchar(128) DEFAULT NULL,
  `fGebuehr` decimal(12,2) DEFAULT NULL,
  `fGebuehr1` decimal(12,2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


ALTER TABLE `dc_AuftragDetail` ADD `kDetail` INT NOT NULL ;