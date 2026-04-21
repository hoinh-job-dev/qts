<?php

class Token_model extends MY_model {

    // テーブル名
    const T_TOKEN = 't_token';
    const T_ORDER = 't_order';
    const T_USER = "t_user";
    const T_EMAIL_QUEUE_REDEEM = "t_email_queue_redeem";

    const VALID = 0;



    public function __construct() {
        parent::__construct();

        date_default_timezone_set("Asia/Tokyo");
    }

    /*****************************************
     * トークン発行時
     *****************************************/

    /**
     * トークンと還元コードを登録する
     * 
     * @param type $data
     * @return type
     */
    public function insert_token($data) {
        $this->token_db->insert(self::T_TOKEN, $data);
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); exit;
        $tokencode = $this->select_tokencode_by_ordernumber($data['order_number']);
        return $tokencode;
    }

    /**
     * 作成したレコードのUIDを取得する
     * @param type $agent
     * @return type
     */
    private function select_tokencode_by_ordernumber($order) {
        $this->token_db->select('token_code');
        $this->token_db->from(self::T_TOKEN);
        $this->token_db->where('order_number', $order);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return !empty($result) ? $result->row()->token_code : null;
    }

    // 更新なし


    /*****************************************
     * トークン発行量確認時
     *****************************************/

    // 還元コードで1件取得のみ
    public function select_by_tokencode($code) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_TOKEN);
        $this->token_db->where('token_code', $code);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->row();
    }

    /*****************************************
     * トークン交換時
     *****************************************/

    /**
     * 
     * @return type
     */
    public function select_not_payed() {
        $this->token_db->select('*');
        $this->token_db->from(self::T_TOKEN);
        $this->token_db->where('is_payed', 0);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->result();
    }

    public function select_for_edit_code($create_limit){
        log_message('debug', 'Token_model/select_for_edit_code');
        $this->token_db->select('*');
        $this->token_db->from(self::T_TOKEN);
        $this->token_db->where('is_payed', 0);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->where('create_at <=', $create_limit);
        $result = $this->token_db->get();
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->result();
    }
    /**
     * 
     * @param type $order_number
     * @param type $data
     * @return type
     */
    public function update_by_ordernumber($order_number, $data) {
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        //$this->token_db->set('update_by', $hash);
        $this->token_db->set('update_at', $now);
        $this->token_db->where('order_number', $order_number);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->update(self::T_TOKEN, $data);
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /**
     * The function get data from parameter search
     * @param $search
     * @return $resultset
     */

    public function select_filter_token($search, $type) {
        log_message('debug', 'Token_model/select_filter_token');        

        $query = $this->build_query_token($search, $type);

// echo $this->token_db->query(); echo "<br><br>"; var_dump($query); exit;
//         if(!$this->token_db->query($query)){
//             return NULL;
//         }

        $result = $this->token_db->query($query)->result();
        log_message('debug', $this->token_db->last_query());
       // echo $this->token_db->last_query(); echo "<br><br>"; //var_dump($result); exit;
        return !empty($result) ? $result : array();
        
    }
    /**
     * The function get query statement from parameter search
     * @param $search
     * @return $string : sql query
     */
    public function build_query_token($search, $type){
        log_message('debug', 'Token_model/build_query_token');
        $query = "SELECT "
            . " t1.id as token_id "
            . " , t1.order_number "
            . " , t3.user_hash"
            . " , t3.uid as client_uid "
            . " , t3.family_name as client_familyname "
            . " , t3.first_name as client_firstname "
            . " , t4.is_sent "
            . " , t1.is_payed "
            . " , t1.create_at "
            . " , t1.update_at "
            . " , t4.type "
            . "FROM ". self::T_TOKEN . " as t1 "
            . " left outer join " . self::T_ORDER . " as t2 "
            . "   on t1.order_number = t2.order_number "
            . "  AND (t2.status >= 32 AND t2.status <> 41 AND t2.status <> 42)"
            . " left outer join " . self::T_USER . " as t3 "
            . "   on t2.client_uid = t3.user_hash "
            . " left outer join " . self::T_EMAIL_QUEUE_REDEEM . " as t4 "
            . "   on t2.order_number = t4.object AND t4.type = " . $type . " "
            . "WHERE "           
            . " t1.delete_flag=" . self::VALID . " ";

        if (!$this->IsNullOrEmptyString($search->orderid)) {
            $query = $query . " and t1.order_number = " . $this->token_db->escape($search->orderid);
        }
        if (!$this->IsNullOrEmptyString($search->purchaseid)) {
            $query = $query . " and t3.uid = " . $this->token_db->escape($search->purchaseid);
        }        
        if (!$this->IsNullOrEmptyString($search->client_name)) {
            if (strpos($search->client_name, ' ') > 0) {
                $pieces = explode(" ", $search->client_name);
                $query = $query . " and (t3.family_name like '%" . $pieces[0]
                    . "%' or t3.first_name like '%" . $pieces[1] ."%')";
            }
            else
            {
                $query = $query . " and (t3.family_name like '%" . $search->client_name
                    . "%' or t3.first_name like '%" .$search->client_name."%')";
            }

        }
        if (!$this->IsNullOrEmptyString($search->is_sent)) {
            $query = $query . " and t4.is_sent = " . $this->token_db->escape($search->is_sent);
        }
        if (!$this->IsNullOrEmptyString($search->is_payed)) {
            $query = $query . " and t1.is_payed = " . $this->token_db->escape($search->is_payed);
        }
        // if (!$this->IsNullOrEmptyString($search->is_payed) 
        //         && $search->is_payed >= 0) {
        //     if(2 == $search->is_payed){
        //         $query = $query . " and (t1.is_payed = 2 OR t1.is_payed = 3)";
        //     }else{
        //         $query = $query . " and t1.is_payed = " . $this->token_db->escape($search->is_payed);
        //     }
        // }
        if(isset($search->create_from) && trim($search->create_from) !== "") {
            $query = $query . " and t1.create_at >= " . $this->token_db->escape($search->create_from);
        }
        if(isset($search->create_to) && trim($search->create_to) !== "") {
            $query = $query . " and t1.create_at < DATE_ADD(" . $this->token_db->escape($search->create_to).", interval 1 day)";
        }
        if(isset($search->update_from) && trim($search->update_from) !== "") {
            $query = $query . " and t1.update_at >= " . $this->token_db->escape($search->update_from);
        }
        if(isset($search->update_to) && trim($search->update_to) !== "") {
            $query = $query . " and t1.update_at < DATE_ADD(" . $this->token_db->escape($search->update_to).", interval 1 day)";
        }

        $query = $query . " GROUP BY t1.order_number ";

        if (!$this->IsNullOrEmptyString($search->order_by)) {
            $query = $query . "order by ";
            if ($search->order_by == 'order_number') {
                $query = $query . "t1." . $search->order_by . " " . $search->order_opt;
            } else if ($search->order_by == 'client_uid') {
                $query = $query . "t3.uid " . $search->order_opt;
                return $query;
            }
        }     
        //echo $query; echo "<br><br>"; var_dump($query); exit;
        return $query;
    }

    /**
     * The function get query statement of issueRedeemToken for export CSV file from parameter search
     * @param $search
     * @return $string : query statement
    */
    public function getDataOfIssueAndEditToken($search = null, $type, $is_sent_x, $is_payed_x) {
        log_message('debug', 'Token_model/getDataOfIssueRedeemToken');
        $query = "SELECT "
            . "  t1.order_number as 注文番号"
            . " , t3.uid as 交換者ID"
            . ", CONCAT(t3.family_name" . ", ' '" . ", t3.first_name) as '交換者 名称' "
            . ", CASE t4.is_sent
                    WHEN '1' THEN  '" . $is_sent_x[1] . "'"
                . " WHEN '2' THEN  '" . $is_sent_x[2] . "'"
                . " WHEN '3' THEN  '" . $is_sent_x[3] . "'"
                . " ELSE '' END AS メールの送信状態"
            . ", CASE t1.is_payed
                    WHEN '1' THEN  '" . $is_payed_x[1] . "'"
                . " WHEN '2' THEN  '" . $is_payed_x[2] . "'"
                . " WHEN '3' THEN  '" . $is_payed_x[3] . "'"
                . " ELSE '' END AS リディーム状態"
            . " , t1.create_at as BTC着金日"
            . " , t1.update_at as 変更日"

            . " FROM ". self::T_TOKEN . " as t1 "
            . " left outer join " . self::T_ORDER . " as t2 "
            . "   on t1.order_number = t2.order_number "
            . "  AND (t2.status >= 32 AND t2.status <> 41 AND t2.status <> 42)"
            . " left outer join " . self::T_USER . " as t3 "
            . "   on t2.client_uid = t3.user_hash "
            . " left outer join " . self::T_EMAIL_QUEUE_REDEEM . " as t4 "
            . "   on t2.order_number = t4.object AND t4.type = " . $type . " "
            . " WHERE "
            . " t1.delete_flag=" . self::VALID . " ";

        if (!$this->IsNullOrEmptyString($search->orderid)) {
            $query = $query . " and t1.order_number = " . $this->token_db->escape($search->orderid);
        }
        if (!$this->IsNullOrEmptyString($search->purchaseid)) {
            $query = $query . " and t3.uid = " . $this->token_db->escape($search->purchaseid);
        }
        if (!$this->IsNullOrEmptyString($search->client_name)) {
            if (strpos($search->client_name, ' ') > 0) {
                $pieces = explode(" ", $search->client_name);
                $query = $query . " and (t3.family_name like '%" . $pieces[0]
                    . "%' or t3.first_name like '%" . $pieces[1] ."%')";
            }
            else
            {
                $query = $query . " and (t3.family_name like '%" . $search->client_name
                    . "%' or t3.first_name like '%" .$search->client_name."%')";
            }

        }
        if (!$this->IsNullOrEmptyString($search->is_sent)) {
            $query = $query . " and t4.is_sent = " . $this->token_db->escape($search->is_sent);
        }
        if (!$this->IsNullOrEmptyString($search->is_payed)) {
            $query = $query . " and t1.is_payed = " . $this->token_db->escape($search->is_payed);
        }
        if(isset($search->create_from) && trim($search->create_from) !== "") {
            $query = $query . " and t1.create_at >= " . $this->token_db->escape($search->create_from);
        }
        if(isset($search->create_to) && trim($search->create_to) !== "") {
            $query = $query . " and t1.create_at < DATE_ADD(" . $this->token_db->escape($search->create_to).", interval 1 day)";
        }
        if(isset($search->update_from) && trim($search->update_from) !== "") {
            $query = $query . " and t1.update_at >= " . $this->token_db->escape($search->update_from);
        }
        if(isset($search->update_to) && trim($search->update_to) !== "") {
            $query = $query . " and t1.update_at < DATE_ADD(" . $this->token_db->escape($search->update_to).", interval 1 day)";
        }

        $query = $query . " GROUP BY t1.order_number ";

        if (!$this->IsNullOrEmptyString($search->order_by)) {
            $query = $query . "order by ";
            if ($search->order_by == 'order_number') {
                $query = $query . "t1." . $search->order_by . " " . $search->order_opt;
            } else if ($search->order_by == 'client_uid') {
                $query = $query . "t3.uid " . $search->order_opt;
                return $query;
            }
        }

        $result = $this->token_db->query($query);
        // log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return !empty($result) ? $result : array();
    }  
}
