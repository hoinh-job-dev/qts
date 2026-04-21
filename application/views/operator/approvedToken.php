<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <h2><?php echo $title; ?></h2>

            <?php 
            $sent_statuses = array(
                '0'      => '失敗',
                '1'  => '成功'
            );
            ?>

            <!-- Begin form search -->
            <form action="<?php echo base_url('Operator/viewApprovedToken'); ?>" method="post" id="search" class="search">

                <div class="row">
                    <label for="search_create_from">日付From</label>
                    <input type="text" name="search[search_from]" id="search_from" class="date_input date_popup" value="<?php echo @$search->search_from;?>">
                </div>

                <div class="row">
                    <label for="search_create_to">日付To</label>
                    <input type="text" name="search[search_to]" id="search_to" class="date_input date_popup" value="<?php echo @$search->search_to;?>">
                </div>

                <div class="row">
                    <label for="order_number">注文番号</label>
                    <input type="text" name="search[order_number]" id="order_number" value="<?php echo @$search->order_number;?>">
                </div>

                <div style="clear: both;"></div>

                <div class="row">
                    <label>Hot and Cold送金状態</label>
                    <select name="search[hot_cold_sent_status]">
                        <option value=""></option>
                        <?php 
                        foreach($sent_statuses as $sKey => $sValue) {
                            ?><option value="<?php echo $sKey; ?>" <?php echo $sKey."" == @$search->hot_cold_sent_status ? ' selected="selected"' : NULL; ?>><?php echo $sValue; ?></option><?php
                        }
                        ?>
                    </select>
                </div>

                <div class="row">
                    <label>Commission送金状態</label>
                    <select name="search[commission_sent_status]">
                        <option value=""></option>
                        <?php 
                        foreach($sent_statuses as $sKey => $sValue) {
                            ?><option value="<?php echo $sKey; ?>" <?php echo $sKey."" == @$search->commission_sent_status ? ' selected="selected"' : NULL; ?>><?php echo $sValue; ?></option><?php
                        }
                        ?>
                    </select>
                </div>

                <div class="row">
                    <label style="height: 5px; display: block;">&nbsp;</label>
                    <input type="submit" value="Search" onclick="">     
                </div>
            </form>
            <hr>
            <!-- End form search -->

            <form action="<?php echo base_url('Operator/confirmApprovedToken'); ?>" method="post" id="update">
                <div class="whitebox">
                    <h3>BTC送信対象</h3>
                    <?php
                    if(isset($statusMessage) && !empty($statusMessage)) {
                        ?>
                        <div style="color: red; "><?php echo $statusMessage; ?></div>
                        <?php
                    }
                    ?>
                    <ul id="button">
                        <li><input type="button" id="update" value="Resend" onclick="return submitStatus();"></li>
                    </ul>
                    <div id="tableHead">
                        <table >
                            <tr>
                                <th class="uid"></th>
                                <th class="uid"></th>
                                <th class="uid"></th>
                                <th class="uid"></th>
                                <th class="uid"></th>
                                <th class="uid"></th>
                                <th class="uid"></th>
                                <th class="uid"></th>
                                <th class="uid"></th>
                                <th class="uid"></th>
                            </tr>
                            <tr>  
                                <th class="uid" rowspan="2">Date Time</th>
                                <th class="uid" rowspan="2">Count Orders</th>
                                <th class="uid" rowspan="2">BTC金額</th>
                                 
                                <th class="textLeft" colspan="3" >For Operation Company</th>
                                <th class="textLeft" colspan="2" >For Agents</th>
                                <th class="textLeft" colspan="2" >For Revenue Share</th> 

                            </tr>
                            <tr>
                                <th class="textLeft">Hot Wallet<br>送金額（BTC)</th>
                                <th class="textLeft">Cold Wallet<br>送金額（BTC)</th>
                                <th class="textLeft">Hot and Cold<br>Sent Status</th>
                            
                                <th class="textLeft">Commission<br>送金額（BTC)</th>
                                <th class="textLeft">Commission<br>Sent Status</th>

                                <th class="textLeft">Revenue<br>送金額（BTC)</th>
                                <th class="textLeft">Revenue<br>Sent Status</th>

                            </tr>
                        </table>
                    </div>
                    <div id="tableBody" onscroll="tableScroll();">
                        <table>
                            <?php foreach(@$arrData as $dataRow) { ?>
                                <tr>
                                    <td class="uid">
                                        <?php if (isset($dataRow->closed_date) && !empty($dataRow->closed_date)) { ?>
                                            <a href="#" onclick="showCommissionDetail(<?php echo $dataRow->closed_date; ?>)" style="text-decoration: none;color: blue;">
                                                <?php echo datetime_2line_format($dataRow->closed_date); ?>
                                            </a>
                                        <?php } else { echo '-'; } ?>

                                    </td>
                                    <td class="uid"><?php echo @$dataRow->count_orders; ?></td>
                                    <td class="amount"><?php echo money_format_qts( @$dataRow->total_btc_amount,8); ?></td>
                                    <td class="amount"><?php echo money_format_qts( @$dataRow->total_hot_wallet_btc_amount,8); ?></td>
                                    <td class="amount"><?php echo money_format_qts( @$dataRow->total_cold_wallet_btc_amount,8); ?></td>
                                    <td class="uid"><?php echo @$dataRow->hot_cold_sent_status == '1' ? 'success' : 'failed'; ?></td>
                                    <td class="amount"><?php echo money_format_qts( @$dataRow->total_commission_btc_amount,8); ?></td>
                                    <td class="uid"><?php echo @$dataRow->commission_sent_status == '1' ? 'success' : 'failed'; ?></td>
                                    <td class="amount"><?php echo money_format_qts( @$dataRow->total_special_commission_btc_amount,8); ?></td>
                                    <td class="uid"><?php echo (float) @$dataRow->total_special_commission_btc_amount <= 0 ? '-' : (@$dataRow->special_commission_sent_status == '1' ? 'success' : 'failed'); ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </form>
        </div>
        <div id="dialog" title="BTCの送信詳細">
            <p>This is an animated dialog </p>
        </div>
        <script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>
        <script type="text/javascript">
            $( function() {
                $( "#dialog" ).dialog({
                    autoOpen: false,
                    modal: true,
                    show: {
                        effect: "blind",
                        duration: 1000
                    },
                    width: 1050,
                    maxHeight: 500,
                });

            } );
            function showCommissionDetail(id) {
                var getdataurl = "<?php echo base_url('/Operator/getCommissionData'); ?>/" + id;
                $.ajax({
                    url: getdataurl,
                    context: document.body
                }).done(function(data) {
                    $( "#dialog" ).html(data);
                    $( "#dialog" ).dialog( "open" );
                });

            };
        </script>