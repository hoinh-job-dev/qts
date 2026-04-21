<?php

class Activity_model extends MY_model {

    // テーブル名
    const T_ACTIVITY = 't_activity';
    const T_USER = 't_user';
    const M_GENERAL = 'm_general';

    const VALID = 0;

    public function __construct() {
        parent::__construct();
    }

    /*****************************************
     * アクティビティ発生時
     *****************************************/

    /**
     * アクティビティを登録する
     *
     * @param type $data
     * @return type
     */
    public function insert_activity($data) {
        $result = $this->token_db->insert(self::T_ACTIVITY, $data);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /*****************************************
     * 注文時
     *****************************************/

    /**
     * 登録時に代理店を取得する
     * 
     * @param type $client
     * @return type
     */
    public function get_agentuid_by_userhash($client) {
        $this->token_db->select('user_hash');
        $this->token_db->from(self::T_ACTIVITY);
        $this->token_db->where('object', $client);
        $this->token_db->where('activity_code', $this->config->item('act_gen_link'));
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return (null == $result->row()) ? null : $result->row()->user_hash;
    }

    public function get_status_by_userhash($userhash) {
        $this->token_db->select('count(user_hash)');
        $this->token_db->from(self::T_ACTIVITY);
        $this->token_db->where('user_hash', $userhash);
        $this->token_db->where('activity_code', $this->config->item('act_reg_personal'));
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return (null == $result->row()) ? null : $result->num_rows();
    }

    // アクティビティの更新 なし

    public function select_all() {
        $query = "select "
                . " t1.activity_id "
                . " , t2.uid "
                . " , t2.family_name "
                . " , t2.first_name "
                . " , m1.value as action "
                . " , t1.object "
                . " , t1.memo "
                . " , t1.create_at "
                . "from "
                . " " . self::T_ACTIVITY . " as t1 "
                . " left outer join " . self::M_GENERAL . " as m1 "
                . "   on t1.activity_code = m1.code and m1.key='03' "
                . " left outer join " . self::T_USER . " as t2 "
                . "   on t1.user_hash = t2.user_hash "
                . "where "
                . " t1.delete_flag=" . self::VALID." "
                . "order by "
                . " t1.create_at desc";
        $result = $this->token_db->query($query);
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

    public function select_all_search($id, $user_id, $activity_code, $from, $to, $order_by, $order_opt) {
        $query = "select "
                . " t1.activity_id "
                . " , t2.uid "
                . " , t2.family_name "
                . " , t2.first_name "
                . " , m1.value as action "
                . " , t1.object "
                . " , t1.memo "
                . " , t1.create_at "
                . "from "
                . " " . self::T_ACTIVITY . " as t1 "
                . " left outer join " . self::M_GENERAL . " as m1 "
                . "   on t1.activity_code = m1.code and m1.key='03' "
                . " left outer join " . self::T_USER . " as t2 "
                . "   on t1.user_hash = t2.user_hash "
                . "where "
                . " t1.delete_flag=" . self::VALID." ";

        if(!$this->IsNullOrEmptyString($id)) {
            $query = $query . " and t1.activity_id = " . $this->token_db->escape($id);
        }

        if(!$this->IsNullOrEmptyString($user_id)) {
            $query = $query . " and t2.uid = " . $this->token_db->escape($user_id);   
        }

        if(!$this->IsNullOrEmptyString($activity_code)) {
            $query = $query . " and t1.activity_code = " . $this->token_db->escape($activity_code);   
        }

        if(!$this->IsNullOrEmptyString($from) && !$this->IsNullOrEmptyString($to)) {
            $query = $query . " and t1.create_at between " . $this->token_db->escape($from." 00:00:00") 
                                                . " and " . $this->token_db->escape($to." 23:59:59");  
            
        } else if(!$this->IsNullOrEmptyString($from)) {
            $query = $query . " and t1.create_at >= " . $this->token_db->escape($from." 00:00:00");            
        } elseif (!$this->IsNullOrEmptyString($to)) {
            $query = $query . " and t1.create_at <= " . $this->token_db->escape($to." 23:59:59");
        }
        
        $order_by_table = ['activity_id'=>'t1', 'uid' => 't2', 'activity_code' => 't1', 'create_at'=>'t1'];

        if(isset($order_by) && trim($order_by) !== "") {
            $order_by_str = $this->token_db->escape_str($order_by_table[$order_by].".".$order_by);         
            $query = $query . " order by " . $order_by_str . " " . $this->token_db->escape_str($order_opt);
        } else {
            $query =  $query . " order by t1.create_at desc";    
        }
        $result = $this->token_db->query($query);
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function updateMemo($actId, $memo) {         
        $this->token_db->set('memo', $memo); 
        $this->token_db->where('delete_flag', self::VALID); 
        $this->token_db->where('activity_id', $actId); 
        $result = $this->token_db->update(self::T_ACTIVITY); 
        log_message('debug', $this->token_db->last_query()); 
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit; 
        return $result; 
    } 
}
