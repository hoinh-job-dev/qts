<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class SearchEmail_Model extends MY_Model
{
    public $id = '';
    public $to = '';
    public $memo = '';
    public $object = '';
    public $is_sent = '';
    public $date_from = '';
    public $date_to = '';
    public $create_at = '';

    public function __construct($data=array())
    {
        parent::__construct($data);
    }

}