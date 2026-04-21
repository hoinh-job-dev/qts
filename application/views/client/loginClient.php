<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/common.css'); ?>">
<div class="l-login">
	<section class="login-main login-client">
		<div class="mainbox">
		<div class="whitebox">
			<h1><img src="<?php echo base_url('img/top/marvelous_title_client.png'); ?>" width="237" height="104" alt="Marvelous エージェントログイン"></h1>
			<form action="<?php echo base_url('Client/login'); ?>" method="post" id="login">
            <?php $personalInfo = isset($arrPersonalInfo) ? $arrPersonalInfo : null; ?>
                <div class="group-field">
                    <!--family name & firtname-->
                    <div class="left">
                        <label for="familyname" class="lbl-name-login" id="familyname_label">姓</label>
                        <input type="text" name="family_name" id="family_name" class="txt-name-login" onchange="isValid_text(this);" 
                            value="<?php 
                            if(null == $personalInfo){
                                echo "";
                            }else{
                                echo $personalInfo['family_name'];
                            } ?>" 
                        />
                    </div>
                    <div class="right">
                        <label for="firstname" class="lbl-name-login">名</label>
                        <input type="text" name="first_name" id="first_name" class="txt-name-login" onchange="isValid_text(this);"
                            value="<?php 
                                if(null == $personalInfo){
                                    echo "";
                                }else{ 
                                    echo $personalInfo['first_name'];
                                } ?>" 
                        />
                    </div>
                    <div class="clearfix"></div>
                </div>

                <!--Birthday 生年月日-->
                <div class="clearfix">
                    <p style="margin:30px 0 5px 0">生年月日</label>
                    <ul class="float-list select-box">
                        <li>
                            <select name="year" id="year" onchange="isValid_birthday();">
                                <option value="">--</option>
                                <?php
                                $year_2005 = 2005;//set max-year is 2005
                                for ($i = 0; $i < 100; $i++) {
                                    //echo "<option value='" . strval($year_2005 - $i) . "'>" . strval($year_2005 - $i) . "</option>";
                                    if(null != $personalInfo && strval($year_2005 - $i) == intval(explode("/",$personalInfo['birthday'])[0])){
                                        echo "<option value='" . strval($year_2005 - $i) . "' selected='selected'>" . strval($year_2005 - $i) . "</option>";
                                    }else{
                                        echo "<option value='" . strval($year_2005 - $i) . "'>" . strval($year_2005 - $i) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                            <span>年</span>
                        </li>
                        <li>
                            <select name="month" id="month" onchange="isValid_birthday();">
                                <option value="">--</option>
                                <?php
                                if(null != $personalInfo){

                                }
                                for ($i = 1; $i <= 12; $i++) {
                                    //echo "<option value='" . strval($i) . "'>" . strval($i) . "</option>";
                                    if(null != $personalInfo && $i == explode("/",$personalInfo['birthday'])[1]){
                                        echo "<option value='" . strval($i) . "' selected='selected'>" . strval($i) . "</option>";
                                    }else{
                                        echo "<option value='" . strval($i) . "'>" . strval($i) . "</option>";
                                    }
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
                                    //echo "<option value='" . strval($i) . "'>" . strval($i) . "</option>";
                                    if(null != $personalInfo && $i == explode("/",$personalInfo['birthday'])[2]){
                                        echo "<option value='" . strval($i) . "' selected='selected'>" . strval($i) . "</option>";
                                    }else{
                                        echo "<option value='" . strval($i) . "'>" . strval($i) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                            <span>日</span>
                        </li>
                    </ul>
                    <input type="hidden" name="birthday" id="birthday" value="">
                    <input type="hidden" name="isFirstLoad" value="ok">
                </div>
                <div class="button-client-login">
                    <input type="button" value="login" onclick="loginClient();">
                </div>

			</form>
            <br>
            <?php
                $isErr = isset($isError)? $isError : '00';
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
                case '31':
                    echo "<p>入力したデータは誤りがありますので、もう一度ご入力ください。</p>";
                    break;
                default:
                }
            ?>
            <ul>
				<li style="margin-bottom:10px;"><お問い合わせ先><br><a href="mailto:<?php echo $this->config->item('support_email'); ?>?subject=[WRAPPY]お問い合わせ"><?php echo $this->config->item('support_email'); ?></a></li><br>                
            </ul>
        </div><!-- / .whitebox -->
		</div><!-- / .mainbox -->
	</section><!-- / .login-main -->
</div><!-- /.l-login -->


<script type="text/javascript" src="<?php echo base_url('js/regUser.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/common/login.js'); ?>"></script>