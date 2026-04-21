<?php

class Options extends MY_Controller {

    protected $defaultUser = 'QTS_Operator';

    const COMMON_MENU = 'operator';

    public function __construct() {
        parent::__construct();
        $this->load->model('Option_model', 'option');
    }

    private function getGroupLabel() {
    	return array(
    		'default' => '一般設定',
    		'amount_rules' => 'バリデーションのルール',
    		'role_agent_commission' => '代理店にコミッション',
    		'livenet' => 'Bitcoin livenet',
    		'testnet' => 'Bitcoin testnet',
    		//'livenet' => 'Bitcoin livenet',
    		'mail_accept_order' => 'Accept order email template',
    		'mail_approved_client' => 'Approve client email template',
    		'mail_notify_bankaccount' => 'Notify bank account email template',
    		'mail_notify_btcaddr' => 'Notify BTC address email template',
    		'mail_notify_receivedbtc' => 'Receive BTC email template',
    		'mail_notify_receivedbtc2nd' => 'Receive BTC from 2nd email template',
    		'mail_notify_tokencode' => 'Notify token email template',
    		'mail_agent_register' => 'Agent register email template',
    		'mail_approved_agent' => 'Aprove agent email template',
    		'mail_respond_password' => 'Forgot password email template',
    		'mail_cancel_order' => 'Disable order email template',
            'mail_edit_token' => 'Notify when token was edited',
            'mail_issue_redeem_token' => 'Notify when token issue redeem'
    	);
    }

    public function index($tab_id = 1, $status = NULL)
	{
		log_message('debug', 'Options/index');

		// ログイン判定
		if (!$this->is_login()) {
			$this->safe_redirect('Operator/login');
		}

		$arrMsg = array();
		$statusMessage = @$arrMsg[$status];

		if ($this->isAdmin())
		{
			$tab_id = (int) $tab_id > 0 ? (int) $tab_id : 1;
			$o_arr = $this->option->getOptions($this->getForeignid(), $tab_id);

			// parse into group
			$default_group = 'default';
			$grouped_arr = array(
				$default_group => array()
			);
			$group_order = array($default_group);
			$group_separator = '_ARRAY_';
			foreach($o_arr as $row) {
				$key = $row['key'];
				if(strpos($key, $group_separator) !== false) {
					list($group_key, $group_item_key) = explode($group_separator, $key);
					if(!isset($grouped_arr[$group_key])) {
						$grouped_arr[$group_key] = array();
						$group_order[] = $group_key;
					}
					$grouped_arr[$group_key][] = $row;
				}
				else {
					$grouped_arr[$default_group][] = $row;
				}
			}

			$body = array(
				'grouped_arr' => $grouped_arr,
				'group_label' => $this->getGroupLabel(),
				'group_order' => $group_order,
				'tab_id' => $tab_id,
				'statusMessage' => $statusMessage
			);

		} else {
			$body = array(
				'access_deny' => 1
			);
		}

		$header = array(
			'current_menu' => 'manageOptions',
			'title' => '環境の任意設定',
			'role' => self::COMMON_MENU
		);

		$this->load->view('common/header', $header);
		$this->load->view('options/index', $body);
		$this->load->view('common/footer');
	}

	public function update() 
	{
		log_message('debug', 'Options/index');

		// ログイン判定
		if (!$this->is_login()) {
			$this->safe_redirect('Operator/login');
		}

		if ($this->isAdmin())
		{
			if (isset($_POST['options_update']))
			{
				$this->option->updateOption(
					array(
						'value' => '1|0::0'
					),
					array(
						'foreign_id' => $this->getForeignId(),
						'type' => 'bool',
						'tab_id' => $_POST['tab_id']
					)
				);
			
				foreach ($_POST as $key => $value)
				{
					if (preg_match('/value-(string|text|int|float|enum|bool|color)-(.*)/', $key) === 1)
					{
						$key_separator = '-';
						$key_arr = explode($key_separator, $key);
						$type = $key_arr[1];
						unset($key_arr[0]);
						unset($key_arr[1]);
						$k = implode($key_separator, $key_arr);
						if (!empty($k))
						{
							$this->option->updateOption(
								array(
									'value' => $value
								),
								array(
									'foreign_id' => $this->getForeignId(),
									'key' => $k,
									'tab_id' => $_POST['tab_id']
								),
								1
							);
						}
					}
				}

				$this->safe_redirect('Options/index/'.$_POST['tab_id'].'/success');
			}

			$this->safe_redirect('Options/index');
		} else {
			$body = array(
				'access_deny' => 1
			);
		}

		$header = array(
			'current_menu' => 'option',
			'title' => 'Options',
			'role' => self::COMMON_MENU
		);

		$this->load->view('common/header', $header);
		$this->load->view('options/index', $body);
		$this->load->view('common/footer');
	}

}

?>