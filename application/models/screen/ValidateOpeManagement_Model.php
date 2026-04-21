<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ValidateOpeManagement_Model extends MY_Model{

    public $ope_donew;

    public $ope_doedit;

    public $ope_dodelete;

    public $ope_edit;

    public $formsubmit;

    public $agent_uid;

    public $uid;

    // メールアドレス
    public $email;
    // 姓
    public $family_name;
    // 名
    public $first_name;
    // 姓(カナ)
    public $family_name_kana;
    // 名(カナ)
    public $first_name_kana;

    public $password;    

    public $role;   
    

    public function __construct($data=array()){
        parent::__construct($data);
        $this->load->model('User_model', 'user');
    }

    public static $rules = array(

    );

    public function getRules(){
        $rules = array(
            'email' => array(
                'title' => '「電子メール 」',
                'rule_list' => array(
                    'required',
                    'valid_email',
                    'max_length[50]'
                )
            ),
            'family_name' => array(
                'title' => '「姓」',
                'rule_list' => array(
                    'required',
                    'max_length[20]'
                )
            ),
            'family_name_kana' => array(
                'title' => '「姓(カナ)」',
                'rule_list' => array(
                    'required',
                    'max_length[20]'
                )
            ),
            'first_name' => array(
                'title' => '「名」',
                'rule_list' => array(
                    'required',
                    'max_length[20]'
                )
            ),
            'first_name_kana' => array(
                'title' => '「名(カナ)」',
                'rule_list' => array(
                    'required',
                    'max_length[20]'
                )
            ),
            'password' => array(
                'title' => '「パスワード」',
                'rule_list' => array(
                    'required',
                    'max_length[20]'
                )
            ),
            'role' => array(
                'title' => '「ロール」',
                'rule_list' => array(
                    'required',
                    'max_length[2]'
                )
            )
        );        
        return $rules;
    }

    public function getOpeCRUP(){

        $pass = ($this->password == null || $this->password == '') ? '' :  $this->user->encrypt_password($this->password) ;
        $udata = array(
            'email' => $this->email,
            'password' => $pass,
            'family_name' => $this->family_name,
            'first_name' => $this->first_name,
            'family_name_kana' => $this->family_name_kana,
            'first_name_kana' => $this->first_name_kana,            
            'role' => $this->role,
            'uid' => $this->uid
        );
        return $udata;
    }   
}