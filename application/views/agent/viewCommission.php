	<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">
    <section class="l-contents cf">
		<div class="l-main js-height">
			<div class="l-page-view-commission">
				<h2><?php echo $title;?></h2>
                                <!-- Begin form search -->
                <form action="<?php echo base_url('Agent/viewCommission'); ?>" method="post" id="search" class="search" style="width:98%; margin:10px;">
                    <fieldset>
                        <div class="bigrow row ">
                            <label for="agent_name">代理店名称</label>
                            <input type="text" name="search[agent_name]" value="<?php echo $search->agent_name; ?>" />
                        </div>

                        <div class="row bigrow">
                            <label for="name">交換者名称</label>
                            <input type="text" name="search[client_name]" value="<?php echo $search->client_name; ?>" />
                        </div>

                        <div class="row bigrow">
                            <label for="status">ステータス</label>
                            <select name="search[status]" id="status">
                                <option value="" <?php if ($search->status =="") echo "selected"?>>全て</option>
                                <option value='0' <?php if ($search->status =="0") echo "selected"?>>支払予定</option>
                                <option value='1' <?php if ($search->status =="1") echo "selected"?>>支払済み</option>
                            </select>
                        </div>
                        <div class="row-search row-search-small" style="padding-right:15px;">
                            <label for="search"></label>
                            <input type="submit" value="Search" onclick="">
                        </div>
                    </fieldset>
                </form>
<!-- end form search -->

                                <div class="whitebox">
                                        <h3>コミッション</h3>
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
                                        <table class="user agent-commission-table" >
                                            <tr><td class="head user">支払予定</td><td class="user"><?php echo $notpay; ?>BTC</tr>
                                            <tr><td class="head user">支払済み</td><td class="user"><?php echo $payed; ?>BTC</tr>
                                            <tr></tr>
                                        </table>
                                        <br>
                                        <ul>
                                            <?php
                                            //foreach ($history->result() as $topic) {
                                            foreach ($history as $topic) {
                                            ?>
                                                <li class="user">
                                                    <table class="user agent-commission-table">
                                                    <tr>
                                                        <td class="head user">注文番号</td>
                                                        <td class="body user"><?php echo $topic->order_number; ?></td>
                                                    </tr>
                                                    <!--$config['role_client'] = "03" --> 
                                                    <tr>
                                                        <td class="head user">交換者情報</td>
                                                       <td class="body user">
                                                            <?php 
                                                                if ('' != $topic->client_agent_uid && $topic->client_agent_uid != $user_hash)
                                                                    //echo 'DownLine' ;
                                                                    echo '-' ;
                                                                else
                                                                    echo $topic->client_family_name . " " . $topic->client_first_name;
                                                            ?>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                        <!-- <td class="head user">金額</td> -->
                                                        <td class="head user">送信BTC数</td>
                                                        <td class="body user" style="text-align:left;"><?php echo $topic->commission_amount; ?> BTC</td>
                                                    </tr>
                                                    <tr>
                                                        <!--<td class="head user">状態</td>-->
                                                        <td class="head user">ステータス</td>
                                                        <td class="body user">
                                                            <?php echo ($topic->is_payed == 0 ? '支払予定' : '支払済み'); ?>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="head user">代理店情報</td>
                                                        <td class="body user">
                                                            <?php
                                                                if ('' != $topic->agent_agent_uid && $topic->agent_agent_uid != $user_hash && $topic->client_agent_uid != $user_hash)
                                                                    echo 'DownLine';
                                                                else
                                                                    echo $topic->agent_family_name . " " . $topic->agent_first_name;
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="head user">BTCアドレス</td>
                                                        <td class="body user"><?php echo $topic->btc_address; ?></td>
                                                    </tr> 

                                                    <tr>
                                                        <td class="head user">支払い日時</td>
                                                        <td class="body user"><?php echo ($topic->is_payed == 1 && !empty($topic->update_at) ? $topic->update_at : NULL); ?></td>
                                                    </tr>
                                                    <tr>
                                                    </tr>
                                                    </table>
                                                </li>
                                            <?php
                                            }
                                            ?>
                                        </ul>
                                </div>
			</div><!-- /.l-page-home -->