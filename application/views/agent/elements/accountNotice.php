<?php if ($this->config->item('act_approved')>intval($userstatus)) { ?>
<div class="whitebox" style="color:red;">
	現在、このアカウントはコンプライアンス確認中です。<br>
	確認が終了しましたらご登録のメールアドレスにご連絡差し上げます。
</div>
<?php } ?>