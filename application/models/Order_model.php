<?php

class Order_model extends MY_model {

    // テーブル名
    const T_ORDER = 't_order';
    const T_USER = "t_user";
    const T_ACT = "t_activity";
    const T_TOKEN = "t_token";
    const T_TOKENAPPROVED = "t_token_approved";
    const T_RATE = "t_exchange_rate";
    const M_GENERAL = 'm_general';

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
     * 注文状況を登録する
     * 
     * @param type $data
     * @return type
     */
    public function insert_order($data) {
        $result = $this->token_db->insert(self::T_ORDER, $data);
        if (isset($data['order_number'])){
            return $data['order_number'];
        }
        $order_number = $this->get_ordernumber($data['client_uid']);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $order_number;
    }
    
    public function update_order_to_invalid($ordernumber, $hash) {
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $this->token_db->set('delete_flag', self::INVALID);
        $this->token_db->set('update_by', $hash);
        $this->token_db->set('update_at', $now);
        $this->token_db->where('order_number', $ordernumber);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->update(self::T_ORDER);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /**
     * 作成したレコードの受付番号を取得する
     * 
     * @param type $agent
     * @return type
     */
    private function get_ordernumber($user_hash) {
        $this->token_db->select('order_number');
        $this->token_db->from(self::T_ORDER);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->where('client_uid', $user_hash);
        $this->token_db->order_by('create_at', 'desc');
        $this->token_db->limit('1');
        $result = $this->token_db->get();
        return (null == $result->row()) ? null : $result->row()->order_number;
    }

    /*****************************************
     * 表示時
     *****************************************/

    /**
     * 最新のステータスのみの一覧を取得
     * @return type
     */
    public function select_latest_list($status) {
        $query = "select "
                . " t1.order_number "
                . " , t1.status "
                . " , t1.client_uid "
                . " , t2.uid "
                . " , t2.family_name"
                . " , t2.first_name"
                . " , m1.value as pay_method "
                . " , t1.account_name "
                . " , t1.amount "
                . " , t1.currency_unit "
                . " , t1.create_at "
                . " , t1.rsv_char_2 "
                . "from "
                . " " . self::T_ORDER . " as t1 "
                . " left outer join " . self::T_USER . " as t2 "
                . "   on t1.client_uid = t2.user_hash "
                . " left outer join " . self::M_GENERAL . " as m1 "
                . "   on t1.pay_method = m1.code and m1.key = '07'  "
                . "where "
                . " t1.delete_flag = " . self::VALID
                . " and t1.status in (select max(t3.status) from " . self::T_ORDER . " as t3 where t1.order_number = t3.order_number)"
                . " and t1.status in (";
        for ($i = 0; $i < sizeof($status); $i++) {
            if (0 < $i) {
                $query = $query . ",";
            }
            $query = $query . $status[$i];
        }
        $query = $query . ")";
        $status_btc = $this->config->item('order_orderby_btc');
        if ( in_array($status_btc ,$status )){
            $query = $query . " and (t1.status != '$status_btc' or t1.rsv_char_1='1' ) ";
        }

        if ($this->config->item('act_approved')==$status[0]) {
            $query = $query . " and t2.status = " . $this->config->item('act_approved');
        }
        $query = $query . " order by"
                 . " t1.order_number asc";
        $result = $this->token_db->query($query);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return !empty($result) ? $result->result() : array();
    }

    public function select_for_makeToken($order_number, $reissue = FALSE) {
        $ignore_statuses = array(
            $this->config->item('order_invalid'),
            $this->config->item('order_expired')
        );
        if($reissue === FALSE) {
            $ignore_statuses[] = $this->config->item('order_send_receipt');
        }
        $ignore_statuses = implode("', '", $ignore_statuses);

        $query = "select"
            . " qnt.order_number"
            . " , mg.value as pay_method_name"
            . " , qnt.pay_method"
            . " , qnt.create_at"
            . " , qnt.agent_uid"
            . " , qnt.client_uid"
            . " , qnt.currency_unit"
            . " , qnt.expiration_date"
            . " , t_user.uid"
            . " , t_user.user_hash"
            . " , t_user.family_name"
            . " , t_user.first_name"
            . " , t_user.email"
            . " , usd_origin.amount as usd_amount_origin"
            . " , order_usd.amount as usd_amount"
            . " , abs(100*(order_usd.amount - usd_origin.amount)/usd_origin.amount) as diffRate"
            . " , btc.amount"
            . " , btc.status AS `btc_status`"
            . " , qnt.status"
            . " , (
                    CASE WHEN order_usd.exchange_rate IS NULL THEN qnt.exchange_rate * qnt.memo
                    ELSE order_usd.exchange_rate END
                ) as rate"
            . " , qnt.memo as usdqnt"
            . " , qnt.amount as quantity"
            . " , token.token_code "
            . "from"
            . " " . self::T_ORDER . " as qnt"
            . " left outer join " . self::T_ORDER . " as btc"
            . "   on qnt.order_number = btc.order_number and btc.status in ('" . $this->config->item('order_exchange_jpybtc') . "','" . $this->config->item('order_receiveby_btc') . "')"
            . " left outer join " . self::T_ORDER . " as order_usd"
            . "   on qnt.order_number = order_usd.order_number and order_usd.status in ('" . $this->config->item('order_pre_issue_token') . "')"
            . " left outer join " . self::T_ORDER . " as usd_origin"
            . "   on qnt.order_number = usd_origin.order_number and usd_origin.status in ('" . $this->config->item('order_notify_btcaddr') . "','" . $this->config->item('order_notify_bankaccount') . "')"
            . " left outer join " . self::T_USER
            . "   on t_user.user_hash = qnt.client_uid"
            . " left outer join " . self::T_ACT
            . "   on t_activity.object = qnt.order_number "
            . " left outer join " . self::T_TOKEN . " as token"
            . "   on token.order_number = qnt.order_number "
            . " left outer join " . self::M_GENERAL . " as mg "
            . "   on qnt.pay_method = mg.code and mg.key = '07'  "
            . "where"
            . " qnt.delete_flag = " . self::VALID
            . " and qnt.status = '" . $this->config->item('order_issue_token') . "'"
            . " and qnt.order_number NOT IN (
                    SELECT order_number 
                    FROM `".SELF::T_ORDER."` 
                    WHERE `status` IN ('$ignore_statuses')
                )"
            . (null != $order_number ? " and qnt.order_number = '" . $order_number. "'" : NULL)
            . "group by"
            . " qnt.order_number";
        $result = $this->token_db->query($query);
        //$result = $this->token_db->last_query();
        log_message('debug', $this->token_db->last_query());
        return !empty($result) ? $result->result() : array();
    }

    public function select_for_editToken($order_number) {
        log_message('debug', 'Order_model/select_for_editToken');
        if($order_number == NULL){
            return NULL;
        }

        $ignore_statuses = array(
            $this->config->item('order_invalid'),
            $this->config->item('order_expired')
        );
        $ignore_statuses = implode("', '", $ignore_statuses);

        $query = "SELECT "
            . " t_user.family_name"
            . " , t_user.first_name"
            . " , qnt.order_number"
            . " , qnt.create_at"
            . " , btc.amount"
            . " , (
                    CASE WHEN order_usd.exchange_rate IS NULL THEN qnt.exchange_rate * qnt.memo
                    ELSE order_usd.exchange_rate END
                ) as rate"
            . " , order_usdqnt.memo as usdqnt"
            //. " , qnt.amount as quantity"
            . " , token.quantity as quantity"
            . " , token.token_code "
            . " , t_user.user_hash "
            . " , t_user.email "
            
            . " FROM"
            . " " . self::T_ORDER . " as qnt"
            . " left outer join " . self::T_ORDER . " as btc"
            . "   on qnt.order_number = btc.order_number and btc.status in ('" . $this->config->item('order_exchange_jpybtc') . "','" . $this->config->item('order_receiveby_btc') . "')"
            . " left outer join " . self::T_ORDER . " as order_usd"
            . "   on qnt.order_number = order_usd.order_number and order_usd.status = " . $this->config->item('order_pre_issue_token')

            . " left outer join " . self::T_ORDER . " as order_usdqnt"
            . "   on qnt.order_number = order_usdqnt.order_number and order_usdqnt.status = " . $this->config->item('order_issue_token')

            . " left outer join " . self::T_ORDER . " as usd_origin"
            . "   on qnt.order_number = usd_origin.order_number and usd_origin.status in ('" . $this->config->item('order_notify_btcaddr') . "','" . $this->config->item('order_notify_bankaccount') . "')"
            . " left outer join " . self::T_USER
            . "   on t_user.user_hash = qnt.client_uid"
            . " left outer join " . self::T_ACT
            . "   on t_activity.object = qnt.order_number "
            . " left outer join " . self::T_TOKEN . " as token"
            . "   on token.order_number = qnt.order_number "

            . " left outer join " . self::M_GENERAL . " as mg "
            . "   on qnt.pay_method = mg.code and mg.key = '07'  "
            . " WHERE"
            . " qnt.delete_flag = " . self::VALID
            . " and qnt.status = '" . $this->config->item('order_send_receipt') . "'"
            . " and qnt.order_number NOT IN (
                    SELECT order_number 
                    FROM `".SELF::T_ORDER."` 
                    WHERE `status` IN ('$ignore_statuses')
                )"
            . " and qnt.order_number = '" . $order_number. "'"
            . " GROUP BY"
            . " qnt.order_number";
        $result = $this->token_db->query($query);
        // echo $this->token_db->last_query();DIE;
        // log_message('debug', $this->token_db->last_query());
        return !empty($result) ? $result->result() : array();
    }

    /**
     * 注文進捗表示用のデータ取得
     * 
     * @param type $agent
     * @return type
     */
    public function select_by_ordernumber($order_number) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_ORDER);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->where('order_number', $order_number);
        $this->token_db->order_by('status', 'desc');
        $this->token_db->limit('1');
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->row();
    }
    /**
     * 注文進捗表示用のデータ取得
     * 
     * @param type $agent
     * @return type
     */
    public function select_by_ordernumberstatus($order_number, $status) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_ORDER);
        $this->token_db->where('order_number', $order_number);
        $this->token_db->where('status', $status);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->order_by('status', 'desc');
        $this->token_db->limit('1');
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return !empty($result) ? $result->row() : array();
    }

    public function select_by_userhash($userhash) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_ORDER);
        $this->token_db->where('client_uid', $userhash);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->order_by('status', 'desc');
        $this->token_db->limit('1');
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->row();
    }

    public function select_usd_by_userhash($userhash) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_ORDER);
        $this->token_db->where('client_uid', $userhash);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->order_by('order_number', 'desc');
        $this->token_db->order_by('status', 'desc');
        $this->token_db->limit('1');
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->row();
    }
    public function select_usd_by_ordernumber($order_number) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_ORDER);
        $this->token_db->where('order_number', $order_number);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->order_by('order_number', 'desc');
        $this->token_db->order_by('status', 'desc');
        $this->token_db->limit('1');
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->row();
    }

    public function select_usd_by_order_number($order_number) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_ORDER);
        //$this->token_db->where('client_uid', $userhash);
        $this->token_db->where('order_number', $order_number);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->order_by('order_number', 'desc');
        $this->token_db->order_by('status', 'desc');
        $this->token_db->limit('1');
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->row();
    }

    public function select_usd_by_userhash_and_order_number($userhash, $order_number) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_ORDER);
        $this->token_db->where('client_uid', $userhash);
        $this->token_db->where('order_number', $order_number);
        $this->token_db->where('delete_flag', self::VALID);
        $this->token_db->order_by('order_number', 'desc');
        $this->token_db->order_by('status', 'desc');
        $this->token_db->limit('1');
        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->row();
    }

    public function select_all($search=null) {
        $query = "select "
                . " t1.order_number "
                . " , m1.value as action "
                . " , t2.uid as agent_uid "
                . " , t2.family_name as agent_familyname "
                . " , t2.first_name as agent_firstname "
                . " , t3.uid as client_uid "
                . " , t3.family_name as client_familyname "
                . " , t3.first_name as client_firstname "
                . " , m2.value as pay_method "
                . " , m3.value as currency_unit "
                . " , t1.amount "
                . " , t1.exchange_rate "
                . " , t1.bank_name "
                . " , t1.account_name "
                . " , t1.receive_address "
                . " , t1.expiration_date "
                . " , t1.create_at "
                . " , m4.value as activity_code "
                . " , t20.create_at as notification_timestamp "
                . "from "
                . " " . self::T_ORDER . " as t1 "
                . " left outer join " . self::M_GENERAL . " as m1 "
                . "   on t1.status = m1.code and m1.key='05' "
                . " left outer join " . self::T_USER . " as t2 "
                . "   on t1.agent_uid = t2.user_hash "
                . " left outer join " . self::T_USER . " as t3 "
                . "   on t1.client_uid = t3.user_hash "
                . " left outer join " . self::M_GENERAL . " as m2 "
                . "   on t1.pay_method = m2.code and m2.key='07' "
                . " left outer join " . self::M_GENERAL . " as m3 "
                . "   on t1.currency_unit = m3.code and m3.key='08' "
                . " left outer join " . self::T_ACT . " as t4 "
                . "   on t1.order_number = t4.object and t4.activity_code = '32'"
                . " left outer join " . self::M_GENERAL . " as m4 "
                . "   on t4.activity_code = m4.code and m4.key='03' "
                . " left outer join ("
                . "    select t10.create_at, t10.order_number"
                . "    from t_order as t10"
                . "    where t10.status in ('12', '22')"
                . "  ) as t20 on t1.order_number = t20.order_number "
                . "where "
                . " t1.delete_flag=" . self::VALID." ";
        if (isset($search) && !empty($search)){
            if (!$this->IsNullOrEmptyString($search->orderid)) {
                $query = $query . " and t1.order_number = " . $this->token_db->escape($search->orderid);
            }
            if (!$this->IsNullOrEmptyString($search->status)) {
                $query = $query . " and t1.status = " . $this->token_db->escape($search->status);
            }
            if (!$this->IsNullOrEmptyString($search->agentid)) {
                $query = $query . " and t2.uid = " . $this->token_db->escape($search->agentid);
            }
            if (!$this->IsNullOrEmptyString($search->agent_name)) {
                if (strpos($search->agent_name, ' ') > 0) {
                    $pieces = explode(" ", $search->agent_name);
                    $query = $query . " and (t2.family_name like '%" . $pieces[0]
                        . "%' or t2.first_name like '%" .$pieces[1]."%')";
                } else {
                    $query = $query . " and (t2.family_name like '%" . $search->agent_name
                        . "%' or t2.first_name like '%" .$search->agent_name."%')";
                }

            }
            if (!$this->IsNullOrEmptyString($search->purchaseid)) {
                $query = $query . " and t3.uid = " . $this->token_db->escape($search->purchaseid);
            }
            if (!$this->IsNullOrEmptyString($search->client_name)) {
                if (strpos($search->client_name, ' ') > 0) {
                    $pieces = explode(" ", $search->client_name);
                    $query = $query . " and (t3.family_name like '%" . $pieces[0]
                        . "%' or t3.first_name like '%" . $pieces[1] ."%')";
                }
                else
                {
                    $query = $query . " and (t3.family_name like '%" . $search->client_name
                        . "%' or t3.first_name like '%" .$search->client_name."%')";
                }

            }
            if (!$this->IsNullOrEmptyString($search->type)) {
                $query = $query . " and t1.pay_method = " . $this->token_db->escape($search->type);
            }
            if(isset($search->create_from) && trim($search->create_from) !== "") {
                $query = $query . " and t1.create_at >= " . $this->token_db->escape($search->create_from);
            }
            if(isset($search->create_to) && trim($search->create_to) !== "") {
                $query = $query . " and t1.create_at <= DATE_ADD(" . $this->token_db->escape($search->create_to).", interval 1 day)";
            }
            if (null != $search->apply || true == $search->apply) {
                $query = $query . " and t1.status in ("
                    . $this->config->item('order_orderby_bank')
                    . "," . $this->config->item('order_orderby_btc') . ") ";
            }
            //$query = $query . "order by " . " t1.create_at desc";

            if (!$this->IsNullOrEmptyString($search->order_by)) {
                $query = $query . "order by ";
                if ($search->order_by == 'order_number') {
                    $query = $query . "t1." . $search->order_by . " " . $search->order_opt;
                    return $query;
                } else if ($search->order_by == 'status') {
                    $query = $query . "m1.value " . $search->order_opt;
                    return $query;
                } else if ($search->order_by == 'agent_uid') {
                    $query = $query . "t2.uid " . $search->order_opt;
                    return $query;
                } else if ($search->order_by == 'client_uid') {
                    $query = $query . "t3.uid " . $search->order_opt;
                    return $query;
                }return $query;
            }
            if (!$this->IsNullOrEmptyString($search->account_name)) {
                $query = $query . "and t1.account_name like '%$search->account_name%'";
            }
        } else {
            $query = $query . "order by "
                . " t1.create_at desc";
        }


        $result = $this->token_db->query($query);
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /**
     * 最新のステータスのみの一覧を取得
     * @return type
     */
    public function select_latest_list_search($status, $search) {
        $query = "select "
                . " t1.order_number "
                . " , t1.status "
                . " , t1.client_uid "
                . " , t2.uid "
                . " , t2.family_name"
                . " , t2.first_name"
                . " , m1.value as pay_method "
                . " , t1.account_name "
                . " , t1.amount "
                . " , t1.currency_unit "
                . " , t1.create_at "
                . "from "
                . " " . self::T_ORDER . " as t1 "
                . " left outer join " . self::T_USER . " as t2 "
                . "   on t1.client_uid = t2.user_hash "
                . " left outer join " . self::M_GENERAL . " as m1 "
                . "   on t1.pay_method = m1.code and m1.key = '07'  "
                . "where "
                . " t1.delete_flag = " . self::VALID;

        if(isset($search->order_number) && trim($search->order_number) !== "") {
            $query = $query . " and t1.order_number = " . $this->token_db->escape($search->order_number);
        }

        if(isset($search->user_id) && trim($search->user_id) !== "") {
            $query = $query . " and t2.uid = " . $this->token_db->escape($search->user_id) . "";
        }

        if(isset($search->bank_name) && trim($search->bank_name) !== "") {
            $query = $query . " and t1.bank_name like '%" . $this->token_db->escape_like_str($search->bank_name) . "%'";
        }

        if(isset($search->create_from) && trim($search->create_from) !== "") {
            $query = $query . " and t1.create_at >= " . $this->token_db->escape($search->create_from);
        }
        if(isset($search->create_to) && trim($search->create_to) !== "") {
            $query = $query . " and t1.create_at < DATE_ADD(" . $this->token_db->escape($search->create_to).", interval 1 day)";
        }

        $query = $query . " and t1.status in (select max(t3.status) from " 
                        . self::T_ORDER 
                        . " as t3 where t1.order_number = t3.order_number)"
                        . " and t1.status in (";

        for ($i = 0; $i < sizeof($status); $i++) {
            if (0 < $i) {
                $query = $query . ",";
            }
            $query = $query . $status[$i];
        }
        $query = $query . ")";

        $order_by_table = ['order_number'=>'t1', 'uid' => 't2', 'account_name' => 't1'];

        if(isset($search->order_by) && trim($search->order_by) !== "") {
            $order_by_str = $this->token_db->escape_str($order_by_table[$search->order_by].".".$search->order_by);
            $query = $query . " order by " . $order_by_str . " " . $this->token_db->escape_str($search->order_opt);
        }

        $result = $this->token_db->query($query);
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result->result();
    }


    /**
     * Check string is empty
     */
    // private function IsNullOrEmptyString($question){
    //     return (!isset($question) || trim($question)==='');
    // }


    public function select_filter_order($search) {
        $query = $this->build_query_order($search);

        $result = $this->token_db->query($query);
        //log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function get_SumAmount4Month($target_status = array()){
        $status_str = join(", ", $target_status);

        $query ="select client_uid
                     , sum(amount) as sumamount
                from
                    (
                        Select `order`.* 
                        from t_order `order` inner join
                            (
                                Select order_number
                                    , max(status) as max_status
                                from t_order
                                group by order_number
                            ) order_max
                            on order_max.order_number = `order`.order_number
                        where
                        -- Not complete order 
                        status in ($status_str)
                        and max_status < '30'
                
                        UNION ALL
                
                        Select *
                        from t_order
                        -- Complete order
                        where status = '30'
                    ) listorder
                where create_at >=(CURDATE()-INTERVAL 1 MONTH)
                group by client_uid ";
        $result = $this->token_db->query($query);
        $datalist = ($result ? $result->result() : array());
        $datamap = array();
        foreach ($datalist as $row){
            $datamap[$row->client_uid] = $row->sumamount;
        }

        return $datamap;
    }

    /**
     * BTCで注文日の取得
     * 
     * @param type $order_number
     * @return type
     */
    public function get_OrderDate4BTCOrder($order_number) {
		// order_orderby_btc: 21
        $orderBTCReceive = $this->select_by_ordernumberstatus($order_number, $this->config->item('order_orderby_btc'));
        return $orderBTCReceive->create_at;
    }

    /**
     * JPYで注文日の取得
     * 
     * @param type $order_number
     * @return type
     */
    public function get_OrderDate4JpyOrder($order_number) {
		// order_orderby_bank: 11
        $orderBTCReceive = $this->select_by_ordernumberstatus($order_number, $this->config->item('order_orderby_bank'));
        return $orderBTCReceive->create_at;
    }

    public function selectOrderForApprovedToken($arrOrderNumber) {
        if(!is_array($arrOrderNumber) || empty($arrOrderNumber)) {
            return array();
        }

        $order_notify_btcaddr = $this->config->item('order_notify_btcaddr');
        $order_receiveby_btc = $this->config->item('order_receiveby_btc');
        $order_send_receipt = $this->config->item('order_send_receipt');

        $currency_btc = $this->config->item('currency_btc');

        $tOrder = self::T_ORDER;

        $oNumberString = implode("', '", $arrOrderNumber);

        $btc_amount_sql = "
            SELECT amount 
            FROM `$tOrder`
            WHERE t1.order_number = order_number AND (status = $order_receiveby_btc OR currency_unit = $currency_btc) 
            LIMIT 1 
        ";

        $sql = "
            SELECT t1.order_number, t1.status, t1.agent_uid, t1.client_uid, 
                ($btc_amount_sql) AS `btc_amount`,
                (
                    SELECT receive_address 
                    FROM `$tOrder` 
                    WHERE t1.order_number = order_number AND status = $order_notify_btcaddr 
                    LIMIT 1 
                ) AS `btc_address`
            FROM `$tOrder` AS t1 
            WHERE t1.order_number IN ('$oNumberString') AND t1.status = $order_send_receipt 
        ";

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result_array() : array();

        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;

        return $result; 
    }

    /**
     * @param $search
     * @return string
     */
    public function build_query_order($search)
    {
        $query = "select "
            . " t1.order_number "
            . " , t1.status "
            . " , m1.value as action "
            . " , t2.uid as agent_uid "
            . " , t2.family_name as agent_familyname "
            . " , t2.first_name as agent_firstname "
            . " , t3.user_hash"
            . " , t3.uid as client_uid "
            . " , t3.family_name as client_familyname "
            . " , t3.first_name as client_firstname "
            . " , m2.value as pay_method "
            . " , m3.value as currency_unit "
            . " , t1.amount "
            . " , t1.exchange_rate "
            . " , t1.bank_name "
            . " , t1.account_name "
            . " , t1.receive_address "
            . " , t1.expiration_date "
            . " , t1.create_at "
            . " , m4.value as activity_code "
            . "from ";
        if (null != $search->apply || true == $search->apply) {
            $query = $query . "(select "
                . " t99.order_number "
                . " , max(t99.status) as status "
                . " , t99.agent_uid "
                . " , t99.client_uid "
                . " , t99.pay_method "
                . " , t99.currency_unit "
                . " , t99.amount "
                . " , t99.exchange_rate "
                . " , t99.bank_name "
                . " , t99.account_name "
                . " , t99.receive_address "
                . " , t99.expiration_date "
                . " , t99.create_at "
                . " , t99.delete_flag "
                . "from "
                . " t_order as t99 "
                . "group by "
                . " t99.order_number) as t1 ";
        } else {
            $query = $query . " " . self::T_ORDER . " as t1 ";
        }
        $query = $query . " left outer join " . self::M_GENERAL . " as m1 "
            . "   on t1.status = m1.code and m1.key='05' "
            . " left outer join " . self::T_USER . " as t2 "
            . "   on t1.agent_uid = t2.user_hash "
            . " left outer join " . self::T_USER . " as t3 "
            . "   on t1.client_uid = t3.user_hash "
            . " left outer join " . self::M_GENERAL . " as m2 "
            . "   on t1.pay_method = m2.code and m2.key='07' "
            . " left outer join " . self::M_GENERAL . " as m3 "
            . "   on t1.currency_unit = m3.code and m3.key='08' "
            . " left outer join " . self::T_ACT . " as t4 "
            . "   on t1.order_number = t4.object and t4.activity_code = '32' "
            . " left outer join " . self::M_GENERAL . " as m4 "
            . "   on t4.activity_code = m4.code and m4.key='03' "
            . "where "
            . " t1.delete_flag=" . self::VALID . " ";

        if (!$this->IsNullOrEmptyString($search->orderid)) {
            $query = $query . " and t1.order_number = " . $this->token_db->escape($search->orderid);
        }
        if (!$this->IsNullOrEmptyString($search->status)) {
            $query = $query . " and t1.status = " . $this->token_db->escape($search->status);
        }
        if (!$this->IsNullOrEmptyString($search->agentid)) {
            $query = $query . " and t2.uid = " . $this->token_db->escape($search->agentid);
        }

        if (!$this->IsNullOrEmptyString($search->agent_name)) {
            if (strpos($search->agent_name, ' ') > 0) {
                $pieces = explode(" ", $search->agent_name);
                $query = $query . " and (t2.family_name like '%" . $pieces[0]
                    . "%' or t2.first_name like '%" .$pieces[1]."%')";
            } else {
                $query = $query . " and (t2.family_name like '%" . $search->agent_name
                    . "%' or t2.first_name like '%" .$search->agent_name."%')";
            }

        }
        if (!$this->IsNullOrEmptyString($search->purchaseid)) {
            $query = $query . " and t3.uid = " . $this->token_db->escape($search->purchaseid);
        }
        if (!$this->IsNullOrEmptyString($search->client_name)) {
            if (strpos($search->client_name, ' ') > 0) {
                $pieces = explode(" ", $search->client_name);
                $query = $query . " and (t3.family_name like '%" . $pieces[0]
                    . "%' or t3.first_name like '%" . $pieces[1] ."%')";
            }
            else
            {
                $query = $query . " and (t3.family_name like '%" . $search->client_name
                    . "%' or t3.first_name like '%" .$search->client_name."%')";
            }

        }
        if (!$this->IsNullOrEmptyString($search->account_name)) {
            $query = $query . "and t1.account_name like '%$search->account_name%'";
        }
        if (!$this->IsNullOrEmptyString($search->type)) {
            $query = $query . " and t1.pay_method = " . $this->token_db->escape($search->type);
        }
        if(isset($search->create_from) && trim($search->create_from) !== "") {
            $query = $query . " and t1.create_at >= " . $this->token_db->escape($search->create_from);
        }
        if(isset($search->create_to) && trim($search->create_to) !== "") {
            $query = $query . " and t1.create_at < DATE_ADD(" . $this->token_db->escape($search->create_to).", interval 1 day)";
        }
        if (null != $search->apply || true == $search->apply) {
            $query = $query . " and t1.status in ("
                . $this->config->item('order_orderby_bank')
                . "," . $this->config->item('order_orderby_btc') . ") ";
        }
        //$query = $query . "order by " . " t1.create_at desc";

        if (!$this->IsNullOrEmptyString($search->order_by)) {
            $query = $query . "order by ";
            if ($search->order_by == 'order_number') {
                $query = $query . "t1." . $search->order_by . " " . $search->order_opt;
                return $query;
            } else if ($search->order_by == 'status') {
                $query = $query . "m1.value " . $search->order_opt;
                return $query;
            } else if ($search->order_by == 'agent_uid') {
                $query = $query . "t2.uid " . $search->order_opt;
                return $query;
            } else if ($search->order_by == 'client_uid') {
                $query = $query . "t3.uid " . $search->order_opt;
                return $query;
            }return $query;
        }
        //var_dump($query); die;
        return $query;
    }

    /**
     * select & search for receive-issue-token
     */
    public function getData4_ReceiveIssueToken($status, $search) {
        $this->token_db->SELECT("t1.order_number "
                
                . ", t1.exchange_rate"
                . ", t1.pay_method"
                . ", t1.amount"
				. ", t1.memo"
                . ", t1.create_at"
                . ", t1.status"
                . ", t2.uid as client_uid"
                . ", t2.first_name as client_firstname"
                . ", t2.family_name as client_familyname"
                . ", t3.uid as agent_uid "
                . ", t3.first_name as agent_firstname"
                . ", t3.family_name as agent_familyname"
                . ", t3.user_hash"
                );
        $this->token_db->from(self::T_TOKENAPPROVED . " as t0");
		$this->token_db->join(self::T_ORDER . " as t1", "t1.order_number = t0.order_number", "INNER");
        $this->token_db->join(self::T_USER . " as t2", "t1.client_uid = t2.user_hash", "LEFT");
        $this->token_db->join(self::T_USER . " as t3", "t2.agent_uid = t3.user_hash ", "LEFT");
       
        $this->token_db->where('t1.delete_flag', self::VALID);
        $statusCondition = "";
        for ($i = 0; $i < sizeof($status); $i++) {
            if (0 < $i) {
                $statusCondition = $statusCondition . "','";
            }
            $statusCondition = $statusCondition . $status[$i];
        }
        if(""!=$statusCondition){
            $this->token_db->where('t1.status IN (\'' . $statusCondition . '\')');  
        } 
        if (!$this->IsNullOrEmptyString($search->orderid)) {           
            $this->token_db->where('t1.order_number =\'' . $search->orderid . '\''); 
        }
        if (!$this->IsNullOrEmptyString($search->purchaseid)) {            
            $this->token_db->where('t2.uid = \'' . $search->purchaseid . '\''); 
        }
       
        if (!$this->IsNullOrEmptyString($search->client_familyName)) {
            $this->token_db->where('(t2.family_name like \'%' . $search->client_familyName . '%\')'); 
        }
        if (!$this->IsNullOrEmptyString($search->client_firstName)) {
            $this->token_db->where('(t2.first_name like \'%' . $search->client_firstName . '%\')'); 
        }

        if (!$this->IsNullOrEmptyString($search->agentid)) {
            $this->token_db->where('t3.uid = \'' . $search->agentid . '\''); 
        }
        
        if (!$this->IsNullOrEmptyString($search->agent_familyName)) {
            $this->token_db->where('(t3.family_name like \'%' . $search->agent_familyName . '%\')'); 
        }
        if (!$this->IsNullOrEmptyString($search->agent_firstName)) {
            $this->token_db->where('(t3.first_name like \'%' . $search->agent_firstName . '%\')'); 
        }
        if(isset($search->create_from) && trim($search->create_from) !== "" && 
            isset($search->create_to) && trim($search->create_to) !== "") {            
            // $this->token_db->where('t1.create_at BETWEEN \'' . $search->create_from . '\' AND DATE_ADD(\'' . $search->create_to . '\', interval 1 day)'); 
            $this->token_db->where('t1.create_at >= \'' . $search->create_from . '\' AND t1.create_at < DATE_ADD(\'' . $search->create_to . '\', interval 1 day)'); 
        }else{
            if(isset($search->create_from) && trim($search->create_from) !== "") {            
            $this->token_db->where('t1.create_at >= \'' . $search->create_from . '\''); 
            }
            if(isset($search->create_to) && trim($search->create_to) !== "") {            
                $this->token_db->where('t1.create_at < DATE_ADD(\'' . $search->create_to . '\', interval 1 day)'); 
            }
        }
        //$order_by_table = ['order_number'=>'t1', 'uid' => 't2', 'uid' => 't3'];
        if(isset($search->order_by) && trim($search->order_by) !== "") {
             $this->token_db->order_by($search->order_by, $search->order_opt);
        }

        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());        
        //echo $this->token_db->last_query();//die;
        return $result;
    }

    public function select_for_reOrderConfirm() {
        $status_bank = $this->config->item('order_orderby_bank');
        $status_btc = $this->config->item('order_orderby_btc');
        $query = "select "
            . " t1.order_number "
            . " , t1.status "
            . " , t1.client_uid "
            . " , t2.uid "
            . " , t2.family_name"
            . " , t2.first_name"
            . " , t2.first_name"
            . " , t2.status as user_status"
            . " , t1.agent_uid as agent_hash"
            . " , t3.uid as agent_uid"
            . " , t3.family_name as agent_family_name"
            . " , t3.first_name as agent_first_name"
            . " , m1.value as pay_method "
            . " , t1.account_name "
            . " , t1.amount "
            . " , t1.currency_unit "
            . " , t1.create_at "
            . " , t1.expiration_date "
            . "from "
            . " " . self::T_ORDER . " as t1 "
            . " left outer join " . self::T_USER . " as t2 "
            . "   on t1.client_uid = t2.user_hash "
            . " left outer join " . self::T_USER . " as t3 "
            . "   on t1.agent_uid = t3.user_hash "
            . " left outer join " . self::M_GENERAL . " as m1 "
            . "   on t1.pay_method = m1.code and m1.key = '07'  "
            . "where "
            . " t1.delete_flag = " . self::VALID
            . " and t2.status = '" . $this->config->item('act_approved') . "' "
            . " and t1.status in (select max(t3.status) from " . self::T_ORDER . " as t3 where t1.order_number = t3.order_number)"
            . " and ( t1.status = '" . $status_bank . "' or (t1.status = '" . $status_btc . "' and t1.rsv_char_1 is null))";

        $query = $query . " order by"
            . " t1.order_number asc";
        $result = $this->token_db->query($query);
        // log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return !empty($result) ? $result->result() : array();
    }

    public function update_order($ordernumber, $hash, $data) {
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $this->token_db->set($data);
        $this->token_db->set('update_by', $hash);
        $this->token_db->set('update_at', $now);
        $this->token_db->where('order_number', $ordernumber);
        $this->token_db->where('delete_flag', self::VALID);
        $result = $this->token_db->update(self::T_ORDER);
        // log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function selectOrderForAutoBanking($arrOrderNumber) {
        if(!is_array($arrOrderNumber) || empty($arrOrderNumber)) {
            return array();
        }

        $order_receiveby_bank = $this->config->item('order_receiveby_bank');

        $currency_jpy = $this->config->item('currency_jpy');

        $tOrder = self::T_ORDER;

        $oNumberString = implode("', '", $arrOrderNumber);

        $sql = "
            SELECT t1.*
            FROM `$tOrder` AS t1 
            WHERE t1.order_number IN ('$oNumberString') AND (t1.status = $order_receiveby_bank  OR t1.currency_unit = $currency_jpy)
        ";

        $result = $this->token_db->query($sql);
        $result = !empty($result) ? $result->result() : array();

        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;

        return $result; 
    }

    public function select_first_orders() {
        $query = "select client_uid, min(order_number) as min_order from " . self::T_ORDER
            . " group by client_uid ";
        $result = $this->token_db->query($query)->result();
        $rs_map = array();
        foreach( $result as $row){
            $rs_map[$row->client_uid] = $row->min_order;
        }
        return $rs_map;
    }

    /**
     * Data for export csv file receive-issue-token
     */
    public function exportCSV_RIT($status, $search) {
        $this->token_db->SELECT(
                "t1.order_number as '注文番号'"
                . ", t2.uid as '交換者 UID'" // as client_uid
                
                . ", CONCAT(t2.family_name" 
                . ", ' '"
                . ", t2.first_name) as '交換者 名称' "//clientname
                
                . ", t3.uid as '代理店 UID' "

                . ", CONCAT(t3.family_name"
                . ", ' '"
                . ", t3.first_name) as '代理店 名称'" //agent_name

                . ", t1.create_at as '受領書 発行日'"
                . ", FORMAT(t1.amount, 8 ) '受領金額 (BTC)'"
                . ", t1.exchange_rate as 'USD/BTC レート' "
                . ", t1.memo as 'QNT/USD レート'"
                . ", t1.amount as 'トークン量 (QNT)'"

                // . ", if((t1.status = $status[0] or t1.status = $status[1]), t1.amount , '' ) as amount24"
                // . ", if(t1.status = $status[2], t1.exchange_rate , '' )  as exchange_rate30"
                // . ", if(t1.status = $status[3], t1.memo , '' ) as memo31"
                // . ", t1.amount "

                // . ", t1.exchange_rate as ''"
                // . ", t1.pay_method as ''"
                // . ", t1.amount as ''"   
                // . ", t1.status as ''"
                // . ", t3.user_hash"
                );
        $this->token_db->from(self::T_TOKENAPPROVED . " as t0");
        $this->token_db->join(self::T_ORDER . " as t1", "t1.order_number = t0.order_number", "INNER");
        $this->token_db->join(self::T_USER . " as t2", "t1.client_uid = t2.user_hash", "LEFT");
        $this->token_db->join(self::T_USER . " as t3", "t2.agent_uid = t3.user_hash ", "LEFT");
       
        $this->token_db->where('t1.delete_flag', self::VALID);
        $statusCondition = "";
        for ($i = 0; $i < sizeof($status); $i++) {
            if (0 < $i) {
                $statusCondition = $statusCondition . "','";
            }
            $statusCondition = $statusCondition . $status[$i];
        }
        if(""!=$statusCondition){
            $this->token_db->where('t1.status IN (\'' . $statusCondition . '\')');  
        } 
        if (!$this->IsNullOrEmptyString($search->orderid)) {           
            $this->token_db->where('t1.order_number =\'' . $search->orderid . '\''); 
        }
        if (!$this->IsNullOrEmptyString($search->purchaseid)) {            
            $this->token_db->where('t2.uid = \'' . $search->purchaseid . '\''); 
        }
       
        if (!$this->IsNullOrEmptyString($search->client_familyName)) {
            $this->token_db->where('(t2.family_name like \'%' . $search->client_familyName . '%\')'); 
        }
        if (!$this->IsNullOrEmptyString($search->client_firstName)) {
            $this->token_db->where('(t2.first_name like \'%' . $search->client_firstName . '%\')'); 
        }

        if (!$this->IsNullOrEmptyString($search->agentid)) {
            $this->token_db->where('t3.uid = \'' . $search->agentid . '\''); 
        }
        
        if (!$this->IsNullOrEmptyString($search->agent_familyName)) {
            $this->token_db->where('(t3.family_name like \'%' . $search->agent_familyName . '%\')'); 
        }
        if (!$this->IsNullOrEmptyString($search->agent_firstName)) {
            $this->token_db->where('(t3.first_name like \'%' . $search->agent_firstName . '%\')'); 
        }
        if(isset($search->create_from) && trim($search->create_from) !== "" && 
            isset($search->create_to) && trim($search->create_to) !== "") {
            $this->token_db->where('t1.create_at >= \'' . $search->create_from . '\' AND t1.create_at < DATE_ADD(\'' . $search->create_to . '\', interval 1 day)'); 
        }else{
            if(isset($search->create_from) && trim($search->create_from) !== "") {            
            $this->token_db->where('t1.create_at >= \'' . $search->create_from . '\''); 
            }
            if(isset($search->create_to) && trim($search->create_to) !== "") {            
                $this->token_db->where('t1.create_at < DATE_ADD(\'' . $search->create_to . '\', interval 1 day)'); 
            }
        }
        //$order_by_table = ['order_number'=>'t1', 'uid' => 't2', 'uid' => 't3'];
        if(isset($search->order_by) && trim($search->order_by) !== "") {
             $this->token_db->order_by($search->order_by, $search->order_opt);
        }
        $this->token_db->group_by("t1.order_number");

        $result = $this->token_db->get();
        //log_message('debug', $this->token_db->last_query());        
        //echo $this->token_db->last_query();die;
        return $result;
    }

    public function delete_order($order_number, $status) {

        $tOrder = self::T_ORDER;

        $sql = "DELETE FROM `$tOrder` WHERE `order_number` = '$order_number' AND `status` = '$status'";

        $result = $this->token_db->query($sql);

        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;

        return $result; 
    }

    /*
    * A function get order btc exists btc-address with max-satatus is 22
    */
    public function getOrderBtcHasBtcAddr(){ 
        log_message('debug', 'Order_model/getOrderBtcHasBtcAddr');
        $status_btc = $this->config->item('order_notify_btcaddr');        
        $query = "SELECT t1.* "
            . "FROM " . self::T_ORDER . " as t1 "       
            . " INNER JOIN (SELECT order_number, max(status) as max_status"
                            . " FROM " . self::T_ORDER 
                            . " GROUP BY order_number "
                            ." HAVING max_status = '" . $status_btc . "') as t2 
                        ON t2.order_number = t1.order_number 
                            AND t1.status = '" . $status_btc . "'" 
            . "WHERE t1.delete_flag = " . self::VALID    
            . " AND t1.expiration_date < now() "
            . "ORDER  BY t1.order_number asc";
        $result = $this->token_db->query($query); 
        //echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return !empty($result) ? $result->result() : array();
    }    
}
