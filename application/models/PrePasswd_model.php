<?php

class PrePasswd_model extends MY_Model {

    // テーブル名
    const T_PASSWD = 't_pre_password';
    const T_USER = 't_user';
    const VALID = 0;

    private $kyc_db = null;

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model', 'user');

    }

    /*****************************************
     * 登録時
     *****************************************/

    /**
     * 
     * @param type $data
     * @return type
     */
    public function insert_prepasswd($data) {
        $this->token_db->insert(self::T_PASSWD, $data);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
    }

    public function get_prepasswd($passid) {
        $this->token_db->from(self::T_PASSWD);
        $this->token_db->where("passid",$passid);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->get();
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return (0 == $result->num_rows()) ? null : $result->row();
    }

    public function delete_prepasswd($passid) {
        $this->token_db->where("passid",$passid);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $this->token_db->delete(self::T_PASSWD);
    }

    public function update_passwd($passid, $password) {
        $row = $this->get_prepasswd($passid);
        if (null == $row) {
            return false;
        }
        $this->user->update_password_by_email($row->email, $password);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        $this->delete_prepasswd($passid);
    }
}
