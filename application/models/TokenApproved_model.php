<?php

class TokenApproved_model extends MY_model {

    // 
    const T_USER = 't_user';
    const T_ORDER = 't_order';
    const T_TOKEN_APPROVED = "t_token_approved";
    const T_COMMISION = "t_commission";

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
    public function insert_approved_tokens($arrMultiRecordData) {
        $data = $arrMultiRecordData;
        if(!is_array($data) || empty($data)) {
            return false;
        }
        $result = $this->token_db->insert_batch(self::T_TOKEN_APPROVED, $data);
        log_message('debug', $this->token_db->last_query());
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result > 0;
    }

    public function get_unsent_list($limit=NULL, $offset=NULL) {
        $tUser = self::T_USER;
        $tOrder = self::T_ORDER;
        $tTokenApproved = self::T_TOKEN_APPROVED;
        $tCommission = self::T_COMMISION;

        $limitStr = "";
        if($limit !== NULL && (int) $limit > 0) {
            $offset = (int) $offset >= 0 ? (int) $offset : 0;
            $limitStr = "LIMIT $limit OFFSET $offset";
        }

        $validFlag = SELF::VALID;

        $sql = "
            SELECT t1.*,
                TC.family_name as client_family_name, 
                TC.first_name as client_first_name, 
                TA.family_name as agent_family_name, 
                TA.first_name as agent_first_name,
                TC.uid as client_uid,
                TA.uid as agent_uid
            FROM `$tTokenApproved` AS t1 
            LEFT OUTER JOIN `$tUser` AS TC ON TC.user_hash = t1.client_uid 
            LEFT OUTER JOIN `$tUser` AS TA ON TA.user_hash = t1.agent_uid 
            WHERE t1.`delete_flag` = $validFlag AND t1.`closed_date` IS NULL OR t1.`closed_date` = '' 
            ORDER BY t1.create_at DESC 
            $limitStr 
        ";

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result() : array();

        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;

        return $result; 
    }

    public function update_by_ordernumber($order_number, $data) {
        $this->token_db->where('order_number', $order_number);
        $result = $this->token_db->update(self::T_TOKEN_APPROVED, $data);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }
    
}
