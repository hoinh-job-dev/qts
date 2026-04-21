<div class="whitebox">

    <h3>ID, 識別子</h3>
    <table>
        <tr><th style="color:red;">ユーザID</th><td><?php echo $user->uid; ?></td></tr>
        <tr><th style="color:red;">個人情報ID</th><td><?php echo $user->personal_id; ?></td></tr>
        <tr><th style="color:red;">User hash</th><td><?php echo $user->user_hash; ?></td></tr>
    </table>
    <br>
    <h3>アカウント情報</h3>
    <table style="width:100%;resize:none;">
        <tr>
            <th style="color:red;">ロール</th>
            <td><?php echo $user->role; ?></td>
        </tr>
        <tr>
            <th style="color:red;">種別</th>
            <td><?php echo $user->type; ?></td>
        </tr>
        <?php if ("法人" == $user->type) {?>
            <tr>
                <th style="color:red;">会社名</th>
                <td><?php echo $user->company_name . " (" . $user->company_name_kana . ")"; ?></td>
            </tr>
        <?php }?>

        <tr>
            <th style="color:red;">姓</th>
            <td><input type="text" name="family_name" id="familyname" value="<?php echo $user->family_name; ?>" class="w-short family_name" onchange=""></td>
        </tr>
        <tr>
            <th style="color:red;">姓(カナ)</th>
            <td><input type="text" name="family_name_kana" id="familyname" value="<?php echo $user->family_name_kana; ?>" class="w-short" onchange=""></td>
        </tr>
        <tr>
            <th style="color:red;">名</th>
            <td><input type="text" name="first_name" id="name" value="<?php echo $user->first_name; ?>" class="w-short first_name" onchange=""></td>
        </tr>
        <tr>
            <th style="color:red;">名(カナ)</th>
            <td><input type="text" name="first_name_kana" id="name" value="<?php echo $user->first_name_kana ?>" class="w-short" onchange=""></td>
        </tr>

        <tr>
            <th style="color:red;">コメント</th>
            <td><textarea name="comment"><?php echo $user->comment; ?></textarea></td>
        </tr>

        <tr><th style="color:red;">登録日時</th><td><?php echo $personal['create_at']; ?></td></tr>
    </table>

    <?php if ("交換者" != $user->role) {?>
    <br>
    <h3>コミッション用ビットコインアドレス</h3>
    <?php
        $roleLogin = $this->CI->getSessionRole();
        $roleEdit = array(
                $this->config->item('role_sysadmin'),
                $this->config->item('role_operator')
        );
    ?>
    <table>
        <tr><th style="color:red;">BTCアドレス</th>
            <td>
                <?php if(!in_array($roleLogin, $roleEdit)) {  ?>
                    <?php echo $user->btc_address; ?>
                    <input type="hidden" name="btc_address" id="btc_address" value="<?php echo $user->btc_address ?>"/>
                <?php }else{  ?>
                    <input type="text" name="btc_address" id="btc_address" value="<?php echo $user->btc_address ?>" class="w-short btc_address" >
                <?php } ?>
            </td>
        </tr>
    </table>
    <?php }?>

    <br>
    <h3>ユーザーの状態</h3>
    <table>
        <tr><th style="color:red;">状態</th><td><?php echo $user->status; ?></td></tr>
    </table>

</div>