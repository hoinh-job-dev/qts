<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class SearchListCommission_Model extends MY_Model {
    public $order_number = '';
    public $status = '';
    public $create_from = '';
    public $create_to = '';
    public $pay_method = '';
    public $agent_uid = '';
    public $agent_role = '';

    public $order_by = ''; // order_number, create_at, agent_uid
    public $order_opt = 'ASC'; // ASC, DESC


    public function __construct($data=array())
    {
        parent::__construct($data);
    }
}