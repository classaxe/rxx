# ************************************
# * Table Structures:                *
# ************************************

DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `ID` varchar(13) NOT NULL default '',
  `stationID` varchar(13) NOT NULL default '',
  `yyyymmdd` varchar(8) NOT NULL default '',
  `rx` char(1) NOT NULL default '',
  `notes` varchar(30) default NULL,
  PRIMARY KEY(`ID`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `station`;
CREATE TABLE `station` (
  `ID` varchar(13) NOT NULL default '',
  `khz` varchar(5) NOT NULL default '',
  `call` varchar(3) NOT NULL default '',
  `qth` varchar(20) NOT NULL default '',
  `sta` char(3) NOT NULL default '',
  `cnt` char(3) NOT NULL default '',
  `lat` float NOT NULL default '0',
  `lon` float NOT NULL default '0',
  PRIMARY KEY(`ID`)
) TYPE=MyISAM;

# ************************************
# * (End of Table Structures)        *
# ************************************

# ************************************
# * Table Data:                      *
# ************************************

INSERT IGNORE INTO `station` VALUES
('3f54839a6a795','280','IPA','Easter Island','','CHL',-27.1542,-109.422),
('3f5483ab5fd4f','282.5','RT','Rurutu','','OCE',22.26,-151.367),
('3f5483ee839c9','284.5','MH','Manihi','','TUA',-14.4328,-146.062),
('3f5483ee83d32','316','MAJ','Majuro','','MHL',7.0686,171.282),
('3f5483ee83f7f','332','POA','Pahoa','','HWA',19.5433,-154.975),
('3f5483ee841c3','332.5','AA','Anaa','','TUA',-17.3556,-145.482),
('3f5483ee843fe','345','HH','Huahine','','TUA',-16.6911,-151.03),
('3f5483ee8463a','349','TP','Takapoto','','TUA',-14.7147,-145.253),
('3f5483ee84879','352','RG','Rarotonga','','CKS',-21.2083,-159.822),
('3f5483ee84ab9','353','LLD','Lanai','','HWA',20.7722,-156.973),
('3f5483ee84d24','358','OA','Rangiroa','','TUA',-14.9558,-147.66),
('3f5483ee84fc2','366','PNI','Pohnpei','','FSM',6.9722,158.192),
('3f5484468f663','367','HA','Hao','','TUA',-18.0642,-140.971),
('3f5484468f9c8','373','HHI','Wheeler','','HWA',21.4778,-158.033),
('3f5484468fe33','377.5','MO','Moorea','','OCE',-17.4783,-149.774),
('3f548446900c6','378','APO','Apollo','','JON',16.7264,-169.547),
('3f548446902fe','384','BB','Bora Bora','','OCE',-16.4436,-151.754),
('3f54844690533','403','TUT','Pago Pago','','SMA',-14.3319,-170.719);

#
# (End of table data)
#

