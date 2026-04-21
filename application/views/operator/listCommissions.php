    <link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <h2><?php echo $title; ?></h2>
            <form action="<?php echo base_url('Operator/listCommissions'); ?>" method="post" id="search" class="search search-bank" >
                <div class="row">
                    <label for="search_order_number">注文番号</label>
                    <input type="text" name="search[order_number]" value="<?php echo $search->order_number;?>">
                </div>
                <div class="row">
                    <label for="search_create_from">FROM日付</label>
                    <input type="text" name="search[create_from]" id="create_from" class="date_input date_popup" value="<?php echo $search->create_from;?>">
                </div>
                <div class="row">
                    <label for="search_create_to">TO日付</label>
                    <input type="text" name="search[create_to]" id="create_to" class="date_input date_popup" value="<?php echo $search->create_to;?>">
                </div>

                <div class="row">
                    <label for="pay_method">支払方法</label>
                    <select name="search[pay_method]" id="pay_method">
                        <option value=""></option>
                        <option value="01" <?php if ($search->pay_method === '01') echo "selected" ?> >銀行振込</option>
                        <option value="02" <?php if ($search->pay_method === '02') echo "selected" ?>>BTC送金</option>
                    </select>
                </div>
                <div class="row">
                    <label for="search[agent_uid]">代理店UID</label>
                    <input type="text" name="search[agent_uid]" id="agent_uid" value="<?php echo $search->agent_uid;?>">
                </div>

                <div class="row">
                    <label for="agent_role">代理店ロール</label>
                    <select name="search[agent_role]" id="agent_role">
                        <option value=""></option>
                        <?php
                        foreach ($roles as $code => $value) {
                            $selected = '';
                            if($search->agent_role === $code) {
                                $selected = 'selected';
                            }
                            ?>
                            <option value="<?php echo $code; ?>" <?php echo $selected;?>><?php echo $value; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>

                <div class="row row-order">
                    <span>並べ替え: </span>
                    <label><input type="radio" name="search[order_by]" value="order_number" <?php if ($search->order_by=='order_number') echo "checked"; ?>> 注文番号</label>
                    <label><input type="radio" name="search[order_by]" value="update_at" <?php if ($search->order_by=='update_at') echo "checked"; ?>> 支払日</label>
                    <label><input type="radio" name="search[order_by]" value="agent_uid" <?php if ($search->order_by=='agent_uid') echo "checked"; ?>> エージェントUID</label>
                    <br>
                    <span>並び順: </span>
                    <label><input type="radio" name="search[order_opt]" value="ASC" <?php if ($search->order_opt=='ASC') echo "checked"; ?>> 昇順</label>
                    <label><input type="radio" name="search[order_opt]" value="DESC" <?php if ($search->order_opt=='DESC') echo "checked"; ?>> 降順</label>

                </div>

                <div class="row row-search">
                    <input type="submit" value="Search" onclick="">
                </div>
            </form>
            <hr/>
                <div class="whitebox" id="commission-sumary">

                </div>
                <div class="whitebox">
                    <h3>コミッション実績</h3>
                    <div id="tableHead">
                        <table>
                            <tr>
                                <th class="uid">注文番号</th>
                                <th class="create_at">支払い日</th>
                                <th class="pay_method">支払い方法</th>
                                <th class="uid">交換者<br>ID</th>
                                <th class="name">交換者<br>氏名</th>
                                <th class="uid">代理店<br>ID</th>
								<th class="name">代理店<br>氏名</th>
                                <th class="role">代理店<br>ロール</th>
                                <th class="btc_address">BTCアドレス</th>
                                <th class="uid">コミッション金額<br>(BTC)</th>
                            </tr>
                        </table>
                    </div>
                    <div id="tableBody" onscroll="tableScroll();">
                        <?php $i = 0; $total_amount =0;?>
                        <table>
                            <?php for ($i = 0; $i < sizeof($commissions); $i++) { $total_amount += $commissions[$i]->quantity ;?>
                                <tr>
                                    <td class="uid"><?php echo $commissions[$i]->order_number; ?></td>
                                    <td class="date"><?php echo datetime_2line_format($commissions[$i]->update_at); ?></td>
                                    <td class="pay_method"><?php if ($commissions[$i]->pay_method) echo $pay_methods[$commissions[$i]->pay_method]; ?></td>
                                    <td class="uid"><?php echo $commissions[$i]->client_uid ?></td>
                                    <td class="name"><?php echo $commissions[$i]->client_family_name . " " . $commissions[$i]->client_first_name ?></td>
                                    <td class="uid"><?php echo $commissions[$i]->agent_uid ?></td>
                                    <td class="name"><?php echo $commissions[$i]->agent_family_name . " " . $commissions[$i]->agent_first_name ?></td>
                                    <td class="role"><?php echo $roles[$commissions[$i]->role]; ?></td>
                                    <td class="btc_address"><?php echo $commissions[$i]->btc_address; ?></td>
                                    <td class="amount"><?php echo $commissions[$i]->quantity; ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                        <div id="commission-sumary-info" style="display: none;" >
                            件数 ： <?php echo $i ?><br/> コミッション金額 ： <?php echo $total_amount; ?>
                        </div>
                    </div>
                </div>
        </div>

        <script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>
        <script>
            $("#commission-sumary").html($("#commission-sumary-info").html());
        </script>