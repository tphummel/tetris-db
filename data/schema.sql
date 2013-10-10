CREATE DATABASE IF NOT EXISTS `tnt` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `tnt`;

CREATE TABLE IF NOT EXISTS `location` (
  `locationid` int(15) NOT NULL AUTO_INCREMENT,
  `locationname` varchar(30) DEFAULT NULL,
  `address` varchar(60) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `createdby` int(15) NOT NULL,
  `locationdescription` varchar(255) DEFAULT NULL,
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `image` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`locationid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

CREATE TABLE IF NOT EXISTS `player` (
  `playerid` int(15) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(15) DEFAULT NULL,
  `lastname` varchar(15) DEFAULT NULL,
  `username` varchar(15) NOT NULL,
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `image` varchar(50) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  PRIMARY KEY (`playerid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

CREATE TABLE IF NOT EXISTS `playermatch` (
  `matchid` int(15) NOT NULL,
  `playerid` int(15) NOT NULL,
  `lines` int(5) NOT NULL,
  `time` int(5) NOT NULL,
  `wrank` int(5) NOT NULL,
  `erank` int(5) NOT NULL,
  PRIMARY KEY (`matchid`,`playerid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tntmatch` (
  `matchid` int(15) NOT NULL AUTO_INCREMENT,
  `matchdate` date NOT NULL,
  `inputstamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `enteredby` int(15) NOT NULL,
  `location` int(15) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `universe` int(15) NOT NULL,
  PRIMARY KEY (`matchid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14412 ;