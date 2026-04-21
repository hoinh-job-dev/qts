CREATE TABLE IF NOT EXISTS `t_btc_txs` (
  `tx_id` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `out_address` varchar(35) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `balance` double(18,8) DEFAULT NULL,
  `tx_data` text NOT NULL,
  `status` int(11) NOT NULL,
  `memo` text NOT NULL,
  PRIMARY KEY (`tx_id`, `out_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `t_block` (
  `hash` varchar(65) NOT NULL,
  `height` int(11) NOT NULL,
  `txs` text NOT NULL,
  `complete` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `t_btc_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(35) CHARACTER SET utf32 COLLATE utf32_bin NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `order_number` int(11) DEFAULT NULL,
  `create_by` int(11) DEFAULT NULL,
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_by` int(11) DEFAULT NULL,
  `update_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`address`),
  UNIQUE KEY `id` (`id`),
  KEY `address` (`address`),
  KEY `address_2` (`address`),
  KEY `address_3` (`address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `t_token_approved` (
  `order_number` int(10) NOT NULL AUTO_INCREMENT,
  `status` varchar(3) COLLATE utf8_bin NOT NULL,
  `agent_uid` varchar(32) COLLATE utf8_bin NOT NULL,
  `client_uid` varchar(32) COLLATE utf8_bin NOT NULL,
  `btc_amount` double(18,8) NOT NULL,
  `btc_address` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `closed_date` char(14) COLLATE utf8_bin DEFAULT NULL COMMENT 'Foreign key point to closed_date of t_closed_order_summary table',
  `create_by` int(10) NOT NULL DEFAULT '1',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_by` int(10) DEFAULT NULL,
  `update_at` timestamp NULL DEFAULT NULL,
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',  
  PRIMARY KEY (`order_number`),
  KEY `client_uid` (`client_uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `t_refund` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` int(11) NOT NULL,
  `client_uid` varchar(32) COLLATE utf8_bin NOT NULL,
  `tx_id` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `btc_address` varchar(35) COLLATE utf8_bin NOT NULL,
  `btc_amount` double(20,8) NOT NULL,
  `status` varchar(3) COLLATE utf8_bin NOT NULL COMMENT 'Ref: refund_status',
  `oper_status` varchar(3) COLLATE utf8_bin NOT NULL COMMENT 'Ref: refund_oper_status',
  `sent_status` varchar(3) COLLATE utf8_bin DEFAULT '0',
  `create_by` int(11) DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `update_by` int(11) DEFAULT NULL,
  `update_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='BTCの返金';

CREATE TABLE IF NOT EXISTS `t_closed_order_summary` (
  `closed_date` char(14) COLLATE utf8_bin NOT NULL COMMENT 'MUST be YYYYMMDDHHMMSS, ex: 20160925234525',
  `count_orders` int(10) DEFAULT NULL,
  `total_btc_amount` double(18,8) NOT NULL,
  `total_hot_wallet_btc_amount` double(18,8) DEFAULT NULL,
  `total_cold_wallet_btc_amount` double(18,8) DEFAULT NULL,
  `hot_cold_sent_status` varchar(3) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `total_commission_btc_amount` double(18,8) DEFAULT NULL,
  `commission_sent_status` varchar(3) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `total_special_commission_btc_amount` double(18,8) DEFAULT NULL,
  `special_commission_sent_status` varchar(3) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `create_by` int(10) NOT NULL DEFAULT '1',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_by` int(10) DEFAULT NULL,
  `update_at` timestamp NULL DEFAULT NULL,
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`closed_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


