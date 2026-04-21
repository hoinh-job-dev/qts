<?php

class General_model extends MY_model {
    // テーブル名
    const M_GENERAL = 'm_general';
    const VALID = 0;
    const TYPE = '01';
    const ROLE = '02';
    const STATUS = '03';
    const METHOD = '07';
    const ORDER_STATUS = '05';


    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Tokyo");
    }

    private function get($key) {
        $this->token_db->select("code, value");
        $this->token_db->from(self::M_GENERAL);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->where('key', $key);
        $this->token_db->order_by('code', 'ASC');

        return $this->token_db->get();
    }
    
    public function get_type(){
        return $this->get(self::TYPE);
    }

    public function get_role(){
        return $this->get(self::ROLE);
    }

    public function get_Payment_Method(){
       return $this->get(self::METHOD);
    }

    public function get_status(){
       return $this->get(self::STATUS);
    }
	public function get_Order_Status(){
       return $this->get(self::ORDER_STATUS);
    }

    public function get_map($key) {
        $result = $this->get($key)->result();
        $map = array();
        foreach ($result as $row){
            $map[$row->code] = $row->value;
        }
        return $map;
    }

    public function getPaymentMethodMap(){
        return $this->get_map(self::METHOD);
    }

    public function getRoleMap(){
        return $this->get_map(self::ROLE);
    }

    public function getStatusMap(){
        return $this->get_map(self::STATUS);
    }

    public function getOrderStatusMap(){
        return $this->get_map(self::ORDER_STATUS);
    }
}
