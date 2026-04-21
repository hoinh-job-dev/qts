<?php

class EmailQueueRedeem_model extends MY_model {

    const T_EMAIL_QUEUE_REDEEM = 't_email_queue_redeem';

    const VALID = 0;
    const INVALID = 1;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Tokyo");
    }

    /*****************************************
     * 
     *****************************************/

    public function get_data($is_sent = NULL) {
        log_message('debug', 'EmailQueueRedeem_model/get_data');
        $tEmailQueueRedeem = self::T_EMAIL_QUEUE_REDEEM;

        $cond = '1=1';
        if($is_sent !== NULL) {
            $cond = "t1.is_sent = '$is_sent'";
        }

        // Please note that the SQL query mut be ORDER BY `id` ASC, this means, will get email insert first to send
        $sql = "
            SELECT t1.*
            FROM `$tEmailQueueRedeem` AS t1 
            WHERE $cond
            ORDER BY `id` ASC
        ";

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result() : array();

        $data = array();
        foreach($result as $row) {
            $data[$row->id] = $row;
        }
        //log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $data; 
    }

    public function insert_data($arrMultiRecordData) {
        $data = $arrMultiRecordData;
        if(!is_array($data) || empty($data) || !is_array($data[0]) || empty($data[0])) {
            return false;
        }
        $result = $this->token_db->insert_batch(self::T_EMAIL_QUEUE_REDEEM, $data);
        //log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result > 0;
    }

    public function update_data($id, $data) {
        log_message('debug', 'EmailQueueRedeem_model/update_data');
        if(empty($id)) return false;
        if(is_array($id)) {
            $this->token_db->where_in('id', $id);
        }
        else {
            $this->token_db->where('id', $id);
        }
        $result = $this->token_db->update(self::T_EMAIL_QUEUE_REDEEM, $data);
        //log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function delete_data($object){
        log_message('debug', 'EmailQueueRedeem_model/delete_data');
        if(empty($object)) return false;
        $this->token_db->where('object', $object);
        $result = $this->token_db->delete(self::T_EMAIL_QUEUE_REDEEM);
        log_message('debug', $this->token_db->last_query());
        return $result;
    }
}
