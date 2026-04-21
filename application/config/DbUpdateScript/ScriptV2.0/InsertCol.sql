ALTER TABLE `t_commission` ADD COLUMN `btc_address` varchar(35) DEFAULT NULL AFTER user_hash;

ALTER TABLE `t_btc_txs` ADD COLUMN `id` int(10) unsigned NOT NULL UNIQUE KEY AUTO_INCREMENT;
ALTER TABLE `t_btc_txs` ADD COLUMN `create_by` int(11) DEFAULT NULL;
ALTER TABLE `t_btc_txs` ADD COLUMN `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `t_order`
	ADD COLUMN `rsv_char_1` nvarchar(50) DEFAULT NULL after memo,
	ADD COLUMN `rsv_char_2` nvarchar(50) DEFAULT NULL after rsv_char_1,
	ADD COLUMN `rsv_int_1` INT(10) DEFAULT NULL after rsv_char_2,
	ADD COLUMN `rsv_int_2` INT(10) DEFAULT NULL after rsv_int_1;
