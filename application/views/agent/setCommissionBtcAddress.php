	<section class="l-contents cf">
		<div class="l-main js-height">
			<div class="l-page-home">
				<h2><?php echo $title;?></h2>
				<form action="<?php echo base_url('Agent/setCommissionBtcAddress'); ?>" method="post" id="register">
					<div class="whitebox" style="width:500px;">
							<input type="hidden" name="user_hash" value="<?php echo $userhash; ?>">
							<p>
								<label for="btc_address">ビットコインアドレス</label>
								<input type="text" name="btc_address" id="btc_address" onchange="isValid_btcAddress(this);">
							</p>
							<input type="button" class="confirm" value="登録" onclick="return submitAddr();" id='ConfirmRegister'>
					</div>
				</form>
			</div><!-- /.l-page-home -->
<script type="text/javascript" src="<?php echo base_url('js/wallet-address-validator.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/agent/setBtcAddr.js'); ?>"></script>
