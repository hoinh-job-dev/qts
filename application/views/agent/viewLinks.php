<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">
	<section class="l-contents cf">
		<div class="l-main js-height">
			<div class="l-page-view-links">
				<h2><?php echo $title;?></h2>

                <!-- Begin form search -->
                <form action="<?php echo base_url('Agent/viewLinks'); ?>" method="post" id="search" class="search" style="width:92%; margin:10px;">
                    <fieldset>

                        <div class="bigrow row ">
                            <label for="family_name"> 姓 </label>
                            <input type="text" name="search[family_name]" value="<?php echo $search->family_name; ?>" />
                        </div>

                        <div class="row bigrow">
                            <label for="first_name"> 名</label>
                            <input type="text" name="search[first_name]" value="<?php echo $search->first_name; ?>" />
                        </div>
                        <div class="row bigrow">
                            <label for="status">ステータス </label>
                            <select name="search[status]" id="status">
                                <option value=""></option>
                                <?php
                                foreach ($statuses as $code => $value) {
                                    $selected = '';
                                    if($search->status == $code) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?php echo $code; ?>" <?php echo $selected;?>><?php echo $value; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row row-memo">
                            <label for="memo"> メモ</label>
                            <input type="text" name="search[memo]" value="<?php echo $search->memo; ?>" />
                        </div>
                        <div class="row-search row-search-center">
                            <label for="search"></label>
                            <input type="submit" value="Search" onclick="">
                        </div>
                    </fieldset>
                </form>
                                <style>
                                    li.user {
                                        margin-bottom: 10px;
                                    }
                                    table.user {
                                        border-top: 1px solid #e4e3e3;
                                        border-left: 1px solid #e4e3e3;
                                        width: 100%;
                                    }
                                    td.user {
                                        border-right: 1px solid #e4e3e3;
                                        border-bottom: 1px solid #e4e3e3;
                                        padding : 3px;
                                        word-wrap: break-word;
                                    }
                                    td.head {
                                        width: 100px;
                                    }
                                </style>
                                <div class="vlinkDiv">
                                <div class="whitebox">
                                        <h3>代理店</h3>
                                        <ul>
                                            <?php
                                            foreach ($agents as $agent) {
                                            ?>
                                                <li class="user"><table class="user">
                                                    <tr><td class="head user">リンク</td><td class="body user"><?php echo base_url('Agent')."/".$agent->user_hash; ?></td></tr>
                                                    <tr><td class="head user">名称</td><td class="body user"><?php if ('02'==$agent->type) { echo $agent->compamy_name_kana."<br>".$agent->compamy_name."<br>";} echo $agent->family_name_kana." ".$agent->first_name_kana."<br>".$agent->family_name." ".$agent->first_name; ?></td></tr>
                                                    <tr><td class="head user">メモ</td><td class="body user"><?php echo $agent->memo; ?></td></tr>
                                                    <tr><td class="head user">ステータス</td><td class="body user"><?php if(isset($statuses[$agent->status])) echo $statuses[$agent->status]; ?></td></tr>
                                                </table></li>
                                            <?php
                                            }
                                            ?>
                                        </ul>
                                </div>
                                </div>
                                <div class="vlinkDiv">
                                <div class="whitebox">
                                        <h3>交換者</h3>
                                        <ul>
                                            <?php
                                            foreach ($clients as $client) {
                                            ?>
                                                <li class="user"><table class="user">
                                                    <tr><td class="head user">リンク</td><td class="body user"><?php echo base_url('Client')."/".$client->user_hash; ?></td></tr>
													<tr><td class="head user">名称</td><td class="body user"><?php if ('02'==$client->type) { echo $client->compamy_name_kana."<br>".$client->compamy_name."<br>";} echo $client->family_name_kana." ".$client->first_name_kana."<br>".$client->family_name." ".$client->first_name; ?></td></tr>
                                                    <tr><td class="head user">メモ</td><td class="body user"><?php echo $client->memo; ?></td></tr>
                                                    <tr><td class="head user">ステータス</td><td class="body user"><?php if (isset($statuses[$client->status])) echo $statuses[$client->status]; ?></td></tr>
                                                </table></li>
                                            <?php
                                            }
                                            ?>
                                        </ul>
                                </div>
                                </div>
			</div><!-- /.l-page-home -->