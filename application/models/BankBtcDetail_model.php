<?php

class BankBtcDetail_model extends MY_model {

    // 
    const T_BANK_BTC_DETAIL = 't_bank_btc_details';
    const T_ORDER = 't_order';

    const VALID = 0;
    const INVALID = 1;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Tokyo");
    }

    /*****************************************
     * 
     *****************************************/

    public function get_data($address = NULL) {
        $tBankBtcDetails = self::T_BANK_BTC_DETAIL;
        $tOrder = self::T_ORDER;

        $cond = "1=1";
        if(!empty($address)) {
            $cond = "t1.btc_address = '$address'";
        }

        $sql = "
            SELECT t1.*,
                (
                    SELECT `client_uid` 
                    FROM `$tOrder` 
                    WHERE order_number = t1.order_number 
                    LIMIT 1
                ) AS `client_uid`
            FROM `$tBankBtcDetails` AS t1 
            WHERE $cond
        ";

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result() : array();

        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;

        return $result; 
    }

    public function get_order_numbers($address = NULL) {
        $data = array();
        $arr = $this->get_data($address);
        foreach($arr as $row) {
            $data[] = $row->order_number;
        }
        return $data;
    }

    public function insert_data($arrMultiRecordData) {
        $data = $arrMultiRecordData;
        if(!is_array($data) || empty($data) || !is_array($data[0]) || empty($data[0])) {
            return false;
        }
        $result = $this->token_db->insert_batch(self::T_BANK_BTC_DETAIL, $data);
        log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result > 0;
    }

    public function update_by_ordernumber($order_number, $data) {
        $this->token_db->where('order_number', $order_number);
        $result = $this->token_db->update(self::T_BANK_BTC_DETAIL, $data);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    // check if order has been processed before, this will prevent submit twice
    public function check_order_number($arrOrderNumbers) {
        if(!is_array($arrOrderNumbers) || empty($arrOrderNumbers)) {
            return false;
        }

        $tBankBtcDetails = self::T_BANK_BTC_DETAIL;
        $tOrder = self::T_ORDER;

        $strOrderNumbers = implode("', '", $arrOrderNumbers);

        $sql = "
            SELECT t1.*
            FROM `$tBankBtcDetails` AS t1 
            WHERE t1.order_number IN ('$strOrderNumbers')
        ";

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result() : array();

        //log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;

        return count($result) == 0; 
    }
}
