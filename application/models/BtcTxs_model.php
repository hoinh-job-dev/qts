<?php

class BtcTxs_model extends MY_model {

    // Table name
    const T_BTC_TXS = ' t_btc_txs ';

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
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
        $data = array_merge($data, array(
            'create_by' => $this->config->item('AUTO_ID'),
            'create_at' => $now
        ));
        $result = $this->token_db->insert(self::T_BTC_TXS, $data);
        return $result;
    }

    /**
     * Get by address
     *
     * @param type $address
     * @return type
     */
    public function get_by_addr($out_address){
        $this->token_db->select('*');
        $this->token_db->from(self::T_BTC_TXS);
        $this->token_db->where('out_address', $out_address);
        $result = $this->token_db->get()->result();
        return $result;
    }

    /**
     * Get by tx_id
     *
     * @param type $tx_id
     * @return type
     */
    public function get_by_txid($tx_id){
        $this->token_db->select('*');
        $this->token_db->from(self::T_BTC_TXS);
        $this->token_db->where('tx_id', $tx_id);
        $result = $this->token_db->get()->result();
        return $result;
    }

    /**
     * Get unuse address
     *
     * @param type $count
     * @return type
     */
    public function get_unuse_addr($count){
        $this->token_db->select('*');
        $this->token_db->from(self::T_BTC_TXS);
        $this->token_db->where('status', 0);
        $this->token_db->limit($count);

        $result = $this->token_db->get()->result();
        return $result;
    }

    /**
     * Update tx
     *
     * @param type $tx_id
     * @param type $address
     * @param type $data
     * @return type
     */
    public function update($tx_id, $address, $data){
        $this->token_db->where('out_address', $address);
        $this->token_db->where('tx_id', $tx_id);

        $result = $this->token_db->update(self::T_BTC_TXS, $data);
        return $result;
    }
}
