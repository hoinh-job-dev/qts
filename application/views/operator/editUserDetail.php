<script type="text/javascript" src="<?php echo base_url('js/regUser.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/wallet-address-validator.min.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/userAccount.css'); ?>">

<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <h2><?php echo $title;?></h2>
            <form action="<?php echo base_url('Operator/editUserDetail/'.$user->uid); ?>" method="POST" enctype="multipart/form-data" class="view-user-account">
                <input type="hidden" name="update_user" value="1" />
                <input type="hidden" name="uid" value="<?php echo $user->uid?>" />
                <div class="whitebox">
                    <input type="button" name="cancelUserDetail" value="Cancel" class="button float_left" onclick="window.location.href='<?php echo base_url('Operator/viewUserDetail/'.$user->uid); ?>';" />
                    <input type="button" name="saveUserDetail" value="更新" class="button float_right" onclick="return validateUserAccount(this);" />
                    <div class="clear_both"></div>
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

                <?php 
                include(VIEWPATH) . 'operator/elements/editUserAccountGeneralInfo.php';
                include(VIEWPATH) . 'operator/elements/editUserAccountKYC.php';
                include(VIEWPATH) . 'operator/elements/userAccountAgency.php';
                ?>
            </form>
        </div>

<script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>