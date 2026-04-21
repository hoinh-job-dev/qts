<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/common.css'); ?>">
<style>
    input[type="button"]{
        padding:2px 6px;
        width: 188px;
        height: 31px;
        border: 1px solid;
    } .confirm {
        background-color: #000;
        color: #fff;
        float:right;
    }    
</style>

<section class="l-contents cf">
	<div class="l-main js-height">
		<div class="l-page-link-agent">
			<h2><?php echo $title;?></h2>
			<form action="<?php echo base_url('Agent/confirmGuideAgent'); ?>" method="post">
				<div class="whitebox">
					<h3>代理店ガイダンス</h3>
					<?php
	                    $doc_absolute_dir = FCPATH . $this->config->item('path_pdf_doc');
	                    $search_doc_ext = ".pdf";
	                    $replace_doc_ext = ".html";
                    ?>

					<div class="terms-container">
                        <section class="terms-content"  data-end_reached="F">
                            <?php include_once str_replace($search_doc_ext, $replace_doc_ext, $doc_absolute_dir . $this->config->item('doc_guide_agent')); ?>
                        </section>
                        <div class="guide-action guide-center">
                            <label id="rule_agreement_label" class="rule_agreement">  
                                <input type="checkbox" id="rule_agreement" name="rule_agreement" class="terms-checkbox"  value="rule_agreement" disabled="disabled" 
                                />内容に同意する
                            </label>
                           
                            </br>
                            <input type="button" class="confirm guide-center selectorConfirmRegister" disabled="disable" value="Ok" id="Ok" onclick="return submit();">
                        </div>                        
                    </div>
				</div>
			</form>
		</div>

<script type="text/javascript" src="<?php echo base_url('js/regUser.js'); ?>"></script>
