<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class SearchApprovedToken_Model extends MY_Model
{
    public $order_number = '';
    public $search_from = '';
    public $search_to = '';
    public $hot_cold_sent_status = '';
    public $commission_sent_status = '';
    public function __construct($data=array())
    {
    	if(isset($data['search_from']) && isset($data['search_to']) && !empty($data['search_from']) && !empty($data['search_to'])) {
    		$fromts = strtotime($data['search_from']);
    		$tots = strtotime($data['search_to']);
    		if($fromts > $tots) {
    			$tmpts = $fromts;
    			$fromts = $tots;
    			$tots = $tmpts;
    			$data['search_from'] = date("Y/m/d", $fromts);
    			$data['search_to'] = date("Y/m/d", $tots);
    		}
    	}
        parent::__construct($data);
    }

}