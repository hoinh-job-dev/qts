<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/common.css'); ?>">
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
    .next, .confirm {
        background-color: #000;
        color: #fff;
    }
    #step1 {
        display: block;
    }
    #step2 {
        display: none;
    }
    #step3 {
        display: none;
    }
</style>

<div class="l-order ">
    <section class="order-main">
        <h1>初回注文</h1>
        <div id="progress">
            <ul id="order-flow" class="order-flow order1">
                <li id="step1_nav"class="active" onclick="go('step1')">
                    <span>Step 1</span>
                    連絡先の登録
                </li>
                <li id="step2_nav" onclick="go('step2')">
                    <span>Step 2</span>
                    規約への同意
                </li>
                <li id="step3_nav" onclick="go('step3')">
                    <span>Step 3</span>
                    確認書類の登録
                </li>
            </ul>
            <input type="hidden" id="now_step" value="step1">
        </div>
        <?php if (!empty($arrFieldError)) { ?>
            <div class ="whitebox" style="color:#ff6666;padding:15px;">
                <?php echo "入力したデータはエラーがあります";
                foreach($arrFieldError as $key => $fieldError) {
                    ?>
                    <p class="error_link" style="margin-left:20px;" for="<?php echo $key ?>" ><?php echo $fieldError ?></p>
                    <?php
                }?>
            </div>
        <?php } ?>
        <div class="whitebox">
            <form action="<?php echo base_url('Client/completeOrder'); ?>" method="post" enctype="multipart/form-data" id="order" class="selectorZipcloudSearchWrapper">
                <input type="hidden" name="user_hash" id="user_hash" value="<?php if ($user_hash) echo $user_hash; else echo $user->user_hash; ?>">
                <div id="step1">
                    <h2>個人情報</h2>
                    <p>
                        <label for="account">アカウント種別</label>
                        <select name="type" id="type" onchange="switchType(this);">
                            <option value="<?php echo $this->config->item('type_person'); ?>" <?php if ($user->type==$this->config->item('type_person')) echo "selected" ?>>個人</option>
                            <option value="<?php echo $this->config->item('type_company'); ?>" <?php if ($user->type==$this->config->item('type_company')) echo "selected" ?>>法人</option>
                        </select>
                    </p>
                    <p>
                        <label for="companyname" id="company_name_label" style="display:none;">会社名</label>
                        <input type="text" name="company_name" id="company_name" value="<?php echo $user->company_name ?>" class="w-short"  style="display:none;">
                    </p>
                    <p>
                        <label for="companyname_kana" id="company_name_kana_label" style="display:none;">会社名(カナ)</label>
                        <input type="text" name="company_name_kana" id="company_name_kana" value="<?php echo $user->company_name_kana ?>" class="w-short"  style="display:none;">
                    </p>
                    <!--<p>
                        <label for="familyname" id="familyname_label">姓</label>
                        <input type="text" name="family_name" id="family_name" value="<?php //echo $user->family_name ?>" class="w-short" "> 
                    </p>
                    <p>
                        <label for="family_name_kana" id="familyname_label">姓(カナ)</label>
                        <input type="text" name="family_name_kana" id="family_name_kana" value="<?php //echo $user->family_name_kana ?>" class="w-short" >
                    </p> 
                    <p>
                        <label for="name">名</label>
                        <input type="text" name="first_name" id="first_name" value="<?php //echo $user->first_name ?>" class="w-short" >
                    </p> 
                    <p>
                        <label for="name">名(カナ)</label>
                        <input type="text" name="first_name_kana" id="first_name_kana" value="<?php //echo $user->first_name_kana ?>" class="w-short" >
                    </p>-->

                    <p>
                        <label for="familyname" class="lbl-name-layout" id="familyname_label">姓</label>
                        <label for="name" class="lbl-name-layout">名</label>
                        <br>
                        <input type="text" name="family_name" id="family_name" value="<?php echo $user->family_name ?>" class="txt-name-layout">
                        <input type="text" name="first_name" id="first_name" value="<?php echo $user->first_name ?>" class="txt-name-layout">
                    </p>
                    <p>
                        <label for="family_name_kana" class="lbl-name-layout" id="familyname_label">セイ</label>
                        <label for="name" class="lbl-name-layout">メイ</label>
                        <br>
                        <input type="text" name="family_name_kana" id="family_name_kana" value="<?php echo $user->family_name_kana ?>" class="txt-name-layout">
                        <input type="text" name="first_name_kana" id="first_name_kana" value="<?php echo $user->first_name_kana ?>" class="txt-name-layout">
                    </p>
                    <div>
                        <label for="account">生年月日</label>
                        <ul class="float-list select-box">
                            <li>
                                <select name="year" id="year" onchange="isValid_birthday();">
                                    <option value="">--</option>
                                    <?php
                                    date_default_timezone_set("Asia/Tokyo");
                                    $date = new DateTime();
                                    $this_year = intval(date("Y", $date->getTimestamp()));
                                    for ($i = 0; $i < 100; $i++) {
                                        echo "<option value='" . strval($this_year - $i) . "'>" . strval($this_year - $i) . "</option>";
                                    }
                                    ?>
                                </select>
                                <span>年</span>
                            </li>
                            <li>
                                <select name="month" id="month" onchange="isValid_birthday();">
                                    <option value="">--</option>
                                    <?php
                                    for ($i = 1; $i <= 12; $i++) {
                                        echo "<option value='" . strval($i) . "'>" . strval($i) . "</option>";
                                    }
                                    ?>
                                </select>
                                <span>月</span>
                            </li>
                            <li>
                                <select name="day" id="day" onchange="isValid_birthday();">
                                    <option value="">--</option>
                                    <?php
                                    for ($i = 1; $i <= 31; $i++) {
                                        echo "<option value='" . strval($i) . "'>" . strval($i) . "</option>";
                                    }
                                    ?>
                                </select>
                                <span>日</span>
                            </li>
                        </ul>
                        <input type="hidden" name="birthday" id="birthday" value="<?php echo $user->birthday ?>">
                    </div>
                    <p>
                        <label for="zip">郵便番号</label>
                        <input type="text" maxlength="3" name="zip_code1" id="zip_code1" class="zip_code"
                               value="<?php echo $user->zip_code1?>" style="width:70px;"
                               onchange="mergeZipCode()">
                        -
                        <input type="text" maxlength="4" name="zip_code2" id="zip_code2" class="zip_code"
                               value="<?php echo $user->zip_code2?>" style="width:70px;"
                               onchange="mergeZipCode()">
                        <input type="hidden" name="zip_code" id="zip_code" value="<?php echo $user->zip_code?>"  class="w-short selectorZipcodeValue">
                        <input type="button" name="Search" value="住所検索" class="selectorSearchByZipcode" />
                    </p>
                    <p>
                        <label for="add">都道府県</label>
                        <input type="text" name="prefecture" id="prefecture" value="<?php echo $user->prefecture?>" class="w-short" >
                    </p>
                    <p>
                        <label for="add02">市区郡</label>
                        <input type="text" name="city" id="city" value="<?php echo $user->city?>" >
                    </p>
                    <p>
                        <label for="building">それ以降の住所</label>
                        <input type="text" name="building" id="building" value="<?php echo $user->building?>" class="w-short">
                    </p>
                    <span class="note">
                        <!-- ※登録住所は正確に入力して下さい。
                        <br/>
                        入力された住所が、身分証に記載された住所と一致しない場合、登録住所の再送をしていただきますのでご注意下さい。
                        <br/>
                        ・身分証に〇丁目〇番地と記載されている場合、ご入力いただく住所にも〇丁目〇番地とご記入ください。
                        <br/>
                        ・身分証にマンション名が記載されている場合、ご入力いただく住所にもマンション名をご記入ください。  -->

                        登録住所は身分証と同一内容を正確に入力して下さい。
                        <div class="reg-id-notice"> 
                            ・身分証に〇丁目〇番地と記載されている場合、ご入力いただく住所にも〇丁目〇番地とご記入ください。 
                            <br>
                            ・身分証にマンション名が記載されている場合、ご入力いただく住所にもマンション名をご記入ください。

                        </div>
                    </span>                    
                    <p>
                        <br>
                        <label for="country">国籍</label>
                        <input type="text" name="country" id="country" value="<?php echo $user->country?>" >
                    </p>

                    <h2>連絡先</h2>
                    <p>
                        <label for="tel">電話番号</label>
                        <input type="text" maxlength="5" name="tel1" id="tel1" class="tel"
                               value="<?php echo $user->tel1?>" style="width:70px;"
                               onchange="mergePhoneCode()">
                        -
                        <input type="text" maxlength="4" name="tel2" id="tel2" class="tel"
                               value="<?php echo $user->tel2?>" style="width:70px;"
                               onchange="mergePhoneCode()">
                        -
                        <input type="text" maxlength="4" name="tel3" id="tel3" class="tel"
                               value="<?php echo $user->tel3?>" style="width:70px;"
                               onchange="mergePhoneCode()">
                        <input type="hidden" name="tel" id="tel" value="<?php echo $user->tel?>" class="w-short" >
                        <span class="note">*日中連絡の取れる番号をご入力ください</span>
                    </p>
                    <p>
                        <label for="mail">メールアドレス</label>
                        <input type="text" name="email" id="email" value="<?php echo $user->email?>" >
                    </p>
                    <p>
                        <label for="mail2">メールアドレス(確認用の再入力)</label>
                        <input type="text"  name="email2" id="email2" value="<?php echo $user->email2?>" >
                    </p>
                    <p style="font-size:90%; color:#f00;"><label style="color:#f00;">※ご注意ください</label>
                        <!-- docomo/au/Softbankなどの携帯キャリアメールアドレスによるご登録はご遠慮ください。
                        <br/>
                        キャリアの迷惑メールフィルタにより、弊社から送信される重要な通知を受取ることができないケースが発生しており、ご登録いただくメールアドレスにつきましては、Gmail、YahooなどWEBメールのご登録をお願いいたします。
                        <br/>
                        キャリアメールとは、下記アドレス等で使用する携帯電話キャリアが提供するメールサービスのことです。
                        <br/>
                        [‣‣@docomo.ne.jp/‣‣@softbank.ne.jp/‣‣ezweb.ne.jp] -->
                        携帯キャリアメールアドレスのご登録不可 
                        docomo/au/Softbank 等
                    </p>

                    <h2>支払い方法</h2>
                    <p>
                        <label for="pay_method">支払い方法</label>
                        <select name="pay_method" id="pay_method" class="w-short">
                            <?php if ($this->config->item('enable_banking') === true) { ?><option value="<?php echo $this->config->item('payby_bank'); ?>">銀行振込</option><?php } ?>
                            <option value="<?php echo $this->config->item('payby_btc'); ?>">ビットコイン</option>
                        </select>
                    </p>
                    <p>
                        <label for="amount">予定金額</label>
                        <input type="text" name="amount" id="amount" value="<?php echo $user->amount?>" class="w-medule" ><span class="right">USD</span>
                    </p>
                    <?php if (false) { ?>
                        <p style="font-size:90%;">交換を希望される<span style="font-weight:bold;">日本円またはビットコインをドル換算して</span>予定金額をご入力ください。<br>
                            銀行振込の場合、上記予定金額の<span style="font-weight:bold;">3.75%</span>がビットコイン変換手数料として請求されますので、予めご了承ください。<br>
                            ※参考レート 1USD=<?php echo $rate; ?>円 (<?php echo $now; ?>時点)</p>
                    <?php } ?>
                    <p style="font-size:90%;">予定金額は<span style="font-weight:bold;">ドル換算で</span>ご入力ください。</p>

                    <?php
                    $doc_absolute_dir = FCPATH . $this->config->item('path_pdf_doc');
                    $search_doc_ext = ".pdf";
                    $replace_doc_ext = ".html";
                    ?>

                    <div class="terms-container">
                        <p class="txt-blue"><a href="<?php echo base_url($this->config->item('path_pdf_doc') . $this->config->item('doc_privacypolicy')); ?>" target="_blank" tabindex="-1">個人情報保護方針を読む</a>
                        </p>
                        <p>
                            <label id="personal_agreement_label" class="personal_agreement">
                                <input type="checkbox" name="personal_agreement" id="personal_agreement" <?php echo !isset($userPostData['personal_agreement']) ? '' : ' checked="checked"'; ?> 
                                />&nbsp;個人情報保護方針に同意する
                            </label>
                        </p>

                    </div>

                    <input type="hidden" name="createNewClient" id="createNewClient" value="true" >
                    <input type="button" class="next" value="next" onclick="return go('step2');" style="width:100%;">
                </div>
                <div id="step2">
                    <h2>規約への同意</h2>

                    <?php
                    $doc_absolute_dir = FCPATH . $this->config->item('path_pdf_doc');
                    $search_doc_ext = ".pdf";
                    $replace_doc_ext = ".html";
                    ?>

                    <div class="terms-container">
                        <p>
                            <span class="txt-blue">
                                <a href="<?php echo base_url($this->config->item('path_pdf_doc') . $this->config->item('doc_termsofuse')); ?>" target="_blank" tabindex="-1">利用規約を読む</a>
                            </span>
                            <label id="rule_agreement_label" class="rule_agreement">
                                <input type="checkbox" id="rule_agreement" name="rule_agreement" <?php echo !isset($userPostData['rule_agreement']) ? '' : ' checked="checked"'; ?> 
                                />&nbsp;内容に同意する
                            </label>
                        </p>

                    </div>

                    <div class="terms-container">
                        <p>
                            <span class="txt-blue">
                                <a href="<?php echo base_url($this->config->item('path_pdf_doc') . $this->config->item('doc_termsofrisk')); ?>" target="_blank" tabindex="-1">リスク説明を読む</a>
                            </span>
                            <label id="risk_agreement_label" class="risk_agreement">
                                <input type="checkbox" id="risk_agreement" name="risk_agreement" <?php echo !isset($userPostData['risk_agreement']) ? '' : ' checked="checked"'; ?> 
                                />&nbsp;内容に同意する
                            </label>
                        </p>

                    </div>

<ul class="order-btnlist float-list">
    <li><input type="button" class="next" value="back" tabindex="-1" onclick="return go('step1');"></li>
    <li><input type="button" class="next" value="next" onclick="return go('step3');"></li>
</ul>  
                </div>
                <div id="step3">
                    <h2>身分証とご本人が一緒に写っている写真<br>(セルフィ)</h2>
                    <p style="margin-top:20px;margin-bottom:0;">
                        <span class="order-checkit">Check it !</span>
                    <ul class="photo1">
                        <li>・JPEGまたはPNG形式で、サイズは3.7MB以下の画像である</li>
                        <li>・画面内にご本人の顔と本人確認資料が同時に写っている</li>
                        <li>・本人確認資料の文字,顔写真が明確に判別できる</li>
                    </ul>
                    </p>
                    <div class="file-upload btn btn-primary">
                        <input type="file" accept="image/*" name="photo1" id="photo1" class="upload photo1" onchange="previewPhoto(this);"/>
                    </div>
                    <input type="text" id="fileuploadurl" class="photo1" class="photo1" readonly placeholder="ファイル名が入ります">
                    <div class="order-pht">
                        <img id="preview_photo1" src="#" width="288" height="409" alt="プレビューが表示されます。" style="display:none;">
                    </div>
                    <p style="margin-top:60px;margin-bottom:0;">
                        <span style="font-weight:bold;">画像を2枚以上提出が必要な場合のみ下記より添付ください</span>
                    <ul class="photo2">
                        <li>・記載された住所と現住所が異なる場合、現住所確認書類も併せて必要です。</li>
                        <li>・法人での登録の場合、履歴事項全部証明書が必要です。</li>
                    </ul>
                    </p>
                    <div class="file-upload btn btn-primary">
                        <input type="file" accept="image/*" name="photo2" id="photo2" class="upload" onchange="previewPhoto(this,2);"/>
                    </div>
                    <input type="text" id="fileuploadurl2" class="photo2" readonly placeholder="ファイル名が入ります">
                    <div class="order-pht">
                        <img id="preview_photo2" src="#" width="288" height="409" alt="プレビューが表示されます。" style="display:none;">
                    </div>
                    <ul>
                        　　　　　　　　　　　　　　<li>画像を3枚以上提出が必要な場合、<br><a href="mailto:<?php echo $this->config->item('support_email'); ?>"><?php echo $this->config->item('support_email'); ?></a>まで別途メールにて送付お願いします。</li>
                        　　　　　　　　　　　　　　<li>マニュアル<br>セルフィ撮影に関する詳細は<a href="<?php echo $this->config->item('selfy_manual'); ?>" target="_blank" tabindex="-1" style="color:#00f;">こちら</a></li>
                    </ul>
                    </p>

<ul class="order-btnlist float-list">
    <li><input type="button" class="next" value="back" tabindex="-1" onclick="return go('step2');">
    </li>     
    <li><input type="button" class="next prevent-double-click" value="送信" id="confirm" onclick="return submitOrder();">
    </li>   
    <!-- <li><input type="button" class="next" value="confirm" onclick="return submitOrder();">
    </li> -->
</ul>
                    
                </div>
            </form>
        </div><!-- / .whitebox -->

    </section><!-- / .order-main -->
</div><!-- /.l-login -->

<script type="text/javascript" src="<?php echo base_url('js/regUser.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/client/regClient.js'); ?>"></script>
