<?php

class MY_Controller extends CI_Controller {

    // define this variable here so that from view can access (call) to a method in controller
    public $CI = NULL;

    protected $defaultUser = 'QTS_System';

//    private $token_db;
    public function __construct() {
        parent::__construct();
        $this->CI = & get_instance();

        // Make sure that options has been parsed before manipulate other codes.
        $this->parseOptionsToConfig();

        $this->load->model('Personal_model', 'personal');
        $this->load->model('User_model', 'user');
        $this->load->model('Login_model', 'login');
        $this->load->model('Activity_model', 'act');
        date_default_timezone_set("Asia/Tokyo");
//        $this->token_db = $this->user->getdb();

        // Sort role_agent_commission to make sure that the commmission percentage is sorted DESC, but still keep the index association
        $role_agent_commission = $this->config->item('role_agent_commission');
        arsort($role_agent_commission);
        $this->config->set_item('role_agent_commission', $role_agent_commission);


    }

//    public function _remap($method, $args) {
//        $this->rollback = false;
//        try {
//            $this->token_db->trans_start();
//            call_user_func_array(array($this, $method), $args);
//            log_message('debug',"call_user_func_array $method");
//            if ($this->token_db->trans_status() === FALSE) {
//                throw new Exception("TRANSACTION ERROR on $method");
//            }
//            $this->token_db->trans_commit();
//
//        } catch (Exception $e) {
//            $this->token_db->trans_rollback();
//            log_message('error', $e->getMessage());
//        } finally {
//            $this->token_db->trans_complete();
//        }
//    }
//

    protected function parseOptionsToConfig() {
        $this->load->model('Option_model', 'option');
        $option_arr = $this->option->getPairs($this->getForeignId());
        // parse back to the old way of config for wallet
        $network = $option_arr['network'];
        $option_arr['testnet_mode'] = $network === 'testnet';
        foreach($option_arr[$network] as $key => $value) {
            $option_arr[$key] = $value;
        }
        // convert number of days to timestamp
        $num_days_arr = array('order_expiration_time');
        foreach($num_days_arr as $key) {
            if(isset($option_arr[$key])) {
                $option_arr[$key] *= 86400;
            }
        }
        //copy old config
        $config = array();
        foreach($this->config->config as $key => $value) {
            $config[$key] = $value;
        }
        // override config from option_arr
        foreach($option_arr as $key => $value) {
            $config[$key] = $value;
        }
        // set config
        $this->config->config = $config;
        // NOTE: do NOT use $this->config->set_item(key, value) because it ONLY effects on the key that has already been defined in config.php file
    }

    protected function setLoginData($data) {
        $this->session->set_userdata($this->defaultUser, $data);
    }

    public function getLoginData() {
        return $this->session->userdata($this->defaultUser);
    }

    protected function clearLoginData() {
        $this->session->unset_userdata($this->defaultUser);
    }

    public function safe_redirect($uri = '', $method = 'auto', $code = NULL){
//        try{
//            if ($this->token_db->trans_status() === FALSE){
//                throw new Exception("TRANSACTION ERROR, redirect to $uri");
//            }
//            $this->token_db->trans_commit();
//        } catch (Exception $e) {
//            $this->token_db->trans_rollback();
//            log_message('error', $e->getMessage());
//        } finally {
//            $this->token_db->trans_complete();
//        }

        redirect($uri, $method, $code);
    }

    public function checkAutoBanking() {
        $c = $this->router->fetch_class();
        $m = $this->router->fetch_method();
        if($this->config->item('enable_banking') !== true) {
            $this->safe_redirect("$c/home");
            exit;
        }
    }
    /**
     * ログインを行う
     * @param type $email
     * @param type $pass
     * @param type $role
     * @return boolean
     */
    protected function loginSession($email, $pass, $role) {
        // アカウント有無判定
        $user_hash = $this->user->is_user($email, $pass, $role);
        if (null == $user_hash) {
            return false;
        }
        // セッション情報を作成する
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
        $session_id = md5($email . $now . $this->defaultUser);

        // Get user for uid info
        $user = $this->user->get_user_by_userhash($user_hash);

        // セッションをセットする
        $data = array(
            'session_id' => $session_id,
            'user_hash' => $user_hash,
            'uid' => $user->uid,
            'role' => $user->role,
            'role_text' => $this->defaultUser
        );
        $this->setLoginData($data);

        // DBに保存する
        $data = array(
            'session_id' => $session_id,
            'user_hash' => $user_hash,
            'create_at' => $now
        );
        $result = $this->login->insert_session($data);

        $adata = array(
            'user_hash' => $user_hash,
            'activity_code' => $this->config->item('act_login'),
            'create_by' => $user->uid,
        	'create_at' => $now
        );
        $this->act->insert_activity($adata);

        return $result;
    }

    /**
     * ログイン (セッション) が有効かを判定する
     * @return boolean
     */
    protected function is_login() {
        // クッキーのセッション有無を判定する
        $session_data = $this->getLoginData();
        $session_id = isset($session_data['session_id']) ? $session_data['session_id'] : null;
        if (null == $session_id || '' == $session_id) {
            $this->logoutSession();
            return false;
        }

        // DBのログインレコード有無を判定する
        $result = $this->login->get_session($session_id);
        if (null == $result) {
            $this->logoutSession();
            return false;
        }

        // セッションが有効期限内かを判断する
        $expiration_datetime = strtotime($result->last_access) + $this->config->item('session_expiration_time');
        $date = new DateTime();
        $now = $date->getTimestamp();
        if ($expiration_datetime < $now) {
            $this->logoutSession();
            return false;
        }

        $uri_segments = $this->uri->segment_array();
        $controller = strtolower($uri_segments[1]);
        $method = strtolower($uri_segments[2]);
        $accessRoles = @$this->config->item('access_roles')[$controller][$method];
        if (isset($accessRoles) && !empty($accessRoles)){
            if (!isset($session_data['role']) || !in_array($session_data['role'], $accessRoles)) {
                // log user data when access forbidden method
                if (isset($session_data['role'])){
                    $uid = $session_data['uid'];
                    $role = $session_data['role'];
                    $user = $this->user->get_userinfo_by_uid($uid);
                    $username = $user->family_name . " " . $user->first_name;
                    log_message('error', "request $controller/$method error : uid $uid role $role, username $username");
                } else {
                    log_message('error', "request $controller/$method error : No session" );
                }
                show_error("permission denied", 403);
            }
        }

        // ログインが有効である場合は最終アクセス時刻を更新する
        $data = array(
            'update_at' => date($this->config->item('session_timestamp_format'), $now)
        );
        return $this->login->update_session($session_id, $data);
    }

    /**
     * ログイン (セッション) を切断する
     */
    protected function logoutSession() {
        // DBからセッションレコードを削除する
        $session_data = $this->getLoginData();

        $session_id = isset($session_data['session_id']) ? $session_data['session_id'] : null;
        if (null == $session_id) {
            return false;
        }
        $this->login->delete_session($session_id);

        $user_hash = $session_data['user_hash'];
        $user = $this->user->get_user_by_userhash($user_hash);
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
        $adata = array(
            'user_hash' => $user_hash,
            'activity_code' => $this->config->item('act_logout'),
        	'create_by' => $user->uid,
        	'create_at' => $now
        );
        $this->act->insert_activity($adata);

        // セッションを削除する
        $data = array(
            'session_id' => "",
            'user_hash' => ""
        );
        $this->clearLoginData();
        return true;
    }

    /**
     * パスワードの問い合わせに答える
     * @param type $email
     */
    public function ask_userpassword($email) {
        $response = $this->user->ask_password($email);
        if (null == $response) {
            return null;
        }

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
        
        // メール送信
        $reset_password_url = base_url('Operator/resetPasswd/' . $response->password);
        $replace_token = array(
            $response->family_name, $response->first_name, 
            $reset_password_url,
            $this->getEmailSignature()
        );
        $email_template = $this->parseEmailTemplate('mail_respond_password', $replace_token);

        $emailData = array(
            'to' => $response->email,
            'subject' => $email_template['subject'],
            'message' => $email_template['message'],
            'object' => $response->uid,
            'memo' => 'Operator ask password',
            'create_by' => $response->uid,
            'create_at' => $now
        );
        $this->load->model('EmailQueue_model', 'emailQueue');
        $this->emailQueue->insert_data(array($emailData));

        $adata = array(
            'user_hash' => $response->user_hash,
            'activity_code' => $this->config->item('act_ask_password'),
            'create_by' => $response->uid,
            'create_at' => $now
        );
        $this->act->insert_activity($adata);
    }
    
    public function reset_userpassword($email, $password) {
        if (!($this->user->reset_password($email, $password))) {
            return;
        }

        $role = array($this->config->item('role_sysadmin'), $this->config->item('role_operator'));
        $role = array_merge($role, array_keys($this->config->item('role_agent_commission')));
        $user = $this->user->get_user_by_email_role($email, $role);
        $adata = array(
			'user_hash' => $user->user_hash,
            'activity_code' => $this->config->item('act_reset_password'),
        	'create_by' => $user->uid,
        	'create_at' => $now
        );
        $this->act->insert_activity($adata);
    }

    /**
     * セッション情報に格納されているデータを取得する
     * 
     * @param type $key
     * @return type
     */
    protected function getSessionValue($key) {
        $session_data = $this->getLoginData();
        return (isset($session_data[$key])) ? $session_data[$key] : null;
    }

    public function delete_expired_session(){
        // delete sessions were not updated in 8 days
        $this->login->delete_expired();
    }

    public function getSessionRole() {
        return $this->getSessionValue('role');
    }

    // check csv role 01, 02 can export
    public function check_export_csv_permission(){
        $role = $this->getSessionRole();
        $role_export = $this->config->item('access_roles')['operator']['outputcsv'];
        return in_array($role, $role_export);
    }

    public function isAdmin() {
        return $this->getSessionRole() == $this->config->item('role_sysadmin');
    }

    protected function getForeignId() {
        return 1;   // default id of admin
    }

    public function getSupportEmailTokens($key) {        
        $tokens = array();
        switch ($key) {
            case 'mail_signature':
                // Email tokens for mail_signature   
                $tokens[] = '{site_domain}';
                $tokens[] = '{support_email}';  
                break;
            
            case 'mail_accept_order':
                // Email tokens for mail_accept_order_message                
                $tokens[] = '{client_family_name}';
                $tokens[] = '{client_first_name}';
                $tokens[] = '{mail_signature}'; 
                break;
            case 'mail_approved_client':
                // Email tokens for mail_approved_client_message                
                $tokens[] = '{client_family_name}';
                $tokens[] = '{client_first_name}';
                $tokens[] = '{mail_signature}';
                break;
            case 'mail_notify_bankaccount':
                // Email tokens for mail_notify_bankaccount_message
                $tokens[] = '{client_family_name}';
                $tokens[] = '{client_first_name}';
                $tokens[] = '{order_number}';
                $tokens[] = '{order_amount}';
                $tokens[] = '{service_fee}';
                $tokens[] = '{entry_amount}';
                $tokens[] = '{trade_law_url}';
                $tokens[] = '{mail_signature}';
                break;
            case 'mail_notify_btcaddr':
                // Email tokens for mail_notify_btcaddr_message
                
                $tokens[] = '{client_family_name}';
                $tokens[] = '{client_first_name}';
                $tokens[] = '{view_btc_address_url}';
                $tokens[] = '{mail_signature}';
                break;
            case 'mail_notify_receivedbtc':
                // Email tokens for mail_notify_receivedbtc_message              
                $tokens[] = '{client_family_name}';
                $tokens[] = '{client_first_name}';
                $tokens[] = '{oder_number}';
                $tokens[] = '{btc_received_amount}';
                $tokens[] = '{btc_address}';
                $tokens[] = '{mail_signature}';
                break;
            case 'mail_notify_receivedbtc2nd':
                // Email tokens for mail_notify_receivedbtc2nd_message                
                $tokens[] = '{client_family_name}';
                $tokens[] = '{client_first_name}';
                $tokens[] = '{oder_number}';
                $tokens[] = '{btc_received_amount}';
                $tokens[] = '{mail_signature}';
                break;
            case 'mail_notify_tokencode':
                // // Email tokens for mail_notify_tokencode_message
                $tokens[] = '{client_family_name}';
                $tokens[] = '{client_first_name}';
                $tokens[] = '{order_number}';
                $tokens[] = '{create_at}';
                $tokens[] = '{btc_amount}';
                $tokens[] = '{btc_usd_rate}';
                $tokens[] = '{qnt_usd_rate}';
                $tokens[] = '{token_quantity}';
                $tokens[] = '{token_code}';
                $tokens[] = '{mail_signature}';
                break;
            case 'mail_agent_register':
                // Email tokens for mail_agent_register_message
                $tokens[] = '{agent_family_name}';
                $tokens[] = '{agent_first_name}';
                $tokens[] = '{set_password_url}';
                $tokens[] = '{agent_login_url}';
                $tokens[] = '{mail_signature}';
                break;
            case 'mail_approved_agent':
                // Email tokens for mail_approved_agent_message
                $tokens[] = '{agent_family_name}';
                $tokens[] = '{agent_first_name}';
                $tokens[] = '{agent_login_url}';
                $tokens[] = '{mail_signature}';
                break;
            case 'mail_respond_password':
                 // Email tokens for mail_respond_password_message
                $tokens[] = '{agent_family_name}';
                $tokens[] = '{agent_first_name}';
                $tokens[] = '{reset_password_url}';
                $tokens[] = '{mail_signature}';
                break;
            case 'mail_cancel_order':
                // Email for cancel order                
                $tokens[] = '{agent_family_name}';
                $tokens[] = '{agent_first_name}';      
                $tokens[] = '{order_number}';
                $tokens[] = '{btc_received_amount}'; 
                $tokens[] = '{mail_signature}';    
                break;
            case 'mail_edit_token':
                $tokens[] = '{client_family_name}';
                $tokens[] = '{client_first_name}';
                $tokens[] = '{order_number}';
                $tokens[] = '{create_at}';
                $tokens[] = '{btc_amount}';
                $tokens[] = '{btc_usd_rate}';
                $tokens[] = '{qnt_usd_rate}';
                $tokens[] = '{token_quantity}';
                $tokens[] = '{token_code}';
                $tokens[] = '{mail_signature}';
                break;
            case 'mail_issue_redeem_token':
                $tokens[] = '{client_family_name}';
                $tokens[] = '{client_first_name}';
                $tokens[] = '{order_number}';               
                $tokens[] = '{create_at}';
                $tokens[] = '{btc_amount}';
                $tokens[] = '{btc_usd_rate}';
                $tokens[] = '{qnt_usd_rate}';
                $tokens[] = '{token_quantity}';
                $tokens[] = '{token_code}';
                $tokens[] = '{user_hash}';
                $tokens[] = '{md5_order_number}';
                $tokens[] = '{mail_signature}';
                break;
            default:
                # code...
                break;
        }
        return $tokens;
    }

    protected function getEmailSignature() {
        $key = 'mail_signature';
        $search = $this->getSupportEmailTokens($key);
        $replace = array($this->config->item('site_domain'), $this->config->item('support_email'));
        return str_replace($search, $replace, $this->config->item($key));
    }

    protected function parseEmailTemplate($key, $replace_token) {
        $email_template = $this->config->item($key);
        $search_token = $this->getSupportEmailTokens($key);
        return array(
            'subject' => str_replace($search_token, $replace_token, $email_template['subject']),
            'message' => str_replace($search_token, $replace_token, $email_template['message'])
        );
    }

    /**
     * ログアウト
     */
    public function logout() {
        log_message('debug', 'Operator/logout');
        $this->logoutSession();
        $this->safe_redirect('Operator/login');
    }

    /**
     * A function isLoginClient
     * @param type $email
     * @param type $role
     * @return boolean
     */
    protected function isLoginClient($arrParameter) {

        $listAccount = $this->personal->isAccount($arrParameter);
        if (null == $listAccount) {
            return false;
        }
        $arrPersonalId = array();
        foreach($listAccount->result() as $personalId){
            array_push($arrPersonalId, $personalId->personal_id);
        }

        $user_hash = $this->user->isUserRole($arrPersonalId, $arrParameter['role']);
        if (null == $user_hash) {
            return false;
        }

        // Get user for uid info
        $user = $this->user->get_user_by_userhash($user_hash);

        // セッション情報を作成する
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
        $session_id = md5($user->email . $now . $this->defaultUser);

        // セッションをセットする
        $data = array(
            'session_id' => $session_id,
            'user_hash' => $user_hash,
            'uid' => $user->uid,
            'role' => $user->role,
            'role_text' => $this->defaultUser
        );
        $this->setLoginData($data);

        // DBに保存する
        $data = array(
            'session_id' => $session_id,
            'user_hash' => $user_hash,
            'create_at' => $now
        );
        $result = $this->login->insert_session($data);

        $adata = array(
            'user_hash' => $user_hash,
            'activity_code' => $this->config->item('act_login'),
            'create_by' => $user->uid,
            'create_at' => $now
        );
        $this->act->insert_activity($adata);

        return $result;
    }
}
