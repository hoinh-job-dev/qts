<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class SearchHome_Model extends MY_Model
{
    public $uid = '';
    public $role = '';
    public $type = '';
    public $email = '';
    public $order_by = '';
    public $order_opt = '';

    public function __construct($data=array())
    {
        parent::__construct($data);
    }

}