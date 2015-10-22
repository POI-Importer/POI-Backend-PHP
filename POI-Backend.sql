CREATE TABLE IF NOT EXISTS `content` (
  `feature_id` varchar(50) NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `comment` varchar(500) NOT NULL,
  PRIMARY KEY (`feature_id`,`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `status` (
  `feature_id` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  PRIMARY KEY (`feature_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
