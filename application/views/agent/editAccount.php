<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/agent/agent.css'); ?>">	
<section class="l-contents cf">
	<div class="l-main js-height">
		<div class="l-page-">
			<h2><?php echo $title;?></h2>
			<form class="view-account" action="<?php echo base_url('Agent/editAccount'); ?>" method="post">
				<input type="hidden" name="update_agent" value="1" />
				<?php 
				include(VIEWPATH) . 'agent/elements/accountNotice.php';
				include(VIEWPATH) . 'agent/elements/editAccountInfo.php';
				include(VIEWPATH) . 'agent/elements/accountTerms.php';
				?>
			</form>
		</div><!-- /.l-page-home -->
