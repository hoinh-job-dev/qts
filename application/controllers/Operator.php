<?php

class Operator extends MY_Controller {

    protected $defaultUser = 'QTS_Operator';

    const COMMON_MENU = 'operator';
    const NO_ERROR = '00';
    const ERR_ADDR_PASS = '10';      // receiving invalid email address or invalid password
    const ERR_ADDR = '20';           // receiving invalid email address
    const ERR_ADDR_WHEN_ASKED = '30'; // receiving invalid email address

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model', 'user');
        $this->load->model('Order_model', 'order');
        $this->load->model('Activity_model', 'act');
        $this->load->model('Personal_model', 'personal');
        $this->load->model('Commission_model', 'commission');
        $this->load->model('General_model', 'general');
        $this->load->model('ExchangeRate_model', 'rate');
        $this->load->model('BtcAddress_model', 'btcAddress');
        $this->load->model('BtcTxs_model', 'txs');
        $this->load->model('BtcBlock_model', 'block');
        $this->load->model('TokenApproved_model', 'tokenApproved');
        $this->load->model('Token_model', 'token');
        $this->load->model('ClosedOrderSummary_model', 'closedOrderSummary');
        $this->load->model('Refund_model', 'refund');
        $this->load->model('BankBtcHeader_model', 'bankBtcHeader');
        $this->load->model('BankBtcDetail_model', 'bankBtcDetail');
        $this->load->model('EmailQueue_model', 'emailQueue');        
        $this->load->model('EmailQueueRedeem_model', 'emailQueueRedeem');
        $this->load->model('ImgFile_model', 'img');

        $this->load->library('exchanger');
        $this->load->library('saveImage');
        $this->load->library('email');
        //including validation library
        $this->load->library('form_validation');
    }

    // Redirect to Options controller to manage options
    public function manageOptions() {
        $this->safe_redirect('Options/index');
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }

    public function zipcloudSearch($zipcode = NULL) {
        if(empty($zipcode)) {
            $response = array('status' => 900, 'message' => 'Missing or empty zipcode');
            $this->jsonResponse($response);
        }

        $zipcloudApiUrl = "http://zipcloud.ibsnet.co.jp/api/search?zipcode=" . $zipcode;
        $rawResponse = $this->getApi($zipcloudApiUrl);

        if(empty($rawResponse)) {
            $response = array('status' => 901, 'message' => 'Response is empty');
            $this->jsonResponse($response);
        }

        try {
            $response = json_decode($rawResponse);
            $this->jsonResponse($response);
        } catch(Exception $ex) {
            $response = array('status' => 902, 'message' => 'Cannot parse response');
            $this->jsonResponse($response);
        }
    }
    /*****************************************
     * ログイン
     *****************************************/

    public function login($isError = self::NO_ERROR) {
        log_message('debug', 'Operator/login');

        $header = array(
            'current_menu' => '',
            'title' => 'オペレーターログイン',
            'role' => ''
        );
        $body = array(
            'isError' => $isError
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/loginOperator', $body);
        $this->load->view('common/footer');
    }

    public function home() {
        log_message('debug', 'Operator/home');
        $this->load->model('screen/SearchHome_Model', 'search');

        // ログイン判定
        $uid = isset($_POST['email']) ? $_POST['email'] : null;
        $pw = isset($_POST['password']) ? $_POST['password'] : null;
        if (null == $uid && null == $pw) {
            if (!$this->is_login()) {
                $this->safe_redirect('Operator/login');
            }
        } else {
            $role = array(
                $this->config->item('role_sysadmin'),
                $this->config->item('role_operator'),
                $this->config->item('role_ope_money'),
                $this->config->item('role_ope_order'),
                $this->config->item('role_ope_reg'));
            if (!$this->loginSession($uid, $pw, $role)) {
                $this->safe_redirect('Operator/login/' . self::ERR_ADDR_PASS);
            }
        }

        if (isset($_POST['search'])) {
            // Search function
            $data = $_POST['search'];
        } else{
            $data = array();
        }

        $search = new $this->search($data);

        $userlist = $this->user->search4_Overview($search)->result();

        $users = array();
        foreach ($userlist as $user) {
            $personal = $this->personal->select_latest_by_key($user->personal_id);
            $personal = (null == $personal) ? null : $personal->row();
            array_push($users, array(
                'status' => $user->status,
                'pid' => $user->personal_id,
                'uid' => $user->uid,
                'role' => $user->role,
                'type' => $user->type,
                'name' => $user->family_name . " " . $user->first_name,
                'email' => $user->email,
                'tel' => (null == $personal) ? '-' : $personal->tel,
                'birthday' => (null == $personal) ? '-' : $personal->birthday,
                'zip' => (null == $personal) ? '-' : $personal->zip_code,
                'prefecture' => (null == $personal) ? '-' : $personal->prefecture,
                'city' => (null == $personal) ? '-' : $personal->city,
                'building' => (null == $personal || null == $personal->building) ? '' : $personal->building,
                'country' => (null == $personal) ? '-' : $personal->country,
                'imgfile' => (null == $personal) ? '-' : $personal->imgfile
            ));
        }

        // Load types
        $types = $this->general->get_type()->result();
        $roles = $this->general->get_role()->result();

        $header = array(
            'current_menu' => 'home',
            'title' => '登録者一覧',
            'role' => self::COMMON_MENU,
        );

        $body = array(
            'users' => $users,
            'types' => $types,
            'roles' => $roles,
            'search' => $search
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/overview', $body);
        $this->load->view('common/footer');
    }

    /**
     * 登録者一覧をCSVで出力する
     */
    public function outputCsv_home() {
        log_message('debug', 'Operator/outputCsv_home');
        $this->load->model('screen/SearchHome_Model', 'search');

        if (isset($_POST['search'])) {
            // Search function
            $data = $_POST['search'];
        } else{
            $data = array();
        }

        $search = new $this->search($data);
        $result = $this->user->get_usertree($search);

        $this->outputCsv($result, $this->config->item('csv_home'));
    }

    public function outputCsv_home2() {
        log_message('debug', 'Operator/outputCsv_home2');
        $result = $this->user->get_usertree2();
        $this->outputCsv($result, $this->config->item('csv_home'));
    }

    /**
     * パスワードを忘れた時
     */
    public function ask_password() {
        log_message('debug', 'Operator/ask_password');
        $email = isset($_POST['askemail']) ? $_POST['askemail'] : null;
        $result = $this->ask_userpassword($email);
        if (null == $result) {
            $this->safe_redirect('Operator/login/' . self::ERR_ADDR_WHEN_ASKED);
        }
        $this->safe_redirect('Operator/login');
    }

    /*****************************************
     * オペレーション画面
     *****************************************/

    /**
     * 登録された個人情報と確認書類を表示する
     */
    public function viewPersonal() {
        log_message('debug', 'Operator/viewPersonal');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $personals = array();
        $user = '';

        $userid = '';
        $role = '';
        $type = '';
        $email = '';
        $order_by = '';
        $order_opt = '';
        if (isset($_POST['search'])) {
            // Search function
            $userid = $_POST['userid'];
            $role = $_POST['role'];

            $type = $_POST['type'];
            $email = $_POST['email'];
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order_opt = isset($_POST['order_opt']) ? $_POST['order_opt'] : 'ASC';
        } else {
            // 登録一覧取得
            $userlist = $this->user->select_all()->result();
        }

        // Load types
        $types = $this->general->get_Payment_Method()->result();
        $roles = $this->general->get_role()->result();

        // 未承認のユーザのリストを取得する --> 該当する個人情報を取得する
        $result = $this->user->select_list_not_approved_filter($order_by, $order_opt);
        $keys = array();
        foreach ($result as $row) {
            array_push($keys, $row->personal_id);
        }

        $personallist = $this->personal->select_list_by_key_order($keys, $order_by, $order_opt);

        if(!empty($personallist)) {
            for ($i = 0; $i < $personallist->num_rows(); $i++) {
                $personal = $personallist->row($i);

                if (isset($_POST['search'])) {
                    $user_id = $_POST['userid'];
                    $role = $_POST['role'];

                    $type = $_POST['type'];
                    $email = $_POST['email'];
                    $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
                    $order_opt = isset($_POST['order_opt']) ? $_POST['order_opt'] : 'ASC';

                    $user = $this->user->get_order_by_filter($personal->personal_id, $user_id, $role, $type, $email)->row();
                } else {
                    $user = $this->user->get_order_by_personalid($personal->personal_id)->row();
                }

                if (null == $user) {

                    continue;
                }
                array_push($personals, array(
                    'personal_id' => $personal->personal_id,
                    'company_name' => $personal->company_name,
                    'family_name' => $personal->family_name,
                    'first_name' => $personal->first_name,
                    'birthday' => $personal->birthday,
                    'country' => $personal->country,
                    'zip_code' => $personal->zip_code,
                    'prefecture' => $personal->prefecture,
                    'city' => $personal->city,
                    'building' => $personal->building,
                    'email' => $personal->email,
                    'tel' => $personal->tel,
                    'type' => $user->type,
                    'role' => $user->role,
                    'pay_method' => $user->pay_method,
                    'amount' => $user->amount,
                    'create_at' => $personal->create_at,
                    'imgfile' => $personal->imgfile,
                    'status' => $user->status,
                    'memo' => $user->memo,
                    'role_code' => @$user->role_code,
                    'pay_method_code' => @$user->pay_method_code,
                ));
            }
        }

        $header = array(
            'current_menu' => 'viewPersonal',
            'title' => '審査結果を登録する',
            'role' => self::COMMON_MENU
        );
        $body = array(
            'personals' => $personals,
            'types' => $types,
            'roles' => $roles,
            'search_userid' => $userid,
            'search_role' => $role,
            'search_type' => $type,
            'search_email' => $email,
            'order_opt' => $order_opt,
            'order_by' => $order_by
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/viewPersonal', $body);
        $this->load->view('common/footer');
    }

    /**
     * 認証結果を入力する
     */
    public function confirmPersonalStatus() {
        log_message('debug', 'Operator/confirmPersonalStatus');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $operator_uid = $this->getSessionValue('uid');

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $rownum = isset($_POST['rownum']) ? $_POST['rownum'] : 0;
        for ($i = 0; $i < $rownum; $i++) {
            $row = $this->user->select_by_personalid($_POST['id' . $i]);
            if ($this->config->item('act_approved') == $row->status) {
                continue;
            }

            switch ($_POST['memo' . $i]) {
                case $this->config->item('approval_not_selected'):
                    // 未選択はスキップ
                    continue;

                case $this->config->item('approval_approved'):
                    // 承認
                    $data = array(
                        'status' => $this->config->item('act_approved'),
                        'update_by' => $operator_uid,
                        'update_at' => $now
                    );
                    $this->user->update_by_personalid($_POST['id' . $i], $data);

                    $operator_hash = $this->getSessionValue('user_hash');
                    $adata = array(
                        'user_hash' => $operator_hash,
                        'activity_code' => $this->config->item('act_approved'),
                        'object' => $row->user_hash,
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->act->insert_activity($adata);

                    $user = $this->user->select_by_personalid($_POST['id' . $i]);
                    switch ($user->role) {
                        case "03":
                            // 承認済み通知メール
                            $replace_token = array(
                                $user->family_name, $user->first_name, 
                                $this->getEmailSignature()
                            );
                            $email_template = $this->parseEmailTemplate('mail_approved_client', $replace_token);
                            $emailData = array(
                                'to' => $user->email,
                                'subject' => $email_template['subject'],
                                'message' => $email_template['message'],
                                'object' => $row->user_hash,
                                'memo' => 'Operator: Approve client',
                                'create_by' => $operator_uid,
                                'create_at' => $now
                            );
                            $this->emailQueue->insert_data(array($emailData));

                            $order = $this->order->select_by_userhash($row->user_hash);
                            if ($this->config->item('order_orderby_bank') == $order->status) {
                                // 銀行口座通知メール

                                $order = $this->order->select_by_ordernumberstatus($order->order_number, $this->config->item('order_orderby_bank'));

                                $orderd_amount = $order->amount;
                                // $orderd_amount = money_format_qts($orderd_amount,2);

                                // $service_fee = @doubleval($orderd_amount * (float)$this->config->item('charge')/100);
                                // $service_fee = money_format_qts($service_fee, 2, false);

                                // $vat = $this->vat->get_value_current();
                                // $vat_fee = @doubleval($service_fee * $vat->vat_value);
                                // $vat_fee = money_format_qts($vat_fee, 2, false);

                                // $entry_amount = doubleval($orderd_amount - $service_fee - $vat_fee);
                                // $entry_amount = money_format_qts($entry_amount, 2);
                                // $trade_law_url = $this->config->item('site_domain')."/law/";
                                // $replace_token = array(
                                //     $user->family_name, $user->first_name, $order->order_number,
                                //     $orderd_amount, $service_fee, $vat_fee, $entry_amount,
                                //     $trade_law_url, $this->getEmailSignature()
                                // );

                                $service_fee = doubleval($orderd_amount * (float) $this->config->item('charge') / 100);
                                $entry_amount = doubleval($orderd_amount + $service_fee);
                                $trade_law_url = $this->config->item('site_domain')."/law/";

                                $replace_token = array(
                                    $user->family_name, $user->first_name, $order->order_number,
                                    money_format_qts( $orderd_amount,2), $service_fee, money_format_qts( $entry_amount,2),
                                    $trade_law_url, $this->getEmailSignature()
                                );

                                $email_template = $this->parseEmailTemplate('mail_notify_bankaccount', $replace_token);

                                $emailData = array(
                                    'to' => $user->email,
                                    'subject' => $email_template['subject'],
                                    'message' => $email_template['message'],
                                    'object' => $row->user_hash,
                                    'memo' => 'Operator: Notify user for order pay by bank',
                                    'create_by' => $operator_uid,
                                    'create_at' => $now
                                );
                                $this->emailQueue->insert_data(array($emailData));
                            }
//                            else if ($this->config->item('payby_btc') == $order->pay_method) {
//                                $this->order->update_order($order->order_number, $operator_hash, array('rsv_char_1' => '1'));
//                            }

                            break;
                        case "04":
                        case "05":
                        case "06":
                        case "07":
                            // 承認済み通知
                            $agent_login_url = base_url('Agent/login');
                            $replace_token = array(
                                $user->family_name, $user->first_name, 
                                $agent_login_url,
                                $this->getEmailSignature()
                            );
                            $email_template = $this->parseEmailTemplate('mail_approved_agent', $replace_token);
                            $emailData = array(
                                'to' => $user->email,
                                'subject' => $email_template['subject'],
                                'message' => $email_template['message'],
                                'object' => $row->user_hash,
                                'memo' => 'Operator: Approve agent',
                                'create_by' => $operator_uid,
                                'create_at' => $now
                            );
                            $this->emailQueue->insert_data(array($emailData));
                            break;
                        default:
                    }
                    break;

                case $this->config->item('approval_pending'):
                    // 保留
                    $data = array(
                        'status' => $this->config->item('approval_pending'),
                        'update_by' => $operator_uid,
                        'update_at' => $now
                    );
                    $this->user->update_by_personalid($_POST['id' . $i], $data);

                    $operator_hash = $this->getSessionValue('user_hash');
                    $adata = array(
                        'user_hash' => $operator_hash,
                        'activity_code' => $this->config->item('approval_pending'),
                        'object' => $row->user_hash,
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->act->insert_activity($adata);
                    break;

                case $this->config->item('approval_invalid'):
                    // 無効
                    $data = array(
                        'status' => $this->config->item('approval_invalid'),
                        'update_by' => $operator_uid,
                        'update_at' => $now
                    );
                    $this->user->update_by_personalid($_POST['id' . $i], $data);

                    $operator_hash = $this->getSessionValue('user_hash');
                    $adata = array(
                        'user_hash' => $operator_hash,
                        'activity_code' => $this->config->item('approval_invalid'),
                        'object' => $row->user_hash,
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->act->insert_activity($adata);
                    break;

                case $this->config->item('approval_expired'):
                    // 有効期限切れ
                    $data = array(
                        'status' => $this->config->item('approval_expired'),
                        'update_by' => $operator_uid,
                        'update_at' => $now
                    );
                    $this->user->update_by_personalid($_POST['id' . $i], $data);

                    $operator_hash = $this->getSessionValue('user_hash');
                    $adata = array(
                        'user_hash' => $operator_hash,
                        'activity_code' => $this->config->item('approval_expired'),
                        'object' => $row->user_hash,
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->act->insert_activity($adata);
                    break;

                default:
                    // 未承認
                    $data = array(
                        'status' => $this->config->item('approval_not_approve'),
                        'memo' => $_POST['memo' . $i],
                        'update_by' => $operator_uid,
                        'update_at' => $now
                    );
                    $this->user->update_by_personalid($_POST['id' . $i], $data);

                    $operator_hash = $this->getSessionValue('user_hash');
                    $adata = array(
                        'user_hash' => $operator_hash,
                        'activity_code' => $this->config->item('approval_not_approve'),
                        'object' => $row->user_hash,
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->act->insert_activity($adata);
                    break;
            }
        }

        $this->safe_redirect('Operator/viewPersonal');
    }

    /**
     * 登録された個人情報と確認書類をCSVで出力する
     */
    public function outputCsv_personal() {
        log_message('debug', 'Operator/outputCsv_personal');
        $result = $this->personal->select_list_by_key(null);
        $this->outputCsv($result, $this->config->item('csv_personal'));
    }

    /* -------------------------------------------------- */

    /**
     * 
     */
    public function inputBtcAddr() {
        log_message('debug', 'Operator/inputBtcAddr');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        // BTC払い一覧取得
        $target_status = array(
            $this->config->item('order_orderby_btc')
        );

        $header = array(
            'current_menu' => 'inputBtcAddr',
            'title' => '請求用ビットコインアドレス登録',
            'role' => self::COMMON_MENU
        );
        $target_status_check = array(
            $this->config->item('order_orderby_bank'),
            $this->config->item('order_orderby_btc')
        );
        $body = array(
            'order' => $this->order->select_latest_list($target_status),
            'order_sum4month' => $this->order->get_SumAmount4Month($target_status_check),
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/inputBtcAddr', $body);
        $this->load->view('common/footer');
    }

    public function confirmBtcAddr() {
        log_message('debug', 'Operator/confirmBtcAddr');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $operator_uid = $this->getSessionValue('uid');

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        // レコードのステータスを更新する
        $rownum = isset($_POST['rownum']) ? $_POST['rownum'] : 0;
        for ($i = 0; $i < $rownum; $i++) {
            if ("" == $_POST['btc_address' . $i]) {
                // 未入力の場合はスキップ
                continue;
            }

            // 請求用BTCアドレス画面URL通知メール
            $order = $this->order->select_by_ordernumber($_POST['order' . $i]);
            $user = $this->user->select_user_by_uid($_POST['uid' . $i]);

            $view_btc_addr_url = base_url('Client/viewBtcAddr/' . $order->order_number . "/" . $user->user_hash);
            $replace_token = array(
                $user->family_name, $user->first_name,
                $view_btc_addr_url,
                $this->getEmailSignature()
            );
            $email_template = $this->parseEmailTemplate('mail_notify_btcaddr', $replace_token);
            $emailData = array(
                'to' => $user->email,
                'subject' => $email_template['subject'],
                'message' => $email_template['message'],
                'object' => $order->order_number,
                'memo' => 'Operator: Notify client order by BTC',
                'create_by' => $operator_uid,
                'create_at' => $now
            );
            $this->emailQueue->insert_data(array($emailData));

            $date = new DateTime();
            $expiration_datetime = date($this->config->item('db_timestamp_format'), ($date->getTimestamp() + $this->config->item('order_expiration_time')));

            $row = $this->order->select_by_ordernumber($_POST['order' . $i]);
            $odata = array(
                'order_number' => $row->order_number,
                'status' => $this->config->item('order_notify_btcaddr'),
                'agent_uid' => $row->agent_uid,
                'client_uid' => $row->client_uid,
                'pay_method' => $row->pay_method,
                'currency_unit' => $row->currency_unit,
                'amount' => $row->amount,
                'receive_address' => $_POST['btc_address' . $i],
                'expiration_date' => $expiration_datetime,
                'create_by' => $operator_uid,
                'create_at' => $now
            );
            $this->order->insert_order($odata);

            $operator_hash = $this->getSessionValue('user_hash');
            $adata = array(
                'user_hash' => $operator_hash,
                'activity_code' => $this->config->item('act_notify_btcaddr'),
                'object' => $_POST['order' . $i],
                'memo' => $_POST['btc_address' . $i],
                'create_by' => $operator_uid,
                'create_at' => $now
            );
            $this->act->insert_activity($adata);
        }
        $this->safe_redirect('Operator/inputBtcAddr');
    }

    // TODO CSV出力
    public function TODO_outputCsv_BtcAttr() {
        
    }

    /* -------------------------------------------------- */

    /**
     * 入金待ち一覧を表示する
     */
    public function inputBanking() {
        log_message('debug', 'Operator/inputBanking');
        $this->load->model('screen/SearchInputBanking_Model', 'search');
        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        // 注文一覧取得
        $target_status = array(
            $this->config->item('order_notify_bankaccount'),
            $this->config->item('order_notify_btcaddr')
        );
        $data = array();
        if (isset($_POST['search'])) {
            $search = new $this->search($_POST['search']);
            $orders = $this->order->select_latest_list_search($target_status, $search);
        } else {
            $search = new $this->search($data);
            $orders = $this->order->select_latest_list($target_status);
        }


        $header = array(
            'current_menu' => 'inputBanking',
            'title' => '入金された金額を入力する',
            'role' => self::COMMON_MENU
        );

        $target_status_check = array(
            $this->config->item('order_orderby_bank'),
            $this->config->item('order_orderby_btc')
        );
        $body = array(
            'order' => $orders,
            'order_sum4month' => $this->order->get_SumAmount4Month($target_status_check),
            'search' => $search
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/inputBanking', $body);
        $this->load->view('common/footer');
    }

    /**
     * 入金結果を入力する
     */
    public function confirmBanking() {
        log_message('debug', 'Operator/confirmBanking');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $operator_uid = $this->getSessionValue('uid');

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $rownum = isset($_POST['rownum']) ? $_POST['rownum'] : 0;
        for ($i = 0; $i < $rownum; $i++) {
            // 元の値
            $row = $this->order->select_by_ordernumber($_POST['order' . $i]);
            if ($row->status != $this->config->item('order_notify_bankaccount')
                and $row->status != $this->config->item('order_notify_btcaddr')) {
                continue;
            }
            switch ($_POST['status' . $i]) {
                case $this->config->item('order_not_selected'):
                    // 未選択はスキップ
                    continue;

                case $this->config->item('order_receiveby_bank'):
                    log_message('debug', 'order_not_selected');
                    // 銀行振込で着金確認済み
                    log_message('debug', 'order=' . $row->order_number . " status=" . $this->config->item('order_receiveby_bank'));

                    $account_name = isset($_POST['account_name' . $i]) ? $_POST['account_name' . $i] : '';
                    $jpyAmount = isset($_POST['amount' . $i]) ? $_POST['amount' . $i] : 0;
                    if ('' == $account_name || 0 == $jpyAmount) {
                        // 振込名義が未入力のものはスキップ
                        continue;
                    }

                    $odata = array(
                        'order_number' => $row->order_number,
                        'status' => $this->config->item('order_receiveby_bank'),
                        'agent_uid' => $row->agent_uid,
                        'client_uid' => $row->client_uid,
                        'pay_method' => $row->pay_method,
                        'received' => '01',
                        'currency_unit' => $this->config->item('currency_jpy'),
                        'amount' => $jpyAmount,
                        'account_name' => $account_name,
                        'expiration_date' => $row->expiration_date,
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->order->insert_order($odata);

                    $operator_hash = $this->getSessionValue('user_hash');
                    $adata = array(
                        'user_hash' => $operator_hash,
                        'activity_code' => $this->config->item('act_receiveby_bank'),
                        'object' => $row->order_number,
                        'memo' => "account=" . $_POST['account_name' . $i] . ", amount=" . $jpyAmount . "JPY",
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->act->insert_activity($adata);
                    break;

                case $this->config->item('order_receiveby_btc'):
                    log_message('debug', 'order_receiveby_btc');
                    // ビットコイン送金で着金確認済み
                    log_message('debug', 'order=' . $row->order_number . " status=" . $this->config->item('order_receiveby_btc'));

                    $txtime = isset($_POST['txtime' . $i]) ? $_POST['txtime' . $i] : '';
                    $btcAmount = isset($_POST['amount' . $i]) ? $_POST['amount' . $i] : 0;
                    if ('' == $txtime || 0 == $btcAmount) {
                        continue;
                    }

                    $odata = array(
                        'order_number' => $row->order_number,
                        'status' => $this->config->item('order_receiveby_btc'),
                        'agent_uid' => $row->agent_uid,
                        'client_uid' => $row->client_uid,
                        'received' => '01',
                        'pay_method' => $row->pay_method,
                        'currency_unit' => $this->config->item('currency_btc'),
                        'amount' => $btcAmount,
                        'expiration_date' => $row->expiration_date,
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->order->insert_order($odata);

                    $operator_hash = $this->getSessionValue('user_hash');
                    $adata = array(
                        'user_hash' => $operator_hash,
                        'activity_code' => $this->config->item('act_receivedby_btc'),
                        'object' => $row->order_number,
                        'memo' => "receive_address='" . $row->receive_address . "' amount=" . $_POST['amount' . $i] . "BTC",
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->act->insert_activity($adata);

                    // トークンを発行する
                    // BTCで注文の申込を取得する
                    $orderDate = $this->order->get_OrderDate4BTCOrder($_POST['order' . $i]);
                    $this->exchanger->calc_token($_POST['order' . $i], $orderDate);

                    break;

                case $this->config->item('order_invalid'):
                    log_message('debug', 'order_invalid');
                    // 無効
                    $odata = array(
                        'order_number' => $row->order_number,
                        'status' => $this->config->item('order_invalid'),
                        'agent_uid' => $row->agent_uid,
                        'client_uid' => $row->client_uid,
                        'received' => $row->received,
                        'pay_method' => $row->pay_method,
                        'expiration_date' => $row->expiration_date,
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->order->insert_order($odata);

                    $operator_hash = $this->getSessionValue('user_hash');
                    $adata = array(
                        'user_hash' => $operator_hash,
                        'activity_code' => $this->config->item('approval_invalid'),
                        'object' => $_POST['client_uid'.$i],
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->act->insert_activity($adata);
                    break;

                default:
            }
        }
        $this->safe_redirect('Operator/inputBanking');
    }

    /* -------------------------------------------------- */

    /**
     * JPY/BTC換金待ちを表示する
     */
    public function inputExchangedBtc($btcAddress = NULL) {
        $this->checkAutoBanking();

        log_message('debug', 'Operator/inputExchangedBtc');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $bankBtcData = $this->bankBtcHeader->get_by_address($btcAddress);
        if(!empty($bankBtcData)) {
            $bankBtcData->orders = $this->bankBtcDetail->get_data($bankBtcData->btc_address);
            // Do not show if it is completed
            if($bankBtcData->complete == '1') {
                $bankBtcData = NULL;
            }
        }

        $header = array(
            'current_menu' => 'inputExchangedBtc',
            'title' => 'JPY/BTC換金選択',
            'role' => self::COMMON_MENU
        );
        $target_status = array(
            $this->config->item('order_receiveby_bank')
        );
        $target_status_check = array(
            $this->config->item('order_orderby_bank'),
            $this->config->item('order_orderby_btc')
        );
        $body = array(
            'order' => $this->order->select_latest_list($target_status),
            'order_sum4month' => $this->order->get_SumAmount4Month($target_status_check),
            'ignore_orders' => $this->bankBtcDetail->get_order_numbers(),
            'bankBtcData' => $bankBtcData
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/inputExchangedBtc', $body);
        $this->load->view('common/footer');
    }

    public function deleteJpyOrder() {
        $this->checkAutoBanking();

        log_message('debug', 'Operator/deleteJpyOrder');

        $operator_uid = $this->getSessionValue('uid');
        if(empty($operator_uid)) {
            $operator_uid = $this->config->item('AUTO_ID');
        }

        $operator_hash = $this->getSessionValue('user_hash');
        if(empty($operator_hash)) {
            $operator_hash = sha1($operator_uid);
        }

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $qs = '';
        if(isset($_POST['delete_jpy_order'])) {
            $qs = 'delete/success';
            $order_number = (int) @$_POST['order_number'];
            $status = $this->config->item('order_receiveby_bank');
            $row = $this->order->select_by_ordernumberstatus($order_number, $status);
            if(!empty($row)) {
                $result = $this->order->delete_order($order_number, $status);
                if($result === true) {
                    // insert activity
                    $adata = array(
                        'user_hash' => $operator_hash,
                        'activity_code' => $this->config->item('order_receiveby_bank'),
                        'object' => $row->order_number,
                        'memo' => "Delete JPY Order with status = " . $status,
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->act->insert_activity($adata);
                } else {
                    $qs = 'delete/fail';
                }
            }
            else {
                $qs = 'delete/order_not_found';
            }
        }

        $this->safe_redirect("Operator/inputExchangedBtc/$qs");
    }

    /**
     * JPY/BTC換金結果を入力する
     */
    public function confirmExchangedBtc() {

        $this->checkAutoBanking();

        log_message('debug', 'Operator/confirmExchangedBtc');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $arrOrderNumbers = array();

        $rownum = isset($_POST['rownum']) ? $_POST['rownum'] : 0;
        for ($i = 0; $i < $rownum; $i++) {
            $orderNumber = @$_POST['order' . $i];
            $btcAmount = isset($_POST['amount' . $i]) ? $_POST['amount' . $i] : 0;
            $txtime = isset($_POST['txtime' . $i]) ? $_POST['txtime' . $i] : "";
            if (0 == $btcAmount || "" == $_POST['txtime' . $i]) {

                // Check if tick on set btc address checkbox
                if(isset($_POST['set_btc_address'][$orderNumber])) {
                    $arrOrderNumbers[] = $_POST['order' . $i];
                }

                // 未入力はスキップ
                continue;
            }

            // Insert Exchange BTC order
            $this->insertExchangedBtcOrder($orderNumber, $btcAmount, $txtime);
        }

        // Insert auto banking data
        $btcAddress = $this->setBtcAddressForOrders($arrOrderNumbers);

        $this->safe_redirect('Operator/inputExchangedBtc/'.$btcAddress);
    }

    private function insertExchangedBtcOrder($orderNumber, $btcAmount, $txtime) {
        $btcAmount = (float) $btcAmount;
        if(empty($orderNumber) || $btcAmount <= 0 || empty($txtime)) {
            return false;
        }

        $operator_uid = $this->getSessionValue('uid');
        if(empty($operator_uid)) {
            $operator_uid = $this->config->item('AUTO_ID');
        }

        $operator_hash = $this->getSessionValue('user_hash');
        if(empty($operator_hash)) {
            $operator_hash = sha1($operator_uid);
        }

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $row = $this->order->select_by_ordernumber($orderNumber);
        $odata = array(
            'order_number' => $row->order_number,
            'status' => $this->config->item('order_exchange_jpybtc'),
            'agent_uid' => $row->agent_uid,
            'client_uid' => $row->client_uid,
            'pay_method' => $row->pay_method,
            'received' => $row->received,
            'currency_unit' => $this->config->item('currency_btc'),
            'amount' => $btcAmount,
            'expiration_date' => $row->expiration_date,
            'create_by' => $operator_uid,
            'create_at' => $now
        );
        $this->order->insert_order($odata);

        $adata = array(
            'user_hash' => $operator_hash,
            'activity_code' => $this->config->item('act_exchange_jpybtc'),
            'object' => $row->order_number,
            'memo' => "amount=" . $btcAmount . "BTC",
            'create_by' => $operator_uid,
            'create_at' => $now
        );
        $this->act->insert_activity($adata);

        // トークンを計算する
        $orderDate = $this->order->get_OrderDate4JpyOrder($row->order_number);
        $this->exchanger->calc_token($row->order_number, $orderDate);

        return true;
    }

    private function setBtcAddressForOrders($arrOrderNumbers = array()) {
        if(!is_array($arrOrderNumbers) || empty($arrOrderNumbers)) {
            return false;
        }

        $isValid = $this->bankBtcDetail->check_order_number($arrOrderNumbers);
        if(!$isValid) {
            log_message('debug', 'Operator/setBtcAddressForOrders - some order has been already processed before. Check the orders in t_bank_btc_details: ' . json_encode($arrOrderNumbers));
            return false;
        }

        $unuseAddress = $this->btcAddress->get_unuse_addr(1);
        if(empty($unuseAddress)) {
            return false;
        }
        $btcAddress = $unuseAddress[0]->address;

        $totalJPY = 0;
        $dataArray = array();
        $arrOrders = $this->order->selectOrderForAutoBanking($arrOrderNumbers);
        foreach($arrOrders as $row) {
            $totalJPY += (float) $row->amount;
            $dataArray[] = array(
                'order_number' => $row->order_number,
                'btc_address' => $btcAddress,
                'jpy_amount' => (float) $row->amount
            );
        }

        $data = array(
            'btc_address' => $btcAddress,
            'total_jpy_amount' => $totalJPY
        );

        if(empty($data) || empty($dataArray)) {
            return false;
        }

        $this->bankBtcHeader->insert_data(array($data));
        $this->bankBtcDetail->insert_data($dataArray);

        // update t_btc_address that address is in used
        $data = array(
            'order_number' => $dataArray[0]['order_number'],
            'status' => 1
        );
        $this->btcAddress->update_by_addr($btcAddress, $data);

        return $btcAddress;
    }

    // TODO
    public function TODO_outputCsv_exchangedBtc() {
        
    }

    /* -------------------------------------------------- */

    public function viewBankBtc() {

        $this->checkAutoBanking();

        log_message('debug', 'Operator/viewBankBtc');

        // ログイン判定
        if (!$this->is_login()) {
            redirect('Operator/login');
        }

        $header = array(
            'current_menu' => 'viewBankBtc',
            'title' => 'JPY/BTC換金結果照会',
            'role' => self::COMMON_MENU
        );

        $filter_by_status = isset($_REQUEST['filter_by_status']) && $_REQUEST['filter_by_status'] != '' ? $_REQUEST['filter_by_status'] : NULL;

        $bankBtcData = $this->bankBtcHeader->get_data($filter_by_status);
        foreach($bankBtcData as $i => $row) {
            $bankBtcData[$i]->orders = $this->bankBtcDetail->get_data($row->btc_address);
        }

        $target_status_check = array(
            $this->config->item('order_orderby_bank'),
            $this->config->item('order_orderby_btc')
        );
        $body = array(
            'bankBtcData' => $bankBtcData,
            'order_sum4month' => $this->order->get_SumAmount4Month($target_status_check)
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/viewBankBtc', $body);
        $this->load->view('common/footer');
    }

    /* -------------------------------------------------- */

    /**
     * 未払いのコミッション一覧を表示する
     */
    public function viewCommissions($paginationOffset=0, $status=NULL, $statusValue=NULL) {
        log_message('debug', 'Operator/viewCommissions');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $autoWalletResponse = $this->config->item('auto_wallet_response');
        $statusMessage = '';
        if($status == 'status') {
            $statusMessage = @$autoWalletResponse[$statusValue];
        }

        $header = array(
            'current_menu' => 'viewCommissions',
            'title' => 'コミッション審査対象一覧',
            'role' => self::COMMON_MENU
        );

        $not_pay_list = [];

        $search_user_id = '';
        $search_order_number = '';
        $search_pay_method = '';
        $order_by = '';
        $order_opt = '';

        $paginationLimit = (int) $this->config->item('limit_records');
        $paginationOffset = (int) $paginationOffset > 0 ? (int) $paginationOffset : 0;

        if (isset($_POST['search'])) {
            $search_user_id = $_POST['search_user_id'];
            $search_order_number = $_POST['search_order_number'];
            $search_pay_method = $_POST['search_pay_method'];
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order_opt = isset($_POST['order_opt']) ? $_POST['order_opt'] : 'ASC';

            $all_not_pay_list = $this->commission->get_not_payed_search($search_order_number, $search_user_id, $search_pay_method, $order_by, $order_opt);

            $not_pay_list = $this->commission->get_not_payed_search($search_order_number, $search_user_id, $search_pay_method, $order_by, $order_opt, $paginationLimit, $paginationOffset);
        } else {
            $all_not_pay_list = $this->commission->get_not_payed();

            $not_pay_list = $this->commission->get_not_payed($paginationLimit, $paginationOffset);
        }

        $pay_method = $this->general->get_Payment_Method()->result();

        $this->load->library('pagination');

        $paginationConfig['base_url'] = base_url('Operator/viewCommissions');
        $paginationConfig['total_rows'] = count($all_not_pay_list);
        $paginationConfig['per_page'] = $paginationLimit;
        $paginationConfig['uri_segment'] = 3 ;

        $this->pagination->initialize($paginationConfig);

        $body = array(
            'commission' => $not_pay_list,
            'search_pay_method' => $search_pay_method,
            'search_order_number' => $search_order_number,
            'search_user_id' => $search_user_id,
            'order_opt' => $order_opt,
            'order_by' => $order_by,
            'pay_method' => $pay_method,
            'statusMessage' => $statusMessage,
            'paginationLinks' => $this->pagination->create_links(),
            'paginationOffset' => $paginationOffset
        );

        $this->load->view('common/header', $header);
        $this->load->view('operator/commission', $body);
        $this->load->view('common/footer');
    }

    // TODO
    public function confirmCommissionStatus() {
        log_message('debug', 'Operator/confirmCommissionStatus');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $paginationLimit = (int) $this->config->item('limit_records');
        $paginationOffset = (int) @$_POST['paginationOffset'];
        $paginationOffset = $paginationOffset > 0 ? $paginationOffset : 0;

        if(!isset($_POST['commission_id']) || !is_array($_POST['commission_id']) || empty($_POST['commission_id'])) {
            $this->safe_redirect("Operator/viewCommissions/$paginationOffset/error/no_item_selected");
        }

        $limit_records = (int) $this->config->item('limit_records');

        $operator_uid = $this->getSessionValue('uid');

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        // レコードのステータスを更新する
        $dataArray = array();
        foreach($_POST['commission_id'] as $commission_id => $commission_id) {
            $data = array(
                    "is_payed" => 1,
                    'update_by' => $operator_uid,
                    "update_at" => $now
            );
            $dataArray[$commission_id] = $data;
        }

        // Prepare data to send BTC to agents
        if(!empty($dataArray)) {
            $apiData = array();
            $commissionIds = array();
            $commDataArr = $this->commission->getDataNotPayedByCommissionIds(array_keys($dataArray));
            foreach($commDataArr as $comm) {
                if(isset($comm->btc_address) && !empty($comm->btc_address)) {
                    if(!isset($apiData[$comm->btc_address])) {
                        $apiData[$comm->btc_address] = array('address' => $comm->btc_address, 'amount' => (float) $comm->quantity);
                    }
                    else {
                        $apiData[$comm->btc_address]['amount'] += (float) $comm->quantity;
                    }
                    if(!empty($this->config->item('agent_wallet_message'))) {
                        $apiData[$comm->btc_address]['comment'] = ($this->config->item('agent_wallet_message'));
                    }
                    $commissionIds[$comm->commission_id] = $dataArray[$comm->commission_id];
                }
            }
            if(!empty($apiData)) {
                $apiData = array('data' => array_values($apiData));
                if(!empty($this->config->item('agent_wallet_general_message'))) {
                    $apiData['comment'] = ($this->config->item('agent_wallet_general_message'));
                }
                $apiData = json_encode($apiData);

                $apiUrl = $this->config->item('ope-wallet-server') . '/send';

                $this->load->helper('http');
                $http = new Http();
                $response = $http->postJSON($apiUrl, $apiData);
                log_message('debug', 'Send commission result: ' . json_encode($response));
                $sentStatus = 'fail';
                if(!empty($response)) {
                    $response = json_decode($response);
                    if(is_object($response) && isset($response->status)) {
                        $sentStatus = $response->status == 'success' ? $response->status : @$response->code;
                    }
                }

                if($sentStatus == 'success') {
                    foreach ($commissionIds as $commission_id => $order_data) {
                        // Update commission
                        $this->commission->update_by_commission_id($commission_id, $order_data);
                    }

                    $paginationOffset -= $paginationLimit;
                    $paginationOffset = $paginationOffset > 0 ? $paginationOffset : 0;
                }

                $qs = "/$paginationOffset/status/$sentStatus";
            }
        }
        $this->safe_redirect('Operator/viewCommissions' . @$qs);
    }

    // TODO
    public function TODO_outputCsv_Commission() {
        
    }

    // for cronjob to create order 51
    public function checkCompleteOrders() {
        log_message('debug', 'Operator/checkCompleteOrders');
        $date = new DateTime();
        $now = date($this->config->item('session_timestamp_format'), $date->getTimestamp());

        $operator_uid = $this->getSessionValue('uid');
        if(empty($operator_uid)) {
            $operator_uid = $this->config->item('AUTO_ID');
        }

        $operator_hash = $this->getSessionValue('user_hash');
        if(empty($operator_hash)) {
            $operator_hash = sha1($operator_uid);
        }

        $arr = $this->commission->getDataToCreateCompleteOrder();
        foreach ($arr as $commission_id => $result) {
            // Insert completed order
            $odata = array(
                'order_number' => $result->order_number,
                'status' => $this->config->item('order_completed'),
                'agent_uid' => $result->agent_uid,
                'client_uid' => $result->client_uid,
                'pay_method' => $result->pay_method,
                'currency_unit' => $result->currency_unit,
                'amount' => $result->amount,
                'expiration_date' => $result->expiration_date,
                'create_by' => $operator_uid,
                'create_at' => $now
            );
            $this->order->insert_order($odata);

            //Insert activity
            $adata = array(
                'user_hash' => $operator_hash,
                'activity_code' => $this->config->item('act_order_complete'),
                'object' => $result->order_number,
                'memo' => "完了",
                'create_by' => $operator_uid,
                'create_at' => $now
            );
            $this->act->insert_activity($adata);
        }
    }

    /* -------------------------------------------------- */

    public function makeToken() {
        log_message('debug', 'Operator/makeToken');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $target_status = array(
            $this->config->item('order_exchange_jpybtc'),
            $this->config->item('order_receiveby_btc')
        );
        $target_status_check = array(
            $this->config->item('order_notify_bankaccount'),
            $this->config->item('order_notify_btcaddr')
        );
        $header = array(
            'current_menu' => 'makeToken',
            'title' => '受領書発行対象一覧',
            'role' => self::COMMON_MENU
        );
        $body = array(
            'order' => $this->order->select_for_makeToken(null),
            'order_sum4month' => $this->order->get_SumAmount4Month($target_status_check),
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/makeToken', $body);
        $this->load->view('common/footer');
    }

    public function confirmToken() {
        log_message('debug', 'Operator/confirmToken');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $arrOrderNumber = array();
        $doSendRefund = false;

        // レコードのステータスを更新する
        $rownum = isset($_POST['rownum']) ? $_POST['rownum'] : 0;
        $count = 0 ;
        for ($i = 0; $i < $rownum; $i++) {

            // メールを送信する
            $digit = 8;
            $tmp = pow(10, $digit);

            $operator_uid = $this->getSessionValue('uid');

            $date = new DateTime();
            $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

            $order_number = $_POST['order' . $i];
            $rows = $this->order->select_for_makeToken($order_number);
            if (empty($rows)) {
                continue;
            }
            $result = $rows[0];

            switch ($_POST['status' . $i]) {
                // 未入力の場合はスキップ
                case "00":
                    // do nothing
                break;
                // cancel order
                case $this->config->item('order_invalid'):
                    // insert cancelled order
                    $this->insertCancelledOrder($result->order_number);

                    // insert into refund
                    $refund_status = $this->config->item('refund_status');
                    $arrTxData = $this->refund->get_first_tx_for_order($order_number);
                    if(!empty($arrTxData)) {
                        $this->createRefundData($order_number, array(
                            'tx_id' => $arrTxData->tx_id,
                            'out_address' => $arrTxData->btc_address,
                            'balance' => $arrTxData->amount
                        ), $refund_status['token_cancel']);
                    }
                    $doSendRefund = true;
                    //insert data t_email_queue
                    $this->insertEmailInvalidOrder($result); 
                break;
                // confirm token
                default:
                    // email to client
                    $replace_token = array(
                        $result->family_name, $result->first_name, 
                        $order_number, $result->create_at, money_format_qts($result->amount,8),
                        $result->rate, $result->usdqnt,
                        money_format_qts($result->quantity,8), $result->token_code,
                        $this->getEmailSignature()
                    );
                    $email_template = $this->parseEmailTemplate('mail_notify_tokencode', $replace_token);
                    $emailData = array(
                        'to' => $result->email,
                        'subject' => $email_template['subject'],
                        'message' => $email_template['message'],
                        'is_bcc' => '0',
                        'object' => $order_number,
                        'memo' => 'Operator: confirm token',
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->emailQueue->insert_data(array($emailData));

                    // 受領書を連絡した
                    $odata = array(
                        'order_number' => $result->order_number,
                        'status' => $this->config->item('order_send_receipt'),
                        'agent_uid' => $result->agent_uid,
                        'client_uid' => $result->client_uid,
                        'pay_method' => $result->pay_method,
                        'currency_unit' => $result->currency_unit,
                        //'amount' => $result->amount,
                        'amount' => $result->quantity,
                        'expiration_date' => $result->expiration_date,
                        'create_by' => $operator_uid,
                        'create_at' => $now
                     );
                     $order_number = $this->order->insert_order($odata);
                    log_message('debug', "=============================order_insert _send _receipt $order_number");
                    // トークン発行の完了を登録する
                    $data = array(
                        'user_hash' => $result->user_hash,
                        'activity_code' => $this->config->item('act_issue_token'),
                        'object' => $order_number
                    );
                    $this->act->insert_activity($data);

                    // prepare for approve token
                    $arrOrderNumber[] = $_POST['order' . $i];
                    $count = $count +1;

                    // calculate commission
                    $this->exchanger->calc_commission($order_number);
            }
        }

        $this->makeApprovedToken($arrOrderNumber);


        if($doSendRefund === true) {
            $this->sendRefund();
        }

        $this->safe_redirect('Operator/makeToken');
    }

    /*****************************************
     * 各情報を表示する
     *****************************************/

    /**
     * アクティビティ一覧を表示する
     */
    public function activitylist() {
        log_message('debug', 'Operator/activitylist');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $header = array(
            'current_menu' => 'activitylist',
            'title' => 'アクティビティ一覧',
            'role' => self::COMMON_MENU
        );

        $active_list = [];
        $search_id = '';
        $search_user_id = '';
        $search_active_code = '';
        $search_from = '';
        $search_to = '';

        $order_by = '';
        $order_opt = '';

        if (isset($_POST['search'])) {
            $search_id = $_POST['search_id'];
            $search_user_id = $_POST['search_user_id'];
            $search_active_code = $_POST['search_active_code'];
            $search_from = $_POST['search_from'];
            $search_to = $_POST['search_to'];

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order_opt = isset($_POST['order_opt']) ? $_POST['order_opt'] : 'ASC';

            $active_list = $this->act->select_all_search($search_id, $search_user_id, $search_active_code, $search_from, $search_to, $order_by, $order_opt)->result();
        } else {
            $search_id = null;
            $search_user_id = null;
            $search_active_code = null;
            $search_from = date('Y/m/d', strtotime('-6 days'));
            $search_to = date("Y/m/d");

            $order_by = null;
            $order_opt = null;

            $active_list = $this->act->select_all_search($search_id, $search_user_id, $search_active_code, $search_from, $search_to, $order_by, $order_opt)->result();

            //$active_list = $this->act->getDataOneWeek()->result();
        }

        $active_codes = $this->general->get_status()->result();

        $body = array(
            'acts' => $active_list,
            'active_codes' => $active_codes,
            'search_id' => $search_id,
            'search_user_id' => $search_user_id,
            'search_active_code' => $search_active_code,
            'search_from' => $search_from,
            'search_to' => $search_to,
            'order_opt' => $order_opt,
            'order_by' => $order_by
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/activity', $body);
        $this->load->view('common/footer');
    }

    /**
     * 注文進捗一覧を表示する
     */
    public function orderlist() {
        log_message('debug', 'Operator/orderlist');
        $this->load->model('screen/SearchOrder_Model', 'search');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $header = array(
            'current_menu' => 'orderlist',
            'title' => '注文実績一覧',
            'role' => self::COMMON_MENU
        );

        if (isset($_POST['search'])) {
            // Search function
            $data = $_POST['search'];
        } else{
            //Default set create from to is one week.
            $data = array(
                'create_from' => date('Y/m/d', strtotime('-6 days')),
                'create_to' => date("Y/m/d")
            );
        }
        $search = new $this->search($data);

        // Load types
        $statuses = $this->general->get_Order_Status()->result();
        $types = $this->general->get_Payment_Method()->result();

        $target_status = array(
            $this->config->item('order_orderby_bank'),
            $this->config->item('order_orderby_btc')
        );

        $body = array(
            'orders' => $this->order->select_filter_order($search)->result(),
            'types' => $types,
            'statuses' => $statuses,
            'search' => $search
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/order', $body);
        $this->load->view('common/footer');
    }

    public function confirmOrder() {
        log_message('debug', 'Operator/confirmOrder');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $operator_uid = $this->getSessionValue('uid');

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $rownum = isset($_POST['rownum']) ? $_POST['rownum'] : 0;
        for ($i = 0; $i < $rownum; $i++) {
            switch ($_POST['approvalstatus' . $i]) {
                case "00":
                    // 未選択はスキップ
                    continue;

                case "01":
                    // 金額承認
                    // 銀行口座通知メール

                    $order = $this->order->select_by_ordernumberstatus($_POST['order_number'.$i], $this->config->item('order_orderby_bank'));
                    $orderd_amount = $order->amount;
                    $service_fee = doubleval($orderd_amount * (float) $this->config->item('charge') / 100);
                    $entry_amount = doubleval($orderd_amount + $service_fee);
                    $trade_law_url = $this->config->item('site_domain')."/law/";

                    $user = $this->user->get_user_by_userhash($order->client_uid);

                    $replace_token = array(
                        $user->family_name, $user->first_name, $order->order_number,
                        money_format_qts( $orderd_amount,2), $service_fee, money_format_qts( $entry_amount,2),
                        $trade_law_url, $this->getEmailSignature()
                    );
                    $email_template = $this->parseEmailTemplate('mail_notify_bankaccount', $replace_token);

                    $emailData = array(
                        'to' => $user->email,
                        'subject' => $email_template['subject'],
                        'message' => $email_template['message'],
                        'object' => $user->uid,
                        'memo' => 'Operator: confirm order.' . $email_template['subject'],
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->emailQueue->insert_data(array($emailData));

                    // 有効期限
                    $date = new DateTime();
                    $expiration_datetime = date($this->config->item('db_timestamp_format'), ($date->getTimestamp() + $this->config->item('order_expiration_time')));

                    // 口座情報を連絡した
                    $odata = array(
                        'order_number' => $order->order_number,
                        'status' => $this->config->item('order_notify_bankaccount'),
                        'agent_uid' => $order->agent_uid,
                        'client_uid' => $order->client_uid,
                        'pay_method' => $order->pay_method,
                        'currency_unit' => $order->currency_unit,
                        'amount' => $order->amount,
                        'expiration_date' => $expiration_datetime,
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $order_number = $this->order->insert_order($odata);
                    break;

                case $this->config->item('approval_invalid'):
                    // 無効
                    $data = array(
                        'status' => $this->config->item('approval_invalid'),
                        'update_by' => $operator_uid,
                        'update_at' => $now
                    );
                    $this->user->update_by_userhash($_POST['client_uid'.$i], $data);

                    $operator_hash = $this->getSessionValue('user_hash');
                    $adata = array(
                        'user_hash' => $operator_hash,
                        'activity_code' => $this->config->item('approval_invalid'),
                        'object' => $_POST['client_uid'.$i],
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->act->insert_activity($adata);
                    break;

                default:
            }
        }
        $this->safe_redirect('Operator/orderlist');
    }

    public function outputCsv_order() {
        log_message('debug', 'Operator/outputCsv_order');
        $this->load->model('screen/SearchOrder_Model', 'search');
        if (isset($_POST['search'])) {
            // Search function
            $data = $_POST['search'];
        } else{
            $data = array();
        }
        $search = new $this->search($data);
        $result = $this->order->select_all($search);
        $this->outputCsv($result, $this->config->item('csv_order'));
    }

    public function viewUserDetail($uid, $paramName = '', $paramValue = '') {
        log_message('debug', 'Operator/viewUserDetail');

        if ($paramName == 'update' && $paramValue == 'success') {
            $updateMessage = 'ユーザ情報は正常に更新されました';
        }

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        if (null == $uid) {
            $this->safe_redirect('Operator/home');
        }
        $user = $this->user->get_user_by_uid($uid);
        if ($user == null) {
            $this->safe_redirect('Operator/home');
        }

        $personal = null;
        if (null == $user->personal_id) {
            $personal = array(
                'create_at' => '--',
                'birthday' => '--',
                'country' => '--',
                'zip_code' => '--',
                'prefecture' => '--',
                'city' => '--',
                'building' => '--',
                'email' => '--',
                'tel' => '--',
                'imgfile' => '',
                'sex' => '--');
        } else {
            $personal = $this->personal->select_latestarray_by_key($user->personal_id);
        }
        $agents = $this->user->get_agents_chain($user->agent_uid);
        $orders = array(); // TODO
        $rolemap = $this->general->getRoleMap();
        $header = array(
            'current_menu' => 'home',
            'title' => 'ユーザー情報詳細',
            'role' => self::COMMON_MENU
        );
        $body = array(
            'user' => $user,
            'personal' => $personal,
            'agents' => $agents,
            'orders' => $orders,
            'updateMessage' => @$updateMessage,
            'rolemap' => $rolemap
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/viewUserDetail', $body);
        $this->load->view('common/footer');
    }

    /* Edit user account information */

    public function editUserDetail($uid) {
        log_message('debug', 'Operator/editUserDetail');
        $this->load->model('screen/RegisterUser_Model', 'regUser');
        $this->load->library('form_validation');

        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        if (null == $uid) {
            $this->safe_redirect('Operator/home');
        }
        $user = $this->user->get_user_by_uid($uid);
        if ($user == null) {
            $this->safe_redirect('Operator/home');
        }
        $arrFieldError = null;
        $regUser = null;
        if (isset($_POST['update_user'])) {
            $regUser = new $this->regUser($_POST);
            $checkEmail = $user->email != $regUser->email;
            $this->session->set_userdata('uid_editing', $uid);
            $this->session->set_userdata('role_editing', $user->role_code);
            foreach ($regUser->getRulesOpeEdit($checkEmail) as $name => $rule){
                $this->form_validation->set_rules($name, $rule['title'], $rule['rule_list']);
            }

            if ($this->form_validation->run() != FALSE) {
                $operator_uid = $this->getSessionValue('uid');
                $date = new DateTime();
                $now = date($this->config->item('session_timestamp_format'), $date->getTimestamp());
                $pdata = $regUser->getPersonalData_OpeEdit($operator_uid, $now);

                $personal_id = $user->personal_id;
                if (empty($user->personal_id)) {
                    $pdata['create_at'] = $now;
                    $pdata['create_by'] = $uid;
                    $pdata['delete_flag'] = Personal_model::VALID;
                    $personal_id = $this->personal->insert_personal($pdata);
                } else {
                    $this->personal->update_by_pid($user->personal_id, $pdata);
                }
                $regUser->personal_id = $personal_id;
                $udata = $regUser->getUserData_OpeEdit($operator_uid, $now);

                $this->user->update_by_userhash($user->user_hash, $udata);
                // save activity log
                $operator_hash = $this->getSessionValue('user_hash');
                // Save activity log
                $adata = array(
                    'user_hash' => $operator_hash,
                    'activity_code' => $this->config->item('act_edit_personal'),
                    'object' => $user->uid,
                    'create_by' =>  $operator_uid,
                    'create_at' => $now
                );
                if ($regUser->deleteImages){
                    $this->img->delete_images($regUser->deleteImages);
                }
                $this->act->insert_activity($adata);

                // 写真が無い場合は戻る
                $personal_id = $user->personal_id;
                log_message('debug', 'NEW PHOTO for ' . $personal_id);
                $formname = 'photo'; // ユーザー情報詳細のファイルインプットフォームの項目名
                $tempfilename = isset($_FILES[$formname]) ? $_FILES[$formname]['tmp_name'] : null;
                log_message('debug', '>> img | tempfilename=' . $tempfilename);
                if (null != $tempfilename) {
                    // 写真がある場合は保存する
                    $filenumber = intval($_POST['filenumber']) + 1;
                    $filename = $this->saveimage->makeImgFile($formname, $personal_id, $filenumber);
                    log_message('debug', 'NEW PHOTO is ' . $filename);
                }

                $this->safe_redirect('Operator/viewUserDetail/' . $uid . '/update/success');
            }
            $arrFieldError = $this->form_validation->error_array();
        }

        $user = $this->user->get_user_by_uid($uid);
        $personal = null;
        if (null == $user->personal_id) {
            $personal = array(
                'create_at' => '',
                'birthday' => '',
                'country' => '',
                'zip_code' => '',
                'prefecture' => '',
                'city' => '',
                'building' => '',
                'email' => '',
                'tel' => '',
                'imgfile' => '',
                'sex' => '--');
        } else {
            $personal = $this->personal->select_latestarray_by_key($user->personal_id);
        }
        $agents = $this->user->get_agents_chain($user->agent_uid);
        $orders = array(); // TODO
        $rolemap = $this->general->getRoleMap();
        $header = array(
            'current_menu' => 'home',
            'title' => 'ユーザー情報詳細',
            'role' => self::COMMON_MENU
        );
        $body = array(
            'user' => $user,
            'regUser' => $regUser,
            'personal' => $personal,
            'agents' => $agents,
            'orders' => $orders,
            'rolemap' => $rolemap,
            'arrFieldError' => $arrFieldError
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/editUserDetail', $body);
        $this->load->view('common/footer');
    }

    public function updateComment() {
        log_message('debug', 'Operator/updateComment');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $uid = isset($_POST['uid']) ? $_POST['uid'] : null;
        if (null == $uid) {
            $this->safe_redirect('Operator/login');
        }
        $this->user->update_comment_by_uid($uid, $_POST['comment']);

        // 個人情報が登録されていないアカウントは戻る
        $user = $this->user->get_user_by_uid($uid);
        if (null == $user || '' == $user->personal_id) {
            $this->safe_redirect('Operator/home');
        }

        // 写真が無い場合は戻る
        $personal_id = $user->personal_id;
        log_message('debug', 'NEW PHOTO for ' . $personal_id);
        $formname = 'photo'; // ユーザー情報詳細のファイルインプットフォームの項目名
        $tempfilename = isset($_FILES[$formname]) ? $_FILES[$formname]['tmp_name'] : null;
        log_message('debug', '>> img | tempfilename=' . $tempfilename);
        if (null == $tempfilename) {
            $this->safe_redirect('Operator/home');
        }

        // 写真がある場合は保存する
        $filenumber = intval($_POST['filenumber']) + 1;
        $filename = $this->saveimage->makeImgFile($formname, $personal_id, $filenumber);
        log_message('debug', 'NEW PHOTO is ' . $filename);

        $this->safe_redirect('Operator/home');
    }

    /* -------------------------------------------------- */

    public function viewRate() {
        log_message('debug', 'OperatorviewRate');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $header = array(
            'current_menu' => 'viewRate',
            'title' => '換金レート取得',
            'role' => self::COMMON_MENU
        );
        $body = array(
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/viewRate');
        $this->load->view('common/footer');
    }

    public function outputCsv_rate() {
        log_message('debug', 'Operator/outputCsv_rate');
        $from = isset($_POST['datefrom']) ? $_POST['datefrom'] : "";
        $to = isset($_POST['dateto']) ? $_POST['dateto'] : "";
        $result = $this->rate->select_all($from, $to);
        $this->outputCsv($result, $this->config->item('csv_rate'));
    }

    /*****************************************
     * CSV出力用
     *****************************************/

    public function outputCsv($arrayList, $filename) {
        log_message('debug', 'Operator/outputCsv');

        if (!$this->check_export_csv_permission()){
            return;
        }
        $this->load->database('token');
        $this->load->dbutil();

        $delimiter = $this->config->item('delimiter');
        $newline = $this->config->item('newline');

        $data = $this->dbutil->csv_from_result($arrayList, $delimiter, $newline);

        $activity_code = '';
        $activity_object = '';
        $activity_code = $this->config->item('act_csv_export');
        if ($filename == $this->config->item('csv_home')){
            $activity_object = $this->config->item('activity_csv_user');
        } else if ($filename == $this->config->item('csv_order')){
            $activity_object = $this->config->item('activity_csv_order');
        } else if ($filename == $this->config->item('csv_personal')){
            $activity_object = $this->config->item('activity_csv_personal');
        }else if ($filename == $this->config->item('csv_receipt_issue_token')){
            $activity_object = $this->config->item('activity_csv_receipt_issue_token');
        }else if ($filename == $this->config->item('csv_issue_redeem_token')){
            $activity_object = $this->config->item('activity_csv_issue_redeem_token');
        }else if ($filename == $this->config->item('csv_edit_issue_token')){
            $activity_object = $this->config->item('activity_csv_edit_redeem_token');
        }
        if ($activity_object != ''){
            $operator_uid = $this->getSessionValue('uid');
            $operator_hash = $this->getSessionValue('user_hash');
            $date = new DateTime();
            $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
            // Save activity log
            $adata = array(
                'user_hash' => $operator_hash,
                'activity_code' => $activity_code,
                'object' => $activity_object,
                'memo' => $filename,
                'create_by' =>  $operator_uid,
                'create_at' => $now
            );
            $this->act->insert_activity($adata);
        }

        $this->load->helper('file');
        $this->load->helper('download');
        force_download($filename, chr(239) . chr(187) . chr(191) . $data);
    }

    /*****************************************
     * Create Agent 20%
     *****************************************/

    public function linkAgent() {
        log_message('debug', 'Operator/linkAgent');

        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $header = array(//operatorLinkAgent
            'current_menu' => 'linkAgent',
            'title' => '代理店(20%)リンク作成',
            'role' => self::COMMON_MENU,
            'user_hash' => ""
        );
        $body = array();
        $this->load->view('common/header', $header);
        $this->load->view('operator/linkAgent', $body);
        $this->load->view('common/footer');
    }

    public function makeAgentLink() {
        log_message('debug', 'Operator/makeAgentLink');

        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $operator_uid = $this->getSessionValue('uid');

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
        $operator_hash = $this->getSessionValue('user_hash');

        $role_agent_arr = array_keys($this->config->item('role_agent_commission'));
        $role = $role_agent_arr[0]; // 20%代理店
        $data = array(
            'role' => $role,
            'agent_uid' => $operator_hash,
            'memo' => $_POST['memo']
        );
        $child_hash = $this->user->insert_user($data);
        log_message('debug', '$agent20%_uid=' . $child_hash);

        $adata = array(
            'user_hash' => $operator_hash,
            'activity_code' => $this->config->item('act_gen_link'),
            'object' => $child_hash,
            'memo' => $_POST['memo'],
            'create_by' => $operator_uid,
            'create_at' => $now
        );
        $this->act->insert_activity($adata);

        $header = array(
            'current_menu' => 'linkAgent',
            'title' => '代理店(20%)リンク作成',
            'role' => self::COMMON_MENU,
            'user_hash' => $child_hash
        );
        $body = array();
        $this->load->view('common/header', $header);
        $this->load->view('operator/linkAgent', $body);
        $this->load->view('common/footer');
    }


    public function confirmActivitylist() {
        log_message('debug', 'Operator/confirmActivitylist');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $arrMemoNum = $_POST['memoRow'];
        $arrActivity = $this->act->select_all()->result();

        if(!empty($arrMemoNum)){
            foreach($arrMemoNum as $i =>$act) {
                foreach($arrActivity as $j =>$act1) {
                    if($i != $j){
                        continue;
                    }
                    $memoNumInTable = $_POST['memo_' . $i];
                    if($memoNumInTable != $act1->memo){
                        //update memo.
                        $this->act->updateMemo($act1->activity_id, $memoNumInTable);
                    }
                }
            }
        }
        $this->safe_redirect('Operator/activitylist');
    }

    /**
     * Response json
     */
    private function returnJson($data) {
        log_message('debug', 'Operator/returnJson');

        header('Content-Type: application/json');
        echo json_encode($data);
        //exit;
    }

    /**
     * Synchronize block
     * Get last block from db -->dbBlockHeight
     * Get last block from from api -->apiBlockHeight
     * Compare height
     * If diff -->
     *      Insert block info to database(t_block)
     *      Get all tx of apiBlock --> tx
     *      Get all address from tx --> addresses
     *      If addresses exist in t_btc_address
     *          update address is use(status = 1)
     *          insert tx info to database(BtTxs)
     *          update block info to database(t_block)
     */
    public function syncBlock($height = 0){
        log_message('debug', 'Operator/syncBlock');
        $rs = array(
            'status' => 'fail',
            'message' => ''
        );

        log_message('debug', 'syncBlock BM START----------------');


        $totalBlocks = 0;
        $totalBlocksExecutionTime = 0;
        $totalRequests = 0;

        try {
            $lastBlockUrl = $this->config->item('block_explorer_url') . '/status?q=getLastBlockHash';
            $statUrl = $this->config->item('block_explorer_url') . '/sync';
            $blockUrl = $this->config->item('block_explorer_url') . '/block/';
            $blockHashUrl = $this->config->item('block_explorer_url') . '/block-index/';
            $txUrl = $this->config->item('block_explorer_url') . '/tx/';
            $dbBlockHeight = $this->config->item('start_blockheight');
            $hash = null;
            $block = null;

            $apiResponse = $this->getApi($lastBlockUrl);
            if(empty($apiResponse)) {
                log_message('error', "--------syncBlock: Cannot get data from API (1): $lastBlockUrl");
                log_message('error', "----------------totalBlocks: $totalBlocks");
                log_message('error', "----------------totalRequests: $totalRequests");
                log_message('error', "----------------totalBlocksExecutionTime: $totalBlocksExecutionTime");
                $rs['status'] = 'fail';
                $rs['message'] = 'Cannot get data from API (1)';
                return $this->returnJson($rs);
            }
            $lastBlockHash = json_decode($apiResponse)->lastblockhash;

            $apiResponse = $this->getApi($blockUrl . $lastBlockHash);
            if(empty($apiResponse)) {
                log_message('error', "--------syncBlock: Cannot get data from API (1): " . $blockUrl . $lastBlockHash);
                log_message('error', "----------------totalBlocks: $totalBlocks");
                log_message('error', "----------------totalRequests: $totalRequests");
                log_message('error', "----------------totalBlocksExecutionTime: $totalBlocksExecutionTime");
                $rs['status'] = 'fail';
                $rs['message'] = 'Cannot get data from API (2)';
                return $this->returnJson($rs);
            }
            $apiBlockHeight = json_decode($apiResponse)->height;
            $apiBlock = null;

            $limitBlockCheck = 10;
            $blockData = $this->block->get_data();
            if(!empty($blockData)) {
                $invalidBlockHashArr = array();
                foreach($blockData as $blockRow) {
                    $apiResponse = $this->getApi($blockHashUrl . $blockRow->height);
                    if(!empty($apiResponse)) {
                        $tmpBlockHash = json_decode($apiResponse)->blockHash;
                        if($tmpBlockHash == $blockRow->hash) {
                            // correct hash
                            $dbBlockHeight = $blockRow->height;
                            break;
                        }
                        else {
                            $invalidBlockHashArr[] = $blockRow->hash;
                            log_message('error', 'System data invalid => Block hash has changed: ' . $blockRow->hash);
                        }
                    }
                }
                if(!empty($invalidBlockHashArr)) {
                    $this->block->delete_by_hash($invalidBlockHashArr);
                }
            }

            $bm = array(
                'totalBlocks' => 0,
                'totalBlocksExecutionTime' => 0,
                'totalRequests' => 0
            );

            if((int) $height > 0) {
                $dbBlockHeight = (int) $height;
            }

            $applyLimitBlocks = true;
            if($applyLimitBlocks) {
                $limitBlocks = 4;
                if($apiBlockHeight - $dbBlockHeight > $limitBlocks) {
                    //$apiBlockHeight = $dbBlockHeight + $limitBlocks;
                }
            }

            if($dbBlockHeight < $apiBlockHeight) {
                for($i = $dbBlockHeight + 1; $i <= $apiBlockHeight; $i++){
                    if(!isset($bm[$i])) {
                        $bm[$i] = array();
                        $totalBlocks++;
                    }
                    $blockStartTime = microtime(TRUE);

                    // Get blockhash by block height
                    $apiResponse = $this->getApi($blockHashUrl. $i);
                    if(empty($apiResponse)) {
                        log_message('debug', "--------syncBlock: Cannot get data from API (3): " . $blockHashUrl. $i);
                        log_message('debug', "----------------totalBlocks: $totalBlocks");
                        log_message('debug', "----------------totalRequests: $totalRequests");
                        log_message('debug', "----------------totalBlocksExecutionTime: $totalBlocksExecutionTime");
                        $rs['status'] = 'fail';
                        $rs['message'] = 'Cannot get data from API (3)';
                        return $this->returnJson($rs);
                    }
                    $hash = json_decode($apiResponse)->blockHash;
                    $totalRequests += 1;

                    // Get block by hash
                    $apiResponse = $this->getApi($blockUrl . $hash);
                    if(empty($apiResponse)) {
                        log_message('debug', "--------syncBlock: Cannot get data from API (3): " . $blockUrl . $hash);
                        log_message('debug', "----------------totalBlocks: $totalBlocks");
                        log_message('debug', "----------------totalRequests: $totalRequests");
                        log_message('debug', "----------------totalBlocksExecutionTime: $totalBlocksExecutionTime");
                        $rs['status'] = 'fail';
                        $rs['message'] = 'Cannot get data from API (3)';
                        return $this->returnJson($rs);
                    }
                    $block = json_decode($apiResponse);
                    $totalRequests += 1;

                    // Insert block
                    $this->block->insert(array(
                        'hash' => $block->hash,
                        'height' => $block->height,
                        'txs' => implode($block->tx, ","),
                        'complete' => 0
                    ));

                    $allTxsStartTime = microtime(TRUE);
                    // Extract all address from block
                    for($j = 0; $j < sizeof($block->tx); $j++) {
                        $totalRequests += 1;
                        $txsData = $this->proccessTx($txUrl . $block->tx[$j]);
                        if(!empty($txsData)) {
                            $this->insertTxs($txsData);
                        }
                        else {
                            log_message('debug', "----------------**************totalBlocks: $totalBlocks");
                            log_message('debug', "----------------**************totalRequests: $totalRequests");
                            log_message('debug', "----------------**************totalBlocksExecutionTime: $totalBlocksExecutionTime");
                        }
                    }
                    $allTxsEndTime = microtime(TRUE);
                    $bm[$i]['block_txs_size'] = sizeof($block->tx);

                    // Update block status to completed
                    $this->block->update($block->hash, array('complete' => 1));

                    $blockEndTime = microtime(TRUE);
                    $bm[$i]['block_execution_time'] = ($blockEndTime - $blockStartTime);
                    $totalBlocksExecutionTime += ($blockEndTime - $blockStartTime);

                    log_message('debug', "BLOCK $i START:+++++++++++");
                    log_message('debug', json_encode($bm[$i]));
                    log_message('debug', "BLOCK $i END:+++++++++++");
                }
            }

            $bm['totalBlocks'] = $totalBlocks;
            $bm['totalBlocksExecutionTime'] = $totalBlocksExecutionTime;

            log_message('debug', "----------------totalBlocks: $totalBlocks");
            log_message('debug', "----------------totalRequests: $totalRequests");
            log_message('debug', "----------------totalBlocksExecutionTime: $totalBlocksExecutionTime");

            log_message('debug', 'syncBlock BM END----------------');

            // clean block data, keep only 10 records
            $bhArr = array();
            $blockData = $this->block->get_data();
            foreach($blockData as $blockRow) {
                $bhArr[] = $blockRow->hash;
            }
            if(!empty($bhArr)) {
                $this->block->delete_ignore_hash($bhArr);
            }

            // Check and send Refund
            $this->sendRefund();

            // Return completed
            $rs['status'] = 'success';
            $rs['message'] = 'Block already synchoronized';
        } catch (Exception $e){
            $rs['message'] = $e->getMessage();
        }

        return $this->returnJson($rs);
    }
	
	public function syncOneBlock($height = 0, $tx_id = ''){
        log_message('debug', 'Operator/syncBlock');
		if ($height == 0 || empty($tx_id))
			return;
		
        $rs = array(
            'status' => 'fail',
            'message' => ''
        );

        log_message('debug', 'syncBlock BM START----------------');


        $totalBlocks = 0;
        $totalBlocksExecutionTime = 0;
        $totalRequests = 0;

        try {
            $lastBlockUrl = $this->config->item('block_explorer_url') . '/status?q=getLastBlockHash';
            $statUrl = $this->config->item('block_explorer_url') . '/sync';
            $blockUrl = $this->config->item('block_explorer_url') . '/block/';
            $blockHashUrl = $this->config->item('block_explorer_url') . '/block-index/';
            $txUrl = $this->config->item('block_explorer_url') . '/tx/';
            $dbBlockHeight = $this->config->item('start_blockheight');
            $hash = null;
            $block = null;

            $apiResponse = $this->getApi($lastBlockUrl);
            if(empty($apiResponse)) {
                log_message('debug', "--------syncBlock: Cannot get data from API (1): $lastBlockUrl");
                log_message('debug', "----------------totalBlocks: $totalBlocks");
                log_message('debug', "----------------totalRequests: $totalRequests");
                log_message('debug', "----------------totalBlocksExecutionTime: $totalBlocksExecutionTime");
                $rs['status'] = 'fail';
                $rs['message'] = 'Cannot get data from API (1)';
                return $this->returnJson($rs);
            }
            $lastBlockHash = json_decode($apiResponse)->lastblockhash;

            $apiResponse = $this->getApi($blockUrl . $lastBlockHash);
            if(empty($apiResponse)) {
                log_message('debug', "--------syncBlock: Cannot get data from API (1): " . $blockUrl . $lastBlockHash);
                log_message('debug', "----------------totalBlocks: $totalBlocks");
                log_message('debug', "----------------totalRequests: $totalRequests");
                log_message('debug', "----------------totalBlocksExecutionTime: $totalBlocksExecutionTime");
                $rs['status'] = 'fail';
                $rs['message'] = 'Cannot get data from API (2)';
                return $this->returnJson($rs);
            }
            $apiBlockHeight = json_decode($apiResponse)->height;
            $apiBlock = null;

            $limitBlockCheck = 10;
            $blockData = $this->block->get_data();
            if(!empty($blockData)) {
                $invalidBlockHashArr = array();
                foreach($blockData as $blockRow) {
                    $apiResponse = $this->getApi($blockHashUrl . $blockRow->height);
                    if(!empty($apiResponse)) {
                        $tmpBlockHash = json_decode($apiResponse)->blockHash;
                        if($tmpBlockHash == $blockRow->hash) {
                            // correct hash
                            $dbBlockHeight = $blockRow->height;
                            break;
                        }
                        else {
                            $invalidBlockHashArr[] = $blockRow->hash;
                            log_message('debug', 'System data invalid => Block hash has changed: ' . $blockRow->hash);
                        }
                    }
                }
                if(!empty($invalidBlockHashArr)) {
                    $this->block->delete_by_hash($invalidBlockHashArr);
                }
            }

            $bm = array(
                'totalBlocks' => 0,
                'totalBlocksExecutionTime' => 0,
                'totalRequests' => 0
            );

            if((int) $height > 0) {
                $dbBlockHeight = (int) $height;
            }

            $applyLimitBlocks = true;
            if($applyLimitBlocks) {
                $limitBlocks = 4;
                if($apiBlockHeight - $dbBlockHeight > $limitBlocks) {
                    //$apiBlockHeight = $dbBlockHeight + $limitBlocks;
                }
            }

            if($dbBlockHeight < $apiBlockHeight) {
                //for($i = $dbBlockHeight + 1; $i <= $apiBlockHeight; $i++){
					$i = $dbBlockHeight;
                    if(!isset($bm[$i])) {
                        $bm[$i] = array();
                        $totalBlocks++;
                    }
                    $blockStartTime = microtime(TRUE);

                    // Get blockhash by block height
                    $apiResponse = $this->getApi($blockHashUrl. $i);
                    if(empty($apiResponse)) {
                        log_message('debug', "--------syncBlock: Cannot get data from API (3): " . $blockHashUrl. $i);
                        log_message('debug', "----------------totalBlocks: $totalBlocks");
                        log_message('debug', "----------------totalRequests: $totalRequests");
                        log_message('debug', "----------------totalBlocksExecutionTime: $totalBlocksExecutionTime");
                        $rs['status'] = 'fail';
                        $rs['message'] = 'Cannot get data from API (3)';
                        return $this->returnJson($rs);
                    }
                    $hash = json_decode($apiResponse)->blockHash;
                    $totalRequests += 1;

                    // Get block by hash
                    $apiResponse = $this->getApi($blockUrl . $hash);
                    if(empty($apiResponse)) {
                        log_message('debug', "--------syncBlock: Cannot get data from API (3): " . $blockUrl . $hash);
                        log_message('debug', "----------------totalBlocks: $totalBlocks");
                        log_message('debug', "----------------totalRequests: $totalRequests");
                        log_message('debug', "----------------totalBlocksExecutionTime: $totalBlocksExecutionTime");
                        $rs['status'] = 'fail';
                        $rs['message'] = 'Cannot get data from API (3)';
                        return $this->returnJson($rs);
                    }
                    $block = json_decode($apiResponse);
                    $totalRequests += 1;

                    // Insert block
                    $this->block->insert(array(
                        'hash' => $block->hash,
                        'height' => $block->height,
                        'txs' => implode($block->tx, ","),
                        'complete' => 0
                    ));

                    $allTxsStartTime = microtime(TRUE);
                    // Extract all address from block
                    for($j = 0; $j < sizeof($block->tx); $j++) {
                        $totalRequests += 1;
						if ($block->tx[$j] == $tx_id) {
							$txsData = $this->proccessTx($txUrl . $block->tx[$j]);
							if(!empty($txsData)) {
								$this->insertTxs($txsData);
							}
							else {
								log_message('debug', "----------------**************totalBlocks: $totalBlocks");
								log_message('debug', "----------------**************totalRequests: $totalRequests");
								log_message('debug', "----------------**************totalBlocksExecutionTime: $totalBlocksExecutionTime");
							}
						}                        
                    }
                    $allTxsEndTime = microtime(TRUE);
                    $bm[$i]['block_txs_size'] = sizeof($block->tx);

                    // Update block status to completed
                    $this->block->update($block->hash, array('complete' => 1));

                    $blockEndTime = microtime(TRUE);
                    $bm[$i]['block_execution_time'] = ($blockEndTime - $blockStartTime);
                    $totalBlocksExecutionTime += ($blockEndTime - $blockStartTime);

                    log_message('debug', "BLOCK $i START:+++++++++++");
                    log_message('debug', json_encode($bm[$i]));
                    log_message('debug', "BLOCK $i END:+++++++++++");
                //}
            }

            $bm['totalBlocks'] = $totalBlocks;
            $bm['totalBlocksExecutionTime'] = $totalBlocksExecutionTime;

            log_message('debug', "----------------totalBlocks: $totalBlocks");
            log_message('debug', "----------------totalRequests: $totalRequests");
            log_message('debug', "----------------totalBlocksExecutionTime: $totalBlocksExecutionTime");

            log_message('debug', 'syncBlock BM END----------------');

            // clean block data, keep only 10 records
            $bhArr = array();
            $blockData = $this->block->get_data();
            foreach($blockData as $blockRow) {
                $bhArr[] = $blockRow->hash;
            }
            if(!empty($bhArr)) {
                $this->block->delete_ignore_hash($bhArr);
            }

            // Check and send Refund
            $this->sendRefund();

            // Return completed
            $rs['status'] = 'success';
            $rs['message'] = 'Block already synchoronized';
        } catch (Exception $e){
            $rs['message'] = $e->getMessage();
        }

        return $this->returnJson($rs);
    }

    /**
     * Generate bitcoin address
     */
    public function createAddress(){
        log_message('debug', 'Operator/createAddress');

        $rs = array(
            'status' => 'fail',
            'message' => ''
        );

        try {
            // Get 20 addresses
            $url = $this->config->item('wallet-server') . '/create-address';
            $apiResponse = $this->getApi($url);
            if(empty($apiResponse)) {
                log_message('debug', "--------syncBlock: Cannot get data from API (5): " . $url);
                $rs['status'] = 'fail';
                $rs['message'] = 'Cannot get data from API (5)';
                return $this->returnJson($rs);
            }
            $res = json_decode($apiResponse);

            if($res->status == 'success'){
                for($i = 0; $i < sizeof($res->data); $i++){
                    $data = array(
                        'address' =>$res->data[$i]
                    );
                    $this->btcAddress->insert_addr($data);
                }
            }
            $rs['status'] = 'success';
            $rs['message'] = 'Insert address success';
        } catch (Exception $e){
            $rs['message'] = $e->getMessage();
        }

        log_message('debug', 'createAddress: ' . $rs['message']);

        return $this->returnJson($rs);
    }

    /**
     * Create get request
     */
    private function getApi($url){
        log_message('debug', '=========Operator/getApi => URL: ' . $url);
        try {
            $this->load->helper('http');
            $http = new Http();
            $http->setMethod('GET');
            $http->request($url);
            $rawResponse = $http->getResponse();
            if(empty($rawResponse)) {
                return NULL;
            }
            $response = json_decode($rawResponse);
            if(isset($response->status) && $response->status == '429') {
                log_message('debug', "Too many requests to URL: $url");
                return NULL;
            }
            return $rawResponse;
        }
        catch(Exception $ex) {
            return NULL;
        }
    }

    /**
     * Get tx from api and extract all address
     */
    private function proccessTx($txUrl){
        //log_message('debug', 'Operator/proccessTx');

        $data = $this->getApi($txUrl);

        if(empty($data)) {
            log_message('debug', "--------proccessTx: Cannot get data from API (4): " . $txUrl);
            return NULL;
        }

        $tx = json_decode($data);
        $items = array();

        // get addresses from vin
        $arrVinAddrs = array();
        for($i = 0; $i < sizeof($tx->vin); $i++) {
            $vin = $tx->vin[$i];
            if(isset($vin->addr) && !empty($vin->addr)){
                $arrVinAddrs[] = $vin->addr;
            }
        }

        for($i = 0; $i < sizeof($tx->vout); $i++) {
            if(isset($tx->vout[$i]->scriptPubKey->addresses)){
                for($j = 0; $j < sizeof($tx->vout[$i]->scriptPubKey->addresses); $j++){
                    $receiveAddr = $tx->vout[$i]->scriptPubKey->addresses[$j];
                    if(in_array($receiveAddr, $arrVinAddrs)) continue;

                    $item = array(
                        'tx_id' => $tx->txid,
                        'out_address' => $receiveAddr,
                        'balance' => $tx->vout[$i]->value,
                        'status' => 0,
                        'memo' => ''
                    );

                    array_push($items, $item);
                }
            }
        }

        $rs = array(
            'tx' => $data,
            'address' => $items
        );
        return $rs;
    }

    /**
     * Insert tx if address existed
     */
    private function insertTxs($data) {
        //log_message('debug', 'Operator/insertTxs');
        for($i = 0; $i < sizeof($data['address']); $i++) {
            // Check address in database
            $addrRow = $this->btcAddress->get_by_addr($data['address'][$i]['out_address']);
            // Check if client send BTC to the address before, if yes, do not insert
            $txRow = $this->txs->get_by_addr($data['address'][$i]['out_address']);
            if(sizeof($addrRow) > 0 ) {
                $ignoreThisTx = false;
                $isFirstTxs = false;
                if (empty($txRow)) {
                   $isFirstTxs = true;
                }
                else {
                    // check if tx is inserted before
                    foreach($txRow as $txDataRow) {
                        if($txDataRow->tx_id == $data['address'][$i]['tx_id']) {
                            $ignoreThisTx = true;
                            break;
                        }
                    }
                }
                if($ignoreThisTx) {
                    log_message('debug', 'Tx has been done before: ' . $data['address'][$i]['tx_id']);
                    continue; // it has been done before. do nothing.
                }

                $orderNumber = $addrRow[0]->order_number;

                // Check: for some reason, a new address record is inserted, the address has balance
                // but has not yet assigned to any order, this case will cause error.
                if(empty($orderNumber)) {
                    log_message('debug', 'order_numebr is empty. Address: ' . $data['address'][$i]['out_address']);
                    continue;
                }

                // Update address is used
                $this->btcAddress->update_by_addr($data['address'][$i]['out_address'], array('status'=>1));

                // Insert tx
                $tx = $data['address'][$i];
                $tx['tx_data'] = $data['tx'];
                $this->txs->insert($tx);

                // Check if address is used for paid by bank order
                $arrBankBtc = $this->bankBtcHeader->get_by_address($data['address'][$i]['out_address']);
                if(!empty($arrBankBtc)) {
                    // Process order for paid by BANK order
                    $this->processOrderPaidByBank($arrBankBtc, $data['address'][$i], $isFirstTxs);
                }
                else {
                    // Insert order for paid by BTC order
                    $this->insertOrderWhenAddressInVoutOfBlock($orderNumber, $data['address'][$i], $isFirstTxs);
                }

            }
        }
    }

    private function processOrderPaidByBank($arrBankBtc, $arrTxData, $isFirstTxs) {
        if($this->config->item('enable_banking') !== true) {
            return false;
        }

        if(empty($arrBankBtc) || empty($arrTxData)) {
            return false;
        }

        if($arrBankBtc->complete == '1') {
            log_message('debug', 'processOrderPaidByBank: ignore record that already completed');
            return false;
        }

        if($isFirstTxs !== true) {
            log_message('debug', 'processOrderPaidByBank: Operator sent from second time to this address: ' . $arrBankBtc->btc_address);
            return false;
        }

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $totalBTC = (float) $arrTxData['balance'];
        $totalJPY = (float) $arrBankBtc->total_jpy_amount;

        $JPY_to_BTC_rate = $totalBTC / $totalJPY;

        $arrOrders = $this->bankBtcDetail->get_data($arrBankBtc->btc_address);

        if(empty($arrOrders)) {
            log_message('debug', 'processOrderPaidByBank: Order (echo in t_bank_btc_details table) for this address is empty: ' . $arrBankBtc->btc_address);
            return false;
        }

        $tmpTotalBTC = 0;
        $lastOrderNumber = $arrOrders[count($arrOrders)-1]->order_number;
        foreach($arrOrders as $order) {
            $rate = (float) $order->jpy_amount / $totalJPY;
            $btcAmount = $totalBTC * $rate;
            $orderNumber = $order->order_number;
            if($lastOrderNumber == $orderNumber) {
                $btcAmount = $totalBTC - $tmpTotalBTC;
            }
            $tmpTotalBTC += $btcAmount;
            $exchangeRate = $btcAmount / (float) $order->jpy_amount;
            // insert the same data as confirmExchangedBtc
            $this->insertExchangedBtcOrder($orderNumber, $btcAmount, $now);
            // update details
            $this->bankBtcDetail->update_by_ordernumber($orderNumber, array('btc_amount' => $btcAmount, 'rate' => $exchangeRate));
        }

        $data = array(
            'total_btc_amount' => $totalBTC,
            'rate' => $JPY_to_BTC_rate,
            'tx_id' => $arrTxData['tx_id'],
            'complete' => 1
        );
        // update header
        $this->bankBtcHeader->update_by_address($arrBankBtc->btc_address, $data);

        return true;
    }

    /**
     * As confirm banking function. This function will automatically insert 1 row to table order0
     */
    private function insertOrderWhenAddressInVoutOfBlock($orderNumber, $arrTxData, $isFirstTxs) {
        log_message('debug', 'Operator/insertOrderWhenAddressInVoutOfBlock');

        // Get order
        $row = $this->order->select_by_ordernumber($orderNumber);

        if(empty($row)) {
            return false;
        }

        //
        $isExpired = false;
        $nowTs = strtotime(date("Y-m-d"));
        if($nowTs > strtotime($row->expiration_date)) {
            $isExpired = true;
        }

        //
        $refund_status = $this->config->item('refund_status');

        if($isFirstTxs) {
            if($isExpired) {
                // insert expired Order
                $this->insertExpiredOrder($row->order_number);
                // Insert data for refund when Order is expired
                $this->createRefundData($orderNumber, $arrTxData, $refund_status['expired_date']);
                return false;
            }

            // Insert Order if the order is not expired
            $this->insertBtcOrder($row->order_number, $arrTxData['balance']);
        }
        else {
            // Insert data for refund when client send from second times
            $this->createRefundData($orderNumber, $arrTxData, $refund_status['send_from_2nd']);
        }

        //@hoinh add new feature send email 20160808
        // modified by @ngocdt:
        // Only send email when:
        // - client sent BTC first time but the order is not expired.
        // - OR client sent BTC from the second time.
        $this->sendMailToUserWhenReceivedBTC($orderNumber, $arrTxData, $isFirstTxs);

        return true;
    }

    private function insertBtcOrder($orderNumber, $btcAmount) {
        log_message('debug', 'Operator/insertBtcOrder');

        // Get order
        $row = $this->order->select_by_ordernumber($orderNumber);

        if(empty($row)) {
            return false;
        }

        $date = new DateTime();
        $now = date($this->config->item('session_timestamp_format'), $date->getTimestamp());

        $operator_uid = $this->getSessionValue('uid');
        if(empty($operator_uid)) {
            $operator_uid = $this->config->item('AUTO_ID');
        }

        $operator_hash = $this->getSessionValue('user_hash');
        if(empty($operator_hash)) {
            $operator_hash = sha1($operator_uid);
        }

        $odata = array(
            'order_number' => $row->order_number,
            'status' => $this->config->item('order_receiveby_btc'),
            'agent_uid' => $row->agent_uid,
            'client_uid' => $row->client_uid,
            'received' => '01',
            'pay_method' => $row->pay_method,
            'currency_unit' => $this->config->item('currency_btc'),
            'amount' => $btcAmount,
            'expiration_date' => $row->expiration_date,
            'create_by' => $operator_uid,
            'create_at' => $now
        );

        // Insert order
        $this->order->insert_order($odata);

        //Insert activity
        $adata = array(
            'user_hash' => $operator_hash,
            'activity_code' => $this->config->item('act_btc_amount'),
            'object' => $row->order_number,
            'memo' => "BTCの受取: $btcAmount",
            'create_by' => $operator_uid,
            'create_at' => $now
        );
        $this->act->insert_activity($adata);

        // トークンを発行する
        $date = new DateTime();
        $txtime = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
        $this->exchanger->calc_token($row->order_number, $row->create_at, $txtime);

        return true;
    }

    private function insertExpiredOrder($orderNumber) {
        log_message('debug', 'Operator/insertExpiredOrder');

        // Check if insert before
        $tmpArr = $this->order->select_by_ordernumberstatus($orderNumber, $this->config->item('order_expired'));
        if(!empty($tmpArr)) {
            return false;
        }

        // Get order
        $row = $this->order->select_by_ordernumber($orderNumber);

        if(empty($row)) {
            return false;
        }

        $date = new DateTime();
        $now = date($this->config->item('session_timestamp_format'), $date->getTimestamp());

        $operator_uid = $this->getSessionValue('uid');
        if(empty($operator_uid)) {
            $operator_uid = $this->config->item('AUTO_ID');
        }

        $operator_hash = $this->getSessionValue('user_hash');
        if(empty($operator_hash)) {
            $operator_hash = sha1($operator_uid);
        }

        $odata = array(
            'order_number' => $row->order_number,
            'status' => $this->config->item('order_expired'),
            'agent_uid' => $row->agent_uid,
            'client_uid' => $row->client_uid,
            'pay_method' => $row->pay_method,
            'currency_unit' => $row->currency_unit,
            'amount' => $row->amount,
            'expiration_date' => $row->expiration_date,
            'create_by' => $operator_uid,
            'create_at' => $now
        );
        // Insert order
        $this->order->insert_order($odata);

        //Insert activity
        $adata = array(
            'user_hash' => $operator_hash,
            'activity_code' => $this->config->item('order_expired'),
            'object' => $row->order_number,
            'memo' => "Order is expired.",
            'create_by' => $operator_uid,
            'create_at' => $now
        );
        $this->act->insert_activity($adata);

        return true;
    }

    private function insertCancelledOrder($orderNumber) {
        log_message('debug', 'Operator/insertCancelledOrder');

        // Check if insert before
        $tmpArr = $this->order->select_by_ordernumberstatus($orderNumber, $this->config->item('order_invalid'));
        if(!empty($tmpArr)) {
            return false;
        }

        // Get order
        $row = $this->order->select_by_ordernumber($orderNumber);

        if(empty($row)) {
            return false;
        }

        $date = new DateTime();
        $now = date($this->config->item('session_timestamp_format'), $date->getTimestamp());

        $operator_uid = $this->getSessionValue('uid');
        if(empty($operator_uid)) {
            $operator_uid = $this->config->item('AUTO_ID');
        }

        $operator_hash = $this->getSessionValue('user_hash');
        if(empty($operator_hash)) {
            $operator_hash = sha1($operator_uid);
        }

        $odata = array(
            'order_number' => $row->order_number,
            'status' => $this->config->item('order_invalid'),
            'agent_uid' => $row->agent_uid,
            'client_uid' => $row->client_uid,
            'pay_method' => $row->pay_method,
            'currency_unit' => $row->currency_unit,
            'amount' => $row->amount,
            'expiration_date' => $row->expiration_date,
            'create_by' => $operator_uid,
            'create_at' => $now
        );
        // Insert order
        $this->order->insert_order($odata);

        //Insert activity
        $adata = array(
            'user_hash' => $operator_hash,
            'activity_code' => $this->config->item('order_invalid'),
            'object' => $row->order_number,
            'memo' => "Order cancelled",
            'create_by' => $operator_uid,
            'create_at' => $now
        );
        $this->act->insert_activity($adata);

        return true;
    }

    private function createRefundData($orderNumber, $arrTxData, $status) {
        log_message('debug', 'Operator/createRefundData');

        if(empty($orderNumber) || !is_array($arrTxData) || empty($arrTxData)) {
            return false;
        }

        $date = new DateTime();
        $now = date($this->config->item('session_timestamp_format'), $date->getTimestamp());

        $operator_uid = $this->getSessionValue('uid');
        if(empty($operator_uid)) {
            $operator_uid = $this->config->item('AUTO_ID');
        }

        $operator_hash = $this->getSessionValue('user_hash');
        if(empty($operator_hash)) {
            $operator_hash = sha1($operator_uid);
        }

        $refund_oper_status = $this->config->item('refund_oper_status');
        $refund_sent_status = $this->config->item('refund_sent_status');

        $orderData = $this->order->select_by_ordernumber($orderNumber);

        if(empty($orderData)) {
            return false;
        }

        $arrRefundData = array(
            'order_number' => $orderData->order_number,
            'client_uid' => $orderData->client_uid,
            'tx_id' => $arrTxData['tx_id'],
            'btc_address' => $arrTxData['out_address'],
            'btc_amount' => $arrTxData['balance'],
            'status' => $status,
            'oper_status' => $refund_oper_status['unconfirm'],
            'sent_status' => $refund_sent_status['fail'],
            'create_by' => $operator_uid,
            'create_at' => $now
        );

        return $this->refund->insert_refund_data($arrRefundData);
    }

    private function sendRefund() {
        log_message('debug', 'Operator/sendRefund');

        $refund_status = $this->config->item('refund_status');
        $refund_oper_status = $this->config->item('refund_oper_status');
        $refund_sent_status = $this->config->item('refund_sent_status');

        $limit_records = (int) $this->config->item('limit_records');

        $this->load->model('screen/SearchRefund_Model', 'search');
        $search = new $this->search(array('sent_status' => $refund_sent_status['fail']));

        $totalRefundAmount = 0;
        $arrIds = array();
        $arrRefundData = $this->refund->get_refund_list($search);
        foreach($arrRefundData as $refundIndex => $refundData) {
            $totalRefundAmount += $refundData->btc_amount;
            $arrIds[] = $refundData->id;
            if($limit_records > 0 && $refundIndex >= $limit_records - 1) {
                break;
            }
        }

        // send refund to operator
        $sentStatus = 'fail';
        if($totalRefundAmount > 0) {
            $comment = !empty($this->config->item('refund_wallet_message')) ? "/" . rawurlencode($this->config->item('refund_wallet_message')) : NULL;
            $apiUrl = $this->config->item('wallet-server') . "/refund/$totalRefundAmount".$comment;

            $response = $this->getApi($apiUrl);
            log_message('debug', 'Send refund result: ' . json_encode($response));
            if(!empty($response)) {
                $response = json_decode($response);
                if(is_object($response) && isset($response->status)) {
                    $sentStatus = $response->status == 'success' ? $response->status : @$response->code;
                }
            }
        }

        // update sent_status if sent successfully
        if($sentStatus == 'success' && count($arrIds) > 0) {
            $this->refund->update_sent_status($arrIds, $refund_sent_status['success']);
        }

        return $sentStatus;
    }

    /**
     * Send mail to user when user rereived BTC
     */
    private function sendMailToUserWhenReceivedBTC($orderNumber, $txsInfo, $isFirstTransaction){
        log_message('debug', "Operator/sendMailToUserWhenReceivedBTC");
        $order = $this->order->select_by_ordernumber($orderNumber);
        $user_hash = $order->client_uid;
        $btcAddress = $txsInfo['out_address'];
        $btcAmount = $txsInfo['balance'];

        if(null ==$user_hash || ''==$user_hash){
            return;
        }
        if ($isFirstTransaction){
            // First time send notification email
            log_message('debug', "sendMailBTC1st userhash $user_hash isFirstTransaction $isFirstTransaction");
            $email_key = 'mail_notify_receivedbtc';
        } else {
            // Second time send notification email
            log_message('debug', "sendMailBTC2st userhash $user_hash isFirstTransaction $isFirstTransaction");
            $email_key = 'mail_notify_receivedbtc2nd';
        }
        $user = $this->user->get_user_by_userhash($user_hash);
        if ($user != null) {
            $date = new DateTime();
            $now = date($this->config->item('session_timestamp_format'), $date->getTimestamp());

            $operator_uid = $this->getSessionValue('uid');
            if(empty($operator_uid)) {
                $operator_uid = $this->config->item('AUTO_ID');
            }

            $operator_hash = $this->getSessionValue('user_hash');
            if(empty($operator_hash)) {
                $operator_hash = sha1($operator_uid);
            }

            $replace_token = array(
                $user->family_name, $user->first_name, 
                $orderNumber, $btcAmount
            );
            if($isFirstTransaction) {
                $replace_token[] = $btcAddress;
            }
            $replace_token[] = $this->getEmailSignature();
            $email_template = $this->parseEmailTemplate($email_key, $replace_token);
            $emailData = array(
                'to' => $user->email,
                'subject' => $email_template['subject'],
                'message' => $email_template['message'],
                'object' => $orderNumber,
                'memo' => 'Auto wallet: Receive BTC amount. ' . $email_template['subject'],
                'create_by' => $operator_uid,
                'create_at' => $now
            );
            $this->emailQueue->insert_data(array($emailData));
        }
    }

    /*-------------------------------------------------------------------------------------------------*/

    /**
     * Auto input btc addr from crontab
     */
    public function autoInputBtcAddr() {
        log_message('debug', 'Operator/autoInputBtcAddr');

        $rs = array(
            'status' => 'success',
            'message' => ''
        );

        $amount_rules = $this->config->item('amount_rules');
        $min_amount = $amount_rules['min_amount'];
        $max_amount = $amount_rules['max_amount'];
        $monthly_amount = $amount_rules['monthly_amount'];

        // BTC払い一覧取得
        $target_status = array(
            $this->config->item('order_orderby_btc')
        );

        // Get payment by bitcoin order list
        $orderlist = $this->order->select_latest_list($target_status);
        log_message('debug', 'TaiPA : orderlist size = ' . sizeof($orderlist));

        // TODO: Get list of unuse btc address
        $unuseAddress = $this->btcAddress->get_unuse_addr(sizeof($orderlist));

        if(empty($unuseAddress)) {
            log_message('debug', 'Trying to autoInputBtcAddr but NOT success because no address available');
            return $this->returnJson(array('status' => 'fail', 'message' => 'No address available'));
        }

        $counter = 0;

        // Assign btc address to each order
        for ($i=0; $i < sizeof($orderlist); $i++) {
            // check rsv_char_2 is setted when reorderconfirm
            if ($orderlist[$i]->rsv_char_2 != null){
                $orderlist[$i]->amount= doubleval($orderlist[$i]->rsv_char_2);
            }

            // Add new order record
            $this->insertOrderWhenSetBtcAddr($orderlist[$i]->order_number, $unuseAddress[$i]->address);

            // TODO: update order_number to used btc_address record
            $data = array(
                'order_number' => $orderlist[$i]->order_number,
                'status' => 1
            );
            $this->btcAddress->update_by_addr($unuseAddress[$i]->address, $data);

            // Send mail
            log_message('debug', 'TaiPA : autoInputBtcAddr success for client_uid = ' . $orderlist[$i]->client_uid);
            $this->sendMailWhenSetBtcAddr($orderlist[$i]->client_uid, $orderlist[$i]->order_number);

            $counter++;

            // Insert action to Activity table
        }

        if($counter == 0 && sizeof($orderlist) > 0) {
            $rs = array(
                'status' => 'fail',
                'message' => 'No order effect'
            );
        }

        return $this->returnJson($rs);
    }

    /**
     * Send mail when seting btc address for a client's order.
     */
    private function sendMailWhenSetBtcAddr($user_hash, $order_number) {
        log_message('debug', 'Operator/sendMailWhenSetBtcAddr');

        $user = $this->user->get_user_by_userhash($user_hash);
        if ($user != null) {
            $operator_uid = $this->getSessionValue('uid');
            if(empty($operator_uid)) {
                $operator_uid = $this->config->item('AUTO_ID');
            }

            $operator_hash = $this->getSessionValue('user_hash');
            if(empty($operator_hash)) {
                $operator_hash = sha1($operator_uid);
            }

            $date = new DateTime();
            $now = date($this->config->item('session_timestamp_format'), $date->getTimestamp());

            $view_btc_addr_url = base_url('Client/viewBtcAddr/' . $order_number . "/" . $user->user_hash);
            $replace_token = array(
                $user->family_name, $user->first_name,
                $view_btc_addr_url,
                $this->getEmailSignature()
            );
            $email_template = $this->parseEmailTemplate('mail_notify_btcaddr', $replace_token);
            $emailData = array(
                'to' => $user->email,
                'subject' => $email_template['subject'],
                'message' => $email_template['message'],
                'object' => $order_number,
                'memo' => 'Auto wallet: Notify client order by BTC',
                'create_by' => $operator_uid,
                'create_at' => $now
            );
            $this->emailQueue->insert_data(array($emailData));
        }
    }

    /**
     * Add record to t_order when set btc_address
     */
    private function insertOrderWhenSetBtcAddr($order_number, $btc_addr) {
        log_message('debug', 'Operator/insertOrderWhenSetBtcAddr');

        $row = $this->order->select_by_ordernumber($order_number);
        if ($row != null) {
            $date = new DateTime();
            $now = date($this->config->item('session_timestamp_format'), $date->getTimestamp());
            $expiration_datetime = date($this->config->item('db_timestamp_format'), ($date->getTimestamp() + $this->config->item('order_expiration_time')));

            // TODO: Need confirm what value will set to create_by field.
            $operator_uid = $this->config->item('AUTO_ID');
            $operator_hash = sha1($operator_uid);
            // check rsv_char_2 is setted when reorderconfirm
            if ($row->rsv_char_2 != null){
                $row->amount= doubleval($row->rsv_char_2);
            }
            $odata = array(
                'order_number' => $row->order_number,
                'status' => $this->config->item('order_notify_btcaddr'),
                'agent_uid' => $row->agent_uid,
                'client_uid' => $row->client_uid,
                'pay_method' => $row->pay_method,
                'currency_unit' => $row->currency_unit,
                'amount' => $row->amount,
                'receive_address' => $btc_addr,
                'expiration_date' => $expiration_datetime,
                'create_by' => $operator_uid,
                'create_at' => $now
            );

            $this->order->insert_order($odata);

            // Insert activity
            $adata = array(
                'user_hash' => $operator_hash,
                'activity_code' => $this->config->item('act_notify_btcaddr'),
                'object' => $row->order_number,
                'memo' => $btc_addr,
                'create_by' => $operator_uid,
                'create_at' => $now
            );
            $this->act->insert_activity($adata);
        }
    }

    /*-------------------------------------------------------------------------------------------------*/

    public function makeClosedOrder($paginationOffset=0, $status=NULL, $statusValue=NULL) {
        log_message('debug', 'Operator/makeClosedOrder');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $autoWalletResponse = $this->config->item('auto_wallet_response');
        $statusMessage = '';
        if($status == 'status') {
            $statusMessage = @$autoWalletResponse[$statusValue];
        }

        $header = array(
            'current_menu' => 'makeClosedOrder',
            'title' => '締め処理',
            'role' => self::COMMON_MENU
        );

        $paginationLimit = (int) $this->config->item('limit_records');
        $paginationOffset = (int) $paginationOffset > 0 ? (int) $paginationOffset : 0;

        $arrAllData = $this->tokenApproved->get_unsent_list();

        $arrData = $this->tokenApproved->get_unsent_list($paginationLimit, $paginationOffset);

        $this->load->library('pagination');

        $paginationConfig['base_url'] = base_url('Operator/makeClosedOrder');
        $paginationConfig['total_rows'] = count($arrAllData);
        $paginationConfig['per_page'] = $paginationLimit;
        $paginationConfig['uri_segment'] = 3 ;

        $this->pagination->initialize($paginationConfig);

        $body = array(
            'arrData' => $arrData,
            'statusMessage' => $statusMessage,
            'paginationLinks' => $this->pagination->create_links(),
            'paginationOffset' => $paginationOffset
        );

        $this->load->view('common/header', $header);
        $this->load->view('operator/closedOrder', $body);
        $this->load->view('common/footer');
    }

    public function confirmClosedOrder() {
        log_message('debug', 'Operator/confirmClosedOrder');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $paginationLimit = (int) $this->config->item('limit_records');
        $paginationOffset = (int) @$_POST['paginationOffset'];
        $paginationOffset = $paginationOffset > 0 ? $paginationOffset : 0;

        if(isset($_POST['order_number']) && is_array($_POST['order_number']) && !empty($_POST['order_number'])) {
            $operator_uid = $this->getSessionValue('uid');

            $result = $this->closedOrderSummary->create_incompleted_orders($_POST['order_number'], $operator_uid);
            $this->sendBtcForClosedOrders();

            if($result['status'] == 'success') {
                $paginationOffset -= $paginationLimit;
                $paginationOffset = $paginationOffset > 0 ? $paginationOffset : 0;
            }
        }

        $this->safe_redirect("Operator/makeClosedOrder/$paginationOffset");
    }

    /*-------------------------------------------------------------------------------------------------*/

    private function makeApprovedToken($arrOrderNumber = array()) {
        log_message('debug', 'Operator/makeApprovedToken');

        if(!is_array($arrOrderNumber) || empty($arrOrderNumber)) {
            return false;
        }

        $arrOrderData = $this->order->selectOrderForApprovedToken($arrOrderNumber);

        if(empty($arrOrderData)) {
            return false;
        }

        $operator_uid = $this->getSessionValue('uid');

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        foreach($arrOrderData as $key => $value) {
            $arrOrderData[$key] = array_merge($value, array(
                'create_by' => $operator_uid,
                'create_at' => $now
            ));
        }

        // Insert into DB
        return $this->tokenApproved->insert_approved_tokens($arrOrderData);
    }

    public function viewApprovedToken($status=NULL, $statusValue=NULL) {
        log_message('debug', 'Operator/viewApprovedToken');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $autoWalletResponse = $this->config->item('auto_wallet_response');
        $statusMessage = '';
        if($status == 'status') {
            $statusMessage = @$autoWalletResponse[$statusValue];
        }

        $this->load->model('screen/SearchApprovedToken_Model', 'search');
        $search = new $this->search((array) @$_POST['search']);

        $header = array(
            'current_menu' => 'viewApprovedToken',
            'title' => 'BTC送信対象',
            'role' => self::COMMON_MENU
        );

        $arrData = $this->closedOrderSummary->get_data($search);

        $body = array(
            'arrData' => $arrData,
            'statusMessage' => $statusMessage,
            'search' => $search
        );

        $this->load->view('common/header', $header);
        $this->load->view('operator/approvedToken', $body);
        $this->load->view('common/footer');
    }

    public function getCommissionData($datetime){
        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $data = $this->closedOrderSummary->getCommissionData($datetime);
        $body = array(
            'data' =>$data
        );
        $this->load->view('operator/commissionDataTable', $body);
    }

    public function confirmApprovedToken() {
        log_message('debug', 'Operator/confirmApprovedToken');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $this->sendBtcForClosedOrders();

        $this->safe_redirect('Operator/viewApprovedToken');
    }

    private function sendBtcForClosedOrders() {
        log_message('debug', 'Operator/sendBtcForClosedOrders');

        $arrData = $this->closedOrderSummary->get_data_to_send();

        if(empty($arrData)) {
            return array(
                'status' => 'success',
                'message' => 'No data to send.'
            );
        }

        foreach($arrData as $row) {
            $totalHotBtcAmount = 0;
            $totalColdBtcAmount = 0;
            $totalCommissionBtcAmount = 0;
            $totalSpecialCommissionBtcAmount = 0;
            $hotColdDateArr = array();
            $commDateArr = array();
            $speCommDateArr = array();

            if($row->hot_cold_sent_status == 0 && $row->total_hot_wallet_btc_amount > 0 && $row->total_cold_wallet_btc_amount > 0) {
                $totalHotBtcAmount += $row->total_hot_wallet_btc_amount;
                $totalColdBtcAmount += $row->total_cold_wallet_btc_amount;
                $hotColdDateArr[] = $row->closed_date;
            }
            if($row->commission_sent_status == 0 && $row->total_commission_btc_amount > 0) {
                $totalCommissionBtcAmount += $row->total_commission_btc_amount;
                $commDateArr[] = $row->closed_date;
            }
            if($row->special_commission_sent_status == 0 && $row->total_special_commission_btc_amount > 0) {
                $totalSpecialCommissionBtcAmount += $row->total_special_commission_btc_amount;
                $speCommDateArr[] = $row->closed_date;
            }

            $this->doSendBtcForClosedOrders($totalHotBtcAmount, $totalColdBtcAmount, $totalCommissionBtcAmount,
                $totalSpecialCommissionBtcAmount, $hotColdDateArr, $commDateArr, $speCommDateArr);
        }

        return array(
            'status' => 'success',
            'message' => 'View logs for more details.'
        );
    }

    private function doSendBtcForClosedOrders($totalHotBtcAmount, $totalColdBtcAmount, $totalCommissionBtcAmount,
        $totalSpecialCommissionBtcAmount, $hotColdDateArr, $commDateArr, $speCommDateArr) {

        $this->load->helper('http');

        // check and send HOT & COLD
        if($totalHotBtcAmount > 0 && $totalColdBtcAmount > 0) {
            $apiData = array(
                "hotWalletBtcAmount" => $totalHotBtcAmount,
                "coldWalletBtcAmount" => $totalColdBtcAmount
            );

            if(!empty($this->config->item('hot_wallet_message'))) {
                $apiData['hotWalletComment'] = $this->config->item('hot_wallet_message');
            }

            if(!empty($this->config->item('cold_wallet_message'))) {
                $apiData['coldWalletComment'] = $this->config->item('cold_wallet_message');
            }

            if(!empty($this->config->item('hot_cold_wallet_general_message'))) {
                $apiData['comment'] = $this->config->item('hot_cold_wallet_general_message');
            }

            $apiData = json_encode($apiData);

            $apiUrl = $this->config->item('wallet-server') . '/send-hot-cold-btc';

            $http = new Http();
            $response = $http->postJSON($apiUrl, $apiData);
            $sentStatus = 'fail';
            if(!empty($response)) {
                $response = json_decode($response);
                if(is_object($response) && isset($response->status)) {
                    $sentStatus = $response->status == 'success' ? $response->status : @$response->code;
                }
            }
            if($sentStatus == 'success') {
                // update status
                $this->closedOrderSummary->update_data($hotColdDateArr, array('hot_cold_sent_status' => 1));
            }
            else {
                log_message('debug', 'sendBtcForClosedOrders: Send HOT & COLD failed. Request data: ' . $apiData);
                log_message('debug', 'sendBtcForClosedOrders: Send HOT & COLD failed. Response data: ' . json_encode($response));
            }
        }

        // check and send Commission
        if($totalCommissionBtcAmount > 0) {
            $comment = !empty($this->config->item('operator_wallet_message')) ? "/" . rawurlencode($this->config->item('operator_wallet_message')) : NULL;
            $apiUrl = $this->config->item('wallet-server') . "/send-commission/$totalCommissionBtcAmount".$comment;

            $response = $this->getApi($apiUrl);
            $sentStatus = 'fail';
            if(!empty($response)) {
                $response = json_decode($response);
                if(is_object($response) && isset($response->status)) {
                    $sentStatus = $response->status == 'success' ? $response->status : @$response->code;
                }
            }
            if($sentStatus == 'success') {
                // update status
                $this->closedOrderSummary->update_data($commDateArr, array('commission_sent_status' => 1));
            }
            else {
                log_message('debug', 'sendBtcForClosedOrders: Send Commission failed. Send Amount: ' . $totalCommissionBtcAmount);
                log_message('debug', 'sendBtcForClosedOrders: Send Commission failed. Response data: ' . json_encode($response));
            }
        }

        // check and send Special Commission
        if($totalSpecialCommissionBtcAmount > 0) {
            $comment = !empty($this->config->item('special_wallet_message')) ? "/" . rawurlencode($this->config->item('special_wallet_message')) : NULL;
            $apiUrl = $this->config->item('wallet-server') . "/send-special-commission/$totalSpecialCommissionBtcAmount".$comment;

            $response = $this->getApi($apiUrl);
            $sentStatus = 'fail';
            if(!empty($response)) {
                $response = json_decode($response);
                if(is_object($response) && isset($response->status)) {
                    $sentStatus = $response->status == 'success' ? $response->status : @$response->code;
                }
            }
            if($sentStatus == 'success') {
                // update status
                $this->closedOrderSummary->update_data($speCommDateArr, array('special_commission_sent_status' => 1));
            }
            else {
                log_message('debug', 'sendBtcForClosedOrders: Send Special Commission failed. Send Amount: ' . $totalSpecialCommissionBtcAmount);
                log_message('debug', 'sendBtcForClosedOrders: Send Special Commission failed. Response data: ' . json_encode($response));
            }
        }
    }

    public function viewRefunds($status=NULL, $statusValue=NULL) {
        log_message('debug', 'Operator/viewRefunds');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $autoWalletResponse = $this->config->item('auto_wallet_response');
        $statusMessage = '';
        if($status == 'status') {
            $statusMessage = @$autoWalletResponse[$statusValue];
        }

        $header = array(
            'current_menu' => 'viewRefunds',
            'title' => '返金対象一覧',
            'role' => self::COMMON_MENU
        );

        $refund_sent_status = $this->config->item('refund_sent_status');

        $this->load->model('screen/SearchRefund_Model', 'search');
        $search = new $this->search((array) @$_POST['search']);

        $arrData = $this->refund->get_refund_list($search);

        $body = array(
            'arrData' => $arrData,
            'statusMessage' => $statusMessage
        );

        $this->load->view('common/header', $header);
        $this->load->view('operator/viewRefunds', $body);
        $this->load->view('common/footer');
    }

    public function confirmRefunds() {
        log_message('debug', 'Operator/confirmRefunds');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $operator_uid = $this->getSessionValue('uid');

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        if(isset($_POST['update_refund'])) {
            foreach(@$_POST['status'] as $id => $operStatus) {
                if($operStatus != '00') {
                    $this->refund->update_data($id, array(
                        'oper_status' => $operStatus,
                        'update_by' => $operator_uid,
                        'update_at' => $now
                    ));
                }
            }
        }

        $qs = NULL;
        if(isset($_POST['resend_refund'])) {
            $sentStatus = $this->sendRefund();
            $qs = "/status/$sentStatus";
        }

        $this->safe_redirect('Operator/viewRefunds' . $qs);
    }

    /*-------------------------------------------------------------------------------------------------*/

    public function listCommissions() {
        log_message('debug', 'Operator/listCommissions');

        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $this->load->model('screen/SearchListCommission_Model', 'search');
        $data = array();
        if (isset($_POST['search'])) {
            $data = $_POST['search'];
        }
        $search = new $this->search($data);

        $commissions = $this->commission->get_payed($search);

        $header = array(
            'current_menu' => 'listCommissions',
            'title' => 'コミッション実績一覧',
            'role' => self::COMMON_MENU,
            'user_hash' => ""
        );

        $pay_methods = $this->general->getPaymentMethodMap();
        $roles = $this->general->getRoleMap();

        $body = array(
            'commissions' => $commissions,
            'roles' => $roles,
            'pay_methods' => $pay_methods,
            'search' => $search
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/listCommissions', $body);
        $this->load->view('common/footer');
    }

    public function receiptIssueToken() {
        log_message('debug', 'Operator/receiptIssueToken');

        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $this->load->model('screen/SearchOrder_Model', 'search');

        if (isset($_POST['search'])) {
            // Search function
            $data = $_POST['search'];
        } else{
            $data = array();
        }
        $search = new $this->search($data);

        $target_status = array(
            $this->config->item('order_exchange_jpybtc'),
            $this->config->item('order_receiveby_btc'),
            $this->config->item('order_pre_issue_token'),
            $this->config->item('order_issue_token')
        );

        // 登録一覧取得
        $orderList = $this->order->getData4_ReceiveIssueToken($target_status,$search)->result();

        // Load types
        $types = $this->general->get_type()->result();
        $roles = $this->general->get_role()->result();

        
        $orders = array();
        foreach ($orderList as $order){        
            $orders[$order->order_number]['data'] =  $order;
            //status = 31
            if($order->status == $this->config->item('order_issue_token')){
                $orders[$order->order_number]['amount'] = $order->amount;
                $orders[$order->order_number]['memo_31'] = $order->memo;
            //status = 30
            }else if($order->status == $this->config->item('order_pre_issue_token')){

                $orders[$order->order_number]['exchange_rate_30'] = $order->exchange_rate;
            //status = 14 or 24
            }else if($order->status == $this->config->item('order_receiveby_btc') || $order->status == $this->config->item('order_exchange_jpybtc')){
                $orders[$order->order_number]['amount_24'] = $order->amount;
            }
        }

        $header = array(
            'current_menu' => 'receiptIssueToken',
            'title' => '受領書発行実績一覧',
            'role' => self::COMMON_MENU,
        );
        $body = array(
            'orderList' => $orders,
            'search' => $search
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/receiptIssueToken', $body);
        $this->load->view('common/footer');
    }
    

    public function operatorManagement($action=NULL, $status=NULL){
        
        log_message('debug', 'Operator/operatorManagement');
        $this->load->model('screen/ValidateOpeManagement_Model', 'crudUser');

        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $header = array(
            'current_menu' => 'operatorManagement',
            'title' => 'アカウント発行',
            'role' => self::COMMON_MENU,
            'user_hash' => ""
        );

        $ope = null;
        $errormsg = null;
        $successMsg = null;
        $arrFieldError = null;
        $roles = $this->user->get_role_opes();

        $crupSuccessMsg = array(
            'add_success' => $this->config->item('operator_added'),
            'update_success' => $this->config->item('operator_edited'),
            'delete_success' => $this->config->item('operator_deleted')
        );
        $crupErrorMsg = array(
            'error_not_operator' => $this->config->item('error_not_operator')
            //'error_not_found' => @$this->config->item('error_not_found')
        );

        $successMsg = @$crupSuccessMsg[strtolower($action).'_'.strtolower($status)];
        $errormsg = @$crupErrorMsg[strtolower($action).'_'.strtolower($status)];

        $operatorList = $this->user->getOperatorUser($roles);
        $body = array(
            'ope' => $ope,
            'roles' => $roles,
            'errormsg' => $errormsg,
            'successMsg' => $successMsg,
            'arrFieldError' => $arrFieldError,
            'operatorList' => $operatorList
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/operatorManagement', $body);
        $this->load->view('common/footer');
    }

    public function crudOperatorManagement(){
        
        log_message('debug', 'Operator/crudOperatorManagement');
        $this->load->model('screen/ValidateOpeManagement_Model', 'crudUser');

        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $header = array(
            'current_menu' => 'operatorManagement',
            'title' => 'アカウント発行',
            'role' => self::COMMON_MENU,
            'user_hash' => ""
        );

        $ope = null;
        $errormsg = null;
        $successMsg = null;
        $arrFieldError = array();
        $roles = $this->user->get_role_opes();

        if(isset($_POST['uid'])) {
            $ope = $this->user->get_userinfo_by_uid($_POST['uid']);            
        }

        // if click on edit or delete on operatorManagement page
        if(isset($_POST['ope_edit']) || isset($_POST['ope_delete'])) {
            // if (!isset($ope)) {
            //     $this->safe_redirect('Operator/operatorManagement/error/not_found');
            // }
            if (!isset($roles[$ope->role] )) {
                $this->safe_redirect('Operator/operatorManagement/error/not_operator');
            }
        }

        // confirm delete
        if(isset($_POST['ope_dodelete'])) {
            if($this->user->delete_user_by_uid($_POST['uid'])) {
                $this->safe_redirect('Operator/operatorManagement/delete/success');
            }
            //$this->safe_redirect('Operator/operatorManagement/delete/fail');
        }

        // confirm add new or update operator
        if (isset($_POST['ope_donew']) || isset($_POST['ope_doedit'])) {
            
            $CRUP_User = new $this->crudUser($_POST); 

            //set rule for fields.
            foreach ($CRUP_User->getRules() as $name => $rule){                
                $this->form_validation->set_rules($name, $rule['title'], $rule['rule_list']);
            }
            $data = $CRUP_User->getOpeCRUP();
            $ope =  (object) $data;
            

            // validate input data
            if(!$this->form_validation->run()) {
                $arrFieldError = $this->form_validation->error_array();
            }

            $roleList = array_keys($roles);
            $userInDB = $this->user->get_user_by_email_role($data['email'],$roleList);
            
            if($userInDB != null && $userInDB->num_rows() > 0) {
                $userInDB = $userInDB->result()[0];
                if(isset($_POST['ope_donew']) || (isset($_POST['ope_doedit']) && $_POST['uid'] != $userInDB->uid)) {
                    $emailInvalid = array('email' => 'ご入力いただいたメールアドレスは既に登録されております。');
                    $arrFieldError = array_merge($arrFieldError, $emailInvalid);
                }
            }
            //if password is empty then doesnot update this field.
            if (isset($_POST['ope_doedit']) && isset($arrFieldError['password'])) { 
                if (isset($_POST['password']) && empty($_POST['password']) && isset($data['password'])) { 
                    unset($data['password']); 
                    unset($arrFieldError['password']); 
                } 
            } 

            // if data all are OK
            if(empty($arrFieldError)) {

                // confirm add new operator
                if (isset($_POST['ope_donew'])) { 
                    $arrUserHash = array('agent_uid' => $this->getSessionValue('user_hash'));
                    $data = array_merge($arrUserHash, $data);
                    $this->user->insert_operator($data);
                    $this->safe_redirect('Operator/operatorManagement/add/success');
                }

                // confirm update operator
                if (isset($_POST['ope_doedit']) ) {
                    $this->user->update_operator_by_uid($data);
                    $this->safe_redirect('Operator/operatorManagement/update/success');
                }
            }        
        }

        $body = array(
            'ope' => $ope,
            'roles' => $roles,
            'errormsg' => $errormsg,
            'successMsg' => $successMsg,
            'arrFieldError' => $arrFieldError
        );
        $this->load->view('common/header', $header);        
        $this->load->view('operator/crudOperatorManagement', $body); 
        $this->load->view('common/footer');
    }

    public function reOrderConfirm() {
        log_message('debug', 'Operator/reOrderConfirm');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        if (isset($_POST['updates'])){
            $operator_uid = $this->getSessionValue('uid');
            $operator_hash = $this->getSessionValue('user_hash');
            $date = new DateTime();
            $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

            foreach ($_POST['updates'] as $order_number=>$value){
                $value = (object) $value;
                if (isset($value->status) and !empty($value->status)) {
                    $row = $this->order->select_by_ordernumber($order_number);
                    if ($value->status == 'cancel'){
                        log_message('debug', 'order_invalid');                        
                        // 無効
                        $odata = array(
                            'order_number' => $order_number,
                            'status' => $this->config->item('order_invalid'),
                            'agent_uid' => $row->agent_uid,
                            'client_uid' => $row->client_uid,
                            'received' => $row->received,
                            'pay_method' => $row->pay_method,
                            'expiration_date' => $row->expiration_date,
                            'create_by' => $operator_uid,
                            'create_at' => $now
                        );
                        $this->order->insert_order($odata);

                        $adata = array(
                            'user_hash' => $row->client_uid,
                            'activity_code' => $this->config->item('approval_invalid'),
                            'object' => $order_number,
                            'create_by' => $operator_uid,
                            'create_at' => $now
                        );
                        $this->act->insert_activity($adata);
                    }

                    $order_amount = $row->amount;
                    if (isset($value->amount) and $value->amount >0 ) {
                        $order_amount = $value->amount;
                    }

                    if ($value->status == 'update'){
                        log_message('debug', 'order_update_amount');

                        if ($row->status == $this->config->item('order_orderby_bank')){
                            // 有効期限
                            $date = new DateTime();
                            $expiration_datetime = date($this->config->item('db_timestamp_format'), ($date->getTimestamp() + $this->config->item('order_expiration_time')));

                            // 口座情報を連絡した
                            $odata = array(
                                'order_number' => $order_number,
                                'status' => $this->config->item('order_notify_bankaccount'),
                                'agent_uid' => $row->agent_uid,
                                'client_uid' => $row->client_uid,
                                'pay_method' => $row->pay_method,
                                'currency_unit' => $row->currency_unit,
                                'amount' => $order_amount,
                                'expiration_date' => $expiration_datetime,
                                'create_by' => $operator_uid,
                                'create_at' => $now
                            );
                            $order_number = $this->order->insert_order($odata);
                        } else if ($row->status == $this->config->item('order_orderby_btc')) {
                            $odata = array(
                                'update_by' => $operator_uid,
                                'update_at' => $now,
                                'rsv_char_1' => '1',
                                'rsv_char_2' => $order_amount
                            );
                            $this->order->update_order($order_number, $operator_hash, $odata);
                        } else {
                            continue;
                        }

                        $adata = array(
                            'user_hash' => $operator_hash,
                            'activity_code' => $this->config->item('act_reorder_confirm'),
                            'object' => $order_number,
                            'memo' => $this->config->item('act_reorder_confirm_memo'),
                            'create_by' => $operator_uid,
                            'create_at' => $now
                        );
                        $this->act->insert_activity($adata);
                    }
                }
            }
        }

        $target_status = array(
            $this->config->item('order_orderby_bank'),
            $this->config->item('order_orderby_btc')
        );
        $header = array(
            'current_menu' => 'reOrderConfirm',
            'title' => '注文審査対象一覧',
            'role' => self::COMMON_MENU
        );

        $body = array(
            'orders' => $this->order->select_for_reOrderConfirm(),
            'order_sum4month' => $this->order->get_SumAmount4Month($target_status),
            'first_orders' => $this->order->select_first_orders(),
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/reOrderConfirm', $body);
        $this->load->view('common/footer');
    }

    /*
    *   Cronjob to send email
    *   is_sent:    0 => not sent
    *               1 => sent
    *               2 => processing
    * Process:
    * 1. get all records where is_sent = 0
    * 2. change is_sent = 2 for the records that get from 1
    * 3. send email, if success change is_sent = 1, if not success change update back to 0
    */
    public function sendEmailCron() {
        $rs = array('status' => 'warn', 'message' => 'nothing happens');

        $not_sent_status = 0;
        $sent_status = 1;
        $processing_status = 2;
        $send_failed_status = 3;

        $arr = $this->emailQueue->get_data($not_sent_status);
        $ids = array_keys($arr);

        // check and change is_sent by 2, this is processing status
        if(!empty($ids)) {
            $this->emailQueue->update_data($ids, array('is_sent' => $processing_status));
        }

        foreach($arr as $row) {
            // check if already sent
            if($row->is_sent != '0') continue;

            // default value from config
            $from = $this->config->item('mail_from');
            $from_name = $this->config->item('mail_admin_name');
            $bcc = $this->config->item('qtsmaillog_email');

            // check and assign data from DB if it is not empty
            $from = !empty($row->from) ? $row->from : $from;
            $from_name = !empty($row->from_name) ? $row->from_name : $from_name;
            $bcc = !empty($row->bcc) ? $row->bcc : $bcc;

            // email setting
            $this->email->from($from, $from_name);
            $this->email->to($row->to);

            if((int) $row->is_bcc === 1) {
                $this->email->bcc($bcc);
            }

            $this->email->subject($row->subject);
            $this->email->message($row->message);

            $status = $this->email->send();

            if($status === true) {
                $this->emailQueue->update_data($row->id, array('is_sent' => $sent_status));
            }
            else {
                if(!isset($rs['fail_id_list'])) {
                    $rs['fail_id_list'] = array();
                }
                $rs['fail_id_list'][] = $row->id;
            }
        }

        if(isset($rs['fail_id_list']) && !empty($rs['fail_id_list'])) {
            // update back to 0 for another process
            $this->emailQueue->update_data($rs['fail_id_list'], array('is_sent' => $send_failed_status));

            if(count($rs['fail_id_list']) == count($ids)) {
                $rs['status'] = 'fail';
                $rs['message'] = 'No email has been sent';
            }
            else {
                $rs['status'] = 'warn';
                $rs['message'] = 'Some email have NOT been sent';
            }
        }
        else {
            $rs['status'] = 'success';
            $rs['message'] = 'Send mail DONE';
        }

        log_message('debug', $rs['message'] . ': ' . json_encode($rs));

        return $this->returnJson($rs);
    }

    public function insertRate() {
        log_message('debug', 'Operator/insertRate');

        if (!$this->is_login()) {
            redirect('Operator/login');
        }

        $operator_uid = $this->getSessionValue('uid');
        $updated = false;
        if (isset($_POST['from']) and isset($_POST['rate'])){
            $from = $_POST['from'];
            $to = $_POST['to'];
            $rate = $_POST['rate'];
            if ($from != $to and $rate > 0){
                $data = array(
                    'from' => $to,
                    'to' => $from,
                    'rate' => $rate,
                    'create_by' => $operator_uid
                );
                $this->rate->insert_rate($data);
                $updated = true;
            }
        }

        $rates = $this->rate->select_top(20);
        $header = array(
            'current_menu' => 'insertRate',
            'title' => 'レート入力',
            'role' => self::COMMON_MENU,
            'rates' => $rates,
            'updated' => $updated
        );
        $body = array();
        $this->load->view('common/header', $header);
        $this->load->view('operator/insertRate', $body);
        $this->load->view('common/footer');
    }

    public function uploadDocs($status=NULL, $statusValue=NULL, $uploadedDoc=NULL) {
        log_message('debug', 'Operator/uploadDocs');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $statusMessage = '';
        if($status == 'upload_status') {
            $statusMessage = getFileUploadErrorMessage($statusValue);
        }

        $header = array(
            'current_menu' => 'uploadDocs',
            'title' => 'ドキュメントアップロード',
            'role' => self::COMMON_MENU
        );
        $body = array(
            'docsList' => get_docs_list(),
            'statusMessage' => $statusMessage,
            'uploadedDoc' => $uploadedDoc
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/uploadDocs', $body);
        $this->load->view('common/footer');
    }

    public function confirmUploadDocs() {
        log_message('debug', 'Operator/confirmUploadDocs');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        // check php limit post size
        $post_max_size = getPostMaxSize();
        $significant_post_max_size = ini_get('post_max_size');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
        {
            log_message('debug', 'Upload file that is exceed post size limit. The file size limit at ' . $significant_post_max_size);
            $this->safe_redirect('Operator/uploadDocs/upload_status/exceed_post_size_limit');
        }

        if(isset($_POST['upload_document'])) {

            $targetFile = @$_POST['targetFile'];
            // check target file name
            if(empty($targetFile)) {
                $this->safe_redirect('Operator/uploadDocs/upload_status/empty_target_file');
            }

            // parse targeFile name and extension
            $arr = explode('.', $targetFile);
            $targetFileName = @$arr[0];
            $targetFileExt = strtolower(@$arr[count($arr) - 1]);

            $uploadPath = dirname(BASEPATH) . "/" . $this->config->item('path_pdf_doc');

            if (isset($_FILES['uploadFile']))
            {
                if($_FILES['uploadFile']['error'] == 0)
                {
                    $this->load->helper('upload');
                    $uploadHandler = new Upload();
                    //$uploadHandler->setAllowedExt(array('pdf'));
                    //$uploadHandler->setAllowedTypes(array('application/pdf'));
                    if ($uploadHandler->load($_FILES['uploadFile']))
                    {
                        // check file extension
                        if($targetFileExt != $uploadHandler->getExtension()) {
                            $this->safe_redirect('Operator/uploadDocs/upload_status/different_file_extension');
                        }

                        $oldFile = $uploadPath . $targetFile;
                        //var_dump($oldFile);exit;
                        if(file_exists($oldFile) && is_file($oldFile)) {
                            // rename $oldFile to back it up
                            $backupFile = $uploadPath . $targetFileName . "-" . date("Y-m-d-H-i-s") . "." . $targetFileExt;
                            if(rename($oldFile, $backupFile)) {
                                log_message('debug', 'File upload => backup old file: ' . $oldFile . ' => ' . $backupFile);
                            }
                        }

                        $name = $uploadHandler->getFile('name');

                        $file = $oldFile;
                        if ($uploadHandler->save($file))
                        {
                            log_message('debug', 'File uploaded: Original file name' . $name);
                            log_message('debug', 'File uploaded: Target file name' . $targetFile);
                            $this->safe_redirect('Operator/uploadDocs/upload_status/success/' . md5($targetFile));
                        }

                        log_message('debug', 'Cannot save upload file. Check again the permission for docs folder.');

                        $this->safe_redirect('Operator/uploadDocs/upload_status/check_write_permission');
                    }
                } else {
                    $uploadErrMap = array(
                        "1" => "exceed_upload_size_limit",
                        "2" => "exceed_max_file_size",
                        "3" => "file_upload_partially",
                        "4" => "no_file_upload",
                        "6" => "missing_tmp_folder",
                        "7" => "cannot_write_to_disk",
                        "8" => "php_ext_err",
                    );
                    
                    $err = $_FILES['uploadFile']['error'];
                    $err = isset($uploadErrMap[$err]) ? $uploadErrMap[$err] : $uploadErrMap["4"];

                    $this->safe_redirect('Operator/uploadDocs/upload_status/' . $err);
                }
            }

        }

        $this->safe_redirect('Operator/uploadDocs');
    }

    /*
    * A function export to CSV file if user receiveIssueToken.
    */
    public function exportCsvReceipIssueToken() {
        log_message('debug', 'Operator/exportCsvReceipIssueToken');
        
        $target_status = array(
            $this->config->item('order_exchange_jpybtc'),
            $this->config->item('order_receiveby_btc'),
            $this->config->item('order_pre_issue_token'),
            $this->config->item('order_issue_token')
        );
        $orderList = $this->order->exportCSV_RIT($target_status, (object)$_POST['search']);
        
        //get result set for export file CSV.
        $this->outputCsv($orderList, $this->config->item('csv_receipt_issue_token'));
        $this->safe_redirect('Operator/receiptIssueToken');
    }

    public function reissueToken($status=NULL) {
        log_message('debug', 'Operator/reissueToken');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $allowRoles = array(
            $this->config->item('role_sysadmin'),
            $this->config->item('role_operator')
        );

        if(!in_array($this->getSessionValue('role'), $allowRoles)) {
            $this->safe_redirect('Operator/login');
        }

        $operator_uid = $this->getSessionValue('uid');

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $msgArr = array(
            'order_not_found' => '注文番号が見つかりません。',
            'password_not_match' => 'パスワードが相違しますので、変更できません。',
            'success' => '受領書メールを再発行しました。'
        );

        $statusMessage = @$msgArr[$status];

        if(isset($_POST['reissue_token'])) {
            $order_number = $_POST['order_number'];
            $password = $_POST['password'];

            $rows = $this->order->select_for_makeToken($order_number, TRUE);
            if (!empty($rows)) {
                $result = $rows[0];
                $user = $this->user->get_user_by_userhash($this->getSessionValue('user_hash'));
                if(!empty($user) && $user->password == $this->user->encrypt_password($password)) {

                    // email to client
                    $replace_token = array(
                        $result->family_name, $result->first_name, 
                        $order_number, $result->create_at, money_format_qts($result->amount,8),
                        $result->rate, $result->usdqnt,
                        money_format_qts($result->quantity,8), $result->token_code,
                        $this->getEmailSignature()
                    );
                    $email_template = $this->parseEmailTemplate('mail_notify_tokencode', $replace_token);
                    $emailData = array(
                        'to' => $result->email,
                        'subject' => $email_template['subject'],
                        'message' => $email_template['message'],
                        'is_bcc' => '0',
                        'object' => $order_number,
                        'memo' => 'Operator: reissue token / 受領書の再発行',
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->emailQueue->insert_data(array($emailData));

                    // トークン発行の完了を登録する
                    $data = array(
                        'user_hash' => $result->user_hash,
                        'activity_code' => $this->config->item('act_issue_token'),
                        'object' => $order_number,
                        'memo' => 'Operator: reissue token / 受領書の再発行',
                        'create_by' => $operator_uid,
                        'create_at' => $now
                    );
                    $this->act->insert_activity($data);

                    $this->safe_redirect('Operator/reissueToken/success');

                }
                else {
                    $statusMessage = @$msgArr['password_not_match'];
                }
            }
            else {
                $statusMessage = @$msgArr['order_not_found'];
            }
        }
        else {

        }

        $header = array(
            'current_menu' => 'reissueToken',
            'title' => '受領書の再発行',
            'role' => self::COMMON_MENU
        );
        $body = array(
            'statusMessage' => $statusMessage
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/reissueToken', $body);
        $this->load->view('common/footer');
    }

    /*
    * The function change password.
    */
    public function changePasswd() {
        log_message('debug', 'Operator/changePasswd');
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }
        $user_hash = $this->getSessionValue('user_hash');
        
        $header = array(
            'current_menu' => 'changePasswd',
            'title' => 'パスワード変更',
            'role' => self::COMMON_MENU
        );
        $body = array(
            'user_hash' => $user_hash
        );
        $this->load->view('common/header', $header);
        $this->load->view('operator/changePassword', $body);
        $this->load->view('common/footer');
    }
    public function confirmChangePasswd() {
        log_message('debug', 'Operator/confirmChangePasswd');
        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }
        
        $this->load->model('screen/RegisterUser_Model', 'regUser');
        $this->load->library('form_validation');

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $user_hash = $this->getSessionValue('user_hash');
        $user_id = $this->getSessionValue('uid');
        $user = $this->user->get_user_by_userhash($user_hash); 
        $userPost = new $this->regUser($_POST);
        foreach ($userPost->getRulesChangePassword() as $name => $rule){
            $this->form_validation->set_rules($name, $rule['title'], $rule['rule_list']);
        }
        $arrFieldError = array();
        if ($this->form_validation->run() == FALSE) {            
            $row = $this->user->get_user_by_userhash($user_hash);
            if (empty($row)){
                show_404();
            }
            $arrFieldError = $this->form_validation->error_array();
        } 
        if($user->password != $this->user->encrypt_password($userPost->currentPassword)){
            $arrFieldError = array_merge($arrFieldError, array('currentPassword' => '現在のパスワードと一致しません。') );
        }
        if($userPost->currentPassword == $userPost->newPassword){
            $arrFieldError = array_merge($arrFieldError, array('currentPassword' => '入力したパスワードを現在のパスワードと同じようにならないでください。') );
        }

        if(!empty($arrFieldError)){
            $header = array(
                'current_menu' => 'changePasswd',
                'title' => 'パスワード変更',
                'role' => self::COMMON_MENU
            );            
            $body = array(
                'user_hash' => $user_hash,
                'arrFieldError' => $arrFieldError,
                'userPostData' => $_POST
            );
            $this->load->view('common/header', $header);
            $this->load->view('operator/changePassword', $body);
            $this->load->view('common/footer');
            return;
        }   
  
        $data = array(
            'password' => $this->user->encrypt_password($userPost->newPassword),            
            'update_by' => $user_id,
            'update_at' => $now
        );
        $this->user->update_by_userhash($user_hash, $data);
        $this->safe_redirect('Operator/viewUserDetail/' . $user->uid . '/update/success');   
    }

    /*
    * A function check experation BTC of client order.
    */
    public function checkExpiredBtcOrder() {
        log_message('debug', 'Operator/checkExpiredBtcOrder');
        $rsRecords = $this->order->getOrderBtcHasBtcAddr();
        if(!empty($rsRecords)){
            foreach ($rsRecords as $order ) {
                $data = array('order_number' => $order->order_number,
                    'status' => $this->config->item('order_expired'),
                    'agent_uid' => $order->agent_uid,
                    'client_uid' =>  $order->client_uid,
                    'pay_method' => $order->pay_method,
                    'received' => $order->received,
                    'currency_unit' => $order->currency_unit,
                    'amount' => $order->amount,
                    'receive_address' => $order->receive_address,
                    'create_by' => $this->config->item('AUTO_ID')
                );
                $this->order->insert_order($data);
            }
            return true;
        }
        return false;
    }

    /*
    *A function send-email manual
    */
    public function resendEmail() {
        log_message('debug', 'Operator/resendEmail');
        $this->load->model('screen/SearchEmail_Model', 'search');     
        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }   

        $status = null;
        if(isset($_POST['listEmailId']) && '' != $_POST['listEmailId']){
            //process send email manual
            $status = $this->processSendEmail($_POST['listEmailId']);
        }

        $memo_x = array(
            $this->config->item('memo_0') => '登録済み',
            $this->config->item('memo_1') => '交換者の承認通知',
            $this->config->item('memo_2') => '注文完了の通知',
            $this->config->item('memo_3') => 'BTCの注文でBTCアドレス通知',
            $this->config->item('memo_4') => 'BTCの注文でBTC着金',
            $this->config->item('memo_5') => '振込の注文で送金通知',
            $this->config->item('memo_6') => '代理店の登録済み',
            $this->config->item('memo_7') => '代理店のパスワード設定',
            $this->config->item('memo_8') => '代理店の承認通知',
            $this->config->item('memo_9') => '受領書の発行',
            $this->config->item('memo_10') => '受領書の「再」発行',
            $this->config->item('memo_11') => '注文の無効メール'
        );
        $is_sent_x = array(
            $this->config->item('is_sent_0') => '未送信',
            $this->config->item('is_sent_1') => '送信 済',
            $this->config->item('is_sent_2') => '送信中',
            $this->config->item('is_sent_3') => '送信 失敗'
        );

        if (isset($_POST['search'])) {
            // Search function
            $data = $_POST['search'];
        } else{
            $data = array(
                'date_from' => date('Y/m/d'),
                'date_to' => date('Y/m/d')
            );            
        }

        $search = new $this->search($data);      
        $arrEmail = $this->emailQueue->getData4Search($search); 

        $header = array(
            'current_menu' => 'resendEmail',
            'title' => 'メールの再送信',
            'role' => self::COMMON_MENU
        );
        $body = array(
            'arrEmail' => $arrEmail,
            'search' => $search,
            'memo_x' => $memo_x,
            'is_sent_x' => $is_sent_x,
            'email_status' => $status        
        );

        $this->load->view('common/header', $header);
        $this->load->view('operator/resendEmail', $body);
        $this->load->view('common/footer');
    }

    private function processSendEmail($listEmailId) {
        log_message('debug', 'Operator/processSendEmail');
        
        $rs = array(
            'status' => 'warn', 
            'message' => 'nothing happens'
        );

        $user_hash = $this->getSessionValue('user_hash');
        $operator_uid = $this->getSessionValue('uid');
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $not_sent_status = 0;
        $sent_status = 1;
        $processing_status = 2;
        $send_failed_status = 3;

        $arr = $this->emailQueue->getData4SentEmail($listEmailId);
        $ids = array_keys($arr);

        // check and change is_sent by 2, this is processing status
        if(!empty($ids)) {
            $data = array(
                'is_sent' => $processing_status,
                'update_by' => $operator_uid,
                'update_at' => $now
            );
            $this->emailQueue->update_data($ids, $data);
        }
        
        foreach($arr as $row) {            
            // default value from config
            $from = $this->config->item('mail_from');
            $from_name = $this->config->item('mail_admin_name');
            $bcc = $this->config->item('qtsmaillog_email');

            // check and assign data from DB if it is not empty
            $from = !empty($row->from) ? $row->from : $from;
            $from_name = !empty($row->from_name) ? $row->from_name : $from_name;
            $bcc = !empty($row->bcc) ? $row->bcc : $bcc;

            // email setting
            $this->email->from($from, $from_name);
            $this->email->to($row->to);

            if((int) $row->is_bcc === 1) {
                $this->email->bcc($bcc);
            }

            $this->email->subject($row->subject);
            $this->email->message($row->message);

            $status = $this->email->send();

            if($status === true) {
                $data = array(
                    'is_sent' => $sent_status,
                    'update_by' => $operator_uid,
                    'update_at' => $now
                );
                $this->emailQueue->update_data($row->id, $data);
            }else {
                if(!isset($rs['fail_id_list'])) {
                    $rs['fail_id_list'] = array();
                }
                $rs['fail_id_list'][] = $row->id;
            }
        }
        // $adata = array(
        //     'user_hash' => $user_hash,
        //     'activity_code' => '1111', //code send email
        //     'object' => 'send email manual', //send email manual
        //     'update_by' => $operator_uid,
        //     'update_at' => $now
        // );
        // $this->act->insert_activity($adata);  
        if(isset($rs['fail_id_list']) && !empty($rs['fail_id_list'])) {
            // update back to 0 for another process
            $data = array(
                'is_sent' => $send_failed_status,
                'update_by' => $operator_uid,
                'update_at' => $now
            );
            $this->emailQueue->update_data($rs['fail_id_list'], $data); 
            return "fail";           
        } 
        return "success";      
    }

    public function getCommissionDetailShowPopUp($order_number){
        log_message('debug', 'Operator/getCommissionDetailShowPopUp');
        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $data = $this->commission->getCommissionDetailByOrderNumber($order_number);
        $roles = $this->general->get_role()->result();
        $arrRole = array();
        foreach ($roles as $role) {
            $arrRole[$role->code] = $role->value;
        }
        $body = array(
            'data' =>$data,
            'arrRole' => $arrRole
        );
        $this->load->view('operator/commissionDetailByOrderNumber', $body);
    }

    /**
    * A function insert email_queue when operator-user cancel order.
    * @param : object
    * @return: void
    */
    private function insertEmailInvalidOrder($order){
        log_message('debug', 'Operator/insertEmailInvalidOrder');

        $operator_uid = $this->getSessionValue('uid');
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        //get order client from user-hash.
        $userClient = $this->user->get_user_by_userhash($order->client_uid);
        $emailCancelOrder = array(
            $userClient->family_name,
            $userClient->first_name,
            $order->order_number,
            $order->amount,
            $this->getEmailSignature()
        );
        $email_template = $this->parseEmailTemplate('mail_cancel_order', $emailCancelOrder);
        $emailData = array(
            'to' => $userClient->email,
            'subject' => $email_template['subject'],
            'message' => $email_template['message'],
            'is_bcc' => '0',
            'object' => $order->order_number,
            'memo' => 'Operator: email cancel order',
            'create_by' => $operator_uid,
            'create_at' => $now
        );
        $this->emailQueue->insert_data(array($emailData));
        ///end task.
    }

    public function editToken($create_limit){
        log_message('debug', "Operator/editToken");
        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }
        $session_data = $this->getLoginData();
        $user_role = $session_data['role'];
        if ($user_role != "01") {
            die("not admin");
        }
        $date = new DateTime($create_limit);
        $date->add(new DateInterval('P1D')); // add 1
        $create_limit = date($this->config->item('session_timestamp_format'), $date->getTimestamp());
        $rows = $this->token->select_for_edit_code($create_limit);
        echo "注文IDのリスト\r\n<br/>";
        foreach($rows as $row) {
            $order_number = $row->order_number;
            $this->editTokenAndSendEmail($order_number);
        }
        echo "DONE";
    }

    private function editTokenAndSendEmail($order_number) {
        log_message('debug', 'Operator/editTokenAndSendEmail');
        $rows = $this->order->select_for_editToken($order_number);
        if (empty($rows)) {
            return;
        }
        try{
            $operator_uid = $this->getSessionValue('uid');
            if(empty($operator_uid)) {
                $operator_uid = $this->config->item('AUTO_ID');
            }

            $operator_hash = $this->getSessionValue('user_hash');
            if(empty($operator_hash)) {
                $operator_hash = sha1($operator_uid);
            }
            //Insert activity
            $date = new DateTime();
            $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
            $adata = array(
                'user_hash' => $operator_hash,
                'activity_code' => $this->config->item('act_edit_token'),
                'object' => $order_number,
                'memo' => "Edit_Token",
                'create_by' => $operator_uid,
                'create_at' => $now,
            );
            $this->act->insert_activity($adata);
            
            // send email with new token code
            $result=$rows[0];
            $date = new DateTime();
            $now = date($this->config->item('session_timestamp_format'), $date->getTimestamp());

            // edit token;
            $new_token_code = $this->getTokenCode($order_number);
            $data = array('token_code' => $new_token_code);
            // update to t_token
            $this->token->update_by_ordernumber($order_number, $data);

            // Mail parameter list
            $replace_token = array(
                $result->family_name,
                $result->first_name,
                $order_number,
                $result->create_at,
                money_format_qts($result->amount,8),
                $result->rate,
                $result->usdqnt,
                money_format_qts($result->quantity,8),
                $new_token_code,
                $this->getEmailSignature()
            );
            $email_template = $this->parseEmailTemplate('mail_edit_token', $replace_token);
            $emailData = array(
                'type' => 1,
                'to' => $result->email,
                'subject' => $email_template['subject'],
                'message' => $email_template['message'],
                'object' => $order_number,
                'memo' => 'Edit Token: Notify client that his token code was edited',
                'create_by' => $operator_uid,
                'create_at' => $now,
                'is_bcc' => 0
            );

            //$this->emailQueue->insert_data(array($emailData));
            $this->emailQueueRedeem->insert_data(array($emailData));

        }catch (Exception $e) {
            log_message('error', 'Operator/editTokenAndSendEmail' . $e->getMessage());
            return 'fail';
        }
        return 'success';
    }

    private function processRedeem($order_number) {
        log_message('debug', 'Operator/processRedeem');
        $rows = $this->order->select_for_editToken($order_number);
        if (empty($rows)) {
            return;
        }
        try{
            $operator_uid = $this->getSessionValue('uid');
            if(empty($operator_uid)) {
                $operator_uid = $this->config->item('AUTO_ID');
            }

            $operator_hash = $this->getSessionValue('user_hash');
            if(empty($operator_hash)) {
                $operator_hash = sha1($operator_uid);
            }
            //Insert activity
            $date = new DateTime();
            $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
            $adata = array(
                'user_hash' => $operator_hash,
                'activity_code' => $this->config->item('act_edit_token'),
                'object' => $order_number,
                'memo' => "Edit_Token",
                'create_by' => $operator_uid,
                'create_at' => $now,
            );
            $this->act->insert_activity($adata);

            $result=$rows[0];
            $date = new DateTime();
            $now = date($this->config->item('session_timestamp_format'), $date->getTimestamp());

            $issue_redeem_token = array(
                $result->family_name,
                $result->first_name,
                $order_number,
                $result->create_at,
                money_format_qts($result->amount,8),
                $result->rate,
                $result->usdqnt,
                money_format_qts($result->quantity,8),
                $result->token_code,//token_code from t_token table.
                $result->user_hash,
                md5($order_number),
                $this->getEmailSignature()
            );
            $email_template = $this->parseEmailTemplate('mail_issue_redeem_token', $issue_redeem_token);

            $emailData = array(
                'type' => 2,
                'to' => $result->email,
                'subject' => $email_template['subject'],
                'message' => $email_template['message'],
                'object' => $order_number,
                'memo' => 'Issue Redeem Token: Notify client that his token issue redeem',
                'create_by' => $operator_uid,
                'create_at' => $now,
                'is_bcc' => 1
            );
            $this->emailQueueRedeem->insert_data(array($emailData));

        }catch (Exception $e) {
            log_message('error', 'Operator/processRedeem' . $e->getMessage());
            return 'fail';
        }
        return 'success';
    }

    private function getTokenCode($order_number)
    {
        //return "QNTx" . $order_number;
        $date = new DateTime();
        $now = date($this->config->item('session_timestamp_format'), $date->getTimestamp());
        return  md5("QNT" . $now . $order_number);
    }

    public function sendEmailRedeemCron($parameter=NULL) {
        log_message('debug', 'Operator/sendEmailRedeemCron');
        $encryption_key = $this->config->item('encryption_key');
        if(is_null($parameter) || (isset($parameter) && $encryption_key != $parameter)){
            //parameter is null or difference encryption_key encryption_key and parameter then return
            return;
        }
        return $this->returnJson($this->sendEmailRedeem());
    }

    /*
    * The function sendEmailRedeem the same process sendEmail
    *
    */
    protected function sendEmailRedeem() {
        log_message('debug', 'Operator/sendEmail');
        $emailconfig = $this->config->item('email_redeem_info');
        $this->email->setConfig($emailconfig);
        $from = $emailconfig['smtp_user'];
        $from_name = $this->config->item('mail_name_redeem');
        $bcc = $this->config->item('mail_bcc_redeem');
        $rs = array('status' => 'warn', 'message' => 'nothing happens');
        $not_sent_status = 0;
        $sent_status = 1;
        $processing_status = 2;
        $send_failed_status = 3;
        $arr = $this->emailQueueRedeem->get_data($not_sent_status);
        $ids = array_keys($arr);
        // check and change is_sent by 2, this is processing status
        if(!empty($ids)) {
            $this->emailQueueRedeem->update_data($ids, array('is_sent' => $processing_status));
        }
        foreach($arr as $row) {
            // check if already sent
            if($row->is_sent != '0') continue;
            // default value from config
            // check and assign data from DB if it is not empty
            $from = !empty($row->from) ? $row->from : $from;
            $from_name = !empty($row->from_name) ? $row->from_name : $from_name;
            $bcc = !empty($row->bcc) ? $row->bcc : $bcc;
            // email setting
            $this->email->from($from, $from_name);
            $this->email->to($row->to);
            if((int) $row->is_bcc === 1) {
                $this->email->bcc($bcc);
            }
            $this->email->subject($row->subject);
            $this->email->message($row->message);
            $status = $this->email->send();
            if($status === true) {
                $this->emailQueueRedeem->update_data($row->id, array('is_sent' => $sent_status));
            }
            else {
                if(!isset($rs['fail_id_list'])) {
                    $rs['fail_id_list'] = array();
                }
                $rs['fail_id_list'][] = $row->id;
            }
        }
        if(isset($rs['fail_id_list']) && !empty($rs['fail_id_list'])) {
            // update back to 0 for another process
            $this->emailQueueRedeem->update_data($rs['fail_id_list'], array('is_sent' => $send_failed_status));
            if(count($rs['fail_id_list']) == count($ids)) {
                $rs['status'] = 'fail';
                $rs['message'] = 'No email has been sent';
            }
            else {
                $rs['status'] = 'warn';
                $rs['message'] = 'Some email have NOT been sent';
            }
        }
        else {
            $rs['status'] = 'success';
            $rs['message'] = 'Send mail DONE';
        }
        log_message('debug', $rs['message'] . ': ' . json_encode($rs));
        
        if($rs['status'] == 'success'){
            return 'success';
        }
        return 'fail';
    }

    /*
    *   A function editTokenList
    */
    public function editTokenList() {
        log_message('debug', 'Operator/editTokenList');
        $this->load->model('screen/SearchToken_Model', 'search');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $status = NULL;
        if(isset($_POST['listOrderNumber'])){
            if ('' != $_POST['listOrderNumber']){ 
                //process batch job.
                $status = $this->processBatchToken($_POST['listOrderNumber']);
            }else{
                if(isset($_POST['btnSubmit']) == FALSE){
                    $status = "No-item-selected";
                }
            }
        }

        $header = array(
            'current_menu' => 'editTokenList',
            'title' => '還元コードの変更',
            'role' => self::COMMON_MENU
        );

        if (isset($_POST['search'])) {
            // Search function
            $data = $_POST['search'];
        } else{
            //Default set create from to is one week.
            $data = array(
                'create_from' => date('Y/m/d', strtotime('-6 days')),
                'create_to' => date("Y/m/d")
            );            
        }        
        $search = new $this->search($data);

        $body = array(
            'tokens' => $this->token->select_filter_token($search, $this->config->item('edit_token_type')),            
            'search' => $search,
            'status' => $status,
            'is_sent_x' => $this->config->item('email_sent_status'),
            'is_payed_x' => $this->config->item('token_payed_status')
        );

        $this->load->view('common/header', $header);
        $this->load->view('operator/editTokenList', $body);
        $this->load->view('common/footer');
    }
    
    /*
    *   A function processing Batch state
    *   Call function processBatchToken(lstOrderNumber, flag)
    *   flag parameter is False => call editToken
    *                     True  => call issueRedeem
    */
    public function processBatchToken($orderNumberList, $redeem = FALSE) {
        log_message('debug', 'Operator/processBatchToken');
        $statusResult = NULL;

        //call editToken
        $arrOrderNumber = explode(",", $orderNumberList);
        foreach ($arrOrderNumber as $key => $value) { 
            //$statusResult = $this->editTokenAndSendEmail($value, $redeem);
                        
            if($redeem == FALSE){
                $statusResult = $this->editTokenAndSendEmail($value);
                if('fail' == $statusResult){
                    return 'fail';//fail editToken&SendEmail with orderNumber 
                }
            }    
            $statusResult = $this->processRedeem($value);        
            if('fail' == $statusResult){
                return 'fail';//fail redeem with orderNumber.
            }
        }

        //call sendEmailRedeem
        $statusResult = $this->sendEmailRedeem();

        return $statusResult;//success
    }
    /*
    *   A function issueReddemToken
    */
    public function issueRedeemToken() {
        log_message('debug', 'Operator/issueRedeemToken');
        $this->load->model('screen/SearchToken_Model', 'search');

        // ログイン判定
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }

        $status = NULL;
        if(isset($_POST['listOrderNumber'])){
            if ('' != $_POST['listOrderNumber']){ 
                //process batch job.
                $status = $this->processBatchToken($_POST['listOrderNumber'], TRUE);
            }else{
                if(isset($_POST['btnSubmit']) == FALSE){
                    $status = "No-item-selected";
                }
            }
        }

        $header = array(
            'current_menu' => 'issueRedeemToken',
            'title' => 'リディームリンク発行',
            'role' => self::COMMON_MENU
        );

        if (isset($_POST['search'])) {
            // Search function
            $data = $_POST['search'];
        } else{
            //Default set create from to is one week.
            $data = array(
                'create_from' => date('Y/m/d', strtotime('-6 days')),
                'create_to' => date("Y/m/d")
            );            
        }        
        $search = new $this->search($data);

        $body = array(
            'tokens' => $this->token->select_filter_token($search, $this->config->item('redeem_token_type')),
            'search' => $search,
            'status' => $status,
            'is_sent_x' => $this->config->item('email_sent_status'),
            'is_payed_x' => $this->config->item('token_payed_status')
        );

        $this->load->view('common/header', $header);
        $this->load->view('operator/issueRedeemToken', $body);
        $this->load->view('common/footer');
    }

    /*
    * A function export data of editTokenList to CSV file.
    *
    */
    public function exportCsvEditTokenList() {
        log_message('debug', 'Operator/exportCsvEditTokenList');
        $this->load->model('screen/SearchToken_Model', 'search');

        if (isset($_POST['search'])) {
            // Search function
            $data = $_POST['search'];
        } else{
            //Default set create from to is one week.
            $data = array(
                'create_from' => date('Y/m/d', strtotime('-6 days')),
                'create_to' => date("Y/m/d")
            );
        }
        $search = new $this->search($data);

        $tokenList = $this->token->getDataOfIssueAndEditToken($search, $this->config->item('edit_token_type'), $this->config->item('email_sent_status'), $this->config->item('token_payed_status'));

        //get result statement for export file CSV.
        if(empty($tokenList) == FALSE){
            $this->outputCsv($tokenList, $this->config->item('csv_edit_token_list'));
        }else{
            log_message('error', 'Export file csv : ' . $this->config->item('csv_edit_token_list'));
        }
        $this->safe_redirect('Operator/editTokenList');
    }

    /*
    * A function export data of issueRedeemToken to CSV file.
    *
    */
    public function exportCsvIssueRedeemToken() {
        log_message('debug', 'Operator/exportCsvIssueRedeemToken');
        $this->load->model('screen/SearchToken_Model', 'search');

        if (isset($_POST['search'])) {
            // Search function
            $data = $_POST['search'];
        } else{
            //Default set create from to is one week.
            $data = array(
                'create_from' => date('Y/m/d', strtotime('-6 days')),
                'create_to' => date("Y/m/d")
            );
        }
        $search = new $this->search($data);

        $redeemTokenList = $this->token->getDataOfIssueAndEditToken($search, $this->config->item('redeem_token_type'), $this->config->item('email_sent_status'), $this->config->item('token_payed_status'));

        //get result statement for export file CSV.
        if(empty($redeemTokenList) == FALSE){
            $this->outputCsv($redeemTokenList, $this->config->item('csv_issue_redeem_token'));
        }else{
            log_message('error', 'Export file csv : ' . $this->config->item('csv_issue_redeem_token'));
        }
        $this->safe_redirect('Operator/issueRedeemToken');
    }

    public function deleteEmailRedeem($order_number){
        log_message('debug', 'Operator/deleteEmailRedeem');
        if (!$this->is_login()) {
            $this->safe_redirect('Operator/login');
        }
        if (!isset($order_number)){
            echo "/Operator/deleteEmailRedeem/&lt;order_number&gt;";
        } else {
            log_message('debug', 'Operator/deleteEmailRedeem order_number $order_number');
            $result = $this->emailQueueRedeem->delete_data($order_number);
            if ($result){
                echo "delete successful order_number $order_number";
            } else {
                echo "delete failed order_number $order_number";
            }
        }
    }
}