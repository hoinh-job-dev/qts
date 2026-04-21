DROP PROCEDURE IF EXISTS get_AllAgentParents;

delimiter //
CREATE PROCEDURE get_AllAgentParents(IN hashUser varchar(32))
 BEGIN
 declare str_hash varchar(32);

 create TEMPORARY  table IF NOT EXISTS temp_user as (select * from t_user where 1=0);
 truncate table temp_user;


 WHILE hashUser is not NULL and not hashUser ='0' DO
  insert into temp_user select * from t_user WHERE user_hash = hashUser;

  set str_hash = NULL;
	select agent_uid into str_hash from temp_user where user_hash = hashUser;
  set hashUser = str_hash;
 END WHILE;

 select * from temp_user;

 END //
 delimiter ;
-- call get_AllAgentParents('8e6b42f1644ecb1327dc03ab345e618b');
