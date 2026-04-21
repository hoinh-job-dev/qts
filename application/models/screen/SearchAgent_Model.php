<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class SearchAgent_Model extends MY_Model
{

    public static $statuses ;
    public static $statuses_view ;

    public function init(){
        if (isset(self::$statuses) and isset(self::$statuses_view))
            return;
        $order_invalid = $this->config->item('order_invalid');
        $order_expired = $this->config->item('order_expired');
        $order_completed = $this->config->item('order_completed');
        $order_orderby_bank = $this->config->item('order_orderby_bank');
        $order_orderby_btc = $this->config->item('order_orderby_btc');

        $order_notify_bankaccount = $this->config->item('order_notify_bankaccount');
        $order_receiveby_bank = $this->config->item('order_receiveby_bank');
        $order_exchange_jpybtc = $this->config->item('order_exchange_jpybtc');
        $order_notify_btcaddr = $this->config->item('order_notify_btcaddr');
        $order_receiveby_btc = $this->config->item('order_receiveby_btc');
        $order_pre_issue_token = $this->config->item('order_pre_issue_token');
        $order_issue_token = $this->config->item('order_issue_token');
        $order_send_receipt = $this->config->item('order_send_receipt');


        self::$statuses = array(
            "$order_orderby_bank,$order_orderby_btc" => '承認待ち',
            "$order_notify_bankaccount,$order_receiveby_bank,$order_exchange_jpybtc,$order_notify_btcaddr,$order_receiveby_btc,$order_pre_issue_token,$order_issue_token,$order_send_receipt" => '請求書発行済',
            "$order_completed" => '完了',
            "$order_invalid,$order_expired" => '無効'
        );

        self::$statuses_view = array(
            $order_orderby_bank => '承認待ち',
            $order_orderby_btc => '承認待ち',
            $order_notify_bankaccount => '請求書発行済',
            $order_receiveby_bank => '請求書発行済',
            $order_exchange_jpybtc => '請求書発行済',
            $order_notify_btcaddr => '請求書発行済',
            $order_receiveby_btc => '請求書発行済',
            $order_pre_issue_token => '請求書発行済',
            $order_issue_token => '請求書発行済',
            $order_send_receipt => '請求書発行済',
            $order_invalid => '無効',
            $order_expired => '無効',
            $order_completed => '完了',
        );

    }

    // 状態
    public $status = '';
    // 姓
    public $first_name = '';
    // 名
    public $family_name = '';
    // メモ
    public $memo = '';


    public function __construct($data=array())
    {
        parent::__construct($data);
        $this->init();
    }

    public function getStatuses(){
        return self::$statuses;
    }

    public function getStatusesView(){
        return self::$statuses_view;
    }
}