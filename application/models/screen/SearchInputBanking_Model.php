<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class SearchInputBanking_Model extends MY_Model
{
    public $bank_name = '';
    public $user_id = '';
    public $order_number = '';
    public $create_from ='';
    public $create_to='';
    public $order_by = '';
    public $order_opt = '';
    public function __construct($data=array())
    {
        parent::__construct($data);
    }
}