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
			<td><input type="password" id="password" name="password" /></td>
		</tr>
                <?php } ?>
		<tr>
			<th>ビットコインアドレス</th>
			<td><input type="text" id="btc_address" name="btc_address" value="<?php echo $btc_address; ?>" /></td>
		</tr>
		<?php 
		if(isset($arrErrMessages) && count($arrErrMessages) > 0) {
			?>
			<tr>
				<th colspan="2">
					<ul>
						<?php
						foreach ($arrErrMessages as $key => $value) {
							?><li class="error"><?php echo $value; ?></li><?php
						}
						?>
					</ul>
				</th>
			</tr>
			<?php
		}
		?>
		<tr>
			<th colspan="2">
				<input type="button" name="cancelAgentInfo" value="Cancel" class="button" onclick="window.location.href='<?php echo base_url('Agent/home'); ?>'; return false;" />&nbsp;
				<input type="button" name="saveAgentInfo" value="更新" class="button" onclick="this.form.submit();" />
			</th>
		</tr>
	</table>
</div>