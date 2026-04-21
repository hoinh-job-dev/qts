<?php

class EmailQueue_model extends MY_model {

    // 
    const T_EMAIL_QUEUE = 't_email_queue';

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
        $tEmailQueue = self::T_EMAIL_QUEUE;

        $cond = '1=1';
        if($is_sent !== NULL) {
            $cond = "t1.is_sent = '$is_sent'";
        }

        // Please note that the SQL query mut be ORDER BY `id` ASC, this means, will get email insert first to send
        $sql = "
            SELECT t1.*
            FROM `$tEmailQueue` AS t1 
            WHERE $cond
            ORDER BY `id` ASC
        ";

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result() : array();

        $data = array();
        foreach($result as $row) {
            $data[$row->id] = $row;
        }

        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;

        return $data; 
    }

    public function insert_data($arrMultiRecordData) {
        $data = $arrMultiRecordData;
        if(!is_array($data) || empty($data) || !is_array($data[0]) || empty($data[0])) {
            return false;
        }
        $result = $this->token_db->insert_batch(self::T_EMAIL_QUEUE, $data);
        log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result > 0;
    }

    public function update_data($id, $data) {
        if(empty($id)) return false;
        if(is_array($id)) {
            $this->token_db->where_in('id', $id);
        }
        else {
            $this->token_db->where('id', $id);
        }
        $result = $this->token_db->update(self::T_EMAIL_QUEUE, $data);
        log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /*
     * A function get some records data following condition search of t_email_queue
    */
    public function getData4Search($search) {
        $tEmailQueue = self::T_EMAIL_QUEUE;
        $valid = self::VALID;

        $sql = "
            SELECT t1.*
            FROM `$tEmailQueue` AS t1 
            WHERE `delete_flag` = $valid
        ";
        if (!$this->IsNullOrEmptyString($search->id)) {
            $sql = $sql . " AND id = " . $this->token_db->escape($search->id);
        }
        if (!$this->IsNullOrEmptyString($search->to)) {
            $sql = $sql . " AND `to` like '%" . $search->to . "%'";
        }
        if (!$this->IsNullOrEmptyString($search->memo)) {
            if($this->config->item('memo_4') == $search->memo){
                $sql = $sql . " AND memo like '" . $search->memo . "%'";
            }else{
                $sql = $sql . " AND memo = " . $this->token_db->escape($search->memo);
            }            
        }
        if (!$this->IsNullOrEmptyString($search->object)) {
            $sql = $sql . " AND object like '%" . $search->object . "%'";
        }
        if (!$this->IsNullOrEmptyString($search->is_sent)) {
            $sql = $sql . " AND is_sent = " . $this->token_db->escape($search->is_sent);
        }
        if (!$this->IsNullOrEmptyString($search->date_from)) {
            $sql = $sql . " AND create_at >= " . $this->token_db->escape($search->date_from . " 00:00:00");
        }
        if (!$this->IsNullOrEmptyString($search->date_to)) {
            $sql = $sql . " AND create_at <= " . $this->token_db->escape($search->date_to . " 23:59:59");
        }
        $sql = $sql . " ORDER BY id desc ";        

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result() : array();

        $data = array();
        foreach($result as $row) {
            $data[$row->id] = $row;
        }
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $data; 
    }

    /*
     * A function get data for send email manual
    */
    public function getData4SentEmail($arrID) {
        $tEmailQueue = self::T_EMAIL_QUEUE;
        $valid = self::VALID;

        $arrID=explode(',',$arrID);

        $condition = "(";
        foreach ($arrID as $id) {
            $condition = $condition . $id . ", ";           
        }
        $condition = $condition . "0)";

        $sql = "
            SELECT t1.*
            FROM `$tEmailQueue` AS t1 
            WHERE `delete_flag` = $valid AND id IN $condition
        ";                
        $sql = $sql . " ORDER BY id ";           

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result() : array();

        $data = array();
        foreach($result as $row) {
            $data[$row->id] = $row;
        }
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $data; 
    }

    /**
     * Check string is empty
     */
    // private function IsNullOrEmptyString($question){
    //     return (!isset($question) || trim($question)==='');
    // }
}
