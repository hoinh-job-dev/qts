<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/userAccount.css'); ?>">

<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <h2><?php echo $title;?></h2>
            <form class="view-user-account">
                <div class="whitebox">
                    <input type="button" name="backToOperator" value="戻る" class="button float_left" onclick="window.location.href='<?php echo base_url('Operator/home'); ?>';" />
                    <!--<input type="button" name="editUserDetail" value="編集" class="button float_right" onclick="window.location.href='<?php //echo base_url('Operator/editUserDetail/'.$user->uid); ?>';" />-->
                    <?php if ($this->config->item('act_approved') != $user->approved_status || ($this->config->item('act_approved') == $user->approved_status && $this->CI->isAdmin())){ ?>
                        <input type="button" name="editUserDetail" value="編集" class="button float_right" onclick="window.location.href='<?php echo base_url('Operator/editUserDetail/'.$user->uid); ?>';" />
                    <?php } ?>
                    <div class="clear_both"></div>
                </div>
                <?php 
                if(isset($updateMessage) && !empty($updateMessage)) {
                	?>
                	<div class="whitebox">
	                    <span class="info"><?php echo $updateMessage; ?></span>
	                </div>
                	<?php 
                }
                include(VIEWPATH) . 'operator/elements/userAccountGeneralInfo.php';
                include(VIEWPATH) . 'operator/elements/userAccountKYC.php';
                include(VIEWPATH) . 'operator/elements/userAccountAgency.php';
                ?>
            </form>
        </div>