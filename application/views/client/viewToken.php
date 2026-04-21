<style>
    h2 {
        font-size: x-large;
    }
    span.value {
        margin-left: 20px;
    }
</style>

	<div class="l-login">
		<section class="login-main">
			<div class="mainbox">
			<div class="whitebox">
                            <?php if (1==$isError) { echo "<span>該当する還元番号が見つかりません。<br>ご入力された番号をご確認の上、運営窓口までご相談ください。</span>"; } ?>
                            <?php if (""==$token_code) { ?>
                            <form action="<?php echo base_url('Client/viewToken'); ?>" method="post" id="order">
                                <h2>受付番号</h2>
                                <p>
                                    <label for="token_id">還元コード</label>
                                    <input type="text" name="order_number" id="token_id" value="" onchange="isValid_text(this);">
                                </p>
                                <ul class="order-btnlist float-list">
                                    <li><input type="button" class="confirm" value="send" onclick="return submit();"></li>
                                </ul>
                            </form>
                            <?php } ?>
                            <?php if (""!=$token_code && 0==$isError) { ?>
                            <h2 style="margin-top:40px;">トークン</h2>
                            <p>
                                <label>トークン</label>
                                <span class="value"><?php echo $amount; ?> Quanta</span>
                            </p>
                            <?php } ?>
                        </div><!-- / .whitebox -->
			</div><!-- / .mainbox -->
		</section><!-- / .login-main -->
	</div><!-- /.l-login -->

<script type="text/javascript" src="<?php echo base_url('js/client/viewToken.js'); ?>"></script>
