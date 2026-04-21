DELETE from `t_options` where `key` in ('mail_cancel_order_ARRAY_subject', 'mail_cancel_order_ARRAY_message');
INSERT INTO `t_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `description`, `order`, `is_visible`, `style`) VALUES
(1, 'mail_cancel_order_ARRAY_subject', 3, '[Quanta]ワンエイトアソシエーション株式会社 QNT交換処理の無効によるBTCご返送処理につきまして', NULL, 'string', 'Email cancel order subject', 25, 1, NULL),
(1, 'mail_cancel_order_ARRAY_message', 3, '{agent_family_name} {agent_first_name}様\r\n\r\n\r\nお世話になります。\r\nワンエイトアソシエーション株式会社　サポートデスクです。\r\n\r\nこの度、ご送金いただきましたBTCにつきまして弊社での確認をさせていただきましたが、希望額と\r\n着金BTCの乖離または、交換者様からの申出により交換処理を無効とさせていただきます。\r\n\r\nつきましては受領BTCを交換者様への返送処理をおこなわせていただきますので、本メールへ返送先\r\nBTCアドレス情報の返信をお願いいたします。\r\n\r\n※受信BTCの返送にかかる取引所手数料は、交換者様負担での返送処理となりますことをご了承ください。\r\n\r\n▼返送させていただく情報 \r\n===========================================\r\n\r\n【 振 込 I D 】{order_number}\r\n【返送額 】{btc_received_amount} BTC\r\n\r\n===========================================\r\n\r\n{mail_signature}\r\n', NULL, 'text', 'Email cancel order', 26, 1, NULL);

ALTER TABLE  `t_email_queue` ADD  `update_by` int(10) DEFAULT NULL,
ADD  `update_at` timestamp NULL DEFAULT NULL;

update t_user set 	rsv_char_2 = 'readGuide' where role in ("04", "05", "06", "07") and status = 21;