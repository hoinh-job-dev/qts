<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">
        <section class="l-contents cf">
		<div class="l-main js-height">
			<div class="l-page-">
				<h2><?php echo $title;?></h2>

                <!-- Begin form search -->
                <form action="<?php echo base_url('Operator/issueRedeemToken'); ?>" method="post" id="search" class="search">

                    <input type="hidden" name="listOrderNumber" id="listOrderNumber" value="">
                    <div class="group-line">
                        <div class="row">
                            <label for="orderid">注文番号</label>
                            <input type="text" name="search[orderid]" id="orderid" value="<?php echo $search->orderid;?>">
                        </div>
                        <div class="row">
                            <label for="search[purchaseid]">交換者ID</label>
                            <input type="text" name="search[purchaseid]" id="purchaseid" value="<?php echo $search->purchaseid;?>">
                        </div>
                        <div class="row">
                            <label for="search[client_name]">交換者名称</label>
                            <input type="text" name="search[client_name]" id="client_name" value="<?php echo $search->client_name;?>">
                        </div>
                    </div>    
                    
                    <!--line 2--><!--<div class="row" style="height:70px;">&nbsp;</div>-->
                    <div class="group-line">
                        <div class="row">
                            <label for="search_create_from">BTC着金日FROM</label>
                            <input type="text" name="search[create_from]" id="search_from" class="date_input date_popup" value="<?php echo $search->create_from;?>">
                        </div>
                        <div class="row">
                            <label for="search_create_to">BTC着金日TO</label>
                            <input type="text" name="search[create_to]" id="search_to" class="date_input date_popup" value="<?php echo $search->create_to;?>">
                        </div>
                        <div class="row">
                            <label for="search_update_from">変更日FROM</label>
                            <input type="text" name="search[update_from]" id="update_from" class="date_input date_popup" value="<?php echo $search->update_from;?>">
                        </div>
                        <div class="row">
                            <label for="search_update_to">変更日TO</label>
                            <input type="text" name="search[update_to]" id="update_to" class="date_input date_popup" value="<?php echo $search->update_to;?>">
                        </div>
                    </div>  

                    <!--line 3-->
                    <div class="group-line">
                        <div class="row">
                            <label for="is_sent">メールの送信状態</label>
                            <select name="search[is_sent]">                    
                                <option value=""></option>
                                <?php foreach ($is_sent_x as $key=>$value) { ?>
                                   <option value="<?php echo $key;?>"<?php if ($key == $search->is_sent) { echo "selected"; } ?>><?php echo $value; ?>
                                   </option>
                                <?php }?> 
                            </select>
                        </div>
                        <div class="row">
                            <label for="is_payed">リディーム状態</label>
                            <select name="search[is_payed]">
                                <option value=""></option>
                                <?php foreach ($is_payed_x as $key=>$value) { ?>
                                   <option value="<?php echo $key;?>"
                                    <?php if ($key == $search->is_payed) { 
                                        echo "selected"; } ?>><?php echo $value; ?>
                                   </option>
                                <?php }?> 
                            </select>
                        </div>
                    </div> 

                    <!--Option search-->
                    <div class="row row-order" style="margin-top:30px">
                        <span>並べ替え:</span><!--並べ替え--><!---->
                        <label><input type="radio" name="search[order_by]" value="order_number" <?php if ($search->order_by=='order_number') echo "checked"; ?>> 注文番号</label><!--注文番号-->
                        <label><input type="radio" name="search[order_by]" value="client_uid" <?php if ($search->order_by=='client_uid') echo "checked"; ?>> 交換者UID</label><!--交換者UID-->
                        <br>
                        <span>並び順:</span><!--並び順-->
                        <label><input type="radio" name="search[order_opt]" value="ASC" <?php if ($search->order_opt=='ASC') echo "checked"; ?>> 昇順</label><!--昇順-->
                        <label><input type="radio" name="search[order_opt]" value="DESC" <?php if ($search->order_opt=='DESC') echo "checked"; ?>>降順</label><!--降順-->
                    </div>
                        
                    <div class="row row-search">
                        <input type="submit" name ="btnSubmit" value="Search" onclick="">
                    </div>
                </form>
                <hr>
                <!-- End form search -->

                <!-- View result search -->
                <form action="<?php echo base_url('Operator/issueRedeemToken'); ?>" method="post" id="update">
                <div class="whitebox">
                    <h3>還元の関係情報一覧</h3>                    
                    <?php if (isset($status)) { if('success' == $status){ ?>
                        <div class ="whitebox" style="color: blue;">
                            <?php echo "リディームリンクを発行しました。"; ?>
                        </div>
                    <?php }else{ ?>
                        <div class ="whitebox" style="color: red;">
                            <?php if('No-item-selected' == $status){
                                echo "データ選択がないので、還元コードの変更を行いません。";
                            }else if('fail' == $status){
                                echo "還元コードの変更が失敗しました。";
                            } ?>
                        </div>
                    <?php }}?>
                    <ul id="button">
                        <?php if ($this->CI->check_export_csv_permission()){ ?>
                            <input type="button" id="update" value="CSV出力" onclick="exportCsvFromSearch('exportCsvIssueRedeemToken');"/>
                        <?php } ?>
                        <li><input type="button" id="update" value="Submit" onclick="editTokenSubmit();"></li>
                    </ul>                   
                    <div id="tableHead">
                        <table>
                            <tr>
                                <th style="width:37px !important;">全チェック<br>
                                    <input type="checkbox" id="selectorCheckAll" class="selectorCheckAll" />
                                </th>
                                <th class="uid">注文番号</th>
                                <th class="uid">交換者ID</th>
                                <th class="name">交換者名称</th>
                                <th class="name">メールの送信状態</th>
                                <th class="name">リディーム状態</th>
                                <th class="date">BTC着金日</th>
                                <th class="date">変更日</th>
                            </tr>
                        </table>
                    </div>
                    <div id="tableBody" onscroll="tableScroll();">
                        
                        <table>
                            <?php for ($i = 0; $i < sizeof($tokens); $i++) { ?>
                                <tr">                                    
                                    <td style="width:37px !important; text-align: center !important;">
                                        <?php if(1 != $tokens[$i]->is_payed && (NULL == $tokens[$i]->is_sent || 0 == $tokens[$i]->is_sent)){ ?>
                                            <input type="checkbox" name="token_id[<?php echo $tokens[$i]->token_id; ?>]" value="<?php echo $tokens[$i]->order_number; ?>" class="selectorProcessThis" />
                                        <?php }else{ echo '';}?>
                                    </td>                                     
                                    <td class="uid"><?php echo $tokens[$i]->order_number; ?></td>
                                    <td class="uid"><?php echo $tokens[$i]->client_uid; ?></td>
                                    <td class="name"><?php echo $tokens[$i]->client_familyname . " " . $tokens[$i]->client_firstname; ?></td>
                                    <td class="uid"><!--column send status-->
                                        <?php if(NULL !== $tokens[$i]->is_sent && (int)$tokens[$i]->is_sent > 0 ){
                                            echo $is_sent_x[$tokens[$i]->is_sent];
                                        }else{
                                            echo '';
                                        }?>         
                                        </td>
                                    <td class="uid"><!--column is_pay -->
                                        <?php if(NULL !== $tokens[$i]->is_payed && (int)$tokens[$i]->is_payed > 0 ){
                                            echo $is_payed_x[$tokens[$i]->is_payed];
                                        }else{
                                            echo '';
                                        }?>          
                                        </td>
                                    <td class="date"><?php echo $tokens[$i]->create_at; ?></td>
                                    <td class="date"><?php echo $tokens[$i]->update_at; ?></td>                                
                                </tr>
                            <?php } ?>
                        </table> 
                        
                    </div>
                </div>  
                </form>                  
            </div>

<script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>
<script type="text/javascript">
    // Select order by and order option after search
   var order_opt = '<?php echo $search->order_opt;?>';
   var order_by = '<?php echo $search->order_by;?>';

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