<?php

class BtcBlock_model extends MY_model {

    // Table name
    const T_BLOCK = 't_block';

    const VALID = 0;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Insert tx
     *
     * @param type $data
     * @return type
     */
    public function insert($data) {
        $result = $this->token_db->insert(self::T_BLOCK, $data);
        return $result;
    }

    /**
     * Get last block
     *
     * @param type $data
     * @return type
     */
    public function get_lastblock() {
        $this->token_db->select('*');
        $this->token_db->from(self::T_BLOCK);
        $result = $this->token_db->get()->result();
        if(sizeof($result) > 0){
            return $result[0];
        }
        return null;
    }

    public function get_data($limit = 10) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_BLOCK);
        $this->token_db->order_by('height', 'desc');
        $this->token_db->limit($limit);
        $result = $this->token_db->get();
        return !empty($result) ? $result->result() : array();
    }

    /**
     * Update tx
     *
     * @param type $tx_id
     * @param type $address
     * @param type $data
     * @return type
     */
    public function update($hash, $data){
        $this->token_db->where('hash', $hash);
        $result = $this->token_db->update(self::T_BLOCK, $data);
        return $result;
    }

    public function delete_ignore_hash($hashArr){
        if(!empty($hashArr) && is_array($hashArr)) {
            $tBlock = self::T_BLOCK;
            $hashArr = implode("', '", $hashArr);
            $sql = "DELETE FROM `$tBlock` WHERE `hash` NOT IN ('$hashArr')";
            $result = $this->token_db->query($sql);
            return $result;
        }
        return false;
    }

    public function delete_by_hash($hashArr){
        if(!empty($hashArr) && is_array($hashArr)) {
            $tBlock = self::T_BLOCK;
            $hashArr = implode("', '", $hashArr);
            $sql = "DELETE FROM `$tBlock` WHERE `hash` IN ('$hashArr')";
            $result = $this->token_db->query($sql);
            return $result;
        }
        return false;
    }
}
