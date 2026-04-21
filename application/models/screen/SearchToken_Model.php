<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class SearchToken_Model extends MY_Model{
    
    public $orderid = '';
    public $tokencode = '';    
    public $purchaseid = '';
    public $account_name = '';

    public $create_from = '';
    public $create_to = '';
    public $update_from = '';
    public $update_to = '';

    public $client_name = '' ;
    public $client_familyName = '' ;
    public $client_firstName = '' ;

    public $agentid = '';
    public $agent_name = '';
    public $agent_familyName = '';
    public $agent_firstName = '';    

    public $type = '';
    public $order_opt = 'ASC';
    public $order_by = '';
    public $is_sent = '';
    public $is_payed = '';

    public function __construct($data=array())
    {
        parent::__construct($data);
    }

}