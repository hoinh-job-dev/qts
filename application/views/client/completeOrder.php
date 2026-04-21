<style>
    input[type="button"]{
        padding:2px 6px;
        width: 188px;
        height: 31px;
        border: 1px solid;
    }
    .back {
        background-color: #cbcbcb;
        color: #fff;
    }	
 </style>
    
    <div class="l-login">
		<section class="login-main">
			<div class="mainbox">
			<div class="whitebox">
                                <?php switch ($ordered) { 
                                    case '01': ?>
                                    <p>
                                        ご登録いただき、ありがとうございます。<br><br>
                                        コンプライアンス確認作業が完了次第、先ほどご登録いただいたメールアドレス宛に請求書が届きます。<br>
                                        ご不明点などございましたら、弊社サポートにご連絡ください。<br>
                                        後ほど登録されたメールアドレスに請求書が届きます。その手順でお振込ください。<br>
                                        弊社サポートにご連絡ください。
                                    </p>
                                    <?php if (false) { ?>
                                    <p>
                                        後ほど登録されたメールアドレスに請求書が届きます。その手順でお振込ください。<br>
                                        なお通常、銀行振込では1時間、ビットコインでは6営業日以内に請求書が届きます。<br>
                                        これらの期間が経過しても請求書が届かない場合は、弊社サポートにご連絡ください。
                                    </p>
                                    <?php } ?>
                                <?php break;
                                case '02': ?>
								<p>本リンク先は既に登録済のリンクアドレスとなっておりますので、ご利用いただけません。<br>リンク発行者へお問い合わせください。</p>
                                <?php break;
                                case '03': ?>
                                    <p>
                                        ご登録いただいたメールアドレスは既に登録されております。<br>
                                        お手数ですが、もう一度別のメールアドレスでご登録ください。
                                    </p><br><br>
                                    <input type="button" class="back" value="back" onclick="window.history.back();">
                                <?php break;
                                case '04': ?>
                                    <p>
                                        再注文いただき、誠にありがとうございます。<br><br>
                                        コンプライアンス確認が済み次第、ビットコイン送信先のご連絡させていただきますので、今しばらくお待ち下さい。<br>
                                        ご不明点がございましたら、ご連絡くださいますようお願い申し上げます
                                    </p>
                                    <?php if (false) { ?>
                                    <p>
                                        再注文いただき、誠にありがとうございます。<br><br>
                                        コンプライアンス確認が済み次第、振込先またはビットコイン送信先のご連絡させていただきますので、今しばらくお待ち下さい。<br>
                                        ご不明点がございましたら、ご連絡くださいますようお願い申し上げます
                                    </p>
                                    <?php } ?>
                                <?php break;
                                case '05': ?>
                                    <p>
                                        登録が完了しておりません。<br>
                                        お手数ですが以下の戻るボタンから登録画面に戻り、再度お手続きをお願いいたします。
                                    </p><br><br>
                                    <input type="button" class="back" value="back" onclick="window.history.back();">
                                <?php break;
                                case '06': ?>
                                    <p>
                                        メールアドレスが見つかりませんでした。<br><br>
                                        恐れ入りますが、交換者として初回登録してからお試しください。<br>
                                        ご不明点などございましたら、弊社サポートにご連絡ください。
                                    </p>
                                <?php break;
                                case '07': ?>
                                    <p>
                                        コンプライアンスの審査中です。<br><br>
                                        コンプライアンスの承認後にご利用ください。<br>
                                        ご不明点などございましたら、弊社サポートにご連絡ください。
                                    </p>
                                <?php } ?>
            </div><!-- / .whitebox -->
			</div><!-- / .mainbox -->
		</section><!-- / .login-main -->
	</div><!-- /.l-login -->
