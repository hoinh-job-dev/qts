<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <h2><?php echo $title; ?></h2>
            <form action="<?php echo base_url('Operator/reOrderConfirm'); ?>" method="post" id="update">
                <div class="whitebox">
                    <h3>注文審査対象</h3>
                    <ul id="button">
                        <li><input type="submit" id="update" value="更新" class="prevent-double-click" onclick="this.form.submit()"></li>
                    </ul>
                    <div id="tableHead">
                        <table>
                            <tr>
                                <th class="type">区分</th>
                                <th class="pay_method">支払い<br>方法</th>
                                <th class="ordernumber">注文番号</th>
                                <th class="uid clientuid">交換者UID</th>
                                <th class="name clientname">交換者名称</th>
                                <th class="uid">注文金額<br>(USD)</th>
                                <!-- Dissable by task 1686
                                <th class="date expireddate">有効期限</th>
                                -->
                                <th class="uid agentuid">代理店UID</th>
                                <th class="name agentname">代理店名称</th>
                                <th class="date createat">注文日時</th>
                                <th class="uid">累計額<br>(USD)</th>
                                <th class="uid">請求金額<br>(USD)</th>
                                <th class="status">状態</th>
                            </tr>
                        </table>
                    </div>
                    <div id="tableBody" onscroll="tableScroll();">
                        <table>
                        <?php
                            $amount_rules = $this->config->item('amount_rules');
                            $min_amount = $amount_rules['min_amount'];
                            $max_amount = $amount_rules['max_amount'];
                            $monthly_amount = $amount_rules['monthly_amount'];
                            foreach ($orders as $order){
                                $is_invalid = $order->amount < $min_amount || $order->amount >=$max_amount;
                                if (isset($order_sum4month[$order->client_uid])){
                                    $is_invalid = $is_invalid || $order_sum4month[$order->client_uid]>=$monthly_amount;
                                }
                                $invalid_class = $is_invalid ? 'invalid_order_amount' : NULL;
                          ?>
                          <tr class="<?php echo $invalid_class; ?>">
                              <input type="hidden" name="" />
                              <td style="text-align:center;"><?php if ($first_orders[$order->client_uid] != $order->order_number) echo ' 再注文'; ?></td>
                              <td class="uid"><?php echo $order->pay_method ?> </td>
                              <td class="uid"><?php echo $order->order_number ?> </td>
                              <td class="uid"><?php echo $order->uid ?> </td>
                              <td><?php echo $order->family_name . " " . $order->first_name ?> </td>
                              <td class="amount"><?php echo money_format("%.2n",$order->amount) ?> </td>
                              <td class="uid"><?php echo $order->agent_uid ?> </td>
                              <td><?php echo $order->agent_family_name . " " . $order->agent_first_name ?> </td>

                              <td class="date"><?php echo datetime_2line_format($order->create_at) ?> </td>
                              <td><?php if (isset($order_sum4month[$order->client_uid])) echo money_format("%.2n",$order_sum4month[$order->client_uid]) ?> </td>
                              <td><input name="updates[<?php echo $order->order_number ?>][amount]" type="number" value="" style="width:100%; height:100%"  /></td>
                              <td>
                                  <select name="updates[<?php echo $order->order_number ?>][status]">
                                      <option value=""></option>
                                      <option value="update">承認</option>
                                      <option value="cancel">無効</option>
                                  </select>
                              </td>
                          </tr>
                         <?php
                        } ?>
                        </table>
                    </div>
                </div>
            </form>
        </div>