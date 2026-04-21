<?php

class BtcAddress_model extends MY_model {

    // Table name
    const T_BTC_ADDRESS = 't_btc_address';

    const VALID = 0;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Insert address
     *
     * @param type $data
     * @return type
     */
    public function insert_addr($data) {
        $result = $this->token_db->insert(self::T_BTC_ADDRESS, $data);
        return $result;
    }

    /**
     * Get by address
     *
     * @param type $address
     * @return type
     */
    public function get_by_addr($address){
        $this->token_db->select('*');
        $this->token_db->from(self::T_BTC_ADDRESS);
        $this->token_db->where('address', $address);
        $result = $this->token_db->get()->result();
        return $result;
    }

    /**
     * Get by order number
     *
     * @param type $address
     * @return type
     */
    public function get_by_orderNumber($order_number){
        $this->token_db->select('*');
        $this->token_db->from(self::T_BTC_ADDRESS);
        $this->token_db->where('order_number', $order_number);
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
        $result = $this->get_available_addrs($count);

        while(count($result) < $count) {
            $this->create_addresses();
            $result = $this->get_available_addrs($count);
        }

        return $result;
    }

    private function get_available_addrs($count) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_BTC_ADDRESS);
        $this->token_db->where('status', 0);
        $this->token_db->limit($count);

        $result = $this->token_db->get()->result();

        return $result;
    }

    private function create_addresses() {
        $apiUrl = $this->config->item('wallet-server') . '/create-address';
        $this->load->helper('http');
        $http = new Http();
        $http->setMethod('GET');
        $http->request($apiUrl);
        $response = $http->getResponse();
        log_message('debug', '=========create address => URL: ' . $apiUrl);
        log_message('debug', 'Create Address result: ' . json_encode($response));
        if(!empty($response)) {
            $response = json_decode($response);
            if(is_object($response) && isset($response->status) && $response->status == 'success') {
                // insert into DB
                foreach($response->data as $addr) {
                    $result =  $this->insert_addr(array('address' => $addr));
                }
            }
        }
    }

    /**
     * Update address
     *
     * @param type $address
     * @param type $data
     * @return type
     */
    public function update_by_addr($address, $data){
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $this->token_db->set('update_at', $now);

        $this->token_db->where('address', $address);

        $result = $this->token_db->update(self::T_BTC_ADDRESS, $data);
        return $result;
    }

    /**
     * Update address
     *
     * @param type $address
     * @param type $data
     * @return type
     */
    public function update_by_orderNumber($order_number, $data){
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $this->token_db->set('update_at', $now);

        $this->token_db->where('order_number', $order_number);

        $result = $this->token_db->update(self::T_BTC_ADDRESS, $data);
        return $result;
    }
}
