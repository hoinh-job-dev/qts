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

            <!-- Begin form search -->
            <form action="<?php echo base_url('Operator/viewCommissions'); ?>" method="post" id="search" class="search search-bank">
                <div class="row">
                    <label for="search_order_number">注文番号</label>
                    <input type="text" name="search_order_number" value="<?php echo $search_order_number;?>">
                </div>

                <div class="row">
                    <label for="search_user_id">ユーザID</label>
                    <input type="text" name="search_user_id" value="<?php echo $search_user_id;?>">
                </div>
                
                
                <div class="row">
                    <label for="search_pay_method">支払い方法</label>
                    <select name="search_pay_method">
                        <option value=""></option>
                    <?php
                        foreach ($pay_method as $method) {
                            $selected = '';
                            if($search_pay_method === $method->code) {
                                $selected = 'selected';
                            }                                                
                    ?>
                        <option value="<?php echo $method->code; ?>" <?php echo $selected;?>><?php echo $method->value; ?></option>
                    <?php
                        }
                    ?>
                    </select>
                </div>

                <div class="row row-order">
                    <span>並べ替え: </span>
                    <label><input type="radio" name="order_by" value="order_number"> 注文番号</label>
                    <label><input type="radio" name="order_by" value="uid"> ユーザID</label>                     
                    <label><input type="radio" name="order_by" value="pay_method"> 支払い方法</label>
                    <br>
                    <span>並び順: </span>
                    <label><input type="radio" name="order_opt" value="ASC"> 昇順</label>
                    <label><input type="radio" name="order_opt" value="DESC"> 降順</label>

                </div>

                <div class="row row-search">
                    <input type="submit" value="Search" onclick="">     
                </div>

                <input type="hidden" name="search" id="search" value="search">                         
            </form>
            <hr>
            <!-- End form search -->

            <form action="<?php echo base_url('Operator/confirmCommissionStatus'); ?>" method="post" id="update">
                <div class="whitebox">
                    <h3>コミッション審査対象</h3>
                    <?php
                    if(isset($statusMessage) && !empty($statusMessage)) {
                        ?>
                        <div style="color: red; "><?php echo $statusMessage; ?></div>
                        <?php
                    }
                    ?>
                    <ul id="button">
                        <li><input type="button" id="update" value="更新" onclick="return submitStatus();"></li>
                    </ul>
                    <div id="tableHead">
                        <table>
                            <tr>
                                <th style="width:37px !important;">全てチェック<br>
                                    <input type="checkbox" id="selectorCheckAll" class="selectorCheckAll" />
                                </th>
                                <th class="uid">注文番号</th>
                                <th class="uid">交換者<br>ID</th>
                                <th class="name">交換者<br>氏名</th>
                                <th class="uid">代理店<br>ID</th>
								<th class="name">代理店<br>氏名</th>
                                <th class="amount">コミッション金額(BTC)</th>
                                <th class="status" style="width: 27px !important;">状態</th>
                            </tr>
                        </table>
                    </div>
                    <div id="tableBody" onscroll="tableScroll();">
                        <?php $i = 0; ?>
                        <table>
                            <?php for ($i = 0; $i < sizeof($commission); $i++) { ?>
                                <tr>
                                    <td style="width:37px !important; text-align: center !important;">
                                        <input type="checkbox" name="commission_id[<?php echo $commission[$i]->commission_id; ?>]" value="<?php echo $commission[$i]->commission_id; ?>" class="selectorProcessThis" />
                                    </td>
                                    <td class="uid"><?php echo $commission[$i]->order_number; ?></td>
                                    <td class="uid"><?php echo $commission[$i]->client_uid ?></td>
                                    <td class="name"><?php echo $commission[$i]->client_family_name . " " . $commission[$i]->client_first_name ?></td>
                                    <td class="uid"><?php echo $commission[$i]->agent_uid ?></td>
                                    <td class="name"><?php echo $commission[$i]->agent_family_name . " " . $commission[$i]->agent_first_name ?></td>
                                    <td class="amount"><?php echo $commission[$i]->quantity; ?></td>
                                    <td class="status" style="width: 27px !important;"><?php echo (int) $commission[$i]->is_payed == 1 ? '済み' : '-'; ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                    <div class="pagingWrapper"><?php echo @$paginationLinks; ?></div>
                    <input type="hidden" name="paginationOffset" value="<?php echo $paginationOffset ?>" />
                </div>
            </form>
        </div>

        <script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>
        <script type="text/javascript">
           // Select order by and order option after search
           var order_opt = '<?php echo $order_opt;?>';
           var order_by = '<?php echo $order_by;?>';

           if(order_by) {            
               $('input[name="order_by"][value="' + order_by + '"]').attr('checked', 'checked');
           }

           if(order_opt) {            
               $('input[name="order_opt"][value="' + order_opt + '"]').attr('checked', 'checked');
           }
        </script>

        <script>
            $(document).on("change", ".selectorCheckAll", function(){
                $(this).closest("form").find(".selectorProcessThis").prop("checked", $(this).prop("checked"));
            }).on("change", ".selectorProcessThis", function(){
                if(!$(this).prop("checked")) {
                    $(this).closest("form").find(".selectorCheckAll").prop("checked", $(this).prop("checked"));
                }
            });
        </script>