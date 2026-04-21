<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">
        <section class="l-contents cf">
        <div class="l-main js-height">
            <div class="l-page-">
                <h2><?php echo $title;?></h2>

                <!-- Begin form search -->
                <form action="<?php echo base_url('Operator/inputBanking'); ?>" method="post" id="search" class="search search-bank" >
                    <div class="row">
                        <label for="search_order_number">注文番号</label>
                        <input type="text" name="search[order_number]" value="<?php echo $search->order_number;?>">
                    </div>

                    <div class="row">
                        <label for="search_user_id">ユーザID</label>
                        <input type="text" name="search[user_id]" value="<?php echo $search->user_id;?>">
                    </div>
                    
                    
                    <div class="row">
                        <label for="search_bank_name">銀行名称</label>
                        <input type="text" name="search[bank_name]" id="search_bank_name" value="<?php echo $search->bank_name;?>">
                    </div>
                    <div class="row">
                        <label for="search_create_from">FROM日付</label>
                        <input type="text" name="search[create_from]" id="search_from" class="date_input date_popup" value="<?php echo $search->create_from;?>">
                    </div>
                    <div class="row">
                        <label for="search_create_to">TO日付</label>
                        <input type="text" name="search[create_to]" id="search_to" class="date_input date_popup" value="<?php echo $search->create_to;?>">
                    </div>
                    <div class="row row-order" style="padding-top:15px">
                       <span>並べ替え: </span>
                       <label><input type="radio" name="search[order_by]" value="order_number" <?php if ($search->order_by=='order_number') echo "checked"; ?>> 注文番号</label>
                       <label><input type="radio" name="search[order_by]" value="uid" <?php if ($search->order_by=='uid') echo "checked"; ?>> ユーザID</label>
                       <label><input type="radio" name="search[order_by]" value="account_name" <?php if ($search->order_by=='account_name') echo "checked"; ?>> 振込元名義</label>
                       <br>
                       <span>並び順: </span>
                       <label><input type="radio" name="search[order_opt]" value="ASC" <?php if ($search->order_opt=='ASC') echo "checked"; ?>> 昇順</label>
                       <label><input type="radio" name="search[order_opt]" value="DESC" <?php if ($search->order_opt=='DESC') echo "checked"; ?>> 降順</label>
                       
                   </div>

                    <div class="row row-search">
                        <input type="submit" value="Search" onclick="">     
                    </div>
                </form>
                <hr>
                <!-- End form search -->
                <form action="<?php echo base_url('Operator/confirmBanking'); ?>" method="post" id="update" class="clear view-banking-list">
                                    <div class="whitebox">
                                        <h3>入金待ち (銀行振込, BTC送金)</h3>
                                        <ul id="button">
                                            <li><input type="button" id="update" value="更新" onclick="return submitStatus();"></li>
                                        </ul>
                                        <div id="tableHead">
                                            <table>
                                                <tr>
                                                    <th class="uid">注文番号</th>
                                                    <th class="pay_method">支払い方法</th>
                                                    <th class="uid">ユーザID</th>
                                                    <th class="name">氏名</th>
                                                    <th class="name">申込み金額<br>(USD)</th>
                                                    <th class="date">請求書発行日</th>
                                                    <!-- 銀行振込の場合 -->
                                                    <th class="name">振込元名義</th>
                                                    <th class="name">振込金額<br>(JPY)</th>
                                                    <th class="date">入金時刻</th>
                                                    <th class="status">状態</th>
                                                </tr>
                                            </table>
                                        </div>
                                        <div id="tableBody" onscroll="tableScroll();">
                                            <?php
                                                $i=0;
                                                $amount_rules = $this->config->item('amount_rules');
                                                $min_amount = $amount_rules['min_amount'];
                                                $max_amount = $amount_rules['max_amount'];
                                                $monthly_amount = $amount_rules['monthly_amount'];
                                            ?>
                                            <table>
                                                <?php for ($i=0; $i<sizeof($order); $i++) { ?>
                                                <?php
                                                $key = join($this->config->item('fields_separator'), array($order[$i]->order_number, $order[$i]->status));
                                                //$is_invalid = (isset($order_validation) && isset($order_validation[$key]->is_valid) && $order_validation[$key]->is_valid == 0);
                                                $is_invalid = $order[$i]->amount < $min_amount || $order[$i]->amount >=$max_amount;
                                                if (isset($order_sum4month[$order[$i]->client_uid])){
                                                    $is_invalid = $is_invalid || $order_sum4month[$order[$i]->client_uid]>=$monthly_amount;
                                                }
                                                $invalid_class = $is_invalid ? 'invalid_order_amount' : NULL;
                                                ?>
                                                <tr class="<?php echo $invalid_class; ?>"><input type="hidden" name="order<?php echo $i; ?>" value="<?php echo $order[$i]->order_number; ?>">
                                                    <td class="uid"><?php echo $order[$i]->order_number; ?></td>
                                                    <td class="pay_method"><?php echo $order[$i]->pay_method; ?></td>
                                                    <td class="uid"><?php echo $order[$i]->uid; ?></td>
                                                    <td class="name"><?php echo $order[$i]->family_name." ".$order[$i]->first_name; ?></td>
                                                    <td class="amount">
                                                        <?php if('BTC'==$order[$i]->pay_method){
                                                            echo money_format_qts($order[$i]->amount,8);
                                                        }else{
                                                            echo number_format($order[$i]->amount,2);
                                                        } ?>
                                                    </td>
                                                    <td class="date"><?php echo $order[$i]->create_at ?> </div>
                                                    <!-- 銀行振込の場合 -->
                                                    <td class="name"><?php if ('BTC'==$order[$i]->pay_method) { echo '<center>---</center>'; } else { echo '<input type="text" name="account_name'.$i.'" placeholder="振込元名義">'; } ?></td>
                                                    <td class="amount">
                                                        <?php 
                                                        if('BTC'==$order[$i]->pay_method) {
                                                            ?>
                                                            <input type="hidden" name="amount<?php echo $i; ?>" placeholder="BTC">
                                                            <center>---</center>
                                                            <?php
                                                        }
                                                        else {
                                                            ?><input type="text" name="amount<?php echo $i; ?>" placeholder="JPY"><?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="date">
                                                        <center>---</center>
                                                    </td>
                                                    <td class="status">
                                                        <?php
                                                        if('BTC'==$order[$i]->pay_method) {
                                                            ?>
                                                            <input type="hidden" name="status<?php echo $i; ?>" >
                                                            <center>---</center>
                                                            <?php
                                                        }
                                                        else {
                                                            ?>
                                                            <select name="status<?php echo $i; ?>">
                                                                <option value="00">---</option>
                                                                <option value="<?php echo $this->config->item("order_receiveby_bank"); ?>">銀行着</option>
                                                                <option value="<?php echo $this->config->item('order_invalid'); ?>">無効</option>
                                                            </select>
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                            <input type="hidden" name="rownum" value="<?php echo $i; ?>" id="rownum">
                                       </div>
                                    </div>
                                </form>
                        </div>

<script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>
<script>
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
