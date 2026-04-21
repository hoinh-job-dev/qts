<style>
    h2 {
        font-size: x-large;
        margin: 0 auto 20px;
    }
    span.value {
        margin-left: 20px;
    }
</style>
	<div class="l-login">
		<section class="login-main">
			<div class="mainbox">
			<div class="whitebox">
            <?php switch ($styleView) { 
                //style view waiting client send money.
                case 1: ?>
                    <h2>QNT交換　送信先アドレス</h2>
                    <input type="hidden" id="usd" value="<?php echo $amount ?>">
                    <p>
                        <label>交換希望額</label>
                        <span class="value"><?php echo $amount ?></span> USD
                    </p>
                    <p>
                        <label for="rate_label">レート</label>
                        <span id="rate_label" class="value">-</span> USD/BTC
                    </p>
                    <p>
                        <label for="exchanged_amount_label">送信ビットコイン数</label>
                        <span id="exchanged_amount_label" class="value">-</span> BTC
                    </p>
                    <p>
                        <label for="leave_time">レート再取得まで</label>
                        <span class="value">あと</span><span id="leave_time" style="color:#ff0000;">-</span>秒
                    </p>
                    <p>
                        <label for="mail">送信先アドレス</label>
                        <span class="value"><?php echo $addr; ?></span>
                    </p>
                    <p>
                        <label style="font-size:smaller;color:#f00;">※このビットコインアドレスは1度のみ使用可能です。</label>
                    </p>

            <?php   break;
                //style view expired-date.
                case 2: ?>
                    <p>
                        請求書の有効期限が切れております。<br><br><br>
                        手続きが完了していない場合、弊社HPから再度ご注文くださいますようお願い申し上げます。<br><br>
                        → <a href="<?php echo base_url('Client/addOrder'); ?>" style="color:#00f;">再注文について</a>
                    </p>
            <?php break;
                //style view has been sent money. 
                case 3: ?>
                    <p>ご送金頂きありがとうございました。<br><br>弊社での確認が取れましたので、<br>受領書の発行手続きを進めて参ります。</p>
            <?php break; }?>
            </div><!-- / .whitebox -->
			</div><!-- / .mainbox -->
		</section><!-- / .login-main -->
	</div><!-- /.l-login -->

<script type="text/javascript" src="<?php echo base_url('js/client/viewBtcAddr.js'); ?>"></script>
<script type="text/javascript">
/*****************************************
 * レート取得
 *****************************************/
var getRateInterval = 180; // sec, 3分
var leaveTime = 0;
var repeat = null;
var _pow = Math.pow( 10 , 2 ) ;

// レート取得のインターバルを設定する
window.onload = function setRate() {
    var countDownInterval = 1000; // msec, 3分
    repeat = setInterval(countdown, countDownInterval);   
}
function countdown() {
    document.getElementById("leave_time").innerHTML = leaveTime;
    if (0 < leaveTime) {
        leaveTime -= 1;
    } else {
        getRate();
        leaveTime = getRateInterval;
    }
}

// レートを取得する
function getRate() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
        var rate = xhttp.responseText;
        if (""==rate) {
            document.getElementById("rate_label").innerHTML = "(network error)";
        } else {
            rate = Math.round( rate * _pow ) / _pow ;
            document.getElementById("rate_label").innerHTML = rate;
            calcBtc();
        }
    }
  };
  xhttp.open("GET", "<?php echo $this->config->item('base_url'); ?>/Client/getUsdBtcRate", true);
  xhttp.send();
}

// 取得したレートに従い、振込金額を計算する
function calcBtc() {
    var rate = document.getElementById("rate_label").innerHTML;
    if (isNaN(rate)) {
        return;
    }
    var usd = document.getElementById("usd").value;
    var btc = "(network error)";
    if (0!=rate) {
        btc = usd / rate;
    }
    btc = Math.round( btc * _pow ) / _pow ;
    // 出力する
    document.getElementById("exchanged_amount_label").innerHTML = btc;
}
</script>