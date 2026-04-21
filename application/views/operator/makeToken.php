<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <h2><?php echo $title; ?></h2>
            <form action="<?php echo base_url('Operator/confirmToken'); ?>" method="post" id="update">
                <div class="whitebox">
                    <h3>受領書発行対象</h3>
                    <ul id="button">
                        <li><input type="button" id="update" value="発行" onclick="return submitSignal();"></li>
                    </ul>
                    <div id="tableHead">
                        <table>
                            <tr>
                                <th class="uid">注文番号</th>
                                <th class="pay_method">支払い<br>方法</th>
                                <th class="uid">ユーザID</th>
                                <th class="name">氏名</th>
                                <th class="uid">着金金額<br>(BTC)</th>
                                <th class="date">着金時刻</th>
                                <th class="uid">USD/BTC</th>
                                <th class="uid">QNT/USD</th>
                                <th class="uid">トークン量<br>(QNT)</th>
                                <th class="addr">ステータス</th>
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
                            $dif_percentage = $amount_rules['diff_percent_check'];
                        ?>
                        <table>
							<?php for ($i = 0; $i < sizeof($order); $i++) { ?>
                                <?php
                                $key = join($this->config->item('fields_separator'), array($order[$i]->order_number, $order[$i]->btc_status));
                                //$is_invalid = (isset($order_validation) && isset($order_validation[$key]->is_valid) && $order_validation[$key]->is_valid == 0);
                                $is_invalid = $order[$i]->usd_amount < $min_amount || $order[$i]->usd_amount >=$max_amount;
                                if (isset($order_sum4month[$order[$i]->client_uid])){
                                    $is_invalid = $is_invalid || $order_sum4month[$order[$i]->client_uid]>=$monthly_amount;
                                }
                                if ( $order[$i]->diffRate > $dif_percentage) {
                                    $is_invalid = true;
                                }

                                $invalid_class = $is_invalid ? 'invalid_order_amount' : NULL;

                                ?>
                                <tr class="<?php echo $invalid_class; ?>"><input type="hidden" name="order<?php echo $i; ?>" value="<?php echo $order[$i]->order_number; ?>">
				    <input type="hidden" name="uid<?php echo $i; ?>" value="<?php echo $order[$i]->uid ?>">
                                <td class="uid"><?php echo $order[$i]->order_number; ?></td>
                                <td class="pay_method"><?php echo $order[$i]->pay_method_name; ?></td>
                                <td class="uid"><?php echo $order[$i]->uid; ?></td>
                                <td class="name"><?php echo $order[$i]->family_name . " " . $order[$i]->first_name; ?></td>
                                <td class="amount"><?php echo $order[$i]->amount ?></td>
                                <td class="date"><?php echo datetime_2line_format($order[$i]->create_at); ?></td>
                                <td class="amount"><?php echo money_format_qts($order[$i]->rate,2); ?></td> 
                                <td class="uid"><?php echo $order[$i]->usdqnt; ?></td>
                                <td class="amount"><?php echo $order[$i]->quantity; ?></td>
                                <td class="addr"><select name="status<?php echo $i; ?>">
                                                            <option value="00">---</option>
                                                            <option value="<?php echo $this->config->item('order_receiveby_btc'); ?>">発行</option>
                                                            <?php
                                                            if($order[$i]->pay_method == $this->config->item('payby_btc')) {
                                                                ?><option value="<?php echo $this->config->item('order_invalid'); ?>">無効</option><?php
                                                            }
                                                            ?>
                                                        </select></td>
                                </tr>
                            <?php } ?>
                        </table>
                        <input type="hidden" name="rownum" value="<?php echo $i; ?>" id="rownum">
                    </div>
                </div>
            </form>
        </div>

<script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/agent/setBtcAddr.js'); ?>"></script>
<script type="text/javascript">
function submitSignal() {
    $("input[type=button]").attr("disabled","disabled");
    document.getElementById("update").submit(); // 送信
}
</script>