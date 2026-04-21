<?php

class ExchangeRate_model extends MY_model {

    // テーブル名
    const T_RATE = 't_exchange_rate';
    const T_USER = 't_user';

    const VALID = 0;


    public function __construct() {
        parent::__construct();
    }

    /*****************************************
     * 登録時
     *****************************************/

    public function insert_rate($data) {
        return $this->token_db->insert(self::T_RATE, $data);
    }

    /*****************************************
     * 表示時
     *****************************************/

    public function get_rate($from, $to, $txtime) {

        $query = "select "
                . " t1.rate "
                . " , t1.create_at "
                . "from "
                . " " . self::T_RATE . " as t1 "
                . "where "
                . " t1.from = '" . $from . "'"
                . " and t1.to = '" . $to . "'"
                . " and t1.create_at <= '" . $txtime . "' "
                . " and t1.rate > 0 "
                . "group by t1.create_at "
                . "order by t1.create_at desc "
                . "limit 2";

        $result = $this->token_db->query($query);
        $result = !empty($result) ? $result->result() : array();

        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;

        return $result;
    }

    public function select_all($from, $to) {
		$this->token_db->from(self::T_RATE);
		if (""!=$from) {
			$this->token_db->where('create_at>', (10 > strlen($from)) ? $from." 00:00:00" : $from);
		}
		if (""!=$to) {
			$this->token_db->where('create_at<=', (10 > strlen($to)) ? $to." 23:59:59" : $to);
		}
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->order_by('create_at', 'DESC');
        return $this->token_db->get();
    }

    public function select_top($number) {
        $this->token_db->select("t1.*,
            t2.first_name, t2.family_name");
        $this->token_db->from(self::T_RATE . " as t1");
        $this->token_db->join(self::T_USER . " as t2", 't2.uid = t1.create_by', 'left');
        $this->token_db->where('t1.delete_flag', self::VALID);
        $this->token_db->order_by('t1.create_at', 'DESC');
        if ($number != null and $number !=1)
            $this->token_db->limit($number);
        $result = $this->token_db->get();
        $result = !empty($result) ? $result->result() : array();
        return $result;
    }
}
