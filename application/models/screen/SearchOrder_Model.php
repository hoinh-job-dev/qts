<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class SearchOrder_Model extends MY_Model
{
    public $orderid = '';
    public $status = '';
    public $agentid = '';
    public $purchaseid = '';
    public $type = '';
    public $order_opt = 'ASC';
    public $order_by = '';
    public $apply = false;
    public $client_name = '' ;
    public $agent_name = '';
    public $create_from = '';
    public $create_to = '';
    public $client_familyName = '' ;
    public $client_firstName = '' ;
    public $agent_familyName = '';
    public $agent_firstName = '';
    public $account_name = '';
    public function __construct($data=array())
    {
        parent::__construct($data);
    }

}