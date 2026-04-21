<div class="l-order ">
    <section class="order-main">
        <h1 style="font-size: x-large;"><?php echo $title; ?></h1>
        <div class="whitebox">
            <?php if ($isValid) { ?>
            <form action="<?php echo base_url('Agent/updatePasswd'); ?>" method="post" id="register">
				<input type="hidden" name="passid" value="<?php echo $passid ?>">
                <p>
                    <label for="password">パスワード</label>
                    <input type="password" name="password" id="password" class="w-short" onchange="isValid_pass();">
                </p>
                <p>
                    <label for="password2">パスワード(確認用の再入力)</label>
                    <input type="password" id="password2" class="w-short" onchange="isValid_passconfirm();">
                </p>
                <p>
                    <span class="note">*パスワードは6文字以上で、半角英数字、大文字と小文字を入れてください。</span>
                </p>
                <input type="button" class="confirm" value="登録" onclick="return submitPass();">
            </form>
            <?php } else { ?>
                <p style="padding:20px;">
                    このリンクからは、既にパスワードが更新されております。
                </p>
            <?php } ?>
        </div><!-- / .whitebox -->

<script type="text/javascript" src="<?php echo base_url('js/agent/setPassword.js'); ?>"></script>
