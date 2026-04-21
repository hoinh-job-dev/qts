<section class="l-contents cf">
	<div class="l-main js-height">
		<div class="l-page-">
			<!-- <div class="whitebox  greybox">
				<p class="client-title"><?php echo $title;?></p>
			</div> -->

			<form id="quantaWallet">
				<?php
                    $doc_absolute_dir = FCPATH . $this->config->item('path_pdf_doc');
                    $search_doc_ext = ".pdf";
                    $replace_doc_ext = ".html";
                ?>

				<div class="whitebox  greybox">
					<p class="client-header">QNT還元とは</p>
                    <section class="terms-content content-area" >
                        <div class="bg-terms-content">
                            <?php include_once str_replace($search_doc_ext, $replace_doc_ext, $doc_absolute_dir . $this->config->item('client_qntRedeemSupport')); ?></div>
                    </section>
				</div>

				<div class="whitebox greybox">
					<p class="client-header">QNT還元の流れ</p>
                    <section class="terms-content content-area" >
                        <div class="bg-terms-content">
                            <?php include_once str_replace($search_doc_ext, $replace_doc_ext, $doc_absolute_dir . $this->config->item('client_qntRedeemProcess')); ?></div>
                    </section>
				</div>

				<div class="whitebox greybox">
					<p class="client-header">QNT還元サポート資料</p>
                    <section class="terms-content content-area" >
                        <div class="bg-terms-content">
                            <?php include_once str_replace($search_doc_ext, $replace_doc_ext, $doc_absolute_dir . $this->config->item('client_qntRedeemDoc')); ?></div>
                    </section>
				</div>

			</form>
		</div><!-- /.l-page-home -->
