<?php

class Login_model extends MY_model {

    // テーブル名
    const T_LOGIN = 't_login';

    public function __construct() {
        // Model クラスのコンストラクタを呼び出す
        parent::__construct();
    }

    public function insert_session($data) {
        $result = $this->token_db->insert(self::T_LOGIN, $data);
        return $result;
    }

    public function update_session($session_id, $data) {
        $this->token_db->where('session_id', $session_id);
        $result = $this->token_db->update(self::T_LOGIN, $data);
        return $result;
    }

    public function get_session($session_id) {
        $query = "select "
                . " case "
                . "  when update_at is null then create_at "
                . "  when update_at = '0000-00-00 00:00:00' then create_at "
                . "  else update_at "
                . " end as last_access "
                . "from "
                . " ".self::T_LOGIN." "
                . "where "
                . " session_id = '".$session_id."'";

        $result = $this->token_db->query($query);
        return $result->row();
    }

    public function delete_session($session_id) {
        $this->token_db->where('session_id', $session_id);
        $result = $this->token_db->delete(self::T_LOGIN);
        return $result;
    }

    public function delete_expired(){
        $this->token_db->where('create_at < date_sub(now(), interval 8 day)'
            . ' AND '
            . 'update_at < date_sub(now(), interval 8 day)');
        $result = $this->token_db->delete(self::T_LOGIN);
        return $result;
    }

}