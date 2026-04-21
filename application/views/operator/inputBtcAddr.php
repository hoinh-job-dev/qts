<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <h2><?php echo $title; ?></h2>
            <form action="<?php echo base_url('Operator/confirmBtcAddr'); ?>" method="post" id="update">
                <div class="whitebox">
                    <h3>請求用ビットコインアドレス</h3>
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
                                <!-- BTC送金の場合 -->
                                <th class="addr">ビットコインアドレス</th>
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
                            <?php for ($i = 0; $i < sizeof($order); $i++) { ?>
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
									<input type="hidden" name="uid<?php echo $i; ?>" value="<?php echo $order[$i]->uid ?>">
                                <td class="uid"><?php echo $order[$i]->order_number; ?></td>
                                <td class="pay_method"><?php echo $order[$i]->pay_method; ?></td>
                                <td class="uid"><?php echo $order[$i]->uid; ?></td>
                                <td class="name"><?php echo $order[$i]->family_name . " " . $order[$i]->first_name; ?></td>
                                <td class="amount"><?php echo number_format($order[$i]->amount,2);?></td>   
                                <!-- BTC送金の場合 -->
                                <td class="addr"><input type="text" id="btc_address<?php echo $i; ?>" name="btc_address<?php echo $i; ?>" onchange="isValid_btcAddress(this);"></td>
                                </tr>
                            <?php } ?>
                        </table>
                        <input type="hidden" name="rownum" value="<?php echo $i; ?>" id="rownum">
                    </div>
                </div>
            </form>
        </div>

<script type="text/javascript" src="<?php echo base_url('js/wallet-address-validator.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/agent/setBtcAddr.js'); ?>"></script>
