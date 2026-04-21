<div class="l-login">
    <section class="login-main">
        <div class="mainbox">
            <div class="whitebox">
                <h1 style="font-size: x-large;">オペレータログイン</h1>
                <form action="<?php echo base_url('Operator/home'); ?>" method="post" id="login">
                    <p>
                        <label for="mail">email</label>
                        <input type="email" name="email" id="mail" onchange="isValid_email(this);">
                    </p>
                    <p>
                        <label for="pass">password</label>
                        <input type="password" name="password" id="pass" onchange="isValid_text(this);">
                    </p>
                    <input type="button" value="login" onclick="return login();">
                </form>
                <br>
                <?php
                $isErr = isset($isError) ? $isError : '00';
                switch ($isErr) {
                    case '10':
                        echo "<p>メールアドレスまたはパスワードに<br>誤りがあります。<br>もう一度ご入力ください。</p>";
                        break;
                    case '20':
                        echo "<p>メールアドレスに誤りがあります。<br>もう一度ご入力ください。</p>";
                        break;
                    case '30':
                        echo "<p>ご入力いただいたメールアドレスが見つかりませんでした。<br>
				お手数ですが、メールアドレスをご確認の上もう一度お問い合わせください。</p>";
                        break;
                    default:
                }
                ?>
                <ul>
                    <li style="margin-bottom:10px;"><お問い合わせ先><br><a href="mailto:<?php echo $this->config->item('support_email'); ?>?subject=[Quanta]お問い合わせ"><?php echo $this->config->item('support_email'); ?></a></li><br>
                    <li style="margin-bottom:10px;"><label onclick="box_expand()">< パスワードを忘れた方 ></label>
                        <div id="ask_password_box" style="display:none;">
                            <form action="<?php echo base_url('Operator/ask_password'); ?>" id="ask" method="post">
                                <p>
                                    email<br>
                                    <input type="email" name="askemail" id="askmail" onchange="isValid_email(this);">
                                </p>
                                <input type="button" value="問い合わせメール送信" onclick="ask_password();">
                            </form>
                        </div>
                    </li>
                </ul>
            </div><!-- / .whitebox -->
        </div><!-- / .mainbox -->
    </section><!-- / .login-main -->
</div><!-- /.l-login -->

<script type="text/javascript" src="<?php echo base_url('js/common/login.js'); ?>"></script>
