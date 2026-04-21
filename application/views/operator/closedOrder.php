<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

<style type="text/css">
    div#tableBody {
        height: auto !important;
    }
</style>

<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <h2><?php echo $title; ?></h2>

            <form action="<?php echo base_url('Operator/confirmClosedOrder'); ?>" method="post" id="update">
                <div class="whitebox">
                    <h3>締め処理</h3>
                    <?php
                    if(isset($statusMessage) && !empty($statusMessage)) {
                        ?>
                        <div style="color: red; "><?php echo $statusMessage; ?></div>
                        <?php
                    }
                    ?>
                    <ul id="button">
                        <li><input type="button" id="update" value="実行" class="selectorSubmitCloseOrder"></li>
                    </ul>
                    <div id="tableHead">
                        <table>
                            <tr>
                                <th class="uid">注文番号</th>
                                <th class="uid">交換者UID</th>
                                <th class="uid">交換者名</th>
                                <th class="uid">代理店UID</th>
                                <th class="uid">代理店名</th>
                                <?php /* ?>
                                <th class="uid">Client</th>
                                <?php */ ?>
                                <th class="uid">BTC金額</th>
                            </tr>
                        </table>
                    </div>
                    <div id="tableBody" onscroll="tableScroll();">
                        <table>
                            <?php $totalBtcAmount = 0; ?>
                            <?php foreach(@$arrData as $dataRow) { ?>
                                <?php $totalBtcAmount += $dataRow->btc_amount; ?>
                                <tr>
                                    <td class="uid">
                                        <?php if (isset($dataRow->order_number) && !empty($dataRow->order_number)) { ?>
                                            <a href="#" onclick="showCommissionDetailByOrderNumber(<?php echo $dataRow->order_number; ?>)" style="text-decoration: none;color: blue;">
                                                <?php echo $dataRow->order_number; ?>
                                            </a>
                                        <?php } else { echo '-'; } ?>

                                        <input type="hidden" name="order_number[]" value="<?php echo @$dataRow->order_number; ?>" />
                                        
                                    </td>
                                    <td class="uid">
                                        <?php echo @$dataRow->client_uid; ?>
                                    </td>
                                    <td class="uid">
                                        <?php echo @$dataRow->client_family_name . " " . @$dataRow->client_first_name; ?>
                                    </td>
                                    <td class="uid">
                                        <?php echo @$dataRow->agent_uid; ?>
                                    </td>
                                    <td class="uid"><?php echo @$dataRow->agent_family_name . " " . @$dataRow->agent_first_name; ?></td>
                                    <?php /* ?>
                                    <td class="uid"><?php echo @$dataRow->client_family_name . " " . @$dataRow->client_first_name; ?></td>
                                    <?php */ ?>
                                    <td class="amount"><?php echo money_format_qts( @$dataRow->btc_amount,8); ?></td>
                                </tr>
                            <?php } ?>
                            <?php 
                            if($totalBtcAmount > 0) {
                                ?>
                                <tr>
                                    <td class="amount" colspan="5" style="font-weight: bolder;">BTC総金額: </td>
                                    <td class="amount" style="font-weight: bolder;"><?php echo money_format_qts( $totalBtcAmount,8); ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                    </div>
                    <div class="pagingWrapper"><?php echo @$paginationLinks; ?></div>
                    <input type="hidden" name="paginationOffset" value="<?php echo $paginationOffset ?>" />
                </div>
            </form>
        </div>

        <script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>

        <div id="closedOrderDialog" title="締め処理">
        
            <p>締め処理実行しますか？</p>

        </div>

        <script type="text/javascript">
            (function ($, undefined) {
                $(function () {
                    "use strict";

                    var dialog = ($.fn.dialog !== undefined),
                        $closedOrderDialog = $("#closedOrderDialog");

                    if($closedOrderDialog.length > 0 && dialog) {

                        $closedOrderDialog.dialog({
                            autoOpen: false,
                            modal: true,
                            draggable: false,
                            resizable: false,
                            width: "300px",
                            buttons: {
                                'はい': function () {
                                    $(this).dialog("close");
                                    $("input[type=button]").attr("disabled","disabled");
                                    submitStatus();
                                },
                                'いいえ': function () {
                                    $(this).dialog("close");
                                }
                            }
                        });

                        $(document).on("click", ".selectorSubmitCloseOrder", function(){
                            $closedOrderDialog.dialog("open");
                        });

                    }
                });
            })(jQuery);
        </script>

        <div id="dialog" title="注文のコミッション詳細">
            <p>This is an animated dialog </p>
        </div>
        <script type="text/javascript">
            $( function() {
                $( "#dialog" ).dialog({
                    autoOpen: false,
                    modal: true,
                    show: {
                        effect: "blind",
                        duration: 1000
                    },
                    width: 1200,
                    height: 320,
                    maxHeight: 500,
                });

            } );
            function showCommissionDetailByOrderNumber(order_number) {
                var getdataurl = "<?php echo base_url('/Operator/getCommissionDetailShowPopUp'); ?>/" + order_number;
                $.ajax({
                    url: getdataurl,
                    context: document.body
                }).done(function(data) {
                    $( "#dialog" ).html(data);
                    $( "#dialog" ).dialog( "open" );
                });

            };
        </script>