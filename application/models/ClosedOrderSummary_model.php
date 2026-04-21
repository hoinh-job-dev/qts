<?php

class ClosedOrderSummary_model extends MY_model {

    // 
    const T_USER = 't_user';
    const T_ORDER = 't_order';
    const T_TOKEN_APPROVED = "t_token_approved";
    const T_COMMISION = "t_commission";
    const T_CLOSED_ORDER_SUMMARY = "t_closed_order_summary";

    const VALID = 0;
    const INVALID = 1;



    public function __construct() {
        parent::__construct();

        date_default_timezone_set("Asia/Tokyo");
    }

    /*****************************************
     * 注文時
     *****************************************/

    /**
     * 
     * 
     * @param type $data
     * @return type
     */
    public function insert_data($data) {
        if(!is_array($data) || empty($data)) {
            return false;
        }
        $result = $this->token_db->insert(self::T_CLOSED_ORDER_SUMMARY, $data);
        log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function get_data($search = NULL) {
        $tUser = self::T_USER;
        $tOrder = self::T_ORDER;
        $tTokenApproved = self::T_TOKEN_APPROVED;
        $tCommission = self::T_COMMISION;
        $tClosedOrderSummary = self::T_CLOSED_ORDER_SUMMARY;

        $conds = array();

        if(isset($search->search_from) && isset($search->search_to) && !empty($search->search_from) && !empty($search->search_to)) {
            $fromts = strtotime($search->search_from);
            $tots = strtotime($search->search_to);
            if($fromts > $tots) {
                $tmpts = $fromts;
                $fromts = $tots;
                $tots = $tmpts;
                $search->search_from = date("Y/m/d", $fromts);
                $search->search_to = date("Y/m/d", $tots);
            }
        }

        $from = $search->search_from;
        $to = $search->search_to;

        $dtFormat = "Ymd000000";
        if(!empty($from) && !empty($to)) {
            $from = date($dtFormat, strtotime($from));
            $to = date($dtFormat, strtotime($to) + 86400);
            $conds[] = sprintf("t1.closed_date >= '%1\$s' AND t1.closed_date < '%2\$s'", $from, $to);
        } else if(!empty($from)) {
            $from = date($dtFormat, strtotime($from));
            $conds[] = sprintf("t1.closed_date >= '%1\$s'", $from);
        } else if(!empty($to)) {
            $to = date($dtFormat, strtotime($to) + 86400);
            $conds[] = sprintf("t1.closed_date < '%2\$s'", $to);
        }

        if(isset($search->order_number) && !empty($search->order_number)) {
            $conds[] = sprintf("t1.closed_date IN (SELECT closed_date FROM `$tTokenApproved` WHERE order_number = '%s')", $search->order_number);
        }

        if(isset($search->hot_cold_sent_status) && strlen(trim($search->hot_cold_sent_status)) > 0) {
            $conds[] = sprintf("t1.hot_cold_sent_status = '%s'", $search->hot_cold_sent_status);
        }

        if(isset($search->commission_sent_status) && strlen(trim($search->commission_sent_status)) > 0) {
            $conds[] = sprintf("t1.commission_sent_status = '%s'", $search->commission_sent_status);
        }

        $conds = !empty($conds) ? implode(" AND ", $conds) : "1=1";

        $sql = "
            SELECT t1.*
            FROM `$tClosedOrderSummary` AS t1 
            WHERE $conds
            ORDER BY t1.closed_date DESC 
        ";

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result() : array();

        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;

        return $result; 
    }

    public function get_data_to_send() {
        $tUser = self::T_USER;
        $tOrder = self::T_ORDER;
        $tTokenApproved = self::T_TOKEN_APPROVED;
        $tCommission = self::T_COMMISION;
        $tClosedOrderSummary = self::T_CLOSED_ORDER_SUMMARY;

        $sql = "
            SELECT t1.*
            FROM `$tClosedOrderSummary` AS t1 
            WHERE t1.hot_cold_sent_status IS NULL OR t1.hot_cold_sent_status = 0 
                OR t1.commission_sent_status IS NULL OR t1.commission_sent_status = 0 
                OR t1.special_commission_sent_status IS NULL OR t1.special_commission_sent_status = 0 
            ORDER BY t1.closed_date DESC 
        ";

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result() : array();

        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;

        return $result; 
    }

    public function update_data($closedDateTime, $data) {
        if(empty($closedDateTime)) {
            return false;
        }
        if(is_array($closedDateTime)) {
            $this->token_db->where_in('closed_date', $closedDateTime);
        }
        else {
            $this->token_db->where('closed_date', $closedDateTime);
        }
        $result = $this->token_db->update(self::T_CLOSED_ORDER_SUMMARY, $data);
        // log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function create_incompleted_orders($orderNumberArray, $operator_uid = NULL) {
        if(!is_array($orderNumberArray) || empty($orderNumberArray)) {
            log_message('debug', '=================> Try to close orders with empty data');
        }

        if(empty($operator_uid)) {
            $operator_uid = $this->config->item('AUTO_ID');
        }

        $tUser = self::T_USER;
        $tOrder = self::T_ORDER;
        $tTokenApproved = self::T_TOKEN_APPROVED;
        $tCommission = self::T_COMMISION;
        $tClosedOrderSummary = self::T_CLOSED_ORDER_SUMMARY;

        $validFlag = SELF::VALID;

        $hot_btc_rate = $this->config->item('hot_btc_rate');
        $cold_btc_rate = $this->config->item('cold_btc_rate');
        $comm_btc_rate = $this->config->item('commission_btc_rate');
        $spe_comm_btc_rate = $this->config->item('special_commission_btc_rate');

        $dateFormat = 'Y-m-d';
        $dateTimeFormat = 'Y-m-d H:i:s';

        $now = date($dateTimeFormat);

        $closedDateTime = date('YmdHis', strtotime($now));

        $orderNumberStr = implode("', '", $orderNumberArray);
        $sql = "
            SELECT * 
            FROM `$tTokenApproved` 
            WHERE `delete_flag` = $validFlag AND `create_at` <= '$now' AND (`closed_date` IS NULL OR `closed_date` = '') AND `order_number` IN ('$orderNumberStr')
        ";

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result() : array();

        if(empty($result)) {
            log_message('debug', 'create_incompleted_orders: No order to execute. Count order = 0.');
            return array(
                'status' => 'fail',
                'message' => 'No order to execute. Count order = 0.'
            );
        }

        $countOrders = count($result);
        $arrONums = array();
        $totalBtcAmount = 0;
        foreach($result as $row) {
            $totalBtcAmount += (float) $row->btc_amount;
            $arrONums[] = $row->order_number;
        }

        if($totalBtcAmount <= 0) {
            log_message('debug', 'create_incompleted_orders: BTC amount = 0.');
            return array(
                'status' => 'fail',
                'message' => 'BTC amount = 0.'
            );
        }

        $data = array(
            'closed_date' => $closedDateTime,
            'count_orders' => $countOrders,
            'total_btc_amount' => $totalBtcAmount,
            'total_hot_wallet_btc_amount' => ($totalBtcAmount * $hot_btc_rate / 100),
            'total_cold_wallet_btc_amount' => ($totalBtcAmount * $cold_btc_rate / 100),
            'total_commission_btc_amount' => ($totalBtcAmount * $comm_btc_rate / 100),
            'total_special_commission_btc_amount' => ($totalBtcAmount * $spe_comm_btc_rate / 100),
            'hot_cold_sent_status' => 0,
            'commission_sent_status' => 0,
            'special_commission_sent_status' => 0,
            'create_by' => $operator_uid,
            'create_at' => $now
        );

        $result = $this->insert_data($data);

        if($result !== true) {
            log_message('debug', 'create_incompleted_orders: Cannot insert Order Summary data.');
            return array(
                'status' => 'fail',
                'message' => 'Cannot insert Order Summary data.'
            );
        }

        // create success, update data for order in t_token_approved
        $arrONumsStr = implode("', '", $arrONums);
        $sql = "UPDATE `$tTokenApproved` SET `closed_date` = '$closedDateTime' WHERE order_number IN ('$arrONumsStr')";
        $result = $this->token_db->query($sql);
        
        if($result !== true) {
            log_message('debug', 'create_incompleted_orders: Cannot update Approved Token data.');
            return array(
                'status' => 'fail',
                'message' => 'Cannot update Approved Token data.'
            );
        }

        // calculate commission
        // $this->load->library('exchanger');
        // foreach($arrONums as $order_number) {
        //     $this->exchanger->calc_commission($order_number);
        // }
        
        foreach($arrONums as $order_number) {
            //update approve_flag = 1
            $sqlUpdate = "UPDATE `$tCommission` SET `approve_flag` = 1 WHERE order_number = " . $order_number ;
            $this->token_db->query($sqlUpdate);
        }


        log_message('debug', 'create_incompleted_orders: DONE');
        return array(
            'status' => 'success',
            'message' => 'DONE.'
        );
    }

    public function getCommissionData($closed_date){
        $this->token_db->select("t1.closed_date"
            . " , t2.order_number as order_number"
            . " , t3.uid as agent_uid"
            . " , t3.family_name "
            . " , t3.first_name "
            . " , t3.company_name "
            . " , t3.family_name_kana"
            . " , t3.first_name_kana"
            . " , t3.company_name_kana"
            . " , t4.quantity as amount"
            . " , t4.btc_address as btc_address"
        );
        $this->token_db->from(self::T_CLOSED_ORDER_SUMMARY . " as t1");
        $this->token_db->join(self::T_TOKEN_APPROVED . " as t2", "t2.closed_date = t1.closed_date", "left");
        $this->token_db->join(self::T_USER . " as t3", "t3.user_hash = t2.agent_uid", "left");
        $this->token_db->join(self::T_COMMISION . " as t4", "t3.user_hash = t4.user_hash and t2.order_number=t4.order_number", "left");
        $this->token_db->where('t1.delete_flag', self::VALID);
        $this->token_db->where('t2.closed_date', $closed_date);

        $this->token_db->order_by('t1.create_at', 'DESC');
        $result = $this->token_db->get();
        // log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->result();
    }    
}
