# ***********************************************************************
# * Filename:   reset.sql                                               *
# * System:     steve's NDB Database                                    *
# * Date:       01/09/2003                                              *
# ***********************************************************************

# To build an update file:
#   1) Remove all drop and create statements
#   2) Remember the INSERT IGNORE option avoids duplicates only with mySQL 3.22.10 and later.

# Remember to triple escape any single quotes in data if manually editing:
#    e.g. "The user\\\'s account"

# ************************************
# * Table Structures:                *
# ************************************

DROP TABLE IF EXISTS `station`;
CREATE TABLE `station` (
  `ID` char(13) NOT NULL default '',
  `khz` char(5) NOT NULL default '',
  `call` char(5) NOT NULL default '',
  `qth`  varchar(20) NOT NULL default '',
  `sta`  char(3) NOT NULL default '',
  `cnt`  char(3) NOT NULL default '',
  `lat`   FLOAT(4) signed NOT NULL default '0',
  `lon`   FLOAT(4) signed NOT NULL default '0',
  PRIMARY KEY(`ID`)
) TYPE=MyISAM;


DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `ID` char(13) NOT NULL default '',
  `stationID` char(13) NOT NULL default '',
  `yyyymmdd`  char(8) NOT NULL,
  `rx`  char(1) NOT NULL default '',
  `notes`  varchar(30),
  PRIMARY KEY(`ID`)
) TYPE=MyISAM;




#
# (Start of table data)
#


INSERT IGNORE INTO `station` VALUES
('3f54839a6a795','280','IPA','Easter Island','','CHL','-27.1542','-109.4217'),
('3f5483ab5fd4f','282.5','RT','Rurutu','','OCE','22.26','-109.4217'),
('3f5483ee839c9','284.5','MH','Manihi','','TUA','-14.4328','-146.0622'),
('3f5483ee83d32','316','MAJ','Majuro','','MHL','7.0686','171.2817'),
('3f5483ee83f7f','332','POA','Pahoa','','HWA','19.5433','-154.975'),
('3f5483ee841c3','332.5','AA','Anaa','','TUA','-17.3556','-145.4817'),
('3f5483ee843fe','345','HH','Huahine','','TUA','-16.6911','-151.0297'),
('3f5483ee8463a','349','TP','Takapoto','','TUA','-14.7147','-145.2525'),
('3f5483ee84879','352','RG','Rarotonga','','CKS','-21.2083','-159.8217'),
('3f5483ee84ab9','353','LLD','Lanai','','HWA','20.7722','-156.9733'),
('3f5483ee84d24','358','OA','Rangiroa','','TUA','-14.9558','-147.6603'),
('3f5483ee84fc2','366','PNI','Pohnpei','','FSM','6.9722','158.1917'),
('3f5484468f663','367','HA','Hao','','TUA','-18.0642','-140.9714'),
('3f5484468f9c8','373','HHI','Wheeler','','HWA','21.4778','-158.0333'),
('3f5484468fe33','377.5','MO','Moorea','','OCE','-17.4783','-149.7744'),
('3f548446900c6','378','APO','Apollo','','JON','16.7264','-169.5467'),
('3f548446902fe','384','BB','Bora Bora','','OCE','-16.4436','-151.7544'),
('3f54844690533','403','TUT','Pago Pago','','SMA','-14.3319','-170.7194');

#
# (End of table data)
#

