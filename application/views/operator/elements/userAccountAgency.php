<?php foreach ($agents as $agent ) {
?>
<!-- Sales, PT -->
<?php if (0 < sizeof($agent)) { ?>
<div class="whitebox">
    <h3>代理店</h3>
    <table>
        <tr><th style="color:red;">代理店UID</th><td><?php echo $agent->uid; ?></td></tr>
        <tr><th style="color:red;">ロール</th><td><?php echo $rolemap[$agent->role]; ?></td></tr>
        <tr><th style="color:red;">種別</th><td><?php echo $agent->type; ?></td></tr>
        <?php if ("法人"==$agent->type) { ?><tr><th style="color:red;">会社名</th><td><?php echo $agent->company_name. " (".$agent->company_name_kana.")"; ?></td></tr><?php } ?>
        <tr><th style="color:red;">氏名</th><td><?php echo $agent->family_name.' '.$agent->first_name. " (".$agent->family_name_kana.' '.$agent->first_name_kana.")"; ?></td></tr>
    </table>
</div>
<?php } ?>
<?php if (0 < sizeof($orders)) { ?>
<div class="whitebox">
    <h3>注文情報</h3>
    <table>
    </table>
</div>
<?php }} ?>


