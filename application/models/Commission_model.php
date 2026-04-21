<?php

class Commission_model extends MY_model {

    // テーブル名
    const T_COMMISSION = 't_commission';
    const T_USER = 't_user';
    const T_ORDER = 't_order';
    const T_ACTIVITY = 't_activity';

    const VALID = 0;

    public function __construct() {
        parent::__construct();

        date_default_timezone_set("Asia/Tokyo");
    }

    /*****************************************
     * コミッション獲得時
     *****************************************/

    // 代理店が獲得したコミッションを登録する
    public function insert_comission($data) {
        $query = $this->token_db->insert(self::T_COMMISSION, $data);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($query); exit;
        return $query ? true : false;
    }

    // 更新なし


    /*****************************************
     * コミッション金額確認時
     *****************************************/

    // 代理店が獲得したコミッション金額合計
    public function select_by_uid($userhash, $is_payed) {
        $query = "select "
                . "  sum(quantity) as quantity "
                . "from "
                . "  " . self::T_COMMISSION . " "
                . "where "
                . "  user_hash='" . $userhash . "' "
                . "  and is_payed=" . $is_payed . " "
                . "  and delete_flag = " . self::VALID;

        $result = $this->token_db->query($query);
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        $result = $result->result();
        return $result[0];
    }

    // 代理店が獲得したコミッション金額合計
    public function get_history_by_userhash($userhash) {
        $this->token_db->select("t1.order_number"
                . ", t3.uid as client_uid"
                . ", t3.family_name as client_family_name"
                . ", t3.first_name as client_first_name"
                . ", t1.quantity"
                . ", t1.create_at"
                . ", t1.update_at"
                . ", t1.is_payed"
                . ", t1.user_hash AS `comm_agent_uid`"
                . ", t2.agent_uid AS `order_agent_uid`"
                . ", (IF(t1.user_hash != t2.agent_uid, 1, 0)) AS `is_down_line`"
                . ", t3.email AS `client_email`" 
                . ", t3.role " 
                . ", t3.btc_address"); 
        $this->token_db->from(self::T_COMMISSION . " as t1");
        $this->token_db->join(self::T_ORDER . " as t2", "t2.order_number = t1.order_number and t2.status='31'", "left");
        $this->token_db->join(self::T_USER . " as t3", "t3.user_hash = t2.client_uid", "left");
        $this->token_db->where('t1.user_hash', $userhash);
        $this->token_db->where('t1.delete_flag', self::VALID);
        $this->token_db->order_by('t1.create_at', 'DESC');
        $result = $this->token_db->get();
        // log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /*****************************************
     * コミッション支払い時
     *****************************************/

    /**
     * 
     * @return type
     */
    public function select_not_payed($userhash) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_COMMISSION);
        $this->token_db->where('user_hash', $userhash);
        $this->token_db->where('is_payed', 0);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->result();
    }

    /**
     * 
     * @param type $commission_id
     * @param type $data
     * @return type
     */
    public function update_by_commission_id($commission_id, $data) {
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
        
        $this->token_db->set('update_at', $now);
        $this->token_db->set('is_payed', 1);
        $this->token_db->where('commission_id', $commission_id);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->update(self::T_COMMISSION, $data);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function get_not_payed($limit=NULL, $offset=NULL) {
        $limitStr = "";
        if($limit !== NULL && (int) $limit > 0) {
            $offset = (int) $offset >= 0 ? (int) $offset : 0;
            $limitStr = "LIMIT $limit OFFSET $offset";
        }
        $query = "select "
                . " t1.order_number "
                . " , t1.commission_id"
                . " , t5.uid as client_uid"
                . " , t5.family_name as client_family_name"
                . " , t5.first_name as client_first_name"
                . " , t3.uid as agent_uid"
                . " , t3.family_name as agent_family_name"
                . " , t3.first_name as agent_first_name"
                . " , t1.quantity "
                . " , t1.is_payed "
                . "from "
                . " " . self::T_COMMISSION . " as t1 "
                . " left outer join " . self::T_USER . " as t3 "
                . "   on t1.user_hash = t3.user_hash "
                . " left outer join " . self::T_ORDER . " as t4 "
                . "   on t1.order_number = t4.order_number and t4.status = '31' "
                . " left outer join " . self::T_USER . " as t5 "
                . "   on t4.client_uid = t5.user_hash "
                . "where "
                . " t1.delete_flag = " . self::VALID
                . " and t1.is_payed = 0 "
                . " and t1.approve_flag = 1 "
                . "order by "
                . " t1.create_at"
                . " $limitStr";
        $result = $this->token_db->query($query);
        //log_message('debug', $this->token_db->last_query());
         //echo $this->token_db->last_query(); echo "<br><br>"; //var_dump($result); exit;
        return $result->result();
    }

    /**
     * Check string is empty
     */
    // private function IsNullOrEmptyString($question){
    //     return (!isset($question) || trim($question)==='');
    // }

    public function get_not_payed_search($order_number, $uid, $pay_method, $order_by, $order_opt, $limit=NULL, $offset=NULL) {
        $query = "select "
                . " t1.order_number "
                . " , t1.commission_id"
                . " , t5.uid as client_uid"
                . " , t5.family_name as client_family_name"
                . " , t5.first_name as client_first_name"
                . " , t3.uid as agent_uid"
                . " , t3.family_name as agent_family_name"
                . " , t3.first_name as agent_first_name"
                . " , t1.quantity "
                . " , t1.is_payed "
                . "from "
                . " " . self::T_COMMISSION . " as t1 "
                . " left outer join " . self::T_USER . " as t3 "
                . "   on t1.user_hash = t3.user_hash "
                . " left outer join " . self::T_ORDER . " as t4 "
                . "   on t1.order_number = t4.order_number and t4.status = '31' "
                . " left outer join " . self::T_USER . " as t5 "
                . "   on t4.client_uid = t5.user_hash "
                . "where "
                . " t1.delete_flag = " . self::VALID
                . " and t1.is_payed = 0 "
                . " and t1.approve_flag = 1 ";

        if(!$this->IsNullOrEmptyString($order_number)){
            $query = $query . " and t1.order_number = " . $this->token_db->escape($order_number);
        }

        if(!$this->IsNullOrEmptyString($pay_method)){
            $query = $query . " and t4.pay_method = " . $this->token_db->escape($pay_method);
        }

        if(!$this->IsNullOrEmptyString($uid)){
            $query = $query . " and t3.uid = " . $this->token_db->escape($uid);
        }

        $order_by_table = ['order_number'=>'t1', 'uid' => 't3', 'pay_method' => 't4'];

        if(isset($order_by) && trim($order_by) !== "") {
            $order_by_str = $this->token_db->escape_str($order_by_table[$order_by].".".$order_by);         
            $query = $query . " order by " . $order_by_str . " " . $this->token_db->escape_str($order_opt);
        } else {
            $query =  $query . " order by t1.create_at";    
        }        

        $limitStr = "";
        if($limit !== NULL && (int) $limit > 0) {
            $offset = (int) $offset >= 0 ? (int) $offset : 0;
            $limitStr = "LIMIT $limit OFFSET $offset";
        }

        $query .= " $limitStr";

        $result = $this->token_db->query($query);
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->result();
    }

    function getDataNotPayedByCommissionIds($arrCommissionIds = array()) {
        $this->token_db->select("t1.*");
        $this->token_db->from(self::T_COMMISSION . " AS t1");
        if(is_array($arrCommissionIds) && !empty($arrCommissionIds)) {
            $this->token_db->where_in("t1.commission_id", $arrCommissionIds);
        }
        $this->token_db->where('t1.is_payed','0');
        $result = $this->token_db->get();
        $result = (!empty($result) ? $result->result() : array());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    function getDataToCreateCompleteOrder() {
        $tOrder = self::T_ORDER;
        $tCommission = self::T_COMMISSION;
        $tActivity = self::T_ACTIVITY;

        $order_send_receipt = $this->config->item('order_send_receipt');
        $act_order_complete = $this->config->item('act_order_complete');

        $sql = "
            SELECT t1.* 
            FROM `$tOrder` AS t1 
            WHERE t1.status = '$order_send_receipt'
                AND t1.order_number IN (
                    SELECT order_number
                    FROM `$tCommission`
                    WHERE is_payed = '1'
                )
                AND t1.order_number NOT IN (
                    SELECT order_number
                    FROM `$tCommission`
                    WHERE is_payed = '0'
                )
                AND t1.order_number NOT IN (
                    SELECT `object`
                    FROM `$tActivity`
                    WHERE activity_code = '$act_order_complete'
                )
        ";

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result() : array();
        //log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }


    public function get_payed($search) {
        $query = "select "
            . " t1.order_number "
            . " , t1.create_at "
			. " , t1.update_at "
            . " , t4.pay_method"
            . " , t5.uid as client_uid"
            . " , t5.family_name as client_family_name"
            . " , t5.first_name as client_first_name"
            . " , t3.uid as agent_uid"
            . " , t3.family_name as agent_family_name"
            . " , t3.first_name as agent_first_name"
            . " , t3.role"
            . " , t1.btc_address "
            . " , t1.quantity "
            . "from "
            . " " . self::T_COMMISSION . " as t1 "
            . " left outer join " . self::T_USER . " as t3 "
            . "   on t1.user_hash = t3.user_hash "
            . " left outer join " . self::T_ORDER . " as t4 "
            . "   on t1.order_number = t4.order_number and t4.status = '31' "
            . " left outer join " . self::T_USER . " as t5 "
            . "   on t4.client_uid = t5.user_hash "
            . "where "
            . " t1.delete_flag = " . self::VALID
            . " and t1.is_payed = 1 ";
        if(isset($search->order_number) && trim($search->order_number) !== "") {
            $query = $query . " and t1.order_number = " . $this->token_db->escape($search->order_number);
        }
        if(isset($search->create_from) && trim($search->create_from) !== "") {
            $query = $query . " and t1.update_at >= " . $this->token_db->escape($search->create_from);
        }
        if(isset($search->create_to) && trim($search->create_to) !== "") {            
            $query = $query . " and t1.update_at < DATE_ADD(" . $this->token_db->escape($search->create_to) . ", interval 1 day)";
        }
        if (!$this->IsNullOrEmptyString($search->pay_method)) {
            $query = $query . " and t4.pay_method = " . $this->token_db->escape($search->pay_method);
        }
        if (!$this->IsNullOrEmptyString($search->agent_uid)) {
            $query = $query . " and t3.uid = " . $this->token_db->escape($search->agent_uid);
        }
        if (!$this->IsNullOrEmptyString($search->agent_role)) {
            $query = $query . " and t3.role = " . $this->token_db->escape($search->agent_role);
        }
        if (!$this->IsNullOrEmptyString($search->order_by)) {
            $query = $query . "order by ";
            if ($search->order_by == 'order_number') {
                $query = $query . "t1." . $search->order_by . " " . $search->order_opt;
            } else if ($search->order_by == 'update_at') {
                $query = $query . "t1.update_at " . $search->order_opt;
            } else if ($search->order_by == 'agent_uid') {
                $query = $query . "t3.uid " . $search->order_opt;
            }
        }
        $result = $this->token_db->query($query);
        return $result->result();
    }

     // 代理店が獲得したコミッション金額合計
    public function getAllAgentChild($userhash, $search) {
        $sSQL = "SELECT t1.order_number,
            t3.family_name as client_family_name,
            t3.first_name as client_first_name,
            t4.family_name as agent_family_name,
            t4.first_name as agent_first_name,
            t1.quantity,
            t2.amount as order_amount,
            t1.quantity as commission_amount,
            t1.create_at,
            t1.update_at,
            t1.is_payed,
            t1.btc_address, 
            t3.agent_uid AS client_agent_uid,
            t4.agent_uid AS agent_agent_uid
            FROM " . self::T_COMMISSION . " as t1
            INNER JOIN " . self::T_ORDER . " as t2 
            ON t2.order_number = t1.order_number
                    AND (t2.status = '14' or t2.status = '24')
            INNER JOIN " . self::T_USER . " as t3 
            ON t3.user_hash = t2.client_uid
            LEFT JOIN " . self::T_USER . " as t4
            ON t4.user_hash = t2.agent_uid
            WHERE t1.user_hash = '$userhash'";
        if (!$this->IsNullOrEmptyString($search->status)){
            $sSQL .= " AND t1.is_payed = $search->status ";
        }
        //search agent name
        if (!$this->IsNullOrEmptyString($search->agent_name)){
            if (strpos($search->agent_name, ' ') > 0) {
                $pieces = explode(" ", $search->agent_name);
                $sSQL = $sSQL . " and (t4.family_name like '%" . $pieces[0]
                    . "%' or t4.first_name like '%" .$pieces[1]."%')";
            } else {
                $sSQL = $sSQL . " and (t4.family_name like '%" . $search->agent_name
                    . "%' or t4.first_name like '%" .$search->agent_name."%')";
            }
        }
        // if (!$this->IsNullOrEmptyString($search->first_name)){
        //     $sSQL .= " AND t3.first_name LIKE '%$search->first_name%' AND t3.agent_uid = '$userhash'";
        // }
        // if (!$this->IsNullOrEmptyString($search->family_name)){
        //     $sSQL .= " AND t3.family_name LIKE '%$search->family_name%' AND t3.agent_uid = '$userhash'";
        // }

        //search family_name & first name from txtField client_name.
        if (!$this->IsNullOrEmptyString($search->client_name)){
            if (strpos($search->client_name, ' ') > 0) {
                $pieces = explode(" ", $search->client_name);
                $sSQL = $sSQL . " and (t3.family_name like '%" . $pieces[0]
                    . "%' or t3.first_name like '%" . $pieces[1] ."%')";
            }else{
                $sSQL = $sSQL . " and (t3.family_name like '%" . $search->client_name
                    . "%' or t3.first_name like '%" .$search->client_name."%')";
            }
        }
        
        $query = $this->token_db->query($sSQL);
        $result = $query->result();
        return $result;
    }

    public function getCommissionDetailByOrderNumber($order_number){
        $this->token_db->select("t1.order_number as order_number"
            . " , t2.uid as agent_uid"
            . " , t2.family_name "
            . " , t2.first_name "
            . " , t2.company_name "
            . " , t2.family_name_kana"
            . " , t2.first_name_kana"
            . " , t2.company_name_kana"
            . " , t2.role"
            . " , t1.quantity as amount"
            . " , t1.btc_address as btc_address"
        );
        $this->token_db->from(self::T_COMMISSION . " as t1");
        $this->token_db->join(self::T_USER . " as t2", "t1.user_hash = t2.user_hash", "inner");
        $this->token_db->where('t1.delete_flag', self::VALID);
        $this->token_db->where('t1.order_number', $order_number);
        $this->token_db->order_by('t1.create_at', 'DESC');
        $result = $this->token_db->get();
        // log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->result();
    }
}
