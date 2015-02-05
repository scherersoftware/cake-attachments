CREATE TABLE `attachments` (
  `id` char(36) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filetype` varchar(45) NOT NULL,
  `filesize` int(10) NOT NULL,
  `model` varchar(255) NOT NULL,
  `foreign_key` char(36) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;