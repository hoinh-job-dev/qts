<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-link-client">
            <h2><?php echo $title;?></h2>  

            <?php if (!empty($arrFieldError)) { ?>
                <div class ="whitebox" style="color:#ff6666;">
                    <?php echo "入力したデータはエラーがあります"; 
                        foreach($arrFieldError as $key => $fieldError) {
                        ?>
                        <p class="error_link" for="<?php echo $key ?>" ><?php echo $fieldError ?></p>
                        <?php
                    }?>
                </div>
            <?php } ?>

            <form action="<?php echo base_url('Agent/confirmChangePasswd'); ?>" method="post" id="agentChangePasswd">
                <input type="hidden" name="user_hash" value="<?php echo $user_hash; ?>" >

                <div class="whitebox">

                <p>
                    <label for="password">現在のパスワード</label>
                    <input type="password" name="currentPassword" id="currentPassword" class="w-short" >
                </p>
                <p>
                    <label for="newPassword">パスワード</label>
                    <input type="password" name="newPassword" id="newPassword" class="w-short" >
                </p>
                <p>
                    <label for="confirmNewPassword">パスワード(確認用の再入力)</label>
                    <input type="password" name="confirmNewPassword" id="confirmNewPassword" class="w-short">
                </p>
                <p>
                    <span class="note">*パスワードは6文字以上で、半角英数字、大文字と小文字を入れてください。</span>
                </p>
                    <input type="button" class="confirm" value="Update" onclick="return submitChangePassword();">
                </div>               
            </form>
        </div>
<script type="text/javascript" src="<?php echo base_url('js/regUser.js'); ?>"></script> 
