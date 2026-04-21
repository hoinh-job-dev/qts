<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Model extends CI_Model{
    public function __construct($data = null)
    {
        parent::__construct();
        if (isset($data)){
            foreach ($data AS $key => $value) {
              if(is_string($value)) {
                $this->{$key} = trim($value);
              } else {
                $this->{$key} = $value;
              }
            }
        }
        if (self::$token_db_conn == null)
            self::$token_db_conn = $this->load->database('token', true);
        $this->token_db = &self::$token_db_conn;
    }

    public static $token_db_conn = null;

    protected $token_db = null;

    public function getdb(){
        return $this->token_db;
    }

    /**
     * Check string is empty
     */
    public function IsNullOrEmptyString($question){
        return (!isset($question) || trim($question)==='');
    }
}