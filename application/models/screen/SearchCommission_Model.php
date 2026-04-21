<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class SearchCommission_Model extends MY_Model
{    
    // 状態
    public $status = '';
    //client name
    public $client_name = '';  
    //agent name
    public $agent_name = ''; 

    // メモ
    public $memo = '';

    public function __construct($data=array())
    {
        parent::__construct($data);
    }

}