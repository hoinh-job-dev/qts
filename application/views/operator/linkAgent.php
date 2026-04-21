<section class="l-contents cf">
	<div class="l-main js-height">
		<div class="l-page-link-agent">
			<h2><?php echo $title;?></h2>
			<form action="<?php echo base_url('Operator/makeAgentLink'); ?>" method="post" id="link">
				<div class="whitebox">
					<h3>リンク作成</h3>
					<label>メモ</label>
					<input type="text" id="memo" name="memo"/ >
					<input type="submit" value="リンク作成" />
				</div>
				<div class="whitebox">
					<h3>作成したリンク</h3>
					<p><?php if (0<strlen($user_hash)) { echo base_url('Agent')."/".$user_hash; } ?></p>
				</div>
			</form>
		</div>