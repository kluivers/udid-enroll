CREATE TABLE `udids` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `udid` varchar(40) DEFAULT NULL,
  `name` text,
  `email` text,
  `product` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=112 DEFAULT CHARSET=utf8;