<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

        <section class="l-contents cf">
		<div class="l-main js-height">
			<div class="l-page-">
				<h2><?php echo $title;?></h2>
                                    <div class="whitebox">
                                        <h3>レート取得</h3>
                                        <form action="<?php echo base_url('Operator/outputCsv_rate'); ?>" method="post" id="outputCsv">
                                                <ul id="button">
													<li>日時 from : <input type="text" id="from" name="datefrom" placeholder="yyyy/mm/dd (HH:mm:ss)"></li>
													<li>日時 to : <input type="text" id="from" name="dateto" placeholder="yyyy/mm/dd (HH:mm:ss)"></li>
                                                    <li><input type="button" id="update" value="CSV出力" onclick="document.getElementById('outputCsv').submit();"></li>
                                                </ul>
                                        </form>
                                    </div>
                        </div>

<script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>