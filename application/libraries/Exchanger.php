<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Exchanger {

    private $CI = null;

    /*const PERCENT20 = 0.2;
    const PERCENT15 = 0.15;
    const PERCENT10 = 0.1;
    const PERCENT5 = 0.05;*/

    public function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->model('Order_model', 'order');
        $this->CI->load->model('Activity_model', 'act');
        $this->CI->load->model('Token_model', 'token');
        $this->CI->load->model('User_model', 'user');
        $this->CI->load->model('Commission_model', 'commission');
        $this->CI->load->model('Mst_UsdQnt_Rate_model', 'mst_usdqnt_rate');
        $this->CI->load->library('interbank');
        $this->CI->load->library('email');
        date_default_timezone_set("Asia/Tokyo");
    }

    /**
     * USDからJPYを計算する
     * @param type $amount
     * @return type
     */
    public function getJpyAmount($amount) {
        return $amount / $this->CI->interbank->getUsdJpyRate();
    }

    /**
     * JPYからBTCを計算する
     * @param type $amount
     * @return type
     */
    public function getBtcAmount($amount) {
        return $amount / $this->CI->interbank->getJpyBtcRate();
    }

    /**
     * BTCからトークンを計算する
     * @param type $order_number
     * @return type
     */
    public function calc_token($order_number, $order_date) {
        log_message('debug', 'Exchanger/calc_token');
        // トークン計算
        $usdbtc_rate = $this->CI->interbank->getUsdBtcRate();
        if (0 == $usdbtc_rate) {
            return;
        }
        $order_btc = $this->CI->order->select_by_ordernumber($order_number);

        $usd = $order_btc->amount * $usdbtc_rate;
        //$token = $usd / doubleval($this->CI->config->item('rate_usdtoken'));
		
		// 注文日でトークンのレートを取得する
		$m_UsdQnt_Rate = $this->CI->mst_usdqnt_rate->select_UsdQnt_Rate($order_date);
		$token = $usd / $m_UsdQnt_Rate->rate_value;
		
		//log_message('debug', 'rate_value: ' . $m_UsdQnt_Rate->rate_value);
		//log_message('debug', 'token: ' . $token);
		
        // トークンをDBに保存する
        $tdata = array('token_code' => md5("QNT" . $order_number),
            'quantity' => $token,
            'order_number' => $order_number
        );
        $tokencode = $this->CI->token->insert_token($tdata);

        // Insert order to keep track BTC to USD exchange rate
        $odata = array(
            'order_number' => $order_btc->order_number,
            // order_pre_issue_token: 30
            'status' => $this->CI->config->item('order_pre_issue_token'),
            'agent_uid' => $order_btc->agent_uid,
            'client_uid' => $order_btc->client_uid,
            'pay_method' => $order_btc->pay_method,
            'received' => $order_btc->received,
            'currency_unit' => $this->CI->config->item('currency_usd'),
            'amount' => $usd,
            'exchange_rate' => $usdbtc_rate,
            'expiration_date' => $order_btc->expiration_date
        );
        $this->CI->order->insert_order($odata);

        // トークン発行の完了を登録する
        $odata = array(
            'order_number' => $order_btc->order_number,
			// order_issue_token: 31
            'status' => $this->CI->config->item('order_issue_token'),
            'agent_uid' => $order_btc->agent_uid,
            'client_uid' => $order_btc->client_uid,
            'pay_method' => $order_btc->pay_method,
            'received' => $order_btc->received,
            'currency_unit' => $this->CI->config->item('currency_qnt'),
            'amount' => $token,
            'exchange_rate' => (doubleval($usdbtc_rate) / $m_UsdQnt_Rate->rate_value),
            'expiration_date' => $order_btc->expiration_date,
            'memo' => $m_UsdQnt_Rate->rate_value
        );
        $this->CI->order->insert_order($odata);
    }

    /**
     * 代理店のコミッションを計算する
     * @param type $order_number
     */
    public function calc_commission($order_number) {

        // オーダー
        // get BTC order
        $order = $this->CI->order->select_by_ordernumberstatus($order_number, $this->CI->config->item('order_receiveby_btc'));
        if(empty($order)) {
            $order = $this->CI->order->select_by_ordernumberstatus($order_number, $this->CI->config->item('order_exchange_jpybtc'));
        }

        if(empty($order)) {
            log_message('debug', 'calc_commission: order_number not found: ' . $order_number);
            return false;
        }

        $agent_hash = $order->agent_uid;    // parent user_hash of client

        $role_agent_commission = $this->CI->config->item('role_agent_commission');
        $role_agent_arr = array_keys($role_agent_commission);

        // prepare data
        $highest_comm_role_agent = $role_agent_arr[0];
        $total_children_comm_rate = 0;
        $agent_arr = array();
        do {
            $agent = $this->CI->user->get_user_by_userhash($agent_hash);
            if(empty($agent)) {
                break;
            }
            if(!in_array($agent->role, $role_agent_arr)) {
                log_message('debug', 'calc_commission: invalid agent_role: ' . $agent->role);
                return false;
            }
            $agent->commission_rate = (float) $role_agent_commission[$agent->role] - $total_children_comm_rate;
            if($agent->commission_rate < 0) {
                log_message('debug', 'calc_commission: agent commission rate is nagative. Agent hash: ' . $agent->user_hash);
                return false;
            }
            $agent->commission = $order->amount * $agent->commission_rate / 100;
            $total_children_comm_rate += $agent->commission_rate;
            $agent_hash = $agent->agent_uid;    // parent user_hash of agent
            $agent_arr[] = $agent;
        } while($agent->role != $highest_comm_role_agent);

        if(empty($agent_arr)) {
            log_message('debug', 'calc_commission: no agent found');
            return false;
        }

        // insert commission data
        foreach($agent_arr as $agent) {
            $date = new DateTime();
            $now = date($this->CI->config->item('db_timestamp_format'), $date->getTimestamp());
            $data = array(
                'quantity' => $agent->commission,
                'order_number' => $order_number,
                'user_hash' => $agent->user_hash,
                'btc_address' => $agent->btc_address,
                'approve_flag' => 0,
                'create_by' => $agent->uid,
                'create_at' => $now
            );
            $this->CI->commission->insert_comission($data);
        }

        return true;
    }

    /**
     * 代理店のコミッションを計算する
     * @param type $order_number
     */
    /*public function calc_commission($order_number) {

        // オーダー
        // get BTC order
        $order = $this->CI->order->select_by_ordernumberstatus($order_number, $this->CI->config->item('order_receiveby_btc'));
        if(empty($order)) {
            $order = $this->CI->order->select_by_ordernumberstatus($order_number, $this->CI->config->item('order_exchange_jpybtc'));
        }

        // 代理店
        $agent = $this->CI->user->get_user_by_userhash($order->agent_uid);

        // コミッション計算
        $commission = 0;
        switch ($agent->role) {
            case $this->CI->config->item('role_agent20'):
                $commission = doubleval($order->amount * self::PERCENT20);
                break;
            case $this->CI->config->item('role_agent15'):
                $this->calc_agent_commission($agent->agent_uid, $agent->role, $order_number, $order->amount);
                $commission = doubleval($order->amount * self::PERCENT15);
                break;
            case $this->CI->config->item('role_agent10'):
                $this->calc_agent_commission($agent->agent_uid, $agent->role, $order_number, $order->amount);
                $commission = doubleval($order->amount * self::PERCENT10);
                break;
            case $this->CI->config->item('role_agent5'):
                $this->calc_agent_commission($agent->agent_uid, $agent->role, $order_number, $order->amount);
                $commission = doubleval($order->amount * self::PERCENT5);
                break;
            default:
        }

        $date = new DateTime();
		$now = date($this->CI->config->item('db_timestamp_format'), $date->getTimestamp());
        $data = array(
            'quantity' => $commission,
            'order_number' => $order_number,
            'user_hash' => $order->agent_uid,
            'btc_address' => $agent->btc_address,
        	'create_by' => $agent->uid,
        	'create_at' => $now
        );
        $this->CI->commission->insert_comission($data);
    }*/

    /**
     * 親代理店のコミッションを計算する
     * @param type $userhash
     * @param type $child_rank
     * @param type $order_number
     * @param type $buy_amount
     */
    /*private function calc_agent_commission($userhash, $child_rank, $order_number, $buy_amount) {
        // 代理店
        $agent = $this->CI->user->get_user_by_userhash($userhash);

        // コミッション計算
        $commission = 0;
        switch ($agent->role) {
            case $this->CI->config->item('role_agent20'):
                $commission = doubleval($buy_amount * self::PERCENT5);
                if ($this->CI->config->item('role_agent5') == $child_rank) {
                    $commission = doubleval($buy_amount * self::PERCENT15);
                } else if ($this->CI->config->item('role_agent10') == $child_rank) {
                    $commission = doubleval($buy_amount * self::PERCENT10);
                } else {
                    $commission = doubleval($buy_amount * self::PERCENT5);
                }
                break;
            case $this->CI->config->item('role_agent15'):
                $this->calc_agent_commission($agent->agent_uid, $agent->role, $order_number, $buy_amount);
                if ($this->CI->config->item('role_agent5') == $child_rank) {
                    $commission = doubleval($buy_amount * self::PERCENT10);
                } else {
                    $commission = doubleval($buy_amount * self::PERCENT5);
                }
                break;
            case $this->CI->config->item('role_agent10'):
                $this->calc_agent_commission($agent->agent_uid, $agent->role, $order_number, $buy_amount);
                $commission = doubleval($buy_amount * self::PERCENT5);
                break;
            default:
        }

        $date = new DateTime();
		$now = date($this->CI->config->item('db_timestamp_format'), $date->getTimestamp());
        $data = array(
            'quantity' => $commission,
            'order_number' => $order_number,
            'user_hash' => $userhash,
            'btc_address' => $agent->btc_address,
        	'create_by' => $agent->uid,
        	'create_at' => $now
        );
        $this->CI->commission->insert_comission($data);
    }*/
}
