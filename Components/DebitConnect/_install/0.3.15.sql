 CREATE TABLE `dc_cronlog` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`dAction` DATETIME NOT NULL ,
`cStep` VARCHAR( 128 ) NOT NULL ,
`cResult` TEXT NULL ,
 `pkOrder` INT NULL ,
`jResult` TEXT  NULL ,
`bIserror` INT NOT NULL DEFAULT '0'
) ENGINE = MYISAM