	<section class="l-contents cf">
		<div class="l-main js-height">
			<div class="l-page-link-client">
				<h2><?php echo $title;?></h2>
				<form action="<?php echo base_url('Agent/makeClientLink'); ?>" method="post" id="link">
					<div class="whitebox">
						<h3>リンク作成</h3>
						<label>リンク利用回数：</label><br>
						<input type="radio" id="can0" name="can_recursive" value=0 <?php if(0 == $can_recursive) echo "checked"; ?> ><label for="can0">1回のみ</label>
						<input type="radio" id="can1" name="can_recursive" value=1 <?php if(1 == $can_recursive) echo "checked"; ?> ><label for="can1">複数回</label><br>

						<label>メモ</label>
						<input type="text" id="memo" name="memo">
                        <input type="submit" value="リンク作成" onclick="return makeLink();"><br><br>
					</div>
					<div class="whitebox">
						<h3>作成したリンク</h3>
						<p><?php if (0<strlen($user_hash)) { echo base_url('Client')."/".$user_hash; } ?></p>
					</div>
				</form>
			</div>

<script type="text/javascript" src="<?php echo base_url('js/agent/makeLink.js'); ?>"></script>
