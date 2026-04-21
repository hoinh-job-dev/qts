<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">
<style>
	th {
		background-color: #18bc9c;
		color: #fff;
	}

	.personal table {
        width: 100%;
    }
    .personal table th {
        width: 10px;
    }
    .personal th, td {
        vertical-align: top;
    }

    .personal {
        width: 400px;    
    }
    .document {
        width: 700px;    
    }
    .approve_status {
        width: 200px;
    }
</style>

        <section class="l-contents cf">
		<div class="l-main js-height">
			<div class="l-page-">
				<h2><?php echo $title;?></h2>

                <!-- Begin form search -->
				<!--
                <form action="<?php echo base_url('Operator/viewPersonal'); ?>" method="post" id="search" class="search">
                   <div class="row">
                        <label for="userid">ユーザID</label>
                        <input type="text" name="userid" id="userid" value="<?php echo $search_userid;?>">
                    </div>
                  
                   <div class="row">
                        <label for="role">ロール</label>
                        <select name="role" id="role">
                        <option value=""></option>
                        <?php
                            foreach ($roles as $role) {     
                                $selected = '';
                                if($search_role === $role->code) {
                                    $selected = 'selected';
                                }                                            
                        ?>
                            <option value="<?php echo $role->code; ?>" <?php echo $selected;?>><?php echo $role->value; ?></option>
                        <?php
                            }
                        ?>
                        </select>
                    </div>
                   
                   <div class="row">
                        <label for="type">種別</label>
                        <select name="type" id="type">
                            <option value=""></option>
                        <?php
                            foreach ($types as $type) {
                                $selected = '';
                                if($search_type === $type->code) {
                                    $selected = 'selected';
                                }                                                
                        ?>
                            <option value="<?php echo $type->code; ?>" <?php echo $selected;?>><?php echo $type->value; ?></option>
                        <?php
                            }
                        ?>
                        </select>
                    </div>
               
                    <div class="row">
                        <label for="search_email">Email</label>
                        <input type="text" name="email" id="email" value="<?php echo $search_email;?>">                                        
                    </div>


                   <input type="hidden" name="search" id="search" value="search">

                   <div class="row row-order">
                       <span>並べ替え: </span>
                       <label><input type="radio" name="order_by" value="personal_id"> ユーザID</label> 
                       <!-- <label><input type="radio" name="order_by" value="role"> ロール</label> - ->
                       <!-- <label><input type="radio" name="order_by" value="type"> 種別</label> - ->
                       <label><input type="radio" name="order_by" value="email"> Email</label>
                       <br>
                       <span>並び順: </span>
                       <label><input type="radio" name="order_opt" value="ASC"> 昇順</label>
                       <label><input type="radio" name="order_opt" value="DESC"> 降順</label>
                       
                   </div>

                    <div class="row row-search">
                        <input type="submit" name ="submit" value="Search" onclick="">     
                    </div>               
                                              
                </form>
                <hr>
				-->
                <!-- End form search -->




				<form action="<?php echo base_url('Operator/confirmPersonalStatus'); ?>" method="post" id="update">
                                    <div class="whitebox">
                                        <h3>審査対象 (代理店, 交換希望者)</h3>
                                            <ul id="button">
                                                <?php if ($this->CI->check_export_csv_permission()){ ?>
                                                <li><input type="button" id="update" value="CSV出力" onclick="var form=document.getElementById('update');form.action='outputCsv_personal';form.submit();"></li>
                                                <?php } ?>
                                                <li><input type="button" id="update" value="更新" onclick="return submitStatus();"></li>
                                            </ul>
                                        <div id="tableHead">
                                            <table>
                                                <tr>
                                                    <th class="personal">個人情報</th>
                                                    <th class="document">身分証明書</th>
                                                    <th class="approve_status">状態</th>
                                                </tr>
                                            </table>
                                        </div>
                                        <div id="tableBody" onscroll="tableScroll();">
                                            <?php
                                                $i=0;
                                                $amount_rules = $this->config->item('amount_rules');
                                                $min_amount = $amount_rules['min_amount'];
                                                $max_amount = $amount_rules['max_amount'];
                                            ?>
                                            <table>
                                                <?php for ($i=0; $i<sizeof($personals); $i++) { ?>
                                                <tr class="<?php if (""!=$personals[$i]['amount'] && ($personals[$i]['amount'] < $min_amount || $personals[$i]['amount'] >=$max_amount )) echo 'invalid_order_amount'; ?>" >
                                                    <td class="personal" style="padding-bottom:20px;"><table>
                                                                <tr><th>ID</th><td><input type="hidden" name="id<?php echo $i; ?>" value="<?php echo $personals[$i]['personal_id']; ?>"><?php echo $personals[$i]['personal_id']; ?></td></tr>
                                                                <tr><th>氏名</th><td><?php if ('法人'==$personals[$i]['type']) { echo $personals[$i]['company_name']."<br>"; } echo $personals[$i]['family_name']." ".$personals[$i]['first_name']; ?></td></tr>
                                                                <tr><th>生年月日</th><td><?php echo $personals[$i]['birthday']; ?></td></tr>
                                                                <tr><th>国籍</th><td><?php echo $personals[$i]['country']; ?></td></tr>
                                                                <tr><th>郵便番号</th><td><?php echo $personals[$i]['zip_code']; ?></td></tr>
                                                                <tr><th>都道府県</th><td><?php echo $personals[$i]['prefecture']; ?></td></tr>
                                                                <tr><th>市区町村</th><td><?php echo $personals[$i]['city']; ?></td></tr>
                                                                <tr><th>建物</th><td><?php echo $personals[$i]['building']; ?></td></tr>
                                                                <tr><th>Email</th><td><?php echo $personals[$i]['email']; ?></td></tr>
                                                                <tr><th>電話番号</th><td><?php echo $personals[$i]['tel']; ?></td></tr>
                                                                <tr><th>種別</th><td><?php echo $personals[$i]['type']; ?></td></tr>
                                                                <tr><th>ロール</th><td><?php echo $personals[$i]['role']; ?></td></tr>
                                                                <tr><th>交換方法</th><td><?php echo $personals[$i]['pay_method']; ?></td></tr>
                                                                <tr><th>交換希望額</th><td><?php if (""!=$personals[$i]['amount']) {echo number_format($personals[$i]['amount'],2); } ?></td></tr> 
                                                                <tr><th>登録日</th><td><?php echo $personals[$i]['create_at']; ?></td></tr>
                                                            </table></td>
                                                    <td class="document" style="padding-bottom:20px;">
                                                    <?php 
													$files = $personals[$i]['imgfile'];
													$filenumber = (0==sizeof($files))? 0 : sizeof(explode(',',$files));
													$filearray = '';
                                                    if (null!=$files || 0<$filenumber) {
														$filearray = explode(',',$files); ?>
                                                        <img src="<?php echo base_url_img('readimg/'.$filearray[0]); ?>" alt="本人と身分証明書が一緒に写っている写真"></img>
                                                    <?php } ?>
                                                    </td>
                                                    <td class="approve_status" style="padding-bottom:20px;"><select name="memo<?php echo $i; ?>">
                                                            <option value="<?php echo $this->config->item('approval_not_selected'); ?>">---</option>
															<option value="<?php echo $this->config->item('approval_approved'); ?>" <?php if ($this->config->item('approval_approved')==$personals[$i]['status']) { echo "selected"; } ?>>承認</option>
                                                            <option value="<?php echo $this->config->item('approval_pending'); ?>" <?php if ($this->config->item('approval_pending')==$personals[$i]['status']) { echo "selected"; } ?>>保留</option>
                                                            <option value="<?php echo $this->config->item('approval_lack'); ?>" <?php if ($this->config->item('approval_lack')==$personals[$i]['memo']) { echo "selected"; } ?>>[未承認] 未記入あり</option>
                                                            <option value="<?php echo $this->config->item('approval_imcompleted'); ?>" <?php if ($this->config->item('approval_imcompleted')==$personals[$i]['memo']) { echo "selected"; } ?>>[未承認] 入力不完全</option>
                                                            <option value="<?php echo $this->config->item('approval_unreadable'); ?>" <?php if ($this->config->item('approval_unreadable')==$personals[$i]['memo']) { echo "selected"; } ?>>[未承認] 判読不可</option>
                                                            <option value="<?php echo $this->config->item('approval_tooyoung'); ?>" <?php if ($this->config->item('approval_tooyoung')==$personals[$i]['memo']) { echo "selected"; } ?>>[未承認] 年齢制限</option>
                                                            <option value="<?php echo $this->config->item('approval_name_mistake'); ?>" <?php if ($this->config->item('approval_name_mistake')==$personals[$i]['memo']) { echo "selected"; } ?>>[未承認] 氏名不一致</option>
                                                            <option value="<?php echo $this->config->item('approval_birthday_mistake'); ?>" <?php if ($this->config->item('approval_birthday_mistake')==$personals[$i]['memo']) { echo "selected"; } ?>>[未承認] 生年月日不一致</option>
                                                            <option value="<?php echo $this->config->item('approval_address_mistake'); ?>" <?php if ($this->config->item('approval_address_mistake')==$personals[$i]['memo']) { echo "selected"; } ?>>[未承認] 住所不一致</option>
                                                            <option value="<?php echo $this->config->item('approval_invalid'); ?>" <?php if ($this->config->item('approval_invalid')==$personals[$i]['status']) { echo "selected"; } ?>>[未承認] 無効</option>
                                                            <option value="<?php echo $this->config->item('approval_expired'); ?>" <?php if ($this->config->item('approval_expired')==$personals[$i]['status']) { echo "selected"; } ?>>[未承認] 期限切れ</option>
                                                        </select>
                                                    <?php
													if (1<$filenumber) { ?>
														<br><br><img src="<?php echo base_url_img('readimg/'.$filearray[1]); ?>" alt="" style="width:100%;"></img>
                                                     <?php if (2<$filenumber) { ?>
                                                        <br><img src="<?php echo base_url_img('readimg/'.$filearray[2]); ?>" alt="" style="width:100%;"></img>
                                                     <?php if (3<$filenumber) { ?>
                                                         <br><img src="<?php echo base_url_img('readimg/'.$filearray[3]); ?>" alt="" style="width:100%;"></img>
                                                     <?php } } } ?>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                            <input type="hidden" name="rownum" value="<?php echo $i; ?>">
                                        </div>
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
