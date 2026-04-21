<?php

class User_model extends MY_model {

    // テーブル名
    const T_USER = 't_user';
    const T_ORDER = 't_order';
    const M_GENERAL = 'm_general';
    const VALID = 0;
    const INVALID = 1;


    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Tokyo");
    }

    /**
     * 初回注文画面, 代理店情報登録画面で不慮のアクセスを判定する
     * 
     * @param type $user_hash
     * @return type
     */
    public function is_userhash($user_hash, $role) {
        $this->token_db->select('status');
        $this->token_db->from(self::T_USER);
        $this->token_db->where('user_hash', $user_hash);
        $this->token_db->where_in('role', $role);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->get();

        return (0 == $result->num_rows()) ? null : $result->row()->status;
    }

    /*****************************************
     * ログイン
     *****************************************/

    /**
     * ユーザの存在有無を取得する
     * 
     * @param type $user
     * @param type $pass
     * @return type
     */
    public function is_user($id, $pass, $role) {
        $this->token_db->select('user_hash');
        $this->token_db->from(self::T_USER);
        $this->token_db->where('email', $id);
        $this->token_db->where('password', $this->encrypt_password($pass));
        $this->token_db->where_in('role', $role);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->get();
 //echo "encrypt pass: " ;
   //         var_dump( $this->token_db); die;
     //   var_dump($result);die;
        return (0 == $result->num_rows()) ? null : $result->row()->user_hash;
    }

    /*****************************************
     * リンク作成時
     *****************************************/

    /**
     * リンク作成時
     * UIDとロールのみのレコードを追加する
     * 
     * @param type $data
     * @return type
     */
    public function insert_user($data) {
        // 新規レコードを作成
        $this->token_db->insert(self::T_USER, $data);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        // 作成したレコードのUIDを取得する
        $client_uid = $this->select_new_uid_by_agentid($data['agent_uid']); 
        // UIDのハッシュを追加する
        $client_hash = md5($client_uid); 
        $this->update_by_uid($client_uid, $client_hash);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $client_hash;
    }

    // insert new user
    public function insert_operator($data){
        $this->token_db->insert(self::T_USER, $data);
        $uid = $this->token_db->insert_id();
        $user_hash = md5($uid);
        $this->update_by_uid($uid, $user_hash);
    }

    // get newest user in database
    private function get_new_user() {
        $this->token_db->select('*');
        $this->token_db->from(self::T_USER);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->order_by('create_at', 'DESC');
        $this->token_db->limit('1');
        $result = $this->token_db->get()->row();
        return $result;
    }

    public function get_userinfo_by_uid($uid) {
        $this->token_db->select("*");
        $this->token_db->from(self::T_USER. " as t1");
        $this->token_db->where('uid', $uid);
        $this->token_db->where('t1.delete_flag', self::VALID);
        $result = $this->token_db->get();
        return $result->row();
    }

    // update user data
    public function update_operator_by_uid($data){
        $data = (object) $data;
        if (isset($data->email))
            $this->token_db->set('email', $data->email);
        if (isset($data->password) && !empty($data->password))
            $this->token_db->set('password', $data->password);
        if (isset($data->role))
            $this->token_db->set('role', $data->role);
        if (isset($data->first_name))
            $this->token_db->set('first_name', $data->first_name);
        if (isset($data->family_name)) {
            $this->token_db->set('family_name', $data->family_name);
        }
        if (isset($data->first_name_kana))
            $this->token_db->set('first_name_kana', $data->first_name_kana);
        if (isset($data->family_name_kana)) {
            $this->token_db->set('family_name_kana', $data->family_name_kana);
        }

        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->where('uid', $data->uid);
        $result = $this->token_db->update(self::T_USER);
        return $data;
    }

    // delete user by uid
    public function delete_user_by_uid($uid) {
        $this->token_db->where('uid', $uid);
        $this->token_db->set('delete_flag', self::INVALID);
        $result = $this->token_db->update(self::T_USER);
        return $result;
    }
    /**
     * 作成したレコードのUIDを取得する
     * 
     * @param type $agent
     * @return type
     */
    private function select_new_uid_by_agentid($agent) {        
        $this->token_db->select('uid');
        $this->token_db->from(self::T_USER);
        $this->token_db->where('agent_uid', $agent);
        $this->token_db->where('delete_flag', self::VALID);
        //$this->token_db->order_by('create_at', 'DESC');        
        $this->token_db->order_by('uid', 'DESC');
        $this->token_db->limit('1');
        $result = $this->token_db->get();
        return (0 == $result->num_rows()) ? null : $result->row()->uid;
    }

    /**
     * UIDをキーにレコードを更新する
     * 
     * @param type $uid
     * @param type $data
     * @return type
     */
    private function update_by_uid($uid, $hash) {
        $this->token_db->set('user_hash', $hash);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->where('uid', $uid);
        $result = $this->token_db->update(self::T_USER);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /*****************************************
     * 審査時
     *****************************************/

    public function select_all() {
        $this->token_db->select("m1.value as status "
                . " , t1.personal_id "
                . " , t1.uid "
                . " , t1.user_hash "
                . " , m2.value as role "
                . " , m3.value as type "
                . " , t1.family_name "
                . " , t1.first_name "
                . " , t1.company_name "
                . " , t1.family_name_kana"
                . " , t1.first_name_kana"
                . " , t1.company_name_kana"
                . " , t1.email "
                . " , m4.value as pay_method "
                . " , t2.receive_address");
        $this->token_db->from(self::T_USER . " as t1");
        $this->token_db->join(self::M_GENERAL . " as m1", "t1.status = m1.code and m1.key='03'", "left");
        $this->token_db->join(self::M_GENERAL . " as m2", "t1.role = m2.code and m2.key='02'", "left");
        $this->token_db->join(self::M_GENERAL . " as m3", "t1.type = m3.code and m3.key='01'", "left");
        $this->token_db->join(self::T_ORDER . " as t2", "t1.user_hash = t2.client_uid", "left");
        $this->token_db->join(self::M_GENERAL . " as m4", "t2.pay_method = m4.code and m4.key='07'", "left");
        $this->token_db->where('t1.delete_flag', self::VALID);        
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /*****************************************
     * 審査時
     *****************************************/

    /**
     * 未承認のレコードを取得する
     * 
     * @return type
     */
    public function select_list_not_approved() {
        $this->token_db->select("personal_id");
        $this->token_db->from(self::T_USER);
        $status = array($this->config->item('act_reg_personal')
            , $this->config->item('approval_pending')
            , $this->config->item('approval_not_approve'));
        $this->token_db->where_in('status', $status);
        $this->token_db->where_in('role', array_merge(array($this->config->item('role_client')), array_keys($this->config->item('role_agent_commission'))));
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->group_by("personal_id");
        $result = $this->token_db->get()->result();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /**
     * @param type $pid
     * @param type $data
     * @return type
     */
    public function update_by_personalid($pid, $data) {
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $this->token_db->set('update_by', '0');
        $this->token_db->set('update_at', $now);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->where_not_in('memo', $this->config->item('appruval_approved'));
        $this->token_db->where('personal_id', $pid);
        $result = $this->token_db->update(self::T_USER, $data);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /**
     * personal_idからアカウント情報を取得する
     * 
     * @param type $pid
     * @return type
     */
    public function select_by_personalid($pid) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_USER);
        $this->token_db->where('personal_id', $pid);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->row();
    }

    /*****************************************
     * 再注文時
     *****************************************/

    /**
     * emailからアカウント情報を取得する
     * 
     * @param type $email
     * @return type
     */
    public function get_user_by_email($email) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_USER);
        $this->token_db->where('email', $email);
        $this->token_db->where('role', $this->config->item('role_client'));
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->row();
    }

    /**
     * emailと一致するレコードの個数を返す
     * 
     * @param type $email
     * @return type
     */
    public function get_user_by_email_role($email, $role) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_USER);
        $this->token_db->where('email', $email);
        $this->token_db->where_in('role', $role);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->get();
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function can_not_recursive($user_hash) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_USER);
        $this->token_db->where('user_hash', $user_hash);
        $this->token_db->where_in('status', array(
             $this->config->item('act_reg_personal')
             , $this->config->item('act_approved')));
        $this->token_db->where('can_recursive', 0);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->get();
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

	/**
     * user_hashからアカウント情報を取得する
     * @param type $userhash
     * @return type
     */
    public function get_user_by_userhash($userhash) {
        $this->token_db->select('t1.*, m1.value as rolename');
        $this->token_db->from(self::T_USER . " as t1");
        $this->token_db->join(self::M_GENERAL . " as m1", "t1.role = m1.code  and m1.key='02'", "left");
        $this->token_db->where('t1.user_hash', $userhash);
        $this->token_db->where('t1.delete_flag', self::VALID);
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->row();
    }

    public function get_agents_chain($userhash){
        $data = $this->token_db->query("CALL get_AllAgentParents('$userhash')");
        mysqli_next_result($this->token_db->conn_id);
        return $data->result();
    }
    /*****************************************
     * 各情報を登録する
     *****************************************/

    public function get_registered_userhash($user_hash) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_USER);
        $this->token_db->where('user_hash', $user_hash);
        $this->token_db->where('delete_flag', self::VALID);
        // $this->token_db->where_in('status', array(
        //      $this->config->item('act_reg_personal')
        //      , $this->config->item('act_approved')));
        $this->token_db->where('status <> ', $this->config->item('act_gen_link'));
        $this->token_db->order_by('create_at', 'DESC');
        $this->token_db->limit('1');
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /**
     * User_Hashをキーにレコードを更新する
     * 
     * @param type $hash
     * @param type $data
     * @return type
     */
    public function update_by_userhash($hash, $data) {
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
        //$user_uid = $this->getUidByUserHash($hash);
        //$this->token_db->set('update_by', $user_uid);
        $this->token_db->set('update_by', $hash);
        $this->token_db->set('update_at', $now);
        $this->token_db->where('delete_flag', self::VALID);

        $this->token_db->where('user_hash', $hash);
        $result = $this->token_db->update(self::T_USER, $data);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /*****************************************
     * パスワード忘れ
     *****************************************/
    /**
     * パスワード忘れ
     * 
     * @param type $email
     * @return type
     */
    public function ask_password($email) {
        $this->token_db->select("uid, user_hash, email, password");
        $this->token_db->from(self::T_USER);
        $this->token_db->where('email', $email);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->order_by('create_at', 'DESC');
        $this->token_db->limit('1');
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return (0 == $result->num_rows()) ? null : $result->row();
    }

    /**
     * 
     * @param type $email
     * @param type $password
     * @return boolean
     */
    public function reset_password($email, $password) {
        $hash = $this->get_user_by_email($email);
        if (null == $hash->result()) {
            return false;
        }
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $this->token_db->set('password', $this->encrypt_password($password));
        $this->token_db->set('update_by', $hash);
        $this->token_db->set('update_at', $now);
        $this->token_db->where('email', $email);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->update(self::T_USER);
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /**
     * User_Hashをキーにレコードを更新する
     * 
     * @param type $hash
     * @param type $data
     * @return type
     */
    public function update_password_by_email($email, $passwd) {
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $this->token_db->set('password', $this->encrypt_password($passwd));
        //$this->token_db->set('update_by', $uid);
        $this->token_db->set('update_at', $now);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->where('email', $email);
        $result = $this->token_db->update(self::T_USER);
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

	/*****************************************
     * リンク一覧取得
     *****************************************/
    /**
     * リンク一覧取得
     * 
     * @param type $agnet_hash
     * @param type $role
     * @return type
     */
    public function get_childs($agnet_hash, $role, $search=null) {
        $this->token_db->select("t1.user_hash"
            . ", m1.value as role"
            . ", m2.value as type"
            . ", t1.family_name"
            . ", t1.first_name"
            . ", t1.company_name"
            . ", t1.family_name_kana"
            . ", t1.first_name_kana"
            . ", t1.company_name_kana"
            . ", m3.value as status"
            . ", t1.memo"
//                . ", t2.create_at"
//                . ", t2.order_number"
//                . ", t2.currency_unit"
//                . ", t2.amount"
//                . ", t2.status"
        );
        $this->token_db->from(self::T_USER . " as t1");
        $this->token_db->join(self::M_GENERAL . " as m1", "t1.role = m1.code and m1.key='02'", "left");
        $this->token_db->join(self::M_GENERAL . " as m2", "t1.type = m2.code and m2.key='01'", "left");
        $this->token_db->join(self::M_GENERAL . " as m3", "t1.status = m3.code and m3.key='03'", "left");
//        $this->token_db->join(self::T_ORDER . " as t2", "t1.user_hash = t2.client_uid ", "left");
        $this->token_db->where('t1.agent_uid', $agnet_hash);
        $this->token_db->where_in('t1.role', $role);
        $this->token_db->where('t1.delete_flag', self::VALID);
        if(isset($search)){
//            if(!$this->IsNullOrEmptyString($search->status)) {
//                $statuses = explode(",",$search->status);
//                $query_status = "( t2.status = '$statuses[0]'";
//                for($x = 1; $x < count($statuses); $x++) {
//                    $status = $statuses[$x];
//                    $query_status = $query_status . " or t2.status = '$status'";
//                }
//                $query_status =  $query_status . " ) ";
//                $this->token_db->where($query_status);
//            }
            if(!$this->IsNullOrEmptyString($search->first_name)) {
                $this->token_db
                    ->where('t1.first_name LIKE \'%'. $search->first_name . '%\'');
            }
            if(!$this->IsNullOrEmptyString($search->family_name)) {
                $this->token_db
                    ->where('t1.family_name LIKE \'%'. $search->family_name . '%\'');
            }
            if(!$this->IsNullOrEmptyString($search->memo)) {
                $this->token_db
                    ->where('t1.memo LIKE \'%'. $search->memo . '%\'');
            }
        }
        $result = $this->token_db->get()->result();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result);die;
        return $result;
    }

    // get links, search by user status (approved), name, memo
    public function get_for_view_links($agent_hash, $role, $search=null) {
        $this->token_db->select("t1.user_hash"
            . ", m2.value as type"
            . ", t1.family_name"
            . ", t1.first_name"
            . ", t1.company_name"
            . ", t1.family_name_kana"
            . ", t1.first_name_kana"
            . ", t1.company_name_kana"
            . ", t1.memo"
            . ", t1.status");
        $this->token_db->from(self::T_USER . " as t1");
        $this->token_db->join(self::M_GENERAL . " as m2", "t1.type = m2.code and m2.key='01'", "left");
        $this->token_db->where('t1.agent_uid', $agent_hash);
        $this->token_db->where_in('t1.role', $role);
        $this->token_db->where('t1.delete_flag', self::VALID);
        //$this->token_db->where('t1.email is not null');
        $this->token_db->order_by("t1.create_at", "asc");
        if(isset($search)){
            if(!$this->IsNullOrEmptyString($search->status)) {
                $statuses = explode(",",$search->status);
                $query_status = "( t1.status = '$statuses[0]'";
                for($x = 1; $x < count($statuses); $x++) {
                    $status = $statuses[$x];
                    $query_status = $query_status . " or t1.status = '$status'";
                }
                $query_status =  $query_status . " ) ";
                $this->token_db->where($query_status);
            }
            if(!$this->IsNullOrEmptyString($search->first_name)) {
                $this->token_db
                    ->where('first_name LIKE \'%'. $search->first_name . '%\'');
            }
            if(!$this->IsNullOrEmptyString($search->family_name)) {
                $this->token_db
                    ->where('family_name LIKE \'%'. $search->family_name . '%\'');
            }
            if(!$this->IsNullOrEmptyString($search->memo)) {
                $this->token_db
                    ->where('t1.memo LIKE \'%'. $search->memo . '%\'');
            }
        }
        $result = $this->token_db->get()->result();
        return $result;
    }

    public function select_user_by_uid($uid) {
        $this->token_db->select("*");
        $this->token_db->from(self::T_USER);
        $this->token_db->where('uid', $uid);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->row();
    }

    /*****************************************
     * オペレータ画面用
     *****************************************/

    public function get_user_by_uid($uid) {
        $this->token_db->select("m1.value as status"
                . " , t1.uid"
                . " , t1.personal_id"
                . " , t1.role as role_code"
                . " , m2.value as role"
                . " , m3.value as type "
                . " , t1.family_name"
                . " , t1.first_name"
                . " , t1.company_name"
                . " , t1.family_name_kana"
                . " , t1.first_name_kana"
                . " , t1.company_name_kana"
                . " , t1.user_hash"
                . " , t1.agent_uid"
                . " , t1.btc_address"
                . " , t1.comment"
                . " , t1.email"
                . " , t1.status as approved_status"
        );
        $this->token_db->from(self::T_USER. " as t1");
        $this->token_db->join(self::M_GENERAL . " as m1", "t1.status = m1.code and m1.key='03'", "left");
        $this->token_db->join(self::M_GENERAL . " as m2", "t1.role = m2.code and m2.key='02'", "left");
        $this->token_db->join(self::M_GENERAL . " as m3", "t1.type = m3.code and m3.key='01'", "left");
        $this->token_db->where('uid', $uid);
        $this->token_db->where('t1.delete_flag', self::VALID);
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->row();
    }
    public function update_comment_by_uid($uid, $comment) {
        $this->token_db->set('comment', $comment);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->where('uid', $uid);
        $result = $this->token_db->update(self::T_USER);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return result;
    }

    public function get_usertree($search = null) {
        $this->token_db->select("m1.value as status "
                . " , t1.uid "
                . " , t1.personal_id "
                . " , t1.user_hash "
                . " , m2.value as ロール"
                . " , m3.value as 種別 "
                . " , t1.family_name as 姓"
                . " , t1.first_name as 名"
                . " , t1.company_name as 会社名"
                . " , t1.family_name_kana as '姓(カナ)'"
                . " , t1.first_name_kana as '名(カナ)'"
                . " , t1.company_name_kana as '会社名(カナ)'"
                . " , t1.memo as 'メモ'"  
                . " , t1.comment as 'コメント'"
                . " , t1.agent_uid as 代理店"

                . " , t2.uid as 子"
                . " , m5.value as 子ロール"
                . " , t2.family_name as 子姓"
                . " , t2.first_name as 子名"
                . " , t2.company_name as 子会社名"

                . " , t3.uid as 孫"
                . " , m6.value as 孫ロール"
                . " , t3.family_name as 孫姓"
                . " , t3.first_name as 孫名"
                . " , t3.company_name as 孫会社名"

                . " , t4.uid as ひ孫"
                . " , m7.value as ひ孫ロール"
                . " , t4.family_name as ひ孫姓"
                . " , t4.first_name as ひ孫名"
                . " , t4.company_name as ひ孫会社名"

                . " , t5.uid as ひひ孫"
                . " , m8.value as ひひ孫ロール"
                . " , t5.family_name as ひひ孫姓"
                . " , t5.first_name as ひひ孫名"
                . " , t5.company_name as ひひ会社名"

                . " , t1.email "
                . " , t1.btc_address as コミッション用ビットコインアドレス"
                . " , m4.value as 支払い方法 "
                . " , t6.receive_address as 請求用ビットコインアドレス");

        $this->token_db->from(self::T_USER . " as t1");
        $this->token_db->join(self::T_USER . " as t2", "t1.user_hash = t2.agent_uid", "left");
        $this->token_db->join(self::T_USER . " as t3", "t2.user_hash = t3.agent_uid", "left");
        $this->token_db->join(self::T_USER . " as t4", "t3.user_hash = t4.agent_uid", "left");
        $this->token_db->join(self::T_USER . " as t5", "t4.user_hash = t5.agent_uid", "left");
        $this->token_db->join(self::T_ORDER . " as t6", "t1.user_hash = t6.client_uid", "left");
        $this->token_db->join(self::M_GENERAL . " as m1", "t1.status = m1.code and m1.key='03'", "left");
        $this->token_db->join(self::M_GENERAL . " as m2", "t1.role = m2.code and m2.key='02'", "left");
        $this->token_db->join(self::M_GENERAL . " as m3", "t1.type = m3.code and m3.key='01'", "left");
        $this->token_db->join(self::M_GENERAL . " as m4", "t6.pay_method = m4.code and m4.key='07'", "left");
        $this->token_db->join(self::M_GENERAL . " as m5", "t2.role = m5.code and m5.key='02'", "left");
        $this->token_db->join(self::M_GENERAL . " as m6", "t3.role = m6.code and m6.key='02'", "left");
        $this->token_db->join(self::M_GENERAL . " as m7", "t4.role = m7.code and m7.key='02'", "left");
        $this->token_db->join(self::M_GENERAL . " as m8", "t5.role = m8.code and m8.key='02'", "left");
        $this->token_db->where('t1.uid>', 3);
        $this->token_db->where('t1.delete_flag', self::VALID);
        //Task 1466 -> does't view cell value is リンクのみ
        $this->token_db->where("m1.code <> '" . $this->config->item("act_gen_link") . "'");

        if (isset($search) && !empty($search)){
            if(!$this->IsNullOrEmptyString($search->uid)) {
                $this->token_db->where('t1.uid', $search->uid);
            }

            if(!$this->IsNullOrEmptyString($search->role)) {
                $this->token_db->where('m2.code', $search->role);
            }

            if(!$this->IsNullOrEmptyString($search->type)) {
                $this->token_db->where('m3.code', $search->type);
            }

            if(!$this->IsNullOrEmptyString($search->email)) {
                $this->token_db->where('t1.email like ',  '%'. $search->email . '%');
            }

            if(!$this->IsNullOrEmptyString($search->order_by)) {
                $this->token_db->order_by($this->token_db->escape_str('t1.'.$search->order_by), $this->token_db->escape_str($search->order_opt));
            }
        }


        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function get_usertree2() {
        $query = "select"
                . "  t1.uid as 'uid'"
                . "  , t1.user_hash"
                . "  , t1.personal_id"
                . "  , m1.value as 'ロール'"
                . "  , m11.value as '状態'"
                . "  , m6.value as '種別'"
                . "  , t1.family_name as '姓'"
                . "  , t1.first_name as '名', t1.company_name as 会社名, t1.family_name_kana as '姓(カナ)',                 t1.first_name_kana as '名(カナ)',   t1.company_name_kana as '会社名(カナ)'"
                . "  , t1.memo as 'メモ'"
                . "  , t1.comment as 'コメント'"
                . "  , t1.email"
                . "  , t1.btc_address as コミッション用ビットコインアドレス"
                . "  , m16.value as 支払い方法"
                . "  , t6.receive_address as 正給用ビットコインアドレス"
                . "  , t1.agent_uid as '代理店'"
                
                . "  , t2.uid as 'uid(子)'"
                . "  , m2.value as 'ロール(子)'"
                . "  , m12.value as '状態(子)'"
                . "  , m7.value as '種別(子)'"
                . "  , t2.family_name as '姓(子)'"
                . "  , t2.first_name as '名(子)', t2.company_name as '会社名(子)'"
                . "  , case when t2.role='03' then t7.pay_method else '--' end as '支払い方法(子)'"
                . "  , case when t2.role='03' then t7.amount else '--' end as '交換希望金額(子)'"
                
                . "  , case when t2.role='03' then '--' else t3.uid end as 'uid(孫)'"
                . "  , case when t2.role='03' then '--' else m3.value end as 'ロール(孫)'"
                . "  , case when t2.role='03' then '--' else m13.value end as '状態(孫)'"
                . "  , case when t2.role='03' then '--' else m8.value end as '種別(孫)'"
                . "  , case when t2.role='03' then '--' else t3.family_name end as '姓(孫)'"
                . "  , case when t2.role='03' then '--' else t3.first_name end as '名(孫)'"
                . "  , case when t2.role='03' then '--' else t3.company_name end as '会社名(孫)'"
                . "  , case when t2.role='03' then '--' else t8.pay_method end as '支払い方法(孫)'"
                . "  , case when t2.role='03' then '--' else t8.amount end as '交換希望金額(孫)'"
                
                . "  , case when t2.role='03' or t3.role='03' then '--' else t4.uid end as 'uid(ひ孫)'"
                . "  , case when t2.role='03' or t3.role='03' then '--' else m4.value end as 'ロール(ひ孫)'"
                . "  , case when t2.role='03' or t3.role='03' then '--' else m14.value end as '状態(ひ孫)'"
                . "  , case when t2.role='03' or t3.role='03' then '--' else m9.value end as '種別(ひ孫)'"
                . "  , case when t2.role='03' or t3.role='03' then '--' else t4.family_name end as '姓(ひ孫)'                "
                . "  , case when t2.role='03' or t3.role='03' then '--' else t4.first_name end as '名(ひ孫)'"
                . "  , case when t2.role='03' or t3.role='03' then '--' else t4.company_name end as '会社名(                ひ孫)'"
                . "  , case when t2.role='03' or t3.role='03' then '--' else t9.pay_method end as '支払い方法(                ひ孫)'"
                . "  , case when t2.role='03' or t3.role='03' then '--' else t9.amount end as '交換希望金額(                ひ孫)'"
                
                . "  , case when t2.role='03' or t3.role='03' or t4.role='03' then '--' else t5.uid end as                 'uid(ひひ孫)'"
                . "  , case when t2.role='03' or t3.role='03' or t4.role='03' then '--' else m5.value end                 as 'ロール(ひひ孫)'"
                . "  , case when t2.role='03' or t3.role='03' or t4.role='03' then '--' else m15.value end                 as '状態(ひひ孫)'"
                . "  , case when t2.role='03' or t3.role='03' or t4.role='03' then '--' else m10.value end                 as '種別(ひひ孫)'"
                . "  , case when t2.role='03' or t3.role='03' or t4.role='03' then '--' else t5.family_name                 end as '姓(ひひ孫)'"
                . "  , case when t2.role='03' or t3.role='03' or t4.role='03' then '--' else t5.first_name                 end as '名(ひひ孫)'"
                . "  , case when t2.role='03' or t3.role='03' or t4.role='03' then '--' else                 t5.company_name end as '会社名(ひひ孫)'"
                . "  , case when t2.role='03' or t3.role='03' or t4.role='03' then '--' else t10.pay_method                 end as '支払い方法(ひひ孫)'"
                . "  , case when t2.role='03' or t3.role='03' or t4.role='03' then '--' else t10.amount end                 as '交換希望金額(ひひ孫)' "
                
                . "from"
                . "  t_user as t1"
                . "  left outer join t_user as t2 on t1.user_hash = t2.agent_uid and t2.delete_flag=0"
                . "  left outer join t_user as t3 on t2.user_hash = t3.agent_uid and t3.delete_flag=0"
                . "  left outer join t_user as t4 on t3.user_hash = t4.agent_uid and t4.delete_flag=0"
                . "  left outer join t_user as t5 on t4.user_hash = t5.agent_uid and t5.delete_flag=0"
                . "  left outer join m_general as m1 on t1.role = m1.code and m1.key='02'"
                . "  left outer join m_general as m2 on t2.role = m2.code and m2.key='02'"
                . "  left outer join m_general as m3 on t3.role = m3.code and m3.key='02'"
                . "  left outer join m_general as m4 on t4.role = m4.code and m4.key='02'"
                . "  left outer join m_general as m5 on t5.role = m5.code and m5.key='02'"
                . "  left outer join m_general as m6 on t1.type = m6.code and m6.key='01'"
                . "  left outer join m_general as m7 on t2.type = m7.code and m7.key='01'"
                . "  left outer join m_general as m8 on t3.type = m8.code and m8.key='01'"
                . "  left outer join m_general as m9 on t4.type = m9.code and m9.key='01'"
                . "  left outer join m_general as m10 on t5.type = m10.code and m10.key='01'"
                //Task 1466 -> does't view cell value is リンクのみ
                . "  left outer join m_general as m11 on t1.status = m11.code and m11.key='03' and m11.code <> '" . $this->config->item("act_gen_link") . "'"
                . "  left outer join m_general as m12 on t2.status = m12.code and m12.key='03' and m12.code <> '" . $this->config->item("act_gen_link") . "'"
                . "  left outer join m_general as m13 on t3.status = m13.code and m13.key='03' and m13.code <> '" . $this->config->item("act_gen_link") . "'"
                . "  left outer join m_general as m14 on t4.status = m14.code and m14.key='03' and m14.code <> '" . $this->config->item("act_gen_link") . "'"
                . "  left outer join m_general as m15 on t5.status = m15.code and m15.key='03' and m15.code <> '" . $this->config->item("act_gen_link") . "'"
                . "  left outer join t_order as t6 on t1.user_hash = t6.client_uid and t6.delete_flag=0"
                . "  left outer join m_general as m16 on t6.pay_method = m16.code and m16.key='07'"
                . "  left outer join t_order as t7 on t2.user_hash = t7.client_uid and t7.delete_flag=0"
                . "  left outer join t_order as t8 on t3.user_hash = t8.client_uid and t8.delete_flag=0"
                . "  left outer join t_order as t9 on t4.user_hash = t9.client_uid and t9.delete_flag=0"
                . "  left outer join t_order as t10 on t5.user_hash = t10.client_uid and t10.delete_flag=0 "
                
                . "where"
                . "  t1.delete_flag =0"
                . "  and t1.role='04'"
                . "  and t1.uid>3";
        $result = $this->token_db->query($query);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function get_order_by_personalid($personal_id) {
        $this->token_db->select("uid"
            .", m3.value as type"
            .", m2.value as role"
            .", m4.value as pay_method"
            .", amount"
            .", t1.status"
            .", t1.memo");
        $this->token_db->from(self::T_USER . " as t1");
        $this->token_db->join(self::T_ORDER . " as t2", "t1.user_hash = t2.client_uid "
                ."and t2.status in (".$this->config->item('order_orderby_bank').",".$this->config->item('order_orderby_btc').") "
                ."and t2.delete_flag = ".self::VALID, "left");
        $this->token_db->join(self::M_GENERAL . " as m2", "t1.role = m2.code and m2.key='02'", "left");
        $this->token_db->join(self::M_GENERAL . " as m3", "t1.type = m3.code and m3.key='01'", "left");
        $this->token_db->join(self::M_GENERAL . " as m4", "t2.pay_method = m4.code and m4.key='07'", "left");
        $this->token_db->where('t1.personal_id', $personal_id);
        $this->token_db->where('t1.delete_flag', self::VALID);
        $this->token_db->order_by('t2.create_at', 'DESC');
        $this->token_db->limit('1');
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }
    
    /**
     * Check string is empty
     */
    // private function IsNullOrEmptyString($question){
    //     return (!isset($question) || trim($question)==='');
    // }

    /**
     * Search user
     */
    public function select_all_search($uid, $role, $type, $email, $order_by, $order_opt) {
        $this->token_db->select("m1.value as status "
                . " , t1.personal_id "
                . " , t1.uid "
                . " , t1.user_hash "
                . " , m2.value as role "
                . " , m3.value as type "
                . " , t1.family_name "
                . " , t1.first_name "
                . " , t1.company_name "
                . " , t1.family_name_kana"
                . " , t1.first_name_kana"
                . " , t1.company_name_kana"
                . " , t1.email "
                . " , m4.value as pay_method "
                . " , t2.receive_address");
        $this->token_db->from(self::T_USER . " as t1");
        $this->token_db->join(self::M_GENERAL . " as m1", "t1.status = m1.code and m1.key='03'", "left");
        $this->token_db->join(self::M_GENERAL . " as m2", "t1.role = m2.code and m2.key='02'", "left");
        $this->token_db->join(self::M_GENERAL . " as m3", "t1.type = m3.code and m3.key='01'", "left");
        $this->token_db->join(self::T_ORDER . " as t2", "t1.user_hash = t2.client_uid", "left");
        $this->token_db->join(self::M_GENERAL . " as m4", "t2.pay_method = m4.code and m4.key='07'", "left");
        $this->token_db->where('t1.delete_flag', self::VALID);

        if(!$this->IsNullOrEmptyString($uid)) {
            $this->token_db->where('t1.uid', $uid);    
        }
        
        if(!$this->IsNullOrEmptyString($role)) {
            $this->token_db->where('m2.code', $role);
        }

        if(!$this->IsNullOrEmptyString($type)) {
            $this->token_db->where('m3.code', $type);
        }

        if(!$this->IsNullOrEmptyString($email)) {
            $this->token_db->where('t1.email', $email);     
        }

        if(!$this->IsNullOrEmptyString($order_by)) {            
            $this->token_db->order_by($this->token_db->escape_str('t1.'.$order_by), $this->token_db->escape_str($order_opt));
        }
        
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /**
     * Search user
     */
    public function select_search_user($uid, $role, $type, $email) {
        $this->token_db->select("m1.value as status "
                . " , t1.personal_id "
                . " , t1.uid "
                . " , t1.user_hash "
                . " , m2.value as role "
                . " , m3.value as type "
                . " , t1.family_name "
                . " , t1.first_name "
                . " , t1.company_name "
                . " , t1.family_name_kana"
                . " , t1.first_name_kana"
                . " , t1.company_name_kana"
                . " , t1.email "
                . " , m4.value as pay_method "
                . " , t2.receive_address");
        $this->token_db->from(self::T_USER . " as t1");
        $this->token_db->join(self::M_GENERAL . " as m1", "t1.status = m1.code and m1.key='03'", "left");
        $this->token_db->join(self::M_GENERAL . " as m2", "t1.role = m2.code and m2.key='02'", "left");
        $this->token_db->join(self::M_GENERAL . " as m3", "t1.type = m3.code and m3.key='01'", "left");
        $this->token_db->join(self::T_ORDER . " as t2", "t1.user_hash = t2.client_uid", "left");
        $this->token_db->join(self::M_GENERAL . " as m4", "t2.pay_method = m4.code and m4.key='07'", "left");
        $this->token_db->where('t1.delete_flag', self::VALID);

        if(!$this->IsNullOrEmptyString($uid)) {
            $this->token_db->where('t1.uid', $uid);    
        }
        
        if(!$this->IsNullOrEmptyString($role)) {
            $this->token_db->where('m2.code', $role);
        }

        if(!$this->IsNullOrEmptyString($type)) {
            $this->token_db->where('m3.code', $type);
        }

        if(!$this->IsNullOrEmptyString($email)) {
            $this->token_db->where('t1.email', $email);     
        }

        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function get_order_by_filter($personal_id, $user_id, $role, $type, $email){               
        $this->token_db->select("uid"
            .", m3.value as type"
            .", m2.value as role"
            .", m2.code as role_code"
            .", m4.value as pay_method"
             .", m4.code as pay_method_code"
            .", amount"
            .", t1.status"
            .", t1.memo");
        $this->token_db->from(self::T_USER . " as t1");
        $this->token_db->join(self::T_ORDER . " as t2", "t1.user_hash = t2.client_uid "
                ."and t2.status in (".$this->config->item('order_orderby_bank').",".$this->config->item('order_orderby_btc').") "
                ."and t2.delete_flag = ".self::VALID, "left");
        $this->token_db->join(self::M_GENERAL . " as m2", "t1.role = m2.code and m2.key='02'", "left");
        $this->token_db->join(self::M_GENERAL . " as m3", "t1.type = m3.code and m3.key='01'", "left");
        $this->token_db->join(self::M_GENERAL . " as m4", "t2.pay_method = m4.code and m4.key='07'", "left");
        $this->token_db->where('t1.personal_id', $personal_id);
        $this->token_db->where('t1.delete_flag', self::VALID);

        if(!$this->IsNullOrEmptyString($user_id)){
            $this->token_db->where('t1.personal_id', $user_id);
        }
        else{            
        }

        if(!$this->IsNullOrEmptyString($role)){
            $this->token_db->where('t1.role', $role);
        }
        else{            
        }
       
         
        if(!$this->IsNullOrEmptyString($type)){
            $this->token_db->where('t2.pay_method', $type); 
        }

        if(!$this->IsNullOrEmptyString($email)){
            $this->token_db->where('t1.email', $email);        
        }
        else{            
        }   

        $this->token_db->order_by('t2.create_at', 'DESC');
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;

    }

    /**
     * 未承認のレコードを取得する
     * 
     * @return type
     */
    public function select_list_not_approved_filter($order_by,$order_opt) {
        $this->token_db->select("personal_id");
        $this->token_db->from(self::T_USER);
        $status = array($this->config->item('act_reg_personal')
            , $this->config->item('approval_pending')
            , $this->config->item('approval_not_approve'));
        $this->token_db->where_in('status', $status);
        $this->token_db->where_in('role', array_merge(array($this->config->item('role_client')), array_keys($this->config->item('role_agent_commission'))));
        $this->token_db->where('delete_flag', self::VALID);        


        $this->token_db->group_by("personal_id");
        $result = $this->token_db->get()->result();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

     /**
     * Search user for overview page
     */
    public function search4_Overview($search = array()) {
        $this->token_db->select("m1.value as status "
                . " , t1.personal_id "
                . " , t1.uid "
                . " , t1.user_hash "
                . " , m2.value as role "
                . " , m3.value as type "
                . " , t1.family_name "
                . " , t1.first_name "
                . " , t1.company_name "
                . " , t1.family_name_kana"
                . " , t1.first_name_kana"
                . " , t1.company_name_kana"
                . " , t1.email ");
        $this->token_db->from(self::T_USER . " as t1");
        $this->token_db->join(self::M_GENERAL . " as m1", "t1.status = m1.code and m1.key='03'", "left");
        $this->token_db->join(self::M_GENERAL . " as m2", "t1.role = m2.code and m2.key='02'", "left");
        $this->token_db->join(self::M_GENERAL . " as m3", "t1.type = m3.code and m3.key='01'", "left");
        $this->token_db->where('t1.delete_flag', self::VALID);
        //Task 1466 -> does't view row n and col 1 is data is リンクのみ
        $this->token_db->where("m1.code <> '" . $this->config->item("act_gen_link") . "'");

        if(!$this->IsNullOrEmptyString($search->uid)) {
            $this->token_db->where('t1.uid', $search->uid);
        }

        if(!$this->IsNullOrEmptyString($search->role)) {
            $this->token_db->where('m2.code', $search->role);
        }

        if(!$this->IsNullOrEmptyString($search->type)) {
            $this->token_db->where('m3.code', $search->type);
        }

        if(!$this->IsNullOrEmptyString($search->email)) {
            $this->token_db->where('t1.email like ',  '%'. $search->email . '%');
        }

        if(!$this->IsNullOrEmptyString($search->order_by)) {
            $this->token_db->order_by($this->token_db->escape_str('t1.'.$search->order_by), $this->token_db->escape_str($search->order_opt));
        }
        
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());        
        //echo $this->token_db->last_query();die;
        return $result;
    }

    public static $roles_opes = array(
        '02' => 'サービス運用担当者(管理者)',
        '21' => 'サービス運用担当者(入出金)',
        '22' => 'サービス運用担当者(注文担当)',
        '23' => 'サービス運用担当者(登録承認)'
    );

    public function get_role_opes ( ){
        return self::$roles_opes;
    }

    public function encrypt_password($password){
        if ($this->config->item('is_hash_password')) {
            $salt = $this->config->item('password_salt');           
            return md5 ($salt.$password);
        } else {
            return $password;
        }

    }

    /**
      
     * @param type $user_hash
     * @return uid
     */
    public function getUidByUserHash($userHash) {        
        $this->token_db->select('uid');
        $this->token_db->from(self::T_USER);
        $this->token_db->where('user_hash', $userHash);
        $this->token_db->where('delete_flag', self::VALID);   
        $this->token_db->order_by('uid', 'DESC');
        $this->token_db->limit('1');
        $result = $this->token_db->get();

        //var_dump($this->token_db->last_query()); die;
        return (0 == $result->num_rows()) ? null : $result->row()->uid;
    }

     /**
      
     * @param type $hash
     * @param type $data
     * @return type
     */
    public function updateDataByUserHash($hash, $data) { 
        $this->token_db->where('user_hash', $hash);
        $result = $this->token_db->update(self::T_USER, $data);
        log_message('debug', $this->token_db->last_query());
        return $result;
    }

    // get user-role is operator
    public function getOperatorUser($roles) {        
        $roleKeyList = array_keys($roles); 
        $this->token_db->select("uid"
                . " , family_name"
                . " , first_name"
                . " , email"
                . " , role");
        $this->token_db->from(self::T_USER);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->where_in('role', $roleKeyList);
        $this->token_db->order_by('uid', 'DESC');
        $result = $this->token_db->get()->result();
        //echo $this->token_db->last_query();die;
        return $result;
    }

    /**
     * A function isUserRole 
     * 
     * @param type $email
     * @param type $role
     * @return type-userhash
     */
    public function isUserRole($arrPersonalId, $role) {
        $this->token_db->select('user_hash');
        $this->token_db->from(self::T_USER);
        $this->token_db->where_in('personal_id', $arrPersonalId);
        $this->token_db->where('role', $role);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->limit(1);
        $result = $this->token_db->get();
        return (0 == $result->num_rows()) ? null : $result->row()->user_hash;
    }
}
