<style>
 .link_rule {
    margin-left:20px;
 }
</style>
<div class="whitebox">
	<h3>同意済みの規約一覧</h3>
	<p class="link_rule">
		<span class="txt-blue">
		<?php
		foreach($this->config->item('role_agent_doc_marketer') as $agent_role => $doc_name) {
			if($agent_role == $agentrank) {
				?><a href="<?php echo base_url($this->config->item('path_pdf_doc') . $doc_name); ?>" target="_blank" tabindex="-1" style="color:#00f;">マーケターに関する規約</a><?php
				break;
			}
		}
		?>
		</span>
	</p>
	<p class="link_rule">
            <a href="<?php echo base_url($this->config->item('path_pdf_doc') . $this->config->item('doc_privacypolicy')); ?>" target="_blank" tabindex="-1" style="color:#00f;">プライバシーポリシー</a>
        </p>
</div>
<div class="whitebox">
	<h3>その他のツール</h3>
	<p style="margin-left:20px;">
		代理店登録操作マニュアル<a href="<?php echo base_url($this->config->item('path_pdf_doc') . $this->config->item('doc_agentmanual')); ?>" target="_blank" tabindex="-1" style="color:#00f;">こちら</a>
	</p>
	<p style="margin-left:20px;">
		説明会スケジュールは<a href="<?php echo base_url($this->config->item('path_pdf_doc') . $this->config->item('doc_seminarschedule')); ?>" target="_blank" tabindex="-1" style="color:#00f;">こちら</a>
	</p>
	<p style="margin-left:20px;">
		代理店活動に関する注意点<a href="<?php echo base_url($this->config->item('path_pdf_doc') . $this->config->item('doc_agentnote')); ?>" target="_blank" tabindex="-1" style="color:#00f;">こちら</a>
	</p>
</div>