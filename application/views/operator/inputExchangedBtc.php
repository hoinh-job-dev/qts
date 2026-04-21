<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <h2><?php echo $title; ?></h2>
            <form action="<?php echo base_url('Operator/confirmExchangedBtc'); ?>" method="post" id="update">
                <div class="whitebox">
                    <h3>JPY/BTC換金選択</h3>
                    <ul id="button">
                        <li><input type="button" id="update" value="更新" onclick="return submitStatus();"></li>
                    </ul>
                    <div id="tableHead">
                        <table>
                            <tr>
                                <th class="name">全てチェック<br>
                                    <input type="checkbox" id="selectorCheckAll" class="selectorCheckAll" />
                                </th>
                                <th class="uid">注文番号</th>
                                <th class="pay_method">支払い<br>方法</th>
                                <th class="uid">ユーザID</th>
                                <th class="name">氏名</th>
                                <th class="name">振込元名義</th>
								<th class="name">着金金額<br>(JPY)</th>
                                <th class="name">&nbsp;</th>
                                <!-- BTC送金の場合 -->
                                <?php /*?>
								<th class="amount">換金金額<br>(BTC)</th>
                                <th class="date">入金時刻</th>
                                <?php */?>
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
                                // check to ignore record that in auto banking (a part of auto wallet)
                                if(isset($ignore_orders) && !empty($ignore_orders) && in_array($order[$i]->order_number, $ignore_orders)) {
                                    continue;
                                }
                                $key = join($this->config->item('fields_separator'), array($order[$i]->order_number, $order[$i]->status));
                                //$is_invalid = (isset($order_validation) && isset($order_validation[$key]->is_valid) && $order_validation[$key]->is_valid == 0);
                                $is_invalid = $order[$i]->amount < $min_amount || $order[$i]->amount >=$max_amount;
                                if (isset($order_sum4month[$order[$i]->client_uid])){
                                    $is_invalid = $is_invalid || $order_sum4month[$order[$i]->client_uid]>=$monthly_amount;
                                }
                                $invalid_class = $is_invalid ? 'invalid_order_amount' : NULL;
                                $invalid_class = NULL;  // temporary hide the warning order
                                ?>
                                <tr class="<?php echo $invalid_class; ?>"><input type="hidden" name="order<?php echo $i; ?>" value="<?php echo $order[$i]->order_number; ?>">
                                <td class="name">
                                    <center><input type="checkbox" name="set_btc_address[<?php echo $order[$i]->order_number; ?>]" value="1" class="selectorSetBtcAddress" /></center>
                                </td>
                                <td class="uid"><?php echo $order[$i]->order_number; ?></td>
                                <td class="pay_method"><?php echo $order[$i]->pay_method; ?></td>
                                <td class="uid"><?php echo $order[$i]->uid; ?></td>
                                <td class="name"><?php echo $order[$i]->family_name . " " . $order[$i]->first_name; ?></td>
                                <td class="name"><?php echo $order[$i]->account_name; ?></td>
                                <td class="amount"><?php echo money_format_qts($order[$i]->amount,0); ?></td>
                                <td class="name">
                                    <center>
                                        <input type="button" name="delete_record[<?php echo $order[$i]->order_number; ?>]" value="取消し" class="selectorDeleteOrder btn-warning" data-order_number="<?php echo $order[$i]->order_number; ?>" />
                                    </center>
                                </td>
                                <!-- BTC送金の場合 -->
                                <?php /*?>
                                <td class="amount"><input type="text" name="amount<?php echo $i; ?>" placeholder="BTC"></td>
                                <td class="date"><input type="text" id="datetimepicker<?php echo $i; ?>" name="txtime<?php echo $i; ?>" placeholder="日時"></td>
                                <?php */?>
                                </tr>
                            <?php } ?>
                        </table>
                        <input type="hidden" name="rownum" value="<?php echo $i; ?>" id="rownum">
                    </div>
                </div>
            </form>
            <form action="<?php echo base_url('Operator/deleteJpyOrder'); ?>" method="post" id="deleteJpyOrder">
                <input type="hidden" name="delete_jpy_order" value="1" />
                <input type="hidden" name="order_number" value="" />
            </form>
        </div>

<script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>
<script>
    $(document).on("change", ".selectorCheckAll", function(){
        $(this).closest("form").find(".selectorSetBtcAddress").prop("checked", $(this).prop("checked"));
    }).on("change", ".selectorSetBtcAddress", function(){
        if(!$(this).prop("checked")) {
            $(this).closest("form").find(".selectorCheckAll").prop("checked", $(this).prop("checked"));
        }
    });

    $(document).on("click", ".selectorDeleteOrder", function() {
        var $this = $(this),
            order_number = $this.attr("data-order_number"),
            $form = $("#deleteJpyOrder");
        if($form.length > 0) {
            showConfirmationDialog("警告", "この注文は入金待ちに変更しますか？", function(ac){
                if(ac == 'YES') {
                    $form.find("input[name='order_number']").val(order_number);
                    $form.submit();
                }
            });
        }
    });
</script>

<?php 
if(isset($bankBtcData) && !empty($bankBtcData)) {
    ?>
    <div id="bankBtcDialog" title="Summary">
        
        <table style="width: 100%; ">
            <tr>
                <td style="width: 30%;">BTCアドレス:</td>
                <td><span style="font-weight: bolder !important;"><?php echo $bankBtcData->btc_address; ?></span></td>
            </tr>
            <tr>
                <td>JPY総金額:</td>
                <td><span style="font-weight: bolder !important;"><?php echo (float) $bankBtcData->total_jpy_amount; ?></span></td>
            </tr>
            <?php /*?>
            <tr>
                <td>BTC総金額:</td>
                <td><span style="font-weight: bolder !important;"><?php echo !empty($bankBtcData->total_btc_amount) ? (float) $bankBtcData->total_btc_amount : '-'; ?></span></td>
            </tr>
            <tr>
                <td>ステータス:</td>
                <td><span style="font-weight: bolder !important;"><?php echo $bankBtcData->complete == '1' ? '完了' : '未完了'; ?></span></td>
            </tr>
            <?php */?>
        </table>
        <br>

    </div>

    <script type="text/javascript">
        (function ($, undefined) {
            $(function () {
                "use strict";

                var dialog = ($.fn.dialog !== undefined),
                    tabs = ($.fn.tabs !== undefined),
                    $bankBtcDialog = $("#bankBtcDialog"),
                    $bankBtcTabs = $("#bankBtcTabs");

                if($bankBtcTabs.length > 0 && tabs) {
                    $bankBtcTabs.tabs();
                }

                if($bankBtcDialog.length > 0 && dialog) {

                    $bankBtcDialog.dialog({
                        autoOpen: false,
                        modal: true,
                        draggable: false,
                        resizable: false,
                        width: "60%",
                        buttons: {
                            'Close': function () {
                                $(this).dialog("close");
                            }
                        }
                    });

                    $bankBtcDialog.dialog("open");

                }
            });
        })(jQuery);
    </script>
    <?php
}
?>