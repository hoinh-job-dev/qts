<?php

class BankBtcHeader_model extends MY_model {

    // 
    const T_BANK_BTC_HEADER = 't_bank_btc_header';

    const VALID = 0;
    const INVALID = 1;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Tokyo");
    }

    /*****************************************
     * 
     *****************************************/

    public function get_data($complete = NULL) {
        $tBankBtcHeader = self::T_BANK_BTC_HEADER;

        $cond = '1=1';
        if($complete !== NULL) {
            $cond = "t1.complete = '$complete'";
        }

        $sql = "
            SELECT t1.*
            FROM `$tBankBtcHeader` AS t1 
            WHERE $cond
        ";

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result() : array();

        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;

        return $result; 
    }

    public function get_by_address($address) {
        $tBankBtcHeader = self::T_BANK_BTC_HEADER;

        $sql = "
            SELECT t1.*
            FROM `$tBankBtcHeader` AS t1 
            WHERE t1.btc_address = '$address'
        ";

        $result = $this->token_db->query($sql);
        $result = !empty($result) && !empty($result->result()) ? $result->result()[0] : array();

        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;

        return $result; 
    }

    public function insert_data($arrMultiRecordData) {
        $data = $arrMultiRecordData;
        if(!is_array($data) || empty($data) || !is_array($data[0]) || empty($data[0])) {
            return false;
        }
        $result = $this->token_db->insert_batch(self::T_BANK_BTC_HEADER, $data);
        log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result > 0;
    }

    public function update_by_address($address, $data) {
        $this->token_db->where('btc_address', $address);
        $result = $this->token_db->update(self::T_BANK_BTC_HEADER, $data);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }
}
