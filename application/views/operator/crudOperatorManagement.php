<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">
<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-ope-man">

        <h2><?php echo $title;?></h2>

        <?php if ($errormsg) { ?>
            <div class ="whitebox" style="color: red;">
                <?php echo $errormsg ?>
            </div>
        <?php }elseif($successMsg){ ?>
            <div class ="whitebox" style="color: blue;">
                <?php echo $successMsg ?>
            </div>
        <?php }?>        

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

        <div class="whitebox ope-man-body">
            <form action="<?php echo base_url('Operator/crudOperatorManagement'); ?>" method="post" id="ope-man-form">
               <!-- <div class="row row-half">
                    <a href="<?php //echo base_url('Operator/operatorManagement'); ?>" class="button-link">戻る</a>
                </div> --> 
                
                <div class="row row-half">
                    <input type="button" class="button-return"  value="戻る" onclick="go_url('<?php echo base_url('Operator/operatorManagement'); ?>');" >
                </div> 
               


                <?php if (isset($_POST['ope_new'])) { ?>
                    <div class="row row-half">
                        <input type="submit" value="追加" name="ope_donew">
                        <input type="hidden" value="ope_new" name="ope_new">
                        <input type="hidden" name="formsubmit" value="formsubmit">
                    </div>
                <?php } ?>
                <?php if (isset($_POST['ope_edit'])) { ?>
                    <div class="row row-half">
                        <input type="submit" value="更新" name="ope_doedit">
                        <input type="hidden" value="ope_edit" name="ope_edit">
                        <input type="hidden" name="formsubmit" value="formsubmit">
                    </div>
                <?php } ?>
                <?php $readonly ="";
                    if (isset($_POST['ope_delete'])) {
                        $readonly= "readonly" ?>

                    <div class="row row-half">
                        <input type="submit" value="削除" name="ope_dodelete">
                    </div>
                <?php } ?>

                <div class="row row-ope-man">
                    <label for="uid">アカウントUID</label>
                    <?php if (isset($ope) && '' != $ope->uid) { ?>
                        <input type="text" value="<?php echo $ope->uid;?>" name="uid" readonly="readonly">
                        <?php
                    }else { ?>
                            <br/>New
                    <?php } ?>
                </div>

                <div class="row row-ope-man">
                    <label for="email">電子メール</label>
                    <input type="email" id="email" value="<?php if (isset($ope)) { echo $ope->email; } ?>" name="email" <?php echo $readonly ?>>
                </div>

                <div class="row row-ope-man">
                    <label for="family_name">姓</label>
                    <input type="text" id="family_name" value="<?php if (isset($ope)) echo $ope->family_name ?>" name="family_name" <?php echo $readonly ?>>
                </div>

                <div class="row row-ope-man">
                    <label for="first_name">名</label>
                    <input type="text" id="first_name" value="<?php if (isset($ope)) echo $ope->first_name ?>" name="first_name" <?php echo $readonly ?>>
                </div>

                <div class="row row-ope-man">
                    <label for="family_name_kana">姓(カナ)</label>
                    <input type="text" id="family_name_kana" value="<?php if (isset($ope)) echo $ope->family_name_kana ?>" name="family_name_kana" <?php echo $readonly ?> />
                </div>

                <div class="row row-ope-man">
                    <label for="first_name_kana">名(カナ)</label>
                    <input type="text" id="first_name_kana" value="<?php  if (isset($ope)){ echo $ope->first_name_kana;}else{ echo " ";} ?>" name="first_name_kana" <?php echo $readonly ?> />
                </div>
                <div class="row row-ope-man">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" <?php echo $readonly; if(!empty($arrFieldError)){ ?>
                        value="<?php echo $ope->password;?>" <?php
                    }else { ?>
                            value=""  placeholder="*****"
                    <?php } ?>>
                </div>
                
                <div class="row row-ope-man">
                    <label for="role">ロール</label>
                    <select name="role" id="role" <?php echo $readonly ?>>
                        <option value=""></option>
                        <?php

                        foreach ($roles as $code => $value) {
                            $selected = '';
                            //echo $ope->role . " : " . $code;
                            if(isset($ope) && $ope->role == $code) {
                                $selected = 'selected';
                            }
                            ?>
                            <option value="<?php echo $code; ?>" <?php echo $selected;?>><?php echo $value; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </form>            
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>">
</script> 