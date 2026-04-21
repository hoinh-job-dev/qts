<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <h2><?php echo $title; ?></h2>

            <?php 
            $refund_status_text = $this->config->item('refund_status_text');
            $refund_oper_status = $this->config->item('refund_oper_status');
            $refund_oper_status_text = $this->config->item('refund_oper_status_text');
            $refund_sent_status = $this->config->item('refund_sent_status');
            $refund_sent_status_text = $this->config->item('refund_sent_status_text');
            ?>

            <!-- Begin form search -->
            <form action="<?php echo base_url('Operator/viewRefunds'); ?>" method="post" id="search" class="search">
                <div class="row">
                    <label>手動の返金状態</label>
                    <select name="search[oper_status]">
                        <option value=""></option>
                        <?php 
                        foreach($refund_oper_status as $sKey => $sValue) {
                            ?><option value="<?php echo $sValue; ?>" <?php echo $sValue == @$_POST['search']['oper_status'] ? ' selected="selected"' : NULL; ?>><?php echo $sKey == 'unconfirm' ? '未済み' : @$refund_oper_status_text[$sValue]; ?></option><?php
                        }
                        ?>
                    </select>
                </div>

                <div class="row">
                    <label>Refundに送信</label>
                    <select name="search[sent_status]">
                        <option value=""></option>
                        <?php 
                        foreach($refund_sent_status as $sKey => $sValue) {
                            ?><option value="<?php echo $sValue; ?>" <?php echo $sValue == @$_POST['search']['sent_status'] ? ' selected="selected"' : NULL; ?>><?php echo @$refund_sent_status_text[$sValue]; ?></option><?php
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

            <form action="<?php echo base_url('Operator/confirmRefunds'); ?>" method="post" id="update">
                <div class="whitebox">
                    <h3>返金対象</h3>
                    <?php
                    if(isset($statusMessage) && !empty($statusMessage)) {
                        ?>
                        <div style="color: red; "><?php echo $statusMessage; ?></div>
                        <?php
                    }
                    ?>
                    <ul id="button">
                        <li>
                            <input type="submit" id="update" name="resend_refund" class="prevent-double-click" value="送信の再実行" onclick="submitRefunds('resend_refund')"/>
                            <input type="submit" id="update" name="update_refund" class="prevent-double-click" value="更新" onclick="submitRefunds('update_refund')"/>
                        </li>
                    </ul>
                    <div id="tableHead">
                        <table>
                            <tr>
                                <th class="uid">注文番号</th>
                                <th class="uid">交換者UID</th>
                                <th class="name">交換者名称</th>
                                <th class="uid">着金額（BTC)</th>
                                <th class="status">返金事由</th>
                                <th class="status">Refundに送信</th>
                                <th class="status">状態</th>
                            </tr>
                        </table>
                    </div>
                    <div id="tableBody" onscroll="tableScroll();">
                        <table>
                            <?php foreach(@$arrData as $dataRow) { ?>
                                <tr>
                                    <td class="uid"><?php echo $dataRow->order_number; ?></td>
                                    <td class="uid"><?php echo $dataRow->uid ?></td>
                                    <td class="name"><?php echo $dataRow->client_family_name . " " . $dataRow->client_first_name ?></td>
                                    <td class="amount"><?php echo money_format_qts( $dataRow->btc_amount,8); ?></td>
                                    <td class="status"><?php echo @$refund_status_text[$dataRow->status]; ?></td>
                                    <td class="status"><?php echo @$refund_sent_status_text[$dataRow->sent_status]; ?></td>
                                    <td class="status">
                                        <?php if($dataRow->sent_status == $refund_sent_status['success']) { ?>
                                        <select name="status[<?php echo $dataRow->id; ?>]">
                                            <?php 
                                            foreach($refund_oper_status as $sKey => $sValue) {
                                                ?><option value="<?php echo $sValue; ?>" <?php echo $sValue == $dataRow->oper_status ? ' selected="selected"' : NULL; ?>><?php echo @$refund_oper_status_text[$sValue]; ?></option><?php
                                            }
                                            ?>
                                        </select>
                                        <?php } else { echo '-'; } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </form>
        </div>

        <script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>
        <script>
            function submitRefunds(type){
                $('<input>').attr({'type':'hidden', 'name':type}).appendTo('form');
                $("#update").submit();
            }
        </script>