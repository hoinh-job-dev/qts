ALTER TABLE t_user MODIFY password VARCHAR(32);
UPDATE t_user SET password = MD5(concat('QTS_S41t_',password)) where length(password)<32;