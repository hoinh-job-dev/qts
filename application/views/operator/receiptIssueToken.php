<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

        <section class="l-contents cf">
        <div class="l-main js-height">
            <div class="l-page-">
                <h2><?php echo $title;?></h2>

                <!-- Begin form search -->
                <form action="<?php echo base_url('Operator/receiptIssueToken'); ?>" method="post" id="search" class="search search-bank" >
                   
                   <div class="row">
                        <label for="orderid">注文番号</label>
                        <input type="text" name="search[orderid]" id="orderid" value="<?php echo $search->orderid;?>">
                    </div>
                    <div class="row">
                        <label for="search_create_from">From日付</label>
                        <input type="text" name="search[create_from]" id="search_from" class="date_input date_popup" value="<?php echo $search->create_from;?>">
                    </div>
                    <div class="row">
                        <label for="search_create_to">TO日付</label>
                        <input type="text" name="search[create_to]" id="search_to" class="date_input date_popup" value="<?php echo $search->create_to;?>">
                    </div>

                    <div class="row">
                        <label for="search[purchaseid]">交換者UID</label>
                        <input type="text" name="search[purchaseid]" id="purchaseid" value="<?php echo $search->purchaseid;?>">
                    </div>
                    <div class="row ">
                        <label for="search[client_familyName]">交換者姓</label>
                        <input type="text" name="search[client_familyName]" id="client_familyName" value="<?php echo $search->client_familyName;?>">
                    </div>  
                    <div class="row ">
                        <label for="search[client_firstName]">交換者名</label>
                        <input type="text" name="search[client_firstName]" id="client_firstName" value="<?php echo $search->client_firstName;?>">
                    </div>

                    <div class="row">
                        <label for="search[agentid]">代理店UID</label>
                        <input type="text" name="search[agentid]" id="agentid" value="<?php echo $search->agentid;?>">
                    </div>
                    <div class="row">
                        <label for="search[agent_familyName]">代理店姓</label>
                        <input type="text" name="search[agent_familyName]" id="agent_familyName" value="<?php echo $search->agent_familyName;?>">
                    </div>
                    <div class="row">
                        <label for="search[agent_firstName]">代理店名</label>
                        <input type="text" name="search[agent_firstName]" id="agent_firstName" value="<?php echo $search->agent_firstName;?>">
                    </div>

                    <div class="row-order row-condition-search">

                        <span>並べ替え: </span>
                        <label><input type="radio" name="search[order_by]" value="order_number" <?php if ($search->order_by=='order_number') echo "checked"; ?>> 注文番号</label>
                        
                        <label><input type="radio" name="search[order_by]" value="client_uid" <?php if ($search->order_by=='client_uid') echo "checked"; ?>> 交換者UID</label>
                        <label><input type="radio" name="search[order_by]" value="agent_uid" <?php if ($search->order_by=='agent_uid') echo "checked"; ?>> 代理店UID</label>
                        <br>
                        <span>並び順: </span>
                        <label><input type="radio" name="search[order_opt]" value="ASC" <?php if ($search->order_opt=='ASC') echo "checked"; ?>> 昇順</label>
                        <label><input type="radio" name="search[order_opt]" value="DESC" <?php if ($search->order_opt=='DESC') echo "checked"; ?>> 降順</label>
                    </div>
                    <div class="row row-search">
                        <input type="submit" name ="btn-submit" value="Search" onclick="">     
                    </div>
                </form>
                <hr>
                <!-- End form search -->
                   
                <div class="whitebox" id="table">
                    <h3>受領書発行実績</h3>
                    <?php if ($this->CI->check_export_csv_permission()){ ?>
                    <ul id="button">
                        <!--<li><input type="button" id="update" value="CSV出力" onclick="var form=$('#search'); var formCsvExport=form.clone()[0]; formCsvExport.action='exportCsvReceipIssueToken';formCsvExport.submit();"></li>-->
                        <li><input type="button" id="update" value="CSV出力" onclick="exportCsvFromSearch('exportCsvReceipIssueToken');"></li>
                    </ul>
                    <?php } ?>
                    <div id="tableHead">
                        <table>
                            <tr>
                                <th class="uid">注文番号</th>
                                <th class="uid">交換者<br>UID</th>
                                <th class="name">交換者<br>名称</th>
                                <th class="uid">代理店<br>UID</th>
                                <th class="name">代理店<br>名称</th>
                                <th class="date">受領書<br>発行日</th>
                                <th class="uid">受領金額<br>(BTC)</th>
                                <th class="uid">USD/BTC<br>レート</th>
                                <th class="uid">QNT/USD<br>レート</th>
                                <th class="uid">トークン量<br>(QNT)</th>
                            </tr>
                        </table>
                    </div>
                    <div id="tableBody" onscroll="tableScroll();">
                        
                        <table>
                            <?php foreach ($orderList as $order_number=>$item) {
                                $data = $item['data'];
                                $amount ='';
                                if (isset($item['amount'])) $amount = $item['amount'] ;

                                $memo_31 = '';
                                if (isset($item['memo_31'])) $memo_31 = $item['memo_31'] ;
                                $exchange_rate_30 = '';
                                if (isset($item['exchange_rate_30'])) $exchange_rate_30 = $item['exchange_rate_30'] ;
                                $amount_24 = '';
                                if (isset($item['amount_24'])) $amount_24 = $item['amount_24'] ;
                             ?>
                               
                            <tr class="">

                                <td class="uid"><?php echo $data->order_number; ?></td>
                                <td class="uid"><?php echo $data->client_uid; ?></td>
                                <td class="name"><?php echo $data->client_familyname . " " . $data->client_firstname; ?></td>
                                <td class="uid"><?php echo $data->agent_uid; ?></td>
                                <td class="name"><?php echo $data->agent_familyname . " " . $data->agent_firstname; ?></td>
                                <td class="date"><?php echo datetime_2line_format($data->create_at); ?></td>
                                <td class="amount"><?php if(''!=$amount_24){echo money_format_qts($amount_24,8);} ?></td>
                                <td class="amount"><?php if(''!=$exchange_rate_30){ echo money_format_qts($exchange_rate_30,2);}  ?></td>
                                <td class="amount"><?php if(''!=$memo_31){echo $memo_31;} ?></td>
                                <td class="amount"><?php echo $amount; ?></td>

                            </tr>
                            <?php } ?>
                        </table>
                        
                    </div>
                <div style="clear: both;"></div>
            </div>
        </div>
<script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>