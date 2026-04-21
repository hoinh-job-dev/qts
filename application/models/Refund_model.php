<?php

class Refund_model extends MY_model {

    // 
    const T_REFUND = "t_refund";
    const T_USER = 't_user';
    const T_BTC_TXS = 't_btc_txs';
    const T_ORDER = 't_order';

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
    public function insert_refund_data($data) {
        if(!is_array($data) || empty($data)) {
            return false;
        }
        $result = $this->token_db->insert(self::T_REFUND, $data);
        log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function update_sent_status($arrIds, $status) {
        if(!is_array($arrIds) || empty($arrIds)) {
            return false;
        }
        $tRefund = self::T_REFUND;
        $arrIds = implode(", ", $arrIds);
        $sql = "UPDATE $tRefund SET `sent_status` = '$status' WHERE `id` IN ($arrIds)";
        $result = $this->token_db->query($sql);
        log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function update_data($id, $data) {
        
        $this->token_db->where('id', $id);
        $result = $this->token_db->update(self::T_REFUND, $data);
        log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function get_refund_list($objSearch) {
        $tRefund = self::T_REFUND;
        $tUser = self::T_USER;

        $conds = array();
        if(isset($objSearch->oper_status) && !empty($objSearch->oper_status)) {
            $conds[] = "t1.oper_status = '" . $objSearch->oper_status . "'";
        }
        if(isset($objSearch->sent_status) && !empty($objSearch->sent_status)) {
            $conds[] = "t1.sent_status = '" . $objSearch->sent_status . "'";
        }
        $conds = !empty($conds) ? implode(" AND ", $conds) : '1=1';

        $sql = "
            SELECT t1.*,
                TC.uid,
                TC.family_name as client_family_name, 
                TC.first_name as client_first_name
            FROM `$tRefund` AS t1 
            LEFT OUTER JOIN `$tUser` AS TC ON TC.user_hash = t1.client_uid 
            WHERE $conds
            ORDER BY t1.create_at DESC 
        ";

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result() : array();

        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;

        return $result;
    }

    public function get_data_by_order_number($arrOrderNumber = array()) {
        if(!is_array($arrOrderNumber) || empty($arrOrderNumber)) {
            return array();
        }

        $arrOrderNumber = implode("', '", $arrOrderNumber);

        $tRefund = self::T_REFUND;
        $tUser = self::T_USER;

        $sql = "
            SELECT t1.*,
                TC.family_name as client_family_name, 
                TC.first_name as client_first_name
            FROM `$tRefund` AS t1 
            LEFT OUTER JOIN `$tUser` AS TC ON TC.user_hash = t1.client_uid 
            WHERE t1.order_number IN ('$arrOrderNumber') 
            ORDER BY t1.create_at DESC 
        ";

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result() : array();

        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;

        return $result;
    }

    public function get_tx_ids_by_order_number($arrOrderNumber = array()) {

        $result = $this->get_data_by_order_number($arrOrderNumber);

        $txIds = array();
        foreach($result as $row) {
            $txIds[] = $row->tx_id;
        }

        return $txIds;
    }

    public function get_first_tx_for_order($orderNumber) {
        $ignoreTxIds = $this->get_tx_ids_by_order_number(array($orderNumber));
        if(empty($ignoreTxIds)) {
            $ignoreTxIds = array('-1');
        }
        $ignoreTxIds = implode("', '", $ignoreTxIds);

        $order_notify_btcaddr = $this->config->item('order_notify_btcaddr');
        $order_receiveby_btc = $this->config->item('order_receiveby_btc');

        $currency_btc = $this->config->item('currency_btc');
        
        $tOrder = self::T_ORDER;
        $tBtcTxs = self::T_BTC_TXS;

        $sql = "
            SELECT t1.*,
                (
                    SELECT receive_address 
                    FROM `$tOrder` 
                    WHERE t1.order_number = order_number AND status = '$order_notify_btcaddr' 
                    LIMIT 1 
                ) AS `btc_address`,
                NULL AS tx_id
            FROM $tOrder AS t1
            WHERE t1.order_number = '$orderNumber' AND (t1.status = '$order_receiveby_btc' OR t1.currency_unit = '$currency_btc') 
            LIMIT 1
        ";
        $btcOrderResult = $this->token_db->query($sql);
        $btcOrderResult = !empty($btcOrderResult) ? $btcOrderResult->result()[0] : array();
        if(empty($btcOrderResult)) {
            return array();
        }

        $sql = "
            SELECT * 
            FROM $tBtcTxs 
            WHERE tx_id NOT IN ('$ignoreTxIds')
        ";
        $txsResult = $this->token_db->query($sql);
        $txsResult = !empty($txsResult) ? $txsResult->result() : array();
        foreach($txsResult as $row) {
            if($row->out_address == $btcOrderResult->btc_address && (float) $row->balance == (float) $btcOrderResult->amount) {
                $btcOrderResult->tx_id = $row->tx_id;
                break;
            }
        }

        return $btcOrderResult;
    }
    
}
