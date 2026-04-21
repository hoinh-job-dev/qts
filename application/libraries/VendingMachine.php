<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class VendingMachine {

    public $CI = null;

    public function __construct() {
        $this->CI =& get_instance();
        date_default_timezone_set("Asia/Tokyo");
    }

    // 請求用のビットコインアドレスを作成する
    public function createDepositAddress() {
    }

    // 着金を確認する
    public function checkConfirmation() {
    }

    // コミッションを送金する
    public function sendCommission() {
    }

}
