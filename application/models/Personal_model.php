<?php

class Personal_model extends CI_Model {

    // テーブル名
    const T_PERSONAL = 't_personal';
    const T_IMG = 't_imgfile';
    const T_USER = 't_user';
    const VALID = 0;

    private $kyc_db = null;

    public function __construct() {
        parent::__construct();
        $this->kyc_db = $this->load->database('kyc', true);
    }

    /*****************************************
     * 登録時
     *****************************************/

    /**
     * 個人情報を保存して、作成したレコードのpersonal_idを返す
     *
     * @param type $data
     * @return type
     */
    public function insert_personal($data) {
        // 新規レコードを作成
        $this->kyc_db->set('system_key', $this->config->item('sys_quantatoken'));
        $this->kyc_db->insert(self::T_PERSONAL, $data);
        log_message('debug', $this->kyc_db->last_query());
        // echo $this->kyc_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        // 作成したレコードのpersonal idを取得する
        $result = $this->select_by_email($data['email']);
        // personal idを返す
        return $result->personal_id;
    }

    /**
     * emailと一致するレコードのpersonal_idを返す
     *
     * @param type $email
     * @return type
     */
    private function select_by_email($email) {
        $this->kyc_db->select('personal_id');
        $this->kyc_db->from(self::T_PERSONAL);
        $this->kyc_db->where_in('email', $email);
        $this->kyc_db->where('system_key', $this->config->item('sys_quantatoken'));
        $this->kyc_db->where('delete_flag', self::VALID);
        $this->kyc_db->order_by("create_at desc");
        $this->kyc_db->limit(1);
        $result = $this->kyc_db->get();
        return $result->row();
    }

    /*****************************************
     * 表示時
     *****************************************/

    /**
     * 指定されたIDのレコードを返す
     * *未承認のpersonal idはコール前に取得しておいてもらう
     *
     * @param type $keys
     * @return type
     */
    public function select_list_by_key($keys) {
        $this->kyc_db->select("t1.personal_id"
            . ", family_name"
            . ", first_name"
            . ", company_name"
            . ", family_name_kana"
            . ", first_name_kana"
            . ", company_name_kana"
            . ", birthday"
            . ", email"
            . ", tel"
            . ", zip_code"
            . ", country"
            . ", prefecture"
            . ", city"
            . ", building"
            . ", group_concat(t2.imgfile,'') as imgfile"
            . ", t1.create_at");
        $this->kyc_db->from(self::T_PERSONAL . " as t1");
        $this->kyc_db->join(self::T_IMG . " as t2", "t1.personal_id = t2.personal_id", "left");
        $this->kyc_db->where("t1.system_key", $this->config->item('sys_quantatoken'));
        $this->kyc_db->where_in("t1.personal_id", $keys);
        $this->kyc_db->where("t1.delete_flag", self::VALID);
        $this->kyc_db->where("t2.delete_flag", self::VALID);
        $this->kyc_db->group_by("t1.personal_id");
        $this->kyc_db->order_by("t1.create_at asc");
        $this->kyc_db->order_by("t2.imgfile asc");
        $result = $this->kyc_db->get();
        //log_message('debug', $this->kyc_db->last_query());
        // echo $this->kyc_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /**
     * Check string is empty
     */
    private function IsNullOrEmptyString($question){
        return (!isset($question) || trim($question)==='');
    }

    public function select_list_by_key_order($keys,$order_by,$order_opt) {
        $this->kyc_db->select("t1.personal_id"
            . ", family_name"
            . ", first_name"
            . ", company_name"
            . ", family_name_kana"
            . ", first_name_kana"
            . ", company_name_kana"
            . ", birthday"
            . ", email"
            . ", tel"
            . ", zip_code"
            . ", country"
            . ", prefecture"
            . ", city"
            . ", building"
            . ", group_concat(t2.imgfile,'') as imgfile"
            . ", t1.create_at");
        $this->kyc_db->from(self::T_PERSONAL . " as t1");
        $this->kyc_db->join(self::T_IMG . " as t2", "t1.personal_id = t2.personal_id", "left");

        $this->kyc_db->where("t1.system_key", $this->config->item('sys_quantatoken'));
        $this->kyc_db->where_in("t1.personal_id", $keys);
        $this->kyc_db->where("t1.delete_flag", self::VALID);
        $this->kyc_db->where("t2.delete_flag", self::VALID);
        $this->kyc_db->group_by("t1.personal_id");

        if(!$this->IsNullOrEmptyString($order_by) && !$this->IsNullOrEmptyString($order_opt)){
            if($order_by == 'email' || $order_by == 'personal_id'){
                $this->kyc_db->order_by('t1.' . $order_by, $order_opt);
            }
        }

        //$this->kyc_db->order_by("t1.create_at asc");
        //$this->kyc_db->order_by("t2.imgfile asc");
        $result = $this->kyc_db->get();
        //log_message('debug', $this->kyc_db->last_query());
        // echo $this->kyc_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function select_latest_by_key($key) {
        $this->kyc_db->select("t1.personal_id"
            . ", family_name"
            . ", first_name"
            . ", company_name"
            . ", family_name_kana"
            . ", first_name_kana"
            . ", company_name_kana"
            . ", birthday"
            . ", email"
            . ", tel"
            . ", zip_code"
            . ", country"
            . ", prefecture"
            . ", city"
            . ", building"
            . ", group_concat(t2.imgfile,'') as imgfile"
            . ", t1.create_at");
        $this->kyc_db->from(self::T_PERSONAL . " as t1");
        $this->kyc_db->join(self::T_IMG . " as t2", "t1.personal_id = t2.personal_id", "left");
        $this->kyc_db->where("t1.system_key", $this->config->item('sys_quantatoken'));
        $this->kyc_db->where("t1.personal_id", $key);
        $this->kyc_db->where("t1.delete_flag", self::VALID);
        $this->kyc_db->where("t2.delete_flag", self::VALID);
        $this->kyc_db->group_by("t1.personal_id");
        $this->kyc_db->order_by("t1.create_at asc");
        $this->kyc_db->order_by("t2.imgfile asc");
        $this->kyc_db->limit(1);
        $result = $this->kyc_db->get();
        //log_message('debug', $this->kyc_db->last_query());
        // echo $this->kyc_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }



    public function select_latestarray_by_key($key) {
        $this->kyc_db->select("t1.personal_id"
            . ", family_name"
            . ", first_name"
            . ", company_name"
            . ", family_name_kana"
            . ", first_name_kana"
            . ", company_name_kana"
            . ", birthday"
            . ", email"
            . ", sex" 
            . ", tel"
            . ", zip_code"
            . ", country"
            . ", prefecture"
            . ", city"
            . ", building"
            . ", group_concat(t2.imgfile,'') as imgfile"
            . ", t1.create_at");
        $this->kyc_db->from(self::T_PERSONAL . " as t1");
        $this->kyc_db->join(self::T_IMG . " as t2", "t1.personal_id = t2.personal_id", "left");
        $this->kyc_db->where("t1.system_key", $this->config->item('sys_quantatoken'));
        $this->kyc_db->where("t1.personal_id", $key);
        $this->kyc_db->where("t1.delete_flag", self::VALID);
        $this->kyc_db->where("t2.delete_flag", self::VALID);
        $this->kyc_db->group_by("t1.personal_id");
        $this->kyc_db->order_by("t1.create_at asc");
        $this->kyc_db->order_by("t2.imgfile asc");
        $this->kyc_db->limit(1);
        $result = $this->kyc_db->get()->row_array();
        //log_message('debug', $this->kyc_db->last_query());
        // echo $this->kyc_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function update_by_pid($pid, $data) {
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $this->kyc_db->set('update_by', '0');
        $this->kyc_db->set('update_at', $now);
        $this->kyc_db->where('personal_id', $pid);
        $this->kyc_db->where('delete_flag', self::VALID);
        $result = $this->kyc_db->update(self::T_PERSONAL, $data);
        log_message('debug', $this->kyc_db->last_query());
        // echo $this->kyc_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /**
     * A function check account has exitsted.
     * 
     * @param type $arrParameter
     * @return type $personal_id if account has exitsted.
     */
    public function isAccount($arrParam) {
        $this->kyc_db->select('personal_id');
        $this->kyc_db->from(self::T_PERSONAL);        
        $this->kyc_db->where('family_name', $arrParam['family_name']);
        $this->kyc_db->where('first_name', $arrParam['first_name']);
        $this->kyc_db->where('birthday', $arrParam['birthday']);
        $this->kyc_db->where('delete_flag', self::VALID);
        $this->kyc_db->order_by("create_at desc");        
        $result = $this->kyc_db->get();
        return (0 == $result->num_rows()) ? null : $result;
    }
}
