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
        <tr><th style="color:red;">ロール</th><td><?php echo $user->role; ?></td></tr>
        <tr><th style="color:red;">種別</th><td><?php echo $user->type; ?></td></tr>
        <?php if ("法人"==$user->type) { ?><tr><th style="color:red;">会社名</th><td><?php echo $user->company_name. " (".$user->company_name_kana.")"; ?></td></tr><?php } ?>
        <tr><th style="color:red;">氏名</th><td><?php echo $user->family_name.' '.$user->first_name. " (".$user->family_name_kana.' '.$user->first_name_kana.")"; ?></td></tr>
        <tr><th style="color:red;">コメント</th><td>
            <textarea name="comment" disabled><?php echo $user->comment; ?></textarea>
        </td></tr>
        <tr><th style="color:red;">登録日時</th><td><?php echo $personal['create_at']; ?></td></tr>
    </table>
    <?php if ("交換者"!=$user->role) { ?>
    <br>
    <h3>コミッション用ビットコインアドレス</h3>
    <table>
        <tr><th style="color:red;">BTCアドレス</th><td><?php echo $user->btc_address; ?></td></tr>
    </table>
    <?php } ?>
    <br>
    <h3>ユーザーの状態</h3>
    <table>
        <tr><th style="color:red;">状態</th><td><?php echo $user->status; ?></td></tr>
    </table>
</div>