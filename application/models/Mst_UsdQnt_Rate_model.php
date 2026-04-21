<?php

class Mst_UsdQnt_Rate_model extends MY_model {
    // テーブル名
    const T_MST_USDQNT_RATE = 't_mst_usdqnt_rate';



    public function __construct() {
        parent::__construct();

        date_default_timezone_set("Asia/Tokyo");
    }

    //public function select_rate_token($dateApply) {
    public function select_UsdQnt_Rate($dateApply) {    
        $this->token_db->select('from_date, rate_value');
        $this->token_db->from(self::T_MST_USDQNT_RATE);

        // 条件
        $this->token_db->where('from_date <', $dateApply);
        $this->token_db->order_by('from_date', 'DESC');
        $this->token_db->limit(1);
        
        $result = $this->token_db->get();
        return $result->result()[0];
    }
}
