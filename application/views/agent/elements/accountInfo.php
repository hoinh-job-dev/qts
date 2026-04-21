<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/agent/agent.css'); ?>">
<div class="whitebox">
	<h3>マイアカウント</h3>
	<table>
		<tr>
			<th>種類</th>
			<td><?php echo $typename; ?></td>
		</tr>
		<tr>
			<th>氏名</th>
			<td><?php if ('02'==$type) { echo $company_name_kana."<br>".$company_name."<br>";} echo $family_name_kana." ".$first_name_kana."<br>".$family_name." ".$first_name; ?></td>
		</tr>
		<tr>
			<th>email</th>
			<td><?php echo $email; ?></td>
		</tr>
                <?php if (false) { ?>
		<tr>
			<th>パスワード</th>
			<td><?php echo $password; ?></td>
		</tr>
                <?php } ?>
		<?php 
		if(isset($btc_address) && !empty($btc_address)) {
			?>
			<tr>
				<th>ビットコインアドレス</th>
				<td><?php echo $btc_address; ?></td>
			</tr>
			<?php
		}
		?>
		<tr>
			<th>上記情報変更について</th>
			<td>
				上記情報の変更につきましては、重要情報となるため弊社所定の手続きが必要となりますので変更の際は弊社サポートデスク
				<a href="mailto:support@one8-association.co.jp" style="color:#00f;">support@one8-association.co.jp</a>
				まで事前連絡をお願いします。
			</td>
		</tr>

		<?php
		if(isset($updateMessage) && !empty($updateMessage)) {
			?>
			<tr>
				<th colspan="2" class="info">
					<?php echo $updateMessage; ?>
				</th>
			</tr>
			<?php
		}
		?>
	</table>
</div>