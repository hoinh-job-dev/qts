ALTER TABLE t_email_queue
    ADD COLUMN `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
    ADD COLUMN `update_by` int(10) DEFAULT NULL,
    ADD COLUMN  `update_at` timestamp NULL DEFAULT NULL