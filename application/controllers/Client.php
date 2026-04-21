<?php

class Client extends MY_Controller {

    const COMMON_MENU = 'client';
    const NO_ERROR = '00';
    const NEW_ORDER = "01"; // ok
    const ORDERD = "02"; // error
    const DUPLICATED = "03"; // error
    const ADD_ORDER = "04"; // ok
    const IS_ERROR = "05"; // error
    const INVALID_EMAIL = "06"; // error
    const NOT_APPROVED_YET = "07"; // error    
    const ERR_ADDR = '20';           // receiving invalid email address
    const ERR_PERSONAL_INFO = '31';  // receiving error personal infomation.

    public function __construct() {
        parent::__construct();
        $this->load->model('Personal_model', 'personal');
        $this->load->model('User_model', 'user');
        $this->load->model('Order_model', 'order');
        $this->load->model('Activity_model', 'act');
        $this->load->model('EmailQueue_model', 'emailQueue');
        $this->load->library('exchanger');
        $this->load->library('interbank');
        $this->load->library('saveImage');
        $this->load->library('email');
        date_default_timezone_set("Asia/Tokyo");
    }

    /*****************************************
     * 登録
     *****************************************/

    /**
    * A function login of Client
    */
    public function login() {
        log_message('debug', 'Client/login');

        $header = array(
            'title' => 'エージェントログイン',
            'role' => '',
            'current_menu' => ''
        );

        $family_name = isset($_POST['family_name']) ? $_POST['family_name'] : null;
        $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : null;
        $birthday = isset($_POST['birthday']) ? $_POST['birthday'] : null;

        //check param for login valid.
        if ($family_name == null || $first_name == null || $birthday == null) {
            //load login page.
            $this->load->view('common/header', $header);
            $this->load->view('client/loginClient');
            $this->load->view('common/footer');
            return;
        } else {
            $param =  array(
                'role' => $this->config->item('role_client'),
                'family_name' => $family_name,
                'first_name' => $first_name,
                'birthday' => $birthday
            );
            if ($this->isLoginClient($param) == FALSE) {
                $body = array(
                    'isError' => self::ERR_PERSONAL_INFO,
                    'arrPersonalInfo' => array(
                        'family_name' => $family_name,
                        'first_name' => $first_name,
                        'birthday' => $birthday
                    )
                );
                $this->load->view('common/header', $header);
                $this->load->view('client/loginClient', $body);
                $this->load->view('common/footer');
                return;                
            }
        }
        //login sucessfull
        $this->safe_redirect('Client/quantaWallet');
    }

    /**
    * A function quantaWallet after submit login-Client successfull.
    */
    public function quantaWallet() {
        log_message('debug', 'Client/quantaWallet');

        if (!$this->is_login()) {
            $this->safe_redirect('Client/login');
        }

        $header = array(
            'current_menu' => 'quantaWallet',
            'title' => 'Quanta Wallet',
            'role' => self::COMMON_MENU
        );
        $this->load->view('common/header', $header);
        $this->load->view('client/quanta_wallet');
        $this->load->view('common/footer');      
    }

    public function redeemInfo() {
        log_message('debug', 'Client/redeemInfo');

        if (!$this->is_login()) {
            $this->safe_redirect('Client/login');
        }

        $header = array(
            'current_menu' => 'redeemInfo',
            'title' => 'QNT還元サポート',
            'role' => self::COMMON_MENU
        );
        $this->load->view('common/header', $header);
        $this->load->view('client/redeem_info');
        $this->load->view('common/footer');      
    }

    /**
     * 登録+初回注文する
     */
    public function regAccount($user_hash) {
        log_message('debug', 'Client/regAccount');
        $this->load->model('screen/RegisterUser_Model', 'regUser');
        $this->load->library('form_validation');

        // 不慮のアクセスを判定する
        $status = $this->user->is_userhash($user_hash, $this->config->item('role_client'));
        if (null == $status) {
            $header = array(
                'title' => 'ページが見つかりませんでした。',
                'role' => '',
                'current_menu' => ''
            );
            $this->load->view('common/header', $header);
            $this->load->view("errors/404");
            return;
        }

        // 接続があったユーザをログに出力する
        log_message('debug', 'user_hush=' . $user_hash);

        // 二重登録を防ぐ
        $result = $this->user->can_not_recursive($user_hash);
        if (0 < $result->num_rows()) {
            log_message('debug', 'DUPLICATED');
            $header = array(
                'title' => '既に登録済み',
                'role' => '',
                'current_menu' => ''
            );
            $body = array(
                'ordered' => self::DUPLICATED
            );
            $this->load->view('common/header', $header);
            $this->load->view('client/completeOrder', $body);
            $this->load->view('common/footer');
            return;
        }

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
        $header = array(
            'title' => '初回注文',
            'role' => '',
            'current_menu' => ''
        );
        $row = $this->user->get_user_by_userhash($user_hash);
        $user = new $this->regUser(array('user_hash' => $user_hash));
        $body = array(
            'user_hash' => $user_hash,
            'rank' => $row->role,
            'user' => $user,
            'userPostData' => $_POST
        );
        $this->load->view('common/header', $header);
        $this->load->view('client/regClient', $body);
        $this->load->view('common/footer');
    }

    /**
     * 追加注文する
     */
    public function addOrder() {
        log_message('debug', 'Client/addOrder');
        $header = array(
            'title' => '再注文',
            'role' => ''
        );
        $body = array(
            'isError' => '00'
        );
        $this->load->view('common/header', $header);
        $this->load->view('client/addOrder', $body);
        $this->load->view('common/footer');
    }

    // set error message when imageExtension is not valid
    public function imageExtensionError($str){
        $this->form_validation->set_message('image_name', 'image_name');
        return FALSE;
    }
    // set error message when image size is not valid
    public function imageFileSizeError($str){
        $this->form_validation->set_message('image_size', 'image_size');
        return FALSE;
    }

    public function emailClientAlreadyExists($str){
        $user = $this->user->get_user_by_email($str);
        if (null == $user) {
            return TRUE;
        }
        return TRUE;
        $this->form_validation->set_message('email_exists', 'email_exists');
        return FALSE;
    }

    /**
     * 注文時の入力完了を表示する
     */
    public function completeOrder() {
        log_message('debug', 'Client/completeOrder');
        $this->load->model('screen/RegisterUser_Model', 'regUser');
        $this->load->library('form_validation');

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        // 不慮のアクセスを判定する
        if (!isset($_POST) || empty($_POST)) {
            // check php limit post size
            $post_max_size = getPostMaxSize();
            $significant_post_max_size = ini_get('post_max_size');
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
            {
                die('Upload file that is exceed post size limit. The file size limit at ' . $significant_post_max_size);
            }

            $header = array(
                'title' => 'ページが見つかりませんでした。',
                'role' => ''
            );
            $this->load->view('common/header', $header);
            $this->load->view("errors/404");
            return;
        }

        // 個人情報
        $user = new $this->regUser($_POST);

        $is_first = !empty($user->createNewClient);

        $role = array('03'); 
        $userInDB = $this->user->get_user_by_email_role($_POST['email'],$role);

        if ($is_first) {
            log_message('debug', 'user_hush=' . $_POST['user_hash']);
            foreach ($user->getRulesClient($_FILES) as $name => $rule){
                $this->form_validation->set_rules($name, $rule['title'], $rule['rule_list']);
            }

            if ($this->form_validation->run() == FALSE || (@$userInDB->num_rows() > 0)) {
                $user_hash = $user->user_hash;
                $row = $this->user->get_user_by_userhash($user_hash);
                if (empty($row)){
                    show_404();
                }
                $arrFieldError = $this->form_validation->error_array();

                if(@$userInDB->num_rows() > 0) {
                    //$emailInvalid = array('email' => 'このメールアドレスは登録されていません。');
                    $emailInvalid = array('email' => 'このメールアドレスは登録されました。');
                    $arrFieldError = array_merge($arrFieldError, $emailInvalid);           
                }

                $header = array(
                    'title' => '既に登録済み',
                    'role' => '',
                    'current_menu' => ''
                );
                $body = array(
                    'user_hash' => $user_hash,
                    'rank' => $row->role,
                    'user' => $user,
                    'arrFieldError' => $arrFieldError,
                    'userPostData' => $_POST
                );
                $this->load->view('common/header', $header);
                $this->load->view('client/regClient', $body);
                $this->load->view('common/footer');
                return;
            }
        } else {
            log_message('debug', 'user_hush=' . (null == $user) ? "not user" : $user->user_hash);
        }
        
        ////////////begin check validation addOrder.
        if(in_array("addOrderPost", $_POST)){
            $arrFieldError = array();
            $userAddOrder = new $this->regUser($_POST);
            //set rule for fields.
            foreach ($userAddOrder->getRulesAddOrder() as $name => $rule){                
                $this->form_validation->set_rules($name, $rule['title'], $rule['rule_list']);
            }
     
            // validate input data
            if(!$this->form_validation->run()) {
                $arrFieldError = $this->form_validation->error_array();
            }
            // $role = array('03'); 
            // $userInDB = $this->user->get_user_by_email_role($_POST['email'],$role);
            
            if(is_null($userInDB) ||  $userInDB->num_rows() == 0) {
                $emailInvalid = array('email' => 'このメールアドレスは登録されていません。');
                    $arrFieldError = array_merge($arrFieldError, $emailInvalid);           
            }

            if(!empty($arrFieldError)){
                $header = array(
                'title' => '注文情報の入力が完了しました',
                'role' => ''
                );
                $body = array(
                    'isError' => self::IS_ERROR,
                    'arrFieldError' => $arrFieldError,
                    'userPostData' => $_POST,
                    'ordered' => self::ADD_ORDER
                );
                
                $this->load->view('common/header', $header);
                $this->load->view('client/addOrder', $body);
                $this->load->view('common/footer');
                return;
            }
        }
        ///end function check validation addOrder

        $bRegFirst = false;

        // 初回注文か再注文か
        if ($is_first) {
            // ------------------------------
            // 個人情報を登録する
            // ------------------------------
            log_message('debug', 'NEW PERSONAL');
            $pdata = $user->getPersonalData();
            $personal_id = $this->personal->insert_personal($pdata);
            if (null == $personal_id) {
                $this->safe_redirect("Client/regAccount");
            }

            // 写真を保存する
            log_message('debug', 'NEW PHOTO for ' . $personal_id);
            $formname = 'photo1'; // 1枚目のファイルインプットフォームの項目名
            $filename = $this->saveimage->makeImgFile($formname, $personal_id, 1);

            $formname = 'photo2'; // 2枚目のファイルインプットフォームの項目名
            $tempfilename = isset($_FILES[$formname]) ? $_FILES[$formname]['tmp_name'] : null;
            log_message('debug', '>> img | tempfilename=' . $tempfilename);
            if (null != $tempfilename) {
                $filename = $this->saveimage->makeImgFile($formname, $personal_id, 2);
            }

            $formname = 'photo3'; // 3枚目のファイルインプットフォームの項目名
            $tempfilename = isset($_FILES[$formname]) ? $_FILES[$formname]['tmp_name'] : null;
            log_message('debug', '>> img | tempfilename=' . $tempfilename);
            if (null != $tempfilename) {
                $filename = $this->saveimage->makeImgFile($formname, $personal_id, 3);
            }

            $formname = 'photo4'; // 4枚目のファイルインプットフォームの項目名
            $tempfilename = isset($_FILES[$formname]) ? $_FILES[$formname]['tmp_name'] : null;
            log_message('debug', '>> img | tempfilename=' . $tempfilename);
            if (null != $tempfilename) {
                $filename = $this->saveimage->makeImgFile($formname, $personal_id, 4);
            }
            // ------------------------------
            // アカウント情報を更新する
            // ------------------------------
            // アカウントが登録済みか確認する, 登録済みであれば新しいハッシュを発行して登録を進める
            $user_temp = $this->user->get_registered_userhash($_POST["user_hash"]);
            if (null == $user_temp || null == $user_temp->result()) {
                    // ------------------------------
                    // アカウント情報を更新する
                    // ------------------------------
                    log_message('debug', 'UPDATE ACCOUNT');
                    $udata = $user->getUserData();
                    $udata['personal_id'] = $personal_id;
                    $this->user->update_by_userhash($_POST['user_hash'], $udata);
                    $client_hash = $_POST['user_hash'];
                    $bRegFirst = true;
            } else {
                    $user_temp = $user_temp->result()[0];
                    // ------------------------------
                    // アカウント情報を追加する
                    // ------------------------------
                    log_message('debug', 'INSERT ACCOUNT');
                    $udata = $user->getUserData();
                    $udata['agent_uid'] = $user_temp->agent_uid;
                    $udata['personal_id'] = $personal_id;
                    $udata['role'] = $user_temp->role;
                    $udata['memo'] = $user_temp->memo;
                    $udata['can_recursive'] = 1;
                    $client_hash = $this->user->insert_user($udata);

                    //add by hoinh for update_by & update_at field
                    $user_uid = $this->user->getUidByUserHash($user_temp->agent_uid);
                    $dataUpdate = array(
                        'create_by' => $user_uid,
                        'update_at' => $now
                        );
                    $this->user->updateDataByUserHash($client_hash, $dataUpdate);
            }
            // アクティビティを登録する
            $adata = array(
                'user_hash' => $_POST["user_hash"],
                'activity_code' => $this->config->item('act_reg_personal')
            );
            $this->act->insert_activity($adata);
        }

        // ------------------------------
        // 注文を登録する
        // ------------------------------
        // 銀行振込の場合/ビットコイン支払いの場合
        $status = ($this->config->item('payby_bank') == $user->pay_method) ?
                $this->config->item('order_orderby_bank') : $this->config->item('order_orderby_btc');
        // ユーザハッシュ
        $agent_hash = "";
        $client_hash = "";
        //if ($is_first) {$bRegFirst = false;
        if ($bRegFirst) {
            // 初回注文は、URLの(で渡された)user_hashを取得する
            $agent_hash = $this->act->get_agentuid_by_userhash($_POST["user_hash"]);
            $client_hash = $_POST["user_hash"];
        } else {
            // 再注文は、入力されたemailでDBに登録されているuser_hashを取得する
            $result = $this->user->get_user_by_email($user->email);
            if (null == $result || $this->config->item('role_client') != $result->role) {
                $header = array(
                    'title' => '再注文',
                    'role' => ''
                );
                $body = array(
                    'ordered' => self::INVALID_EMAIL,
                    'userPostData' => $_POST
                );
                $this->load->view('common/header', $header);
                $this->load->view('client/completeOrder', $body);
                $this->load->view('common/footer');
                return;
            }
            if (!$is_first && $this->config->item('act_approved') != $result->status) {            
                $header = array(
                    'title' => '再注文',
                    'role' => ''
                );
                $body = array(
                    'ordered' => self::NOT_APPROVED_YET,
                    'userPostData' => $_POST
                );
                $this->load->view('common/header', $header);
                $this->load->view('client/completeOrder', $body);
                $this->load->view('common/footer');
                return;
            }
            $agent_hash = $result->agent_uid;
            $client_hash = $result->user_hash;
        }
        $currency_unit = $this->config->item('currency_usd');

        $new_user = $this->user->get_user_by_email($user->email);

        if ($is_first && null == $user) {
            log_message('debug', 'IS_ERROR'); // error
            $header = array(
                'title' => 'エラー',
                'role' => ''
            );
            $body = array(
                'ordered' => self::IS_ERROR
            );
            $this->load->view('common/header', $header);
            $this->load->view('client/completeOrder', $body);
            $this->load->view('common/footer');
            return;
        }
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $odata = array(
            'status' => $status,
            'agent_uid' => $agent_hash,
            'client_uid' => $client_hash,
            'pay_method' => $user->pay_method,
            'currency_unit' => $currency_unit,
            'amount' => doubleval($user->amount),
            'memo' => ($this->config->item('payby_bank') == $user->pay_method) ? '' : doubleval($user->amount * (1 - (float) $this->config->item('charge') / 100)),
			'create_by' => $new_user->uid,
            'create_at' => $now
        );
        $order_number = $this->order->insert_order($odata);

        // アクティビティを登録する
        $activity = ($is_first) ? $this->config->item('act_order') : $this->config->item('act_addorder');
        $adata = array(
            'user_hash' => $client_hash,
            'activity_code' => $activity,
            'object' => $order_number,
            'memo' => $user->amount . "USD",
			'create_by' => $new_user->uid,
            'create_at' => $now
        );
        $this->act->insert_activity($adata);

        // メール送信
        $replace_token = array(
            //$user->family_name, $user->first_name,
            $new_user->family_name, $new_user->first_name, 
            $this->getEmailSignature()
        );
        $email_template = $this->parseEmailTemplate('mail_accept_order', $replace_token);
        $emailData = array(
            'to' => $user->email,
            'subject' => $email_template['subject'],
            'message' => $email_template['message'],
            'object' => $order_number,
            'memo' => 'Client complete order',
            'create_by' => $new_user->uid,
            'create_at' => $now
        );
        $this->emailQueue->insert_data(array($emailData));

        $header = array(
            'title' => '注文情報の入力が完了しました',
            'role' => ''
        );
        $body = array(
            'ordered' => self::NEW_ORDER
        );
        if (!$is_first) {
            $body = array(
                'ordered' => self::ADD_ORDER
            );
        }
        $this->load->view('common/header', $header);
        $this->load->view('client/completeOrder', $body);
        $this->load->view('common/footer');
    }

    /*****************************************
     * 各情報を表示する
     *****************************************/

    /**
     * BTC支払いの場合のみ
     * BTCの振込先アドレスを表示する
     */
    public function viewBtcAddr($order_number = null, $user_hash = null) {
        log_message('debug', 'Client/viewBtcAddr');

        if (null == $user_hash && null == $order_number) {
            $header = array(
                'title' => 'ページが見つかりませんでした。',
                'role' => ''
            );
            $this->load->view('common/header', $header);
            $this->load->view("errors/404");
            return;
        }

        // 受付番号が入力されているか
        $body = array();
		$row = null;
        if ($user_hash != null and $order_number != null ){
            $row = $this->order->select_usd_by_userhash_and_order_number($user_hash,$order_number);
        } else {
            $row = $this->order->select_usd_by_order_number($order_number);
        }

        if (null == $row){
            $header = array(
                'title' => 'ページが見つかりませんでした。',
                'role' => ''
            );
            $this->load->view('common/header', $header);
            $this->load->view("errors/404");
            return;
        }else{

            $amount = 0;
            $styleView = null;

            if((($this->config->item('order_notify_bankaccount') == $row->status) 
                || ($this->config->item('order_notify_btcaddr') == $row->status)) 
                && $this->isExpiredDate($row->expiration_date) == FALSE ) {

                $amount = money_format_qts($row->amount,2);
                $styleView = 1;//style view waiting client send money.
            }else if(($this->config->item('order_notify_bankaccount') == $row->status) ||
                ($this->config->item('order_notify_btcaddr') == $row->status) ||
                ($this->config->item('order_invalid') == $row->status) ||
                ($this->config->item('order_expired') == $row->status)) {                              
                
                $styleView = 2;//style view expired-date
            }else{                
                $styleView = 3;//style view has been sent money.
            } 

            $body = array('order_number' => $row->order_number,
                'addr' => $row->receive_address,
                'amount' => $amount,    
                'styleView' => $styleView
            ); 
        }

		// if (null != $row
  //               && intval($this->config->item('order_notify_btcaddr')) < intval($row->status)) {
  //           $body = array('order_number' => $row->order_number,
  //               'addr' => $row->receive_address,
  //               'amount' => 0,
  //               'expired' => false,
  //               'isAlready' => true
  //           );
  //       } else if (null != $row) {
  //           $datetime1 = strtotime("now");
  //           $datetime2 = strtotime($row->expiration_date);
  //           $secs = $datetime1 - $datetime2;

  //           $expired = false;
  //           if (0 < $secs) {
  //               $expired = true;
  //           }

  //           $body = array('order_number' => $row->order_number,
  //               'addr' => $row->receive_address,
  //               'amount' => money_format_qts($row->amount,2),
  //               'expired' => $expired,
  //               'isAlready' => false
  //           );
  //       } else {
  //           $header = array(
  //               'title' => 'ページが見つかりませんでした。',
  //               'role' => self::COMMON_MENU
  //           );
  //           $this->load->view('common/header', $header);
  //           $this->load->view("errors/404");
  //           return;
  //       }

        $header = array(
            'title' => 'ビットコイン送金先アドレス',
            'role' => ''
        );
        $this->load->view('common/header', $header);
        $this->load->view('client/viewBtcAddr', $body);
        $this->load->view('common/footer');
    }

    /**
    * A function isExpiredDate
    * @param: expiration_date
    * @return: true if expiredDate <= now().
    */
    private function isExpiredDate($expiration_date){
        log_message('debug', 'Client/isExpiredDate');
        $expiredDate = strtotime($expiration_date);
        $currentDate = strtotime(date("Y-m-d"));
        
        if ($expiredDate < $currentDate) {
            return true;
        }
        return false;
    }

    /**
     * 換金レートを取得する
     */
    public function getUsdBtcRate() {
        log_message('debug', 'Client/getUsdBtcRate');
        echo $this->interbank->getUsdBtcRate();
    }

    /**
     * 還元コードに紐づくトークン量を表示する
     */
    public function viewToken() {
        log_message('debug', 'Client/viewToken');

        $token_code = isset($_POST['token_code']) ? $_POST['token_code'] : null;

        $body = array(
            'token_code' => "",
            'amount' => "0",
            'isError' => 0
        );
        if (null != $token_code) {
            $record = $this->token->select_by_tokencode($token_code);
            if (null == $record) {
                $body = array(
                    'token_code' => $token_code,
                    'amount' => "",
                    'isError' => 1
                );
            } else {
                $body = array(
                    'token_code' => $token_code,
                    'amount' => $record->quantity,
                    'isError' => 0
                );

                $order = $this->order->select_by_ordernumber($record->order_number);
                $adata = array(
                    'user_hash' => $order->client_uid,
                    'activity_code' => $this->config->item('act_view_token'),
                    'object' => $token_code
                );
                $this->act->insert_activity($adata);
            }
        }

        $header = array(
            'title' => 'トークン量確認',
            'role' => ''
        );
        $this->load->view('common/header', $header);
        $this->load->view('client/viewToken', $body);
        $this->load->view('common/footer');
    }

    /**
     * icoFinish
     */
    public function icoFinish() {
        log_message('debug', 'Client/icoFinish');
        $header = array(
            'title' => 'icoFinish',
            'role' => '',
            'current_menu' => ''
        );
        $body = array(
            'isError' => '00'
        );
        $this->load->view('common/header', $header);
        $this->load->view('client/icoFinish', $body);
        $this->load->view('common/footer');
    }

}
