<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/agent/agent.css'); ?>">	
<section class="l-contents cf">
	<div class="l-main js-height">
		<div class="l-page-">
			<h2><?php echo $title;?></h2>
			<form class="view-account">
				<?php 
				include(VIEWPATH) . 'agent/elements/accountNotice.php'; 
				include(VIEWPATH) . 'agent/elements/accountInfo.php';
				include(VIEWPATH) . 'agent/elements/accountTerms.php';
				?>
			</form>
		</div><!-- /.l-page-home -->
