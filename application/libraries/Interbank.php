<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Interbank {

    public $CI = null;

    const USD = 'USD';
    const JPY = 'JPY';
    const BTC = 'BTC';

    public function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->model('ExchangeRate_model', 'rate');
        date_default_timezone_set("Asia/Tokyo");
    }

    // (銀行振込の場合) 入金予定のUSDからJPYへ換金するレート

    public function getUsdJpyRate(){
        log_message('debug', 'Library/Interbank/getUsdJpyRate');
        return $this->getRateFromDB(self::USD, self::JPY);
    }

    public function getJpyBtcRate(){
        log_message('debug', 'Library/Interbank/getJpyBtcRate');
        return $this->getRateFromDB(self::JPY, self::BTC);
    }

    // get lastest rank, >0 from db
    public function getUsdBtcRate(){
        log_message('debug', 'Library/Interbank/getUsdBtcRate');
        return $this->getRateFromDB(self::USD, self::BTC);
    }

    public function getRateFromDB($from, $to){
        log_message('debug', 'Library/Interbank/getRateFromDB');
        $date = new DateTime();
        $txtime = date("Y/m/d H:i:s", $date->getTimestamp());

        $row = $this->CI->rate->get_rate($from, $to, $txtime);
        if(empty($row)) {
            log_message('debug', 'row is empty.');
            return 0;
        }
        else {
            $rate = $row[0]->rate;
            return doubleval($rate);
        }
    }

    public function get_rate($from, $to, $txtime) {
        log_message('debug', 'Library/Interbank/getRate');
        $row = $this->CI->rate->get_rate($from, $to, $txtime);
        if(empty($row)) {
            log_message('debug', 'row is empty.');
            $rate = $this->getRateApi($from, $to);
        }
        else {
            $rate = $row[0]->rate;

            $date = new DateTime();
            $now = $date->getTimestamp();
            $expiration_datetime = strtotime($row[0]->create_at) + $this->CI->config->item('get_rate_interval');
            if ($expiration_datetime < $now) {
                log_message('debug', 'row is expired.');
                $now_rate = $this->getRateApi($from, $to);
                $rate = (0==$now_rate) ? $rate : $now_rate;
            }
        }

        return doubleval($rate);
    }

    public function getRateApi($from, $to){
        log_message('debug', 'Library/Interbank/getRateApi');
        $url = $this->getApiUrl($from, $to);
        $json = file_get_contents($url);
        if (null == $json || "" == $json) {
            return 0;
        }

        $his = json_decode($json, true);
        if (@doubleval($his['ticker']['price'])) {
            // cryptonator api
            $rate = $his['ticker']['price'];
        } else if (@doubleval($his['0']['price_usd'])) {
            // for api https://api.coinmarketcap.com/v1/ticker/bitcoin/
            $rate = $his['0']['price_usd'];
        } else {
            return 0;
        }
        $data = array(
            'from' => $from,
            'to' => $to,
            'rate' => $rate
        );
        $this->CI->rate->insert_rate($data);

        return $rate;
    }

    public function getApiUrl($from, $to){
        $apis = array(
            'JPYUSD' => "https://www.cryptonator.com/api/ticker/jpy-usd",
            'BTCJPY' => "https://www.cryptonator.com/api/ticker/btc-jpy",
            'USDBTC' => "https://www.cryptonator.com/api/ticker/usd-btc",
            'BTCUSD' => "https://api.coinmarketcap.com/v1/ticker/bitcoin/",
        );
        if (isset($apis[$to . $from]))
            return $apis[$to . $from];
        else
            return "https://www.cryptonator.com/api/ticker/$to-$from";
    }

    public function do_job_get_rate(){
        $this->getRateApi(self::USD, self::JPY);
        $this->getRateApi(self::JPY, self::BTC);
        $this->getRateApi(self::USD, self::BTC);
        $this->getRateApi(self::BTC, self::USD);
    }
}
