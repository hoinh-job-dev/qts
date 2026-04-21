<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
    function __construct()
    {
        parent::__construct();
        $this->CI->load->model('User_model', 'user');
    }
    public function check_btc_address($btc_address){
        if ($btc_address == null || $btc_address == '')
            return TRUE;
        $this->CI->load->library('addressvalidator');
        $mode = $this->CI->config->item('testnet_mode') === true ?  "TESTNET" : "MAINNET";
        return $this->CI->addressvalidator->isValid($btc_address, $mode);
    }

    public function emailAlreadyExists($str){
        $uid_editing = $this->CI->session->userdata('uid_editing');
        $role_editing = $this->CI->session->userdata('role_editing');
        $user = $this->CI->user->get_user_by_email_role($str, $role_editing)->result();
        if (null == $user) {
            return TRUE;
        }
        if ($user !=null)
            $user = $user[0];
        if ($user != null and $uid_editing != null and $uid_editing == $user->uid){
            return TRUE;
        }
        $this->set_message('email_exists', 'email_exists');
        return FALSE;
    }

    public function email_black_list($str_email){
        $blacklist = $this->CI->config->item("email_blacklist_domain");
        $domain = substr(strrchr($str_email, "@"), 1);
        if (in_array($domain, $blacklist)) {
            return FALSE;
        }
        return TRUE;
    }

    //A function check password strong.
    public function is_password_strong($password){
       if (preg_match('#[0-9]#', $password) && 
            preg_match('#[a-zA-Z]#', $password)) {
         return true;
       }
       return false;
    }

}