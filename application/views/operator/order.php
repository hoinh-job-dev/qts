<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">
        <section class="l-contents cf">
		<div class="l-main js-height">
			<div class="l-page-">
				<h2><?php echo $title;?></h2>

                <!-- Begin form search -->
                <form action="<?php echo base_url('Operator/orderlist'); ?>" method="post" id="search" class="search">
                   <div class="row">
                        <label for="orderid">注文番号</label>
                        <input type="text" name="search[orderid]" id="orderid" value="<?php echo $search->orderid;?>">
                    </div>

                    <div class="row">
                        <label for="status">状態</label>
                        <select name="search[status]" id="status">
                        <option value=""></option>
                        <?php
                            foreach ($statuses as $status) {     
                                $selected = '';
                                if($search->status === $status->code) {
                                    $selected = 'selected';
                                }                                            
                        ?>
                            <option value="<?php echo $status->code; ?>" <?php echo $selected;?>><?php echo $status->value; ?></option>
                        <?php
                            }
                        ?>
                        </select>
                    </div>


                    <div class="row">
                        <label for="type">支払方法</label>
                        <select name="search[type]" id="type">
                            <option value=""></option>
                            <?php
                            foreach ($types as $type) {
                                $selected = '';
                                if($search->type === $type->code) {
                                    $selected = 'selected';
                                }
                                ?>
                                <option value="<?php echo $type->code; ?>" <?php echo $selected;?>><?php echo $type->value; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="row">
                        <label for="search[account_name]">振込元名義</label>
                        <input type="text" name="search[account_name]" id="account_name" value="<?php echo $search->account_name;?>">
                    </div>
                    <div class="row">
                        <label for="search[agentid]">代理店UID</label>
                        <input type="text" name="search[agentid]" id="agentid" value="<?php echo $search->agentid;?>">
                    </div>
                    <div class="row">
                        <label for="search[agent_name]">代理店名前</label>
                        <input type="text" name="search[agent_name]" id="agent_name" value="<?php echo $search->agent_name;?>">
                    </div>

                    <div class="row">
                        <label for="search[purchaseid]">交換者UID</label>
                        <input type="text" name="search[purchaseid]" id="purchaseid" value="<?php echo $search->purchaseid;?>">
                    </div>
                    <div class="row">
                        <label for="search[client_name]">交換者名前</label>
                        <input type="text" name="search[client_name]" id="client_name" value="<?php echo $search->client_name;?>">
                    </div>

                    <div class="row">
                        <label for="search_create_from">FROM日付</label>
                        <input type="text" name="search[create_from]" id="search_from" class="date_input date_popup" value="<?php echo $search->create_from;?>">
                    </div>
                    <div class="row">
                        <label for="search_create_to">TO日付</label>
                        <input type="text" name="search[create_to]" id="search_to" class="date_input date_popup" value="<?php echo $search->create_to;?>">
                    </div>

                    <div class="row row-order" style="margin-top:30px">
                        <span>並べ替え: </span>
                        <label><input type="radio" name="search[order_by]" value="order_number" <?php if ($search->order_by=='order_number') echo "checked"; ?>> 注文番号</label>
                        <label><input type="radio" name="search[order_by]" value="status" <?php if ($search->order_by=='status') echo "checked"; ?>> 状態</label>
                        <label><input type="radio" name="search[order_by]" value="agent_uid" <?php if ($search->order_by=='agent_uid') echo "checked"; ?>> 代理店UID</label>
                        <label><input type="radio" name="search[order_by]" value="client_uid" <?php if ($search->order_by=='client_uid') echo "checked"; ?>> 交換者UID</label>
                        <br>
                        <span>並び順: </span>
                        <label><input type="radio" name="search[order_opt]" value="ASC" <?php if ($search->order_opt=='ASC') echo "checked"; ?>> 昇順</label>
                        <label><input type="radio" name="search[order_opt]" value="DESC" <?php if ($search->order_opt=='DESC') echo "checked"; ?>> 降順</label>

                    </div>
                    <!--
                    <div class="row row-order clear">
                        <label><input type="checkbox" name="search[apply]" <?php if (true == $search->apply) { echo" checked"; } ?>>上位代理店</label>
                    </div>
                    -->
                    <div class="row row-search">
                        <input type="submit" name ="btnsubmit" value="Search" onclick="">
                    </div>
                </form>
                <hr>
                <!-- End form search -->

                <form action="<?php echo base_url('Operator/confirmOrder'); ?>" method="post" id="update">
                    <div class="whitebox">
                        <h3>注文実績</h3>
                        <ul id="button">
                            <?php if ($this->CI->check_export_csv_permission()){ ?>
                                <!--<li><input type="button" id="update" value="CSV出力" onclick="var form=$('#search');var fcsv=form.clone()[0]; fcsv.action='outputCsv_order';fcsv.submit();"></li>-->
                                <li><input type="button" id="update" value="CSV出力" onclick="exportCsvFromSearch('outputCsv_order');"></li>
                            <?php } ?>
                            <li><input type="button" id="update" value="更新" onclick="return submitStatus();"></li>
                        </ul>
                        <div id="tableHead">
                            <table>
                                <tr>
                                    <th class="uid" style="background-color:#18bc9c;color:#fff;">新規<br>追加</th>
                                    <th class="paymethod" style="background-color:#18bc9c;color:#fff;">支払い<br>方法</th>
                                    <th class="uid">注文<br>番号</th>
                                    <th class="uid">状態</th>
                                    <th class="uid">交換者<br>UID</th>
                                    <th class="name">交換者<br>名前</th>
                                    <th class="uid">通貨<br>単位</th>
                                    <th class="amount" style="text-align:center;">注文<br>金額</th>
                                    <th class="uid">換金<br>レート</th>
                                    <th class="name">振込元<br>名義</th>
                                    <th class="addr">請求用<br>BTC<br>アドレス</th>
                                    <th class="date">有効<br>期限</th>
                                    <th class="uid">代理店<br>UID</th>
                                    <th class="name">代理店<br>名前</th>
                                    <th class="date">日時</th>
                                    <?php if (true == $search->apply) { ?>
                                    <th class="date">状態</th>
                                    <?php } ?>
                                </tr>
                            </table>
                        </div>
                        <div id="tableBody" onscroll="tableScroll();">
                            <?php
                                $int = 0;
                                $total_amount=array(); $count= 0;
                            ?>
                            <table>
                                <?php for ($i = 0; $i < sizeof($orders); $i++) { ?>
                                    <tr"><input type="hidden" name="order_number<?php echo $int; ?>" value="<?php echo $orders[$i]->order_number; ?>">
                                        <input type="hidden" name="client_uid<?php echo $int; ?>" value="<?php echo $orders[$i]->client_uid; ?>">
                                        <td class="uid" style="background-color:#18bc9c;color:#fff;"><?php echo $orders[$i]->activity_code; ?></td>
                                        <td class="paymethod" style="background-color:#18bc9c;color:#fff;text-align:center;"><?php echo $orders[$i]->pay_method; ?></td>
                                        <td class="uid"><?php echo $orders[$i]->order_number; ?></td>
                                        <td class="uid"><?php echo $orders[$i]->action; ?></td>
                                        <td class="uid"><?php echo $orders[$i]->client_uid; ?></td>
                                        <td class="name"><?php echo $orders[$i]->client_familyname . " " . $orders[$i]->client_firstname; ?></td>
                                        <td class="uid"><?php echo $orders[$i]->currency_unit; ?></td>
                                        <td class="amount"><?php echo $orders[$i]->amount; ?></td>
                                        <td class="uid"><?php echo $orders[$i]->exchange_rate; ?></td>
                                        <td class="name"><?php echo $orders[$i]->account_name; ?></td>
                                        <td class="addr"><?php echo $orders[$i]->receive_address; ?></td>
                                        <td class="date"><?php echo $orders[$i]->expiration_date; ?></td>
                                        <td class="uid"><?php echo $orders[$i]->agent_uid; ?></td>
                                        <td class="name"><?php echo $orders[$i]->agent_familyname . " " . $orders[$i]->agent_firstname; ?></td>
                                        <td class="date"><?php echo $orders[$i]->create_at; ?></td>
                                        <?php if (true == $search->apply) { ?>
                                        <td class="status"><?php if ("追加注文" == $orders[$i]->activity_code && "銀行" == $orders[$i]->pay_method) { ?><select name="approvalstatus<?php echo $int; $int++; ?>">
                                                    <option value="00">---</option>
                                                    <option value="01">金額承認済</option>
                                                    <option value="<?php echo $this->config->item('approval_invalid'); ?>">無効</option>
                                                </select><?php } else { echo "-"; } ?></td>
                                        <?php }
                                        $count = $count +1;
                                        if (!empty($total_amount[$orders[$i]->currency_unit]))
                                            $total_amount[$orders[$i]->currency_unit] += $orders[$i]->amount;
                                        else
                                            $total_amount[$orders[$i]->currency_unit] = $orders[$i]->amount;
                                        ?>
                                    </tr>
                                <?php } ?>
                            </table>
                            <input type="hidden" name="rownum" value="<?php echo $int; ?>" id="rownum">
                        </div>
                        <div>
                            <?php echo "$count results, total "; foreach ($total_amount as $key => $value) { echo "$value $key ";}?>
                        </div>
                </form>
                        </div>
                </div>


<script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>
<script type="text/javascript">
    // Select order by and order option after search
   var order_opt = '<?php echo $search->order_opt;?>';
   var order_by = '<?php echo $search->order_by;?>';

   if(order_by) {
       $('input[name="order_by"][value="' + order_by + '"]').attr('checked', 'checked');
   }

   if(order_opt) {
       $('input[name="order_opt"][value="' + order_opt + '"]').attr('checked', 'checked');
   }
</script>