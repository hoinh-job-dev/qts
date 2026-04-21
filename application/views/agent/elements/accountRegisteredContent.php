<div class="whitebox">
	<h3>登録内容</h3>
	<table>
		<tr>
			<th>種類</th>
			<td><?php echo $typename; ?></td>
		</tr>
		<tr>
			<th>氏名</th>
			<td><?php if ('02'==$type) { echo $company_name_kana."<br>".$company_name."<br>";} echo $family_name_kana." ".$first_name_kana."<br>".$family_name." ".$first_name; ?></td>
		</tr>
	</table>
</div>