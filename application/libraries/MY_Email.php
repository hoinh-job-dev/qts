<?php
	defined("BASEPATH") OR exit("No direct script access allowed");

	/**
	* Email class
	*/
	class MY_Email extends CI_Email
	{
		protected $CI = null;

		protected $config = array();

		protected $name = '[Quanta]ワンエイトアソシエーション株式会社';
		
		public function __construct($config = array())
		{
			$this->CI = & get_instance();

			// check SMTP config from DB
			if($this->CI->config->item('send_email_method') === 'smtp') {
				$this->set('smtp_host', $this->CI->config->item('smtp_host'));

				if(empty($this->CI->config->item('smtp_host'))) {
					log_message('debug', 'SMTP Host is empty');
				}

				if(empty($this->CI->config->item('smtp_port'))) {
					log_message('debug', 'SMTP Port is empty');
				}

				if(empty($this->CI->config->item('smtp_user'))) {
					log_message('debug', 'SMTP Username is empty');
				}

				if(empty($this->CI->config->item('smtp_pass'))) {
					log_message('debug', 'SMTP Password is empty');
				}

				$smtp_config = array(
					"protocol" => "smtp",
					"smtp_host" => $this->CI->config->item('smtp_host'),
					"smtp_port" => $this->CI->config->item('smtp_port'),
					"smtp_user" => $this->CI->config->item('smtp_user'),
					"smtp_pass" => $this->CI->config->item('smtp_pass'),
					"mailtype"  => $this->CI->config->item('mailtype'),
					"charset"   => $this->CI->config->item('charset')
				);

				$config = array_merge($smtp_config, $config);
			}

			$this->setConfig($config);

			parent::__construct($this->config);

			// Force to set new line in double quotes. Do NOT use single quotes.
			$this->set_newline("\r\n");
		}

		/**
		Set config value by key
		**/
		public function set($key, $value) {
			$this->config[$key] = $value;
			return $this;
		}

		/**
		Get config value by key
		**/
		public function get($key) {
			if(isset($this->config[$key])) {
				return $this->config[$key];
			}
			return null;
		}

		public function setConfig($config) {
			if(!empty($config) && is_array($config)) {
				foreach ($config as $key => $value) {
					$this->set($key, $value);
				}
			}
			// reload CI Email config
			$this->initialize($this->config);
			return $this;
		}

		public function getConfig() {
			return $this->config;
		}

		public function from($from, $name = '', $return_path = NULL) {
			if( $name == ""){
				$name = $this->$name;
			}
	 		parent::from($from, $name, $return_path);
		}
	}
?>