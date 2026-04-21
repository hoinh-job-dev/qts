<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class SearchRefund_Model extends MY_Model
{
    public $oper_status = '';
    public $sent_status = '';
    public function __construct($data=array())
    {
        parent::__construct($data);
    }

}