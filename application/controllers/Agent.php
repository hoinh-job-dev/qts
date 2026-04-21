<?php

class Agent extends MY_Controller {

    protected $defaultUser = 'QTS_Agent';

    const COMMON_MENU = 'agent';
    const NO_ERROR = '00';
    const IS_ERROR = "05"; // error
    const ERR_ADDR_PASS = '10';      // receiving invalid email address or invalid password
    const ERR_ADDR = '20';           // receiving invalid email address
    const ERR_ADDR_WHEN_ASKED = '30'; // receiving invalid email address
    const NEW_ORDER = "01";
    const ORDERD = "02";
    const DUPLICATED = "03";

    public function __construct() {
        parent::__construct();
        $this->load->model('General_model', 'general');
        $this->load->model('User_model', 'user');
        $this->load->model('Activity_model', 'act');
        $this->load->model('Personal_model', 'personal');
        $this->load->model('Commission_model', 'commission');
        $this->load->model('PrePasswd_model', 'prepass');
        $this->load->model('EmailQueue_model', 'emailQueue');
        $this->load->library('saveImage');
        date_default_timezone_set("Asia/Tokyo");
        $this->load->model('screen/SearchAgent_Model', 'search');
    }

    /*****************************************
     * 登録
     *****************************************/

    /**
     * 個人情報、同意、確認書類を登録する画面を表示する
     */
    public function regAccount($user_hash) {
        log_message('debug', 'Agent/regAccount');
        $this->load->model('screen/RegisterUser_Model', 'regUser');
        $this->load->library('form_validation');

        // 不慮のアクセスを判定する
        $role = array_keys($this->config->item('role_agent_commission'));
        $status = $this->user->is_userhash($user_hash, $role);
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

        $header = array(
            'title' => '代理店登録',
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
        $this->load->view('agent/regAgent', $body);
        $this->load->view('common/footer');
    }

    public function imageExtensionError($str){
        $this->form_validation->set_message('image_name', 'image_name');
        return FALSE;
    }
    public function imageFileSizeError($str){
        $this->form_validation->set_message('image_size', 'image_size');
        return FALSE;
    }

    public function emailAgentAlreadyExists($str){
        $role = array_keys($this->config->item('role_agent_commission'));
        $user = $this->user->get_user_by_email_role($str,$role)->result();
        if (null == $user) {
            return TRUE;
        }
        $this->form_validation->set_message('email_exists', 'email_exists');
        return FALSE;
    }

    /**
     * 個人情報、同意、確認書類を登録する
     * 個人情報はKYCデータベースへ保存、
     */
    public function complete() {
        log_message('debug', 'Agent/complete');
        $this->load->model('screen/RegisterUser_Model', 'regUser');
        $this->load->library('form_validation');

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
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
                'role' => '',
                'current_menu' => ''
            );
            $this->load->view('common/header', $header);
            $this->load->view("errors/404");
            return;
        }

        $user = new $this->regUser($_POST);

        log_message('debug', 'user_hash=' . $user->user_hash);

        foreach ($user->getRulesAgent($_FILES) as $name => $rule){
            $this->form_validation->set_rules($name, $rule['title'], $rule['rule_list']);
        }


        if ($this->form_validation->run() == FALSE) {
            $user_hash = $user->user_hash;
            $row = $this->user->get_user_by_userhash($user_hash);
            if (empty($row)){
                show_404();
            }
            $arrFieldError = $this->form_validation->error_array();

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
            $this->load->view('agent/regAgent', $body);
            $this->load->view('common/footer');
            return;
        }

        $personal_id = $this->personal->insert_personal($user->getPersonalData());
        if (null == $personal_id) {
            $this->safe_redirect("Agent/regAccount");
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
        $child_hash = "";
        $user_temp = $this->user->get_registered_userhash($_POST["user_hash"]);
        if (null == $user_temp || null == $user_temp->result()) {
            // ------------------------------
            // アカウント情報を更新する
            // ------------------------------
            log_message('debug', 'UPDATE ACCOUNT');
            $udata = $user->getUserData();
            $udata['personal_id'] = $personal_id;
            $this->user->update_by_userhash($user->user_hash, $udata);
            $child_hash = $user->user_hash;
        } else {
            // ------------------------------
            // アカウント情報を追加する
            // ------------------------------
            log_message('debug', 'INSERT ACCOUNT');
            $udata = $user->getUserData();
            $user_temp = $user_temp->result()[0];
            $udata['agent_uid'] = $user_temp->agent_uid;
            $udata['personal_id'] = $personal_id;
            $udata['role'] = $user_temp->role;
            $udata['agent_uid'] = $user_temp->agent_uid;
            $udata['status'] = $this->config->item('act_reg_personal');
            $udata['memo'] = $user_temp->memo;
            $udata['can_recursive'] = 1;
            $child_hash = $this->user->insert_user($udata);
            $user_uid = $this->user->getUidByUserHash($user_temp->agent_uid);
            $dataUpdate = array(
                'create_by' => $user_uid,
                'update_at' => $now
            );
            $this->user->updateDataByUserHash($child_hash, $dataUpdate);
        }

        $role = array_keys($this->config->item('role_agent_commission'));
        $user = $this->user->get_user_by_email_role($user->email, $role)->result();
        if (null == $user) {
            log_message('debug', 'IS_ERROR'); // error
            $header = array(
                'title' => 'エラー',
                'role' => self::COMMON_MENU
            );
            $body = array(
                'ordered' => self::IS_ERROR
            );
            $this->load->view('common/header', $header);
            $this->load->view('agent/complete', $body);
            $this->load->view('common/footer');
            return;
        }

        // アクティビティを登録する
        $adata = array(
            'user_hash' => $child_hash,
            'activity_code' => $this->config->item('act_reg_personal')
        );
        $this->act->insert_activity($adata);

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        //RegAgent received email 4 set password.
        $set_password_url = base_url('Agent/setPassword/' . $child_hash);
        $agent_login_url = base_url('Agent/login');
        $replace_token = array(
            $user[0]->family_name, $user[0]->first_name, 
            $set_password_url, $agent_login_url,
            $this->getEmailSignature()
        );
        $email_template = $this->parseEmailTemplate('mail_agent_register', $replace_token);
        $emailData = array(
            'to' => $user[0]->email,
            'subject' => $email_template['subject'],
            'message' => $email_template['message'],
            'object' => $user[0]->uid,
            'memo' => 'Agent register complete',
            'create_by' => $user[0]->uid,
            'create_at' => $now
        );
        $this->emailQueue->insert_data(array($emailData));

        $header = array(
            'title' => '登録完了',
            'role' => '',
            'current_menu' => ''
        );
        $body = array(
            'ordered' => self::NEW_ORDER
        );
        $this->load->view('common/header', $header);
        $this->load->view('agent/complete', $body);
        $this->load->view('common/footer');
    }

    /*****************************************
     * ログイン
     *****************************************/

    /**
     * 有効なメールアドレスの時にパスワードを設定してログインする
     * @param type $user_hash
     * @return type
     */
    public function setPassword($user_hash) {
        log_message('debug', 'Agent/setPassword');

        $user = $this->user->get_user_by_userhash($user_hash);
        if (null == $user || null == $user->user_hash) {
            $header = array(
                'title' => 'ページが見つかりませんでした。',
                'role' => '',
                'current_menu' => ''
            );
            $this->load->view('common/header', $header);
            $this->load->view("errors/404");
            return;
        }
        if ("" != $user->password) {
            $this->safe_redirect('Agent/login');
        }

        $header = array(
            'title' => 'パスワード設定',
            'role' => '',
            'current_menu' => ''
        );
        $bdata = array(
            'userhash' => $user_hash
        );
        $this->load->view('common/header', $header);
        $this->load->view('agent/setPassword', $bdata);
        $this->load->view('common/footer');
    }

    /**
     * ログイン
     * @param type $isError
     */
    public function login($isError = self::NO_ERROR) {
        log_message('debug', 'Agent/login');

        $header = array(
            'title' => '代理店ログイン',
            'role' => '',
            'current_menu' => ''
        );
        $body = array(
            'isError' => $isError
        );
        $this->load->view('common/header', $header);
        $this->load->view('agent/loginAgent', $body);
        $this->load->view('common/footer');
    }

    /**
     * ログイン後のHOME画面を表示する
     * アカウント情報表示
     */
    public function home($paramName = '', $paramValue = '') {
        log_message('debug', 'Agent/home');
        if($paramName == 'update' && $paramValue == 'success') {
            $updateMessage = 'Account information has been updated successfully';
        }

        // ログイン判定
        $user_hash = isset($_POST['user_hash']) ? $_POST['user_hash'] : null;
        $email = isset($_POST['email']) ? $_POST['email'] : null;
        $pw = isset($_POST['password']) ? $_POST['password'] : null;
        if (null != $user_hash && null != $pw) {
            $data = array(
                'password' => $this->user->encrypt_password($pw)
            );
            $this->user->update_by_userhash($user_hash, $data);

            $user = $this->user->get_user_by_userhash($user_hash);
            $role = array($this->config->item('role_sysadmin'));
            $role = array_merge($role, array_keys($this->config->item('role_agent_commission')));
            if (!$this->loginSession($user->email, $pw, $role)) {
                $this->safe_redirect('Agent/login/' . self::ERR_ADDR_PASS);
            }
        }
        if (null == $email || null == $pw) {
            if (!$this->is_login()) {
                $this->safe_redirect('Agent/login');
            }
        } else {
            $role = array_keys($this->config->item('role_agent_commission'));
            if (!$this->loginSession($email, $pw, $role)) {
                $this->safe_redirect('Agent/login/' . self::ERR_ADDR_PASS);
            }
        }

        // アカウント取得
        $user_hash = (null == $user_hash) ? $this->getSessionValue('user_hash') : $user_hash;
        $user = $this->user->get_user_by_userhash($user_hash);
        if ("" == $user->btc_address) {
            // コミッション送金用のビットコインアドレスが登録されていない場合
            $this->safe_redirect('Agent/setCommissionBtcAddress');
        }

        // パスワードを隠す
        $password = "";
        for ($i = 0; $i < strlen($user->password); $i++) {
            $password = $password . "*";
        }

        $header = array(
            'current_menu' => 'home',
            'title' => 'アカウント情報',
            'role' => self::COMMON_MENU,
            'agentrank' => $user->role,
            'userstatus' => $user->status
        );
        $body = array(
            'agentrank' => $user->role,
            'email' => $user->email,
            'password' => $password,
            'type' => $user->type,
            'typename' => ("1" == $user->type) ? "個人" : "法人",
            'family_name' => $user->family_name,
            'first_name' => $user->first_name,
            'company_name' => $user->company_name,
            'family_name_kana' => $user->family_name_kana,
            'first_name_kana' => $user->first_name_kana,
            'company_name_kana' => $user->company_name_kana,
            'btc_address' => $user->btc_address,
            'updateMessage' => @$updateMessage
        );
        $this->load->view('common/header', $header);
        $this->load->view('agent/viewAccount', $body);
        $this->load->view('common/footer');
    }

    /**
     * コミッション支払い用のビットコインアドレスを登録する
     * @return type
     */
    public function setCommissionBtcAddress() {
        log_message('debug', 'Agent/setCommissionBtcAddress');

        $user_hash = $this->getSessionValue('user_hash');
        $user = $this->user->get_user_by_userhash($user_hash);
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        if (null == $user) {
            $header = array(
                'title' => 'ページが見つかりませんでした。',
                'role' => '',
                'current_menu' => ''
            );
            $this->load->view('common/header', $header);
            $this->load->view("errors/404");
            return;
        }

        $addr = isset($_POST['btc_address']) ? $_POST['btc_address'] : null;
        if (null != $addr) {
            $doc_absolute_dir = FCPATH . $this->config->item('path_pdf_doc');
            $doc_file_name = $this->config->item('doc_guide_agent');

            if(file_exists($doc_absolute_dir . $doc_file_name) == TRUE){
                $data = array(
                    'btc_address' => $addr,
                    'update_by' => $user_hash,
                    'update_at' => $now
                );
                $this->user->update_by_userhash($user_hash, $data);            
                $this->safe_redirect('Agent/guideAgent');
            }else{
                $data = array(
                    'btc_address' => $addr,
                    'rsv_char_2' => 'readGuide',
                    'update_by' => $user_hash,
                    'update_at' => $now
                );
                $this->user->update_by_userhash($user_hash, $data);
                $this->safe_redirect('Agent/home');   
            }

            // $data = array(
            //     'btc_address' => $addr
            // );
            // $this->user->update_by_userhash($user_hash, $data);
            // //$this->safe_redirect('Agent/home');
            // $this->safe_redirect('Agent/guideAgent');
        }

        $header = array(
            'title' => 'コミッション用のビットコインアドレスを登録する',
            'role' => self::COMMON_MENU,
            'agentrank' => $user->role,
            'current_menu' => 'setCommissionBtcAddress',
            'userstatus' => $user->status
        );
        $bdata = array(
            'userhash' => $user_hash
        );
        $this->load->view('common/header', $header);
        $this->load->view('agent/setCommissionBtcAddress', $bdata);
        $this->load->view('common/footer');
    }

    /**
     * ログアウト
     */
    public function logout() {
        log_message('debug', 'Agent/logout');

        $this->logoutSession();
        $this->safe_redirect('Agent/login');
    }

    /**
     * パスワードを忘れた時
     */
    public function ask_password() {
        log_message('debug', 'Agent/ask_password');

        $email = isset($_POST['askemail']) ? $_POST['askemail'] : null;
        if (null == $email) {
            $this->safe_redirect('Agent/login/' . self::ERR_ADDR_WHEN_ASKED);
        }

		// 仮パスワードID作成
		$userobj = $this->user->get_user_by_email_role($email, array_keys($this->config->item('role_agent_commission')));
		if (null == $userobj->result()) {
            $this->safe_redirect('Agent/login');
        }
		$user = $userobj->result()[0];
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
		$passid = md5($email.$now);
        $data = array("passid" => $passid
                , "email" => $email
                , "pre_passwd" => ''
                , "create_by" => $user->uid
                , "create_at" => $now);
        $this->prepass->insert_prepasswd($data);

		// メール変更画面通知
        $reset_password_url = base_url('Agent/resetPasswd/' . $passid);
        $replace_token = array(
            $user->family_name, $user->first_name, 
            $reset_password_url,
            $this->getEmailSignature()
        );
        $email_template = $this->parseEmailTemplate('mail_respond_password', $replace_token);
        $emailData = array(
            'to' => $email,
            'subject' => $email_template['subject'],
            'message' => $email_template['message'],
            'object' => $user->uid,
            'memo' => 'Agent ask password',
            'create_by' => $user->uid,
            'create_at' => $now
        );
        $this->emailQueue->insert_data(array($emailData));

        $this->safe_redirect('Agent/login');
    }

    public function resetPasswd($passid) {
        log_message('debug', 'Agent/resetPassword');
        $bdata = array();
        $prepasswd = $this->prepass->get_prepasswd($passid);  
        if (null == $passid || null == $prepasswd || null == $prepasswd->passid) {           
            $bdata = array_merge(array($bdata), array('passid' => $passid,'isValid' => false));
        }else{
            $bdata = array_merge(array($bdata), array('passid' => $passid,'isValid' => true));
        }        

        $header = array(
            'title' => 'パスワード再設定',
            'role' => '',
            'current_menu' => ''
        );  
        $this->load->view('common/header', $header);
        $this->load->view('agent/resetPassword', $bdata);
        $this->load->view('common/footer');
    }

    public function updatePasswd() {
        log_message('debug', 'Agent/updatePasswd');

        $passid = isset($_POST['passid']) ? $_POST['passid'] : null;
        if (null == $passid) {
            $header = array(
                'title' => 'ページが見つかりませんでした。',
                'role' => '',
                'current_menu' => ''
            );
            $this->load->view('common/header', $header);
            $this->load->view("errors/404");
            return;
        }
        $password = isset($_POST['password']) ? $_POST['password'] : null;
        $this->prepass->update_passwd($passid, $password);

        $this->safe_redirect('Agent/login');
    }

    /*****************************************
     * リンクを発行する
     *****************************************/

    /**
     * 代理店のリンクを発行する
     * ログイン確認を行う
     */
    public function linkAgent() {
        log_message('debug', 'Agent/linkAgent');

        if (!$this->is_login()) {
            $this->safe_redirect('Agent/login');
        }

        // 代理店ごとに作成できるランクを作成
        $user_hash = $this->getSessionValue('user_hash');
        $user = $this->user->get_user_by_userhash($user_hash);
        $ranks = array();
        $rac1 = $this->config->item('role_agent_commission');
        $rac2 = $this->config->item('role_agent_commission');
        foreach($rac1 as $agent_role => $agent_commission) {
            if($user->role != $agent_role) continue;
            foreach($rac2 as $rankval => $percent) {
                if($percent < $agent_commission) {
                    $ranks[] = compact('rankval', 'percent');
                }
            }
        }

        $header = array(
            'current_menu' => 'linkAgent',
            'title' => '代理店リンク作成',
            'role' => self::COMMON_MENU,
            'agentrank' => $user->role,
            'user_hash' => "",
            'userstatus' => $user->status
        );
        $body = array(
            'ranks' => $ranks,
            'can_recursive' => 0
        );
        $this->load->view('common/header', $header);
        $this->load->view('agent/linkAgent', $body);
        $this->load->view('common/footer');
    }

    /**
     * 代理店のリンクを発行する
     */
    public function makeAgentLink() {
        log_message('debug', 'Agent/makeAgentLink');

        if (!$this->is_login()) {
            $this->safe_redirect('Agent/login');
        }
        // 代理店レコード作成
        $agent_uid = $this->getSessionValue('uid');
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
        $can_recursive = isset($_POST['can_recursive']) ? $_POST['can_recursive'] : 0;
        $memo = isset($_POST['memo']) ? $_POST['memo'] : "";

        $agent_hash = $this->getSessionValue('user_hash');
        $role = @$_POST['rank'];
        $role_agent_arr = array_keys($this->config->item('role_agent_commission'));
        if(!in_array($role, $role_agent_arr)) {
            $role = $role_agent_arr[count($role_agent_arr) - 1];
        }
        $data = array(
            'role' => $role,
            'agent_uid' => $agent_hash,
            'memo' => $memo,
			'can_recursive' => $can_recursive,
			'create_by' => $agent_uid,
            'create_at' => $now
        );
        $child_hash = $this->user->insert_user($data);
        log_message('debug', '$client_uid=' . $child_hash);

        $adata = array(
            'user_hash' => $agent_hash,
            'activity_code' => $this->config->item('act_gen_link'),
            'object' => $child_hash,
            'memo' => $memo,
			'create_by' => $agent_uid,
            'create_at' => $now
        );
        $this->act->insert_activity($adata);

        // 代理店ごとに作成できるランクを作成
        $user_hash = $this->getSessionValue('user_hash');
        $user = $this->user->get_user_by_userhash($user_hash);
        $ranks = array();
        $rac1 = $this->config->item('role_agent_commission');
        $rac2 = $this->config->item('role_agent_commission');
        foreach($rac1 as $agent_role => $agent_commission) {
            if($user->role != $agent_role) continue;
            foreach($rac2 as $rankval => $percent) {
                if($percent < $agent_commission) {
                    $ranks[] = compact('rankval', 'percent');
                }
            }
        }
        $header = array(
            'current_menu' => 'linkAgent',
            'title' => '代理店リンク作成',
            'role' => self::COMMON_MENU,
            'agentrank' => $user->role,
            'user_hash' => $child_hash,
            'userstatus' => $user->status,
            'btc_address' => $user->btc_address
        );
        $body = array(
            'ranks' => $ranks,
            'can_recursive' => $can_recursive
            
        );
        $this->load->view('common/header', $header);
        $this->load->view('agent/linkAgent', $body);
        $this->load->view('common/footer');
    }

    /**
     * 交換者のリンクを発行する
     */
    public function linkClient() {
        log_message('debug', 'Agent/linkClient');

        if (!$this->is_login()) {
            $this->safe_redirect('Agent/login');
        }

        $user_hash = $this->getSessionValue('user_hash');
        $user = $this->user->get_user_by_userhash($user_hash);
        $header = array(
            'current_menu' => 'linkClient',
            'title' => '交換者リンク作成',
            'role' => self::COMMON_MENU,
            'agentrank' => $user->role,
            'user_hash' => "",
            'userstatus' => $user->status
        );
         $body = array(
            'can_recursive' => 0
        );
        $this->load->view('common/header', $header);
        $this->load->view('agent/linkClient', $body);
        $this->load->view('common/footer');
    }

    /**
     * 交換者のリンクを発行する
     */
    public function makeClientLink() {
        log_message('debug', 'Agent/makeClientLink');

        if (!$this->is_login()) {
            $this->safe_redirect('Agent/login');
        }

        // 交換者リンク作成
        $agent_uid = $this->getSessionValue('uid');
        $agent_hash = $this->getSessionValue('user_hash');
        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());
        $can_recursive = isset($_POST['can_recursive']) ? $_POST['can_recursive'] : 0;
        $memo = isset($_POST['memo']) ? $_POST['memo'] : "";
        $data = array(
            'role' => $this->config->item('role_client'),
            'agent_uid' => $agent_hash,
            'memo' => $memo,
			'can_recursive' => $can_recursive,
			'create_by' => $agent_uid,
            'create_at' => $now
        );
        $client_hash = $this->user->insert_user($data);

        $adata = array(
            'user_hash' => $agent_hash,
            'activity_code' => $this->config->item('act_gen_link'),
            'object' => $client_hash,
            'memo' => $memo,
			'create_by' => $agent_uid,
            'create_at' => $now
        );
        $this->act->insert_activity($adata);

        $user_hash = $this->getSessionValue('user_hash');
        $user = $this->user->get_user_by_userhash($user_hash);
        $header = array(
            'current_menu' => 'linkClient',
            'title' => '交換者リンク作成',
            'role' => self::COMMON_MENU,
            'agentrank' => $user->role,
            'user_hash' => $client_hash,
            'userstatus' => $user->status
        );
        $body = array(
            'can_recursive' => $can_recursive
        );
        $this->load->view('common/header', $header);
        $this->load->view('agent/linkClient', $body);
        $this->load->view('common/footer');
    }

    /*****************************************
     * 各情報を表示する
     *****************************************/

    public function viewClients() {
        log_message('debug', 'Agent/viewClients');

        if (!$this->is_login()) {
            redirect('Agent/login');
        }

        ////begin
        if (isset($_POST['search'])){
            $data = $_POST['search'];
        } else {
            $data = array();
        }
        $search = new $this->search($data);

        $user_hash = $this->getSessionValue('user_hash');
        $user = $this->user->get_user_by_userhash($user_hash);
        // 交換者一覧取得
        $roles = array(
            $this->config->item('role_client')
        );
        $clients = $this->user->get_childs($user_hash, $roles, $search);

        $header = array(
            'current_menu' => 'viewClients',
            'title' => '交換者一覧',
            'role' => self::COMMON_MENU,
            'agentrank' => $user->role,
            'userstatus' => $user->status,
            'btc_address' => $user->btc_address
        );
        $body = array(
            'clients' => $clients,
            'search' => $search
        );
        $this->load->view('common/header', $header);
        $this->load->view('agent/viewClients', $body);
        $this->load->view('common/footer');
    }

    public function viewClients_21() {
        log_message('debug', 'Agent/viewClients');

        if (!$this->is_login()) {
            $this->safe_redirect('Agent/login');
        }

        ////begin
        if (isset($_POST['search'])){
            $data = $_POST['search'];
        } else {
            $data = array();
        }
        $search = new $this->search($data);
        ////end

        $user_hash = $this->getSessionValue('user_hash');
        $user = $this->user->get_user_by_userhash($user_hash);
        // 交換者一覧取得
        $roles = array(
            $this->config->item('role_client')
        );
        //$clients = $this->user->get_childs($user_hash, $roles);
        $clients = $this->user->get_childs($user_hash, $roles, $search);
        $client_list = array();
        foreach ($clients as $client){
            if (!isset($client_list[$client->order_number])) {
                $client_list[$client->order_number] = array(
                    'family_name' =>$client->family_name,
                    'first_name' =>$client->first_name);
            }
            $obj =  &$client_list[$client->order_number];
            if (!isset($obj['create_time']) || $obj['create_time'] > $client->create_at){
                $obj['create_time'] = $client->create_at;
            }
            if (!isset($obj['min_status']) || $obj['min_status'] > $client->status){
                $obj['min_status'] = $client->status;
                $obj['min_status_amount'] = $client->amount;
            }
            if ( (!isset($obj['max_status']) || $obj['max_status'] < $client->status)){
                $obj['max_status'] = $client->status;
                $obj['last_time'] = $client->create_at;
            }
            if ($client->status==14 || $client->status ==24) {
                $obj['max_status_amount'] = $client->amount;
            }

        }
        ksort($client_list);
        unset($client_list['']);
        $header = array(
            'current_menu' => 'viewClients',
            'title' => '注文一覧',
            'role' => self::COMMON_MENU,
            'agentrank' => $user->role,
            'userstatus' => $user->status
        );
        $body = array(
            'clients' => $clients,
            'client_list' => $client_list,
            'statuses'=> $this->search->getStatuses(),
            'statuses_view' => $this->search->getStatusesView(),
            'search' => $search//
        );
        $this->load->view('common/header', $header);
        $this->load->view('agent/viewClients', $body);
        $this->load->view('common/footer');
    }

    public function viewCommission() {
        log_message('debug', 'Agent/viewCommission');
        $this->load->model('screen/SearchCommission_Model', 'searchCommission');

        if (!$this->is_login()) {
            $this->safe_redirect('Agent/login');
        }

                ////begin
        if (isset($_POST['search'])){
            $data = $_POST['search'];
        } else {
            $data = array();
        }
        $search = new $this->searchCommission($data);
        //end

        $user_hash = $this->getSessionValue('user_hash');
        $user = $this->user->get_user_by_userhash($user_hash);

        //$history = $this->commission->get_history_by_userhash($user_hash);
        $allAgentChild = $this->commission->getAllAgentChild($user_hash, $search);

        $notpay=0;
        $payed=0;
        foreach ($allAgentChild as $child){
            if ($child->is_payed) {
                $payed += $child->quantity;
            } else {
                $notpay += $child->quantity;
            }
        }
        $header = array(
            'current_menu' => 'viewCommission',
            'title' => 'コミッション',
            'role' => self::COMMON_MENU,
            'agentrank' => $user->role,
            'userstatus' => $user->status
        );
        //$statuses = $this->general->get_status()->result();
        $statuses = array(
            '0' => '支払予定',
            '1' => '支払済み'
        );
        $body = array(
            'notpay' => $notpay,
            'payed' => $payed,
            'history' => $allAgentChild,
            'user_hash' => $user_hash,
            'search' => $search,
            'statuses' => $statuses,
            'btc_address' => $user->btc_address
        );
        $this->load->view('common/header', $header);
        $this->load->view('agent/viewCommission', $body);
        $this->load->view('common/footer');
    }

    public function viewLinks() {
        log_message('debug', 'Agent/viewLinks');        

        if (!$this->is_login()) {
            $this->safe_redirect('Agent/login');
        }

        if (isset($_POST['search'])){
            $data = $_POST['search'];
        } else {
            $data = array();
        }
        $search = new $this->search($data);

        $user_hash = $this->getSessionValue('user_hash');
        $user = $this->user->get_user_by_userhash($user_hash);
        // 代理店一覧取得
        $roles = array_keys($this->config->item('role_agent_commission'));
        $agents = $this->user->get_for_view_links($user_hash, $roles, $search);
        // 交換者一覧取得
        $roles = array(
            $this->config->item('role_client')
        );
        $clients = $this->user->get_for_view_links($user_hash, $roles, $search);

        $header = array(
            'current_menu' => 'viewLinks',
            'title' => 'リンク一覧',
            'role' => self::COMMON_MENU,
            'agentrank' => $user->role,
            'userstatus' => $user->status
        );

        $statuses = array(
            $this->config->item('act_gen_link') => "リンクのみ",    // 01
            $this->config->item('act_reg_personal') => "登録済み",  // 11  
            $this->config->item('act_approved') => "承認",         // 21
            $this->config->item('act_invalid_personal') => "無効", // 04
        );

        $body = array(
            'agents' => $agents,
            'clients' => $clients,
            'statuses'=> $statuses,
            'search' => $search
        );
        $this->load->view('common/header', $header);
        $this->load->view('agent/viewLinks', $body);
        $this->load->view('common/footer');
    }

    /*
    * The function change password.
    */
    public function changePasswd() {
        log_message('debug', 'Agent/changePasswd');

        if (!$this->is_login()) {
            $this->safe_redirect('Agent/login');
        }

        $user_hash = $this->getSessionValue('user_hash');
        $user = $this->user->get_user_by_userhash($user_hash);
        $header = array(
            'current_menu' => 'changePassword',
            'title' => 'パスワード変更',
            'role' => self::COMMON_MENU,
            'agentrank' => $user->role,
            'userstatus' => $user->status
        );
        $body = array(
            'user_hash' => $user_hash
        );
        $this->load->view('common/header', $header);
        $this->load->view('agent/changePassword', $body);
        $this->load->view('common/footer');
    }

    public function confirmChangePasswd() {
        log_message('debug', 'Agent/confirmChangePasswd');
        if (!$this->is_login()) {
            $this->safe_redirect('Agent/login');
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
                'current_menu' => 'changePassword',
                'title' => 'パスワード変更',
                'role' => self::COMMON_MENU,
                'agentrank' => $user->role,
                'userstatus' => $user->status
            );
            $body = array(
                'user_hash' => $user_hash,
                'arrFieldError' => $arrFieldError,
                'userPostData' => $_POST
            );
            $this->load->view('common/header', $header);
            $this->load->view('agent/changePassword', $body);
            $this->load->view('common/footer');
            return;
        }     

        $data = array(
            'password' => $this->user->encrypt_password($userPost->newPassword),
            'update_by' => $user_id,
            'update_at' => $now
        );
        $this->user->update_by_userhash($user_hash, $data);
        $this->safe_redirect('Agent/home/update/success');   
    }

     /*
    * A page guide agent.
    */
    public function guideAgent() {
        log_message('debug', 'Agent/guideAgent');

        if (!$this->is_login()) {
            $this->safe_redirect('Agent/login');
        }
       
        $user_hash = $this->getSessionValue('user_hash');
        $user = $this->user->get_user_by_userhash($user_hash);       

        $header = array(
            'current_menu' => 'guideAgent',
            'title' => '代理店ガイダンス',
            'role' => self::COMMON_MENU,
            'agentrank' => $user->role,
            'userstatus' => $user->status
        );
        
        $this->load->view('common/header', $header);
        $this->load->view('agent/guideAgent');
        $this->load->view('common/footer');
    }

    public function confirmGuideAgent() {
        log_message('debug', 'Agent/confirmGuideAgent');
        $this->load->model('screen/RegisterUser_Model', 'regUser');
        $this->load->library('form_validation');

        $date = new DateTime();
        $now = date($this->config->item('db_timestamp_format'), $date->getTimestamp());

        $ruleAgrement = new $this->regUser($_POST);
        $user_hash = $this->getSessionValue('user_hash');
        $user_id = $this->getSessionValue('uid');

        //set rule for fields.
        foreach ($ruleAgrement->getRulesGuideAgent() as $name => $rule){
            $this->form_validation->set_rules($name, $rule['title'], $rule['rule_list']);
        }
        // validate input data
        if(!$this->form_validation->run()) {
            $arrFieldError = $this->form_validation->error_array();
            $this->safe_redirect("Agent/guideAgent");            
        }
        
        $data = array(
            'rsv_char_2' => 'readGuide',
            'update_by' => $user_id,
            'update_at' => $now
        );
        $this->user->update_by_userhash($user_hash, $data);
        $this->safe_redirect('Agent/home');   
 
    }
}
