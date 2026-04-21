<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/common.css'); ?>">

<section class="l-contents cf">
	<div class="l-main js-height">
		<div class="l-page-">
			<!-- <div class="whitebox  greybox title">
				<p class="client-title"><?php echo $title;?></p>
			</div> -->

			<form id="quantaWallet">
				<?php
                    $doc_absolute_dir = FCPATH . $this->config->item('path_pdf_doc');
                    $search_doc_ext = ".pdf";
                    $replace_doc_ext = ".html";
                ?>

				<div class="whitebox greybox">
					<p class="client-header">Quanta Walletとは</p>
                    <section class="terms-content content-area">
                    	<div class="bg-terms-content">
                        <?php include_once str_replace($search_doc_ext, $replace_doc_ext, $doc_absolute_dir . $this->config->item('client_quantawallet')); ?></div>
                    </section>
				</div>

				<div class="whitebox greybox">
					<p class="client-header">暗号通貨の保管に関する諸注意</p>
                    <section class="terms-content content-area" >
                    	<div class="bg-terms-content">
                        <?php include_once str_replace($search_doc_ext, $replace_doc_ext, $doc_absolute_dir . $this->config->item('client_noteCryptoCurrency')); ?></div>
                    </section>
				</div>

				<div class="whitebox greybox">
					<p class="client-header">セットアップ・バックアップ手順</p>
                    <section class="terms-content content-area" >
                    	<div class="bg-terms-content">
                        <?php include_once str_replace($search_doc_ext, $replace_doc_ext, $doc_absolute_dir . $this->config->item('client_backup')); ?></div>
                    </section>
				</div>
			</form>
		</div><!-- /.l-page-home -->
