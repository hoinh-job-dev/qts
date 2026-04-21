<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Batch extends MY_Controller {

    public function __construct() {
        parent::__construct();
        // CLI実行かどうかのチェック
        if (!$this->input->is_cli_request()) {
            echo "システム内部による起動ではありませんでした。" . PHP_EOL;
            exit;
        }
        $this->load->library('interbank');
    }

    public function get_rate() {
        $this->interbank->do_job_get_rate();
    }
}
