CREATE TABLE IF NOT EXISTS `t_email_queue_redeem` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `from_name` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `to` varchar(100) COLLATE utf8_bin NOT NULL,
  `bcc` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `subject` varchar(250) COLLATE utf8_bin NOT NULL,
  `message` text COLLATE utf8_bin NOT NULL,
  `is_bcc` tinyint(1) NOT NULL DEFAULT '1',
  `is_sent` tinyint(1) NOT NULL DEFAULT '0',
  `object` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `memo` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `create_by` int(10) NOT NULL DEFAULT '0',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_by` int(10) DEFAULT NULL,
  `update_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;