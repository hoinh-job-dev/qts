<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <h2><?php echo $title; ?></h2>
            
                <div class="whitebox">
                    <h3>JPY/BTC換金結果照会</h3>
                    <div style="display: block; float: right; margin-bottom: 10px;">
                        <form method="POST">
                            <div style="display: inline-block; ">フィルター: </div>
                            <div style="display: inline-block; ">
                                <select name="filter_by_status" onchange="this.form.submit();">
                                    <option value=''>---全て---</option>
                                    <option value='0' <?php echo @$_REQUEST['filter_by_status'] == '0' ? 'selected="selected"' : NULL; ?>>未完了</option>
                                    <option value='1' <?php echo @$_REQUEST['filter_by_status'] == '1' ? 'selected="selected"' : NULL; ?>>完了</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div id="tableHead">
                        <table>
                            <tr>
                                <th style="width: 45% !important;">注文番号</th>
                                <th style="width: 15% !important;">JPY金額</th>
                                <th style="width: 15% !important;">BTC金額</th>
                                <th style="width: 15% !important;">換金レート</th>
                                <th style="width: 10% !important;">&nbsp;</th>
                            </tr>
                        </table>
                    </div>
                    <div id="tableBody" onscroll="tableScroll();">
                        <?php
                            $i = 0;
                            $amount_rules = $this->config->item('amount_rules');
                            $min_amount = $amount_rules['min_amount'];
                            $max_amount = $amount_rules['max_amount'];
                            $monthly_amount = $amount_rules['monthly_amount'];
                        ?>
                        <table>
                            <?php
                            foreach($bankBtcData as $row) {
                                foreach($row->orders as $order) {
                                    $is_invalid = $order->jpy_amount < $min_amount || $order->jpy_amount >=$max_amount;
                                    if (isset($order_sum4month[$order->client_uid])){
                                        $is_invalid = $is_invalid || $order_sum4month[$order->client_uid]>=$monthly_amount;
                                    }
                                    $invalid_class = $is_invalid ? 'invalid_order_amount' : NULL;
                                    ?>
                                    <tr class="child_<?php echo $row->btc_address; ?> <?php echo $invalid_class; ?>">
                                        <td style="width: 45% !important;"><?php echo $order->order_number; ?></td>
                                        <td style="width: 15% !important;"><?php echo (float) $order->jpy_amount; ?></td>
                                        <td style="width: 15% !important;"><?php echo !empty($order->btc_amount) ? money_format_qts((float) $order->btc_amount, 8) : '-'; ?></td>
                                        <td style="width: 15% !important;"><?php echo !empty($order->rate) ? $order->rate : '-'; ?></td>
                                        <td style="width: 10% !important;">&nbsp;</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr class="parent_<?php echo $row->btc_address; ?>" style="background-color: #cecece !important;">
                                    <td>BTCアドレス:<br><span style="font-weight: bolder !important;"><?php echo $row->btc_address; ?></span></td>
                                    <td>JPY総金額:<br><span style="font-weight: bolder !important;"><?php echo (float) $row->total_jpy_amount; ?></span></td>
                                    <td>BTC総金額:<br><span style="font-weight: bolder !important;"><?php echo !empty($row->total_btc_amount) ? money_format_qts((float) $row->total_btc_amount, 8) : '-'; ?></span></td>
                                    <td>換金レート:<br><span style="font-weight: bolder !important;"><?php echo !empty($row->rate) ? $row->rate : '-'; ?></span></td>
                                    <td>ステータス:<br><span style="font-weight: bolder !important;"><?php echo $row->complete == '1' ? '完了' : '未完了'; ?></span></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                    </div>
                </div>
            
        </div>