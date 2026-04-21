<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">
<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-ope-man">

        <h2><?php echo $title;?></h2>

        <?php if (!empty($errormsg)) { ?>
            <div class ="whitebox" style="color: red;">
                <?php echo $errormsg ?>
            </div>
        <?php }
        if(!empty($successMsg)){ ?>
            <div class ="whitebox" style="color: blue;">
                <?php echo $successMsg ?>
            </div>
        <?php }?>

        
        <div class ="whitebox ope-man-head" >
            <form action="<?php echo base_url('Operator/crudOperatorManagement'); ?>" method="post" id="ope-man-form">
                <div class="row">
                    <label for="uid">アカウントUID</label>
                    <input type="number" value="" name="uid">
                </div>
                
                <div class="row row-submit">                       
                   <input type="submit" value="新規" name="ope_new">
                </div>
                <div class="row row-submit">
                    <input type="submit" value="編集" name="ope_edit">
                </div>
                <div class="row row-submit">
                    <input type="submit" value="削除" name="ope_delete">
                </div>
                <input type="hidden" name="formsubmit" value="formsubmit">
            </form>
        </div>
        <!--Addition new form view user-->
        <div class="whitebox" id="table">
            <h3>登録オペレーター</h3>            
            <div id="tableHead">
                <table>
                    <tr>
                        <th class="uid">オペレータ<br> UID</th>
                        <th class="name">オペレータ<br>氏名 </th>
                        <th class="email">Email</th>
                        <th class="role">ロール</th>
                    </tr>
                </table>
            </div>
            
            <div id="tableBody" onscroll="document.getElementById('tableBody').scrollTop = document.getElementById('tableBody').scrollTop;">
                <table>
                <?php foreach ($operatorList as $user) {?>
                    <tr>
                        <td class="uid"><?php echo $user->uid ; ?></td>
                        <td class="name"><?php echo $user->family_name . " " . $user->first_name; ?></td>
                        <td class="email"><?php echo $user->email; ?></td>
                        <td class="role"><?php echo $roles[$user->role] ; ?></td>
                    </tr>
                <?php }?>
                </table>
           </div>
            
           <div style="clear: both;"></div>
        </div>
    </div>

<script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script> 