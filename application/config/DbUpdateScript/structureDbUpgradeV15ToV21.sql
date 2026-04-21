ALTER TABLE t_commission ADD COLUMN btc_address VARCHAR(35) AFTER user_hash;
ALTER TABLE t_order ADD COLUMN rsv_char_1 VARCHAR(50) AFTER memo;
ALTER TABLE t_order ADD COLUMN rsv_char_2 VARCHAR(50) AFTER rsv_char_1;
ALTER TABLE t_order ADD COLUMN rsv_int_1 int(10) AFTER rsv_char_2;
ALTER TABLE t_order ADD COLUMN rsv_int_2 int(10) AFTER rsv_int_1;
ALTER TABLE t_user DROP can_recursive;
ALTER TABLE t_user ADD COLUMN can_recursive tinyint(1) DEFAULT 0 AFTER comment ;
ALTER TABLE t_user ADD COLUMN rsv_char_1 VARCHAR(50)  NOT NULL COMMENT  'Temporary email' AFTER can_recursive;
ALTER TABLE t_user ADD COLUMN rsv_char_2 VARCHAR(50)  NOT NULL AFTER rsv_char_1;
ALTER TABLE t_user MODIFY COLUMN password  varchar(50);


--
-- Dumping data for table `m_general`
--

INSERT IGNORE INTO `m_general` (`key`, `code`, `value`, `create_by`, `create_at`, `update_by`, `update_at`, `delete_flag`) VALUES
('02', '21', 'サービス運用担当者(入出金)', 0, '2016-09-22 19:16:35', NULL, NULL, 0),
('02', '22', 'サービス運用担当者(注文担当)', 0, '2016-09-22 19:16:35', NULL, NULL, 0),
('02', '23', 'サービス運用担当者(登録承認)', 0, '2016-09-22 19:16:35', NULL, NULL, 0),
('03', '12', '個人情報変更', 0, '2016-09-22 19:16:35', NULL, NULL, 0),
('03', '71', 'CSV出力', 0, '2016-09-22 19:16:35', NULL, NULL, 0),
('03', '35', '注文審査対象', 0, '2016-09-22 19:16:35', NULL, NULL, 0),
('03', '52', 'バッチ」注文の完了処理', 0, '2016-09-22 19:16:35', NULL, NULL, 0),
('05', '51', '完了', 0, '2016-09-22 19:16:35', NULL, NULL, 0);

UPDATE `m_general`
SET `value` = '銀行確認 済'
WHERE `key` = '03' and `code` = '33';

UPDATE `m_general`
SET `value` = 'jpy/btc換金 済'
WHERE `key` = '03' and `code` = '34';

UPDATE `m_general`
SET `value` = 'btcアドレス通知 済'
WHERE `key` = '03' and `code` = '37';

UPDATE `m_general`
SET `value` = 'btc確認 済'
WHERE `key` = '03' and `code` = '38';

UPDATE `m_general`
SET `value` = 'トークン発行 済'
WHERE `key` = '03' and `code` = '41';

UPDATE `m_general`
SET `value` = 'btc/jpy換金済'
WHERE `key` = '05' and `code` = '14';

UPDATE `m_general`
SET `value` = 'BTCからUSDへの為替レート'
WHERE `key` = '05' and `code` = '30';

-- --------------------------------------------------------
-- Table structure for table `t_bank_btc_details`
--


CREATE TABLE IF NOT EXISTS `t_bank_btc_details` (
  `order_number` int(10) NOT NULL,
  `btc_address` varchar(40) COLLATE utf8_bin NOT NULL COMMENT 'Foreign key point to btc_address of t_bank_btc_header table',
  `jpy_amount` double(18,8) NOT NULL,
  `btc_amount` double(18,8) DEFAULT NULL,
  `rate` double(18,8) DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_number`),
  KEY `btc_address` (`btc_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;;

-- --------------------------------------------------------

--
-- Table structure for table `t_bank_btc_header`
--


CREATE TABLE IF NOT EXISTS `t_bank_btc_header` (
  `btc_address` varchar(40) COLLATE utf8_bin NOT NULL,
  `total_jpy_amount` double(18,8) NOT NULL,
  `total_btc_amount` double(18,8) DEFAULT NULL,
  `rate` double(18,8) DEFAULT NULL,
  `tx_id` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `complete` tinyint(1) NOT NULL DEFAULT '0',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`btc_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `t_block`
--


CREATE TABLE IF NOT EXISTS `t_block` (
  `hash` varchar(65) COLLATE utf8_bin NOT NULL,
  `height` int(11) NOT NULL,
  `txs` text COLLATE utf8_bin NOT NULL,
  `complete` int(1) NOT NULL DEFAULT '0',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `t_btc_address`
--


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_btc_txs`
--


CREATE TABLE IF NOT EXISTS `t_btc_txs` (
  `tx_id` varchar(200) COLLATE utf8_bin NOT NULL,
  `out_address` varchar(35) COLLATE utf8_bin NOT NULL,
  `balance` double(18,8) DEFAULT NULL,
  `tx_data` text COLLATE utf8_bin NOT NULL,
  `status` int(11) NOT NULL,
  `memo` text COLLATE utf8_bin NOT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_by` int(11) DEFAULT NULL,
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tx_id`,`out_address`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_closed_order_summary`
--


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

-- --------------------------------------------------------
--
-- Table structure for table `t_email_queue`
--


CREATE TABLE IF NOT EXISTS `t_email_queue` (
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
  `create_by` int(10) NOT NULL DEFAULT '0',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_log_user`
--


CREATE TABLE IF NOT EXISTS `t_log_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `user_hash` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `role` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `agent_uid` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `personal_id` int(10) DEFAULT NULL,
  `type` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `family_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `first_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `company_name` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `family_name_kana` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `first_name_kana` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `company_name_kana` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `status` varchar(3) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '01',
  `btc_address` varchar(35) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `memo` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `comment` varchar(180) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `can_recursive` tinyint(1) DEFAULT '0',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `trigType` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_news`
--


CREATE TABLE IF NOT EXISTS `t_news` (
  `news_id` int(10) NOT NULL,
  `title` varchar(50) COLLATE utf8_bin NOT NULL,
  `content` varchar(300) COLLATE utf8_bin NOT NULL,
  `create_by` int(10) NOT NULL DEFAULT '1',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_by` int(10) DEFAULT NULL,
  `update_at` timestamp NULL DEFAULT NULL,
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `t_options`
--


CREATE TABLE IF NOT EXISTS `t_options` (
  `foreign_id` int(10) unsigned NOT NULL DEFAULT '0',
  `key` varchar(200) NOT NULL DEFAULT '',
  `tab_id` tinyint(3) unsigned DEFAULT NULL,
  `value` text,
  `label` text,
  `type` enum('string','text','int','float','enum','bool') NOT NULL DEFAULT 'string',
  `description` text,
  `order` int(10) unsigned DEFAULT NULL,
  `is_visible` tinyint(1) unsigned DEFAULT '1',
  `style` text,
  PRIMARY KEY (`foreign_id`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `t_options`
--

INSERT IGNORE INTO `t_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `description`, `order`, `is_visible`, `style`) VALUES
(1, 'amount_rules_ARRAY_diff_percent_check', 1, '10', NULL, 'float', 'Amount rule: Different percentage of amount', 5, 1, NULL),
(1, 'amount_rules_ARRAY_max_amount', 1, '4800', NULL, 'float', 'Amount rule: Max amount limit', 3, 1, NULL),
(1, 'amount_rules_ARRAY_min_amount', 1, '1000', NULL, 'float', 'Amount rule: Min amount limit', 2, 1, NULL),
(1, 'amount_rules_ARRAY_monthly_amount', 1, '24000', NULL, 'float', 'Amount rule: Monthly amount limit', 4, 1, NULL),
(1, 'charge', 1, '3.75', NULL, 'float', 'Percentage of order amount that is charged as service fee when the order made by bank', 6, 1, NULL),
(1, 'cold_btc_rate', 2, '50', NULL, 'float', 'Percentage of order amount that will be sent to COLD wallet', 11, 1, NULL),
(1, 'commission_btc_rate', 2, '20', NULL, 'float', 'Percentage of order amount that will be sent to OPERATOR COMMISSION wallet', 12, 1, NULL),
(1, 'enable_banking', 1, '1|0::0', NULL, 'bool', 'Allow order by bank', 1, 1, NULL),
(1, 'hot_btc_rate', 2, '30', NULL, 'float', 'Percentage of order amount that will be sent to HOT wallet', 10, 1, NULL),
(1, 'info_email', 3, 'staging_info@one8-association.co.jp', NULL, 'string', 'Info email', 6, 1, NULL),
(1, 'livenet_ARRAY_block_explorer_url', 2, 'http://52.196.98.93:3001/insight-api', NULL, 'string', 'LIVENET: Insight API url', 2, 1, NULL),
(1, 'livenet_ARRAY_ope-wallet-server', 2, 'http://52.199.30.196:6667/api', NULL, 'string', 'LIVENET: Operator commission wallet client API url', 4, 1, NULL),
(1, 'livenet_ARRAY_start_blockheight', 2, '436708', NULL, 'int', 'LIVENET: Default block height that syncBlock process will be started on', 5, 1, NULL),
(1, 'livenet_ARRAY_wallet-server', 2, 'http://52.199.30.196:6666/api', NULL, 'string', 'LIVENET: Main wallet client API url', 3, 1, NULL),
(1, 'mail_accept_order_ARRAY_message', 3, '{client_family_name} {client_first_name}様\r\n\r\n\r\nご注文ありがとうございます。\r\n\r\n現在、弊社にてコンプライアンス確認を行っております。\r\n確認が完了次第、ビットコイン送信先のご連絡させていただきますので、今しばらくお待ち下さい。\r\n\r\n\r\n※身分証および住所確認書類に不備がある場合は証明書類の追加・再提出をお願いする場合がございます。\r\n\r\n※交換金額が日額で4800ドル以上、または月額で24000ドル以上のQNT交換者様について弊社のコンプライアンス基準に則り、03-5425-4863から交換確認のお電話を差し上げます。\r\n\r\n{mail_signature}\r\n', NULL, 'text', 'Accept order email message', 14, 1, NULL),
(1, 'mail_accept_order_ARRAY_subject', 3, '[Quanta]ワンエイトアソシエーション株式会社　ご注文を受付ました', NULL, 'string', 'Accept order email subject', 13, 1, NULL),
(1, 'mail_admin_name', 3, '<<Staging>>[Quanta]', NULL, 'string', 'Sender name', 9, 1, NULL),
(1, 'mail_agent_register_ARRAY_message', 3, '{agent_family_name} {agent_first_name}様\r\n\r\n\r\nお手続きありがとうございます。\r\n仮登録が完了致しましたので、以下のURLからパスワードを設定し、本登録を完了させて下さい。\r\n\r\nURL : {set_password_url}\r\n\r\n\r\n尚、上記のURLはパスワード設定後に無効となりますのでご注意下さい。\r\nパスワード設定後にログインを行われる場合は、以下のURLをご使用下さい。\r\n\r\nURL : {agent_login_url}\r\n\r\n{mail_signature}\r\n', NULL, 'text', 'Agent register email message', 24, 1, NULL),
(1, 'mail_agent_register_ARRAY_subject', 3, '[Quanta] 仮登録完了のご連絡', NULL, 'string', 'Agent register email subject', 23, 1, NULL),
(1, 'mail_approved_agent_ARRAY_message', 3, '{agent_family_name} {agent_first_name}様\r\n\r\n\r\nお待たせ致しました。\r\nコンプライアンス確認作業が完了し、アカウントが承認されました。\r\n以下URLより、ID、パスワードをご入力の上、ログインお願いいたします。\r\n\r\nログインURL: {agent_login_url}\r\n\r\n尚、弊社WEBサイトからもログイン可能になっております。\r\n\r\n{mail_signature}\r\n', NULL, 'text', 'Agent approved email message', 26, 1, NULL),
(1, 'mail_approved_agent_ARRAY_subject', 3, '[Quanta]ワンエイトアソシエーション株式会社　コンプライアンス確認完了のお知らせ', NULL, 'string', 'Agent approved email subject', 25, 1, NULL),
(1, 'mail_approved_client_ARRAY_message', 3, '{client_family_name} {client_first_name}様\r\n\r\n\r\nお待たせ致しました。\r\nコンプライアンス確認作業が完了しアカウントが承認されました。\r\nなお、請求書につきましては、別途メールにてご連絡致しますので、\r\n今しばらくお待ち下さい。\r\n\r\n{mail_signature}\r\n', NULL, 'text', 'Approved client email message', 16, 1, NULL),
(1, 'mail_approved_client_ARRAY_subject', 3, '[Quanta]ワンエイトアソシエーション株式会社　コンプライアンス確認完了のお知らせ', NULL, 'string', 'Approved client email subject', 15, 1, NULL),
(1, 'mail_bcc', 3, 'qtstest@cardano-labo.com', NULL, 'string', 'Bcc email', 11, 1, NULL),
(1, 'mail_from', 3, 'staging_info@one8-association.co.jp', NULL, 'string', 'Sender email', 10, 1, NULL),
(1, 'mail_notify_bankaccount_ARRAY_message', 3, '{client_family_name} {client_first_name}様\r\n\r\n\r\nお世話になっております。\r\nワンエイトアソシエーション株式会社で御座います。\r\n\r\nQNTのご注文が確定致しました。\r\n下記金額のお振込みをお願い致します。\r\n\r\n入金は日本円のみとなります。\r\n下記の【入金金額】を日本円に換算し、お振り込みお願い致します。\r\n（USDの/JPYの為替レートは変動しますので、おおよその日本円換算金額をご入金ください。）\r\nお振込頂いた金額に対してQNTの発行を致します。\r\n\r\n\r\n▼振込情報\r\n===========================================\r\n\r\n【 振 込 I D 】{order_number}\r\n\r\n\r\n\r\n【注文金額】{order_amount}USD\r\n【交換手数料】{service_fee}USD\r\n【交換対象金額】{entry_amount}USD\r\n\r\n<USDから日本への計算参考サイト>\r\nhttp://info.finance.yahoo.co.jp/fx/\r\n\r\n===========================================\r\n\r\n※ご入金額換算の例\r\nUSD/JPYの為替レートが105 USD/JPY だった場合のご入金額\r\n2,000 USD(【入金金額】) × 105 USD/JPY＝ 210,000円\r\nUSDの/JPYの為替レートは変動しますので、大幅な差異がある場合は確認のお電話をさせていただく場合がございます。\r\n\r\n\r\n▼振込先口座情報\r\n===========================================\r\n\r\n〈金融機関〉西武信用金庫\r\n〈口座種別〉原宿支店　（支店番号：111）\r\n〈支店番号〉普通預金\r\n〈口座番号〉2130699\r\n〈振込先名〉ワンエイトアソシエーション（カ\r\n\r\n※ ※ ※ご 注 意 く だ さ い ※ ※ ※\r\n・お振り込み時には、必ず「振込ID」を振込人名義の前にご記入下さい。\r\n　例) 123456 ワンエイト ハナコ\r\n・交換時に発生する送金手数料等は、お客様のご負担となります。\r\n\r\n============================================\r\n\r\n特定商取引法に基づく表示項目\r\n{trade_law_url}\r\n\r\n{mail_signature}\r\n', NULL, 'text', 'Order by bank notification email message', 18, 1, NULL),
(1, 'mail_notify_bankaccount_ARRAY_subject', 3, '[Quanta]ワンエイトアソシエーション株式会社 QNT交換　ご注文確定致しました', NULL, 'string', 'Order by bank notification email subject', 17, 1, NULL),
(1, 'mail_notify_btcaddr_ARRAY_message', 3, '{client_family_name} {client_first_name}様\r\n\r\n\r\nお世話になっております。\r\nワンエイトアソシエーションで御座います。\r\n\r\nQNTのご注文が確定致しました。\r\n\r\n以下のURLから請求内容をご確認下さい。\r\n内容に問題がございませんでしたら、ご送金のお手続きをお願い致します。\r\n\r\nURL：{view_btc_address_url}\r\n\r\n{mail_signature}\r\n', NULL, 'text', 'BTC address notification email message', 20, 1, NULL),
(1, 'mail_notify_btcaddr_ARRAY_subject', 3, '[Quanta]ワンエイトアソシエーション株式会社 ご注文が確定致しました', NULL, 'string', 'BTC address notification email subject', 19, 1, NULL),
(1, 'mail_notify_receivedbtc2nd_ARRAY_message', 3, '{client_family_name} {client_first_name}様\r\n\r\n\r\nお世話になっております。\r\nワンエイトアソシエーション株式会社　サポートデスクです。\r\n\r\nこの度、ご送付いただきましたBTCにつきまして、弊社指定のBTCアドレスと\r\n相違しております。QNT交換までのお手続きに関しましては、セキュリティの\r\n都合上、ご指定BTCアドレス以外の受領では交換手続きにすすめません。\r\n\r\n交換手続きをすすめるにあたり、受領BTCを一度、交換者様へ返送させていた\r\nだき、再度指定BTCアドレスへのご送付処理が必要となります。\r\n\r\nつきましては、本メールへ返送先BTCアドレス情報の返信をお願いいたします。\r\n\r\n※受信BTCの返送にかかる取引所手数料は、交換者様負担での返送処理となり\r\nますことをご了承ください。\r\n\r\n▼返送させていただく情報 \r\n===========================================\r\n\r\n【 振 込 I D 】{oder_number}\r\n【返送額 】{btc_received_amount} BTC\r\n\r\n===========================================\r\n\r\n\r\n{mail_signature}\r\n', NULL, 'text', 'Received BTC from 2nd notification email message', 32, 1, NULL),
(1, 'mail_notify_receivedbtc2nd_ARRAY_subject', 3, '[Quanta]ワンエイトアソシエーション株式会社 BTCのウォレット相違につきまして', NULL, 'string', 'Received BTC from 2nd notification email subject', 31, 1, NULL),
(1, 'mail_notify_receivedbtc_ARRAY_message', 3, '{client_family_name} {client_first_name}様\r\n\r\n\r\nお世話になっております。\r\nワンエイトアソシエーション株式会社　サポートデスクです。\r\n\r\nご送付頂きましたBTCを受領いたしました。\r\n誠にありがとうございました。\r\n\r\n▼送付情報 \r\n===========================================\r\n\r\n【 振 込 I D 】{oder_number}\r\n【 送付額 】{btc_received_amount} BTC\r\n【 BTCアドレス 】{btc_address}\r\n\r\n===========================================\r\n\r\n後程、受領書をお送りいたしますので、\r\n引き続き、よろしくお願いいたします。\r\n※受領書の発行は、通常１営業日又は、2営業日ほどかかります。\r\n\r\n\r\n{mail_signature}\r\n', NULL, 'text', 'Received BTC notification email_message', 30, 1, NULL),
(1, 'mail_notify_receivedbtc_ARRAY_subject', 3, '[Quanta]ワンエイトアソシエーション株式会社 BTCを受領いたしました', NULL, 'string', 'Received BTC notification email subject', 29, 1, NULL),
(1, 'mail_notify_tokencode_ARRAY_message', 3, '{client_family_name} {client_first_name}様\r\n\r\n\r\nお世話になっております。\r\nワンエイトアソシエーションで御座います。\r\n\r\n\r\nQNTの交換が完了しましたので受領書をお送り致します。\r\n\r\n*******************\r\nこの領収書はQuanta公開時にQNTを自分のウォレットに入金する\r\n際にも必要となりますので大切に保管して下さい。\r\n*******************\r\n\r\n===================\r\n受領書\r\n-------------------\r\nTicket ID: {order_number}\r\n\r\n\r\n交換完了日時: {create_at}\r\n\r\n\r\n受け取ったBitcoinの総額: {btc_amount} BTC\r\n\r\n適用されたUSD/BTCレート: {btc_usd_rate} USD/BTC\r\n\r\n適用されたUSD/QNTレート: {qnt_usd_rate} USD/QNT\r\n\r\n交換されたQNTの総額: {token_quantity} QNT\r\n\r\n\r\nQNT還元用コード: {token_code}\r\n\r\n===================\r\n\r\n{mail_signature}\r\n', NULL, 'text', 'Token code notification email message', 22, 1, NULL),
(1, 'mail_notify_tokencode_ARRAY_subject', 3, '[Quanta]ワンエイトアソシエーション株式会社　受領書をお送りいたします', NULL, 'string', 'Token code notification email subject', 21, 1, NULL),
(1, 'mail_respond_password_ARRAY_message', 3, '{agent_family_name} {agent_first_name}様\r\n\r\n\r\nお世話になっております。\r\nワンエイトアソシエーションで御座います。\r\n\r\nパスワード再設定URL: {reset_password_url}\r\n\r\n{mail_signature}\r\n', NULL, 'text', 'Forgot password email message', 28, 1, NULL),
(1, 'mail_respond_password_ARRAY_subject', 3, '[Quanta] パスワード変更の確認', NULL, 'string', 'Forgot password email subject', 27, 1, NULL),
(1, 'mail_signature', 3, '++++++++++++++++++++++++++++++++++++++\r\nワンエイトアソシエーション株式会社\r\n〒105-0004\r\n東京都港区新橋5丁目12-7\r\n富永ビル4F\r\n営業時間平日10：00~19：00\r\n定休日 土日祝日\r\nWEB： {site_domain}\r\nMAIL：{support_email}\r\nLINE：@bbk7201s\r\n++++++++++++++++++++++++++++++++++++++\r\n', NULL, 'text', 'Email signature', 12, 1, NULL),
(1, 'network', 2, 'livenet|testnet::testnet', 'Livenet|Testnet', 'enum', 'BTC network', 1, 1, NULL),
(1, 'order_expiration_time', 1, '8', NULL, 'int', 'Number of days that an order is considered to be valid', 12, 1, NULL),
(1, 'qtsmaillog_email', 3, 'qtstest@cardano-labo.com', NULL, 'string', 'Log email', 7, 1, NULL),
(1, 'role_agent_commission_ARRAY_04', 1, '20', NULL, 'float', 'Agent commission: Rank A', 7, 1, NULL),
(1, 'role_agent_commission_ARRAY_05', 1, '15', NULL, 'float', 'Agent commission: Rank B', 8, 1, NULL),
(1, 'role_agent_commission_ARRAY_06', 1, '10', NULL, 'float', 'Agent commission: Rank C', 9, 1, NULL),
(1, 'role_agent_commission_ARRAY_07', 1, '5', NULL, 'float', 'Agent commission: Rank D', 10, 1, NULL),
(1, 'selfy_manual', 1, 'http://one8-association.co.jp/manual/#manual_section03', NULL, 'string', 'Selfy manual', 15, 1, NULL),
(1, 'send_email_method', 3, 'mail|smtp::smtp', 'PHP mail()|SMTP', 'enum', 'Send mail method', 1, 1, NULL),
(1, 'show_agent_role_radio', 1, '1|0::1', NULL, 'bool', 'Allow higher agent to create any lower agent level', 11, 1, NULL),
(1, 'site_domain', 1, 'http://one8-association.co.jp', NULL, 'string', 'Site domain', 13, 1, NULL),
(1, 'smtp_host', 3, 'smtp.lolipop.jp', NULL, 'string', 'SMTP host', 2, 1, NULL),
(1, 'smtp_pass', 3, 'minamiji1094', NULL, 'string', 'SMTP password', 5, 1, NULL),
(1, 'smtp_port', 3, '587', NULL, 'int', 'SMTP port', 3, 1, NULL),
(1, 'smtp_user', 3, 'info@one8-association.co.jp', NULL, 'string', 'SMTP username', 4, 1, NULL),
(1, 'special_commission_btc_rate', 2, '0', NULL, 'float', 'Percentage of order amount that will be sent to SPECIAL wallet', 13, 1, NULL),
(1, 'support_email', 3, 'support@one8-association.co.jp', NULL, 'string', 'Support email', 8, 1, NULL),
(1, 'testnet_ARRAY_block_explorer_url', 2, 'http://blocktest.altaapps.io:3000/insight-api', NULL, 'string', 'TESTNET: Insight API url', 6, 1, NULL),
(1, 'testnet_ARRAY_ope-wallet-server', 2, 'http://bws.altaapps.io:5556/api', NULL, 'string', 'TESTNET: Operator commission wallet client API url', 8, 1, NULL),
(1, 'testnet_ARRAY_start_blockheight', 2, '1012000', NULL, 'int', 'TESTNET: Default block height that syncBlock process will be started on', 9, 1, NULL),
(1, 'testnet_ARRAY_wallet-server', 2, 'http://bws.altaapps.io:5555/api', NULL, 'string', 'TESTNET: Main wallet client API url', 7, 1, NULL),
(1, 'use_manual', 1, 'http://one8-association.co.jp/qnt_agent/', NULL, 'string', 'Use manual', 14, 1, NULL);

-- --------------------------------------------------------
--
-- Table structure for table `t_refund`
--


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='BTCの返金' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Table structure for table `t_token_approved`
--


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Table structure for table `t_vat`
--


CREATE TABLE IF NOT EXISTS `t_vat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_date` date NOT NULL,
  `vat_value` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='消費税テーブル' AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `t_vat`
--

TRUNCATE TABLE `t_vat`;
--
-- Dumping data for table `t_vat`
--

INSERT INTO `t_vat` (`id`, `from_date`, `vat_value`) VALUES
(1, '2016-01-01', 0.08);
