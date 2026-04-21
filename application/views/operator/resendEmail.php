<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <h2><?php echo $title; ?></h2>

            <!-- Begin form search -->
            <form action="<?php echo base_url('Operator/resendEmail'); ?>" method="post" id="search" class="search">

                <input type="hidden" name="listEmailId" id="listEmailId" value="">
                <div class="row">
                    <label for="id">メールID</label>
                    <input type="text" name="search[id]" value="<?php echo $search->id;?>">
                </div>
                <div class="row">
                    <label for="to">送信先</label>
                    <input type="text" name="search[to]" value="<?php echo $search->to;?>">
                </div> 
                <div class="row">
                    <label for="memo">メールの通知</label>
                    <select name="search[memo]">                    
                        <option value=""></option>
                        <?php foreach ($memo_x as $key=>$value) { ?>
                           <option value="<?php echo $key;?>"<?php if ($key == $search->memo) { echo "selected"; } ?>><?php echo $value; ?>
                           </option>
                        <?php }?> 
                    </select>
                </div>
                <div class="row">
                    <label for="object">対象</label>
                    <input type="text" name="search[object]" value="<?php echo $search->object;?>">
                </div>
                <div style="width:100%; float:left;"></div>                
                <div class="row">
                    <label for="is_sent">状態</label>                    
                    <select name="search[is_sent]">
                        <option value=""></option>
                        <?php foreach ($is_sent_x as $key=>$value) { ?>
                            <option value="<?php echo $key;?>"<?php if ((string)$key===$search->is_sent) { echo "selected"; } ?>><?php echo $value; ?>
                            </option>
                        <?php }?>
                    </select>
                </div> 
                <div class="row">
                    <label for="date_from">作成日時 from</label>
                    <input type="text" name="search[date_from]" id="from" value="<?php echo $search->date_from;?>" class="date_popup">                    
                </div>
                <div class="row">
                    <label for="date_to">作成日時 to</label>
                    <input type="text" name="search[date_to]" id="date_to" value="<?php echo $search->date_to;?>" class="date_popup">
                </div> 

                <div class="row row-search">
                    <input type="submit" value="Search" onclick="">
                </div>                         
            </form>
            <hr>
            <!-- End form search -->

            <form action="<?php echo base_url('Operator/resendEmail'); ?>" method="post" id="update">
                <div class="whitebox">
                    <h3>メールの再送信</h3>  

                    <?php if (isset($email_status)) { if('success' == $email_status){ ?>
                        <div class ="whitebox" style="color: blue;">
                            <?php echo "選択のメールが正常に送信しました"; ?>
                        </div>
                    <?php }else{ ?>
                        <div class ="whitebox" style="color: red;">
                            <?php echo "選択のメールは送信失敗がありますので、再確認してお願いします"; ?>
                        </div>
                    <?php }}?>       

                    <ul id="button">
                        <li><input type="button" id="update" value="メールの送信" onclick="return submitSendEmail()"></li>
                    </ul>
                    <div id="tableHead">
                        <table>
                            <tr>
                                <th style="width:50px !important;">全てチェック<br>
                                    <input type="checkbox" id="selectorCheckAll" class="selectorCheckAll" /> </th>   
                                <th class="uid">メールID</th>
                                <th class="uid">送信先</th>
                                <th class="uid">メールのヘッダ</th>
                                <th class="uid">メールの通知</th>
                                <th class="uid">対象</th>
                                <th class="uid">状態</th>
                                <th class="uid">作成日時</th>                                
                            </tr>
                        </table>
                    </div>
                    <div id="tableBody" onscroll="tableScroll();">
                        <?php $i = 0; ?>
                        <table> 
                            <?php foreach($arrEmail as $row)  { ?>
                                <?php $i = $row->id?>
                                <tr>
                                    <td style="width:50px !important; text-align: center !important;">
                                        <input type="checkbox" name="email_id[<?php echo $arrEmail[$i]->id; ?>]" value="<?php echo $arrEmail[$i]->id ?>" class="selectorProcessThis" />
                                    </td>
                                    <td class="uid"><?php echo $arrEmail[$i]->id; ?></td>
                                    <td class="uid"><?php echo $arrEmail[$i]->to; ?></td>
                                    <td class="uid"><?php echo $arrEmail[$i]->subject ?></td>
                                    <td class="uid">
                                    <?php 
                                        if(array_key_exists($arrEmail[$i]->memo, $memo_x)){
                                           echo $memo_x[$arrEmail[$i]->memo];
                                        }else{ 
                                            //Ignore compare subject email with case memo_4.
                                            if(strpos($arrEmail[$i]->memo,$this->config->item('memo_4')) !== false ){
                                                echo $memo_x[$this->config->item('memo_4')];
                                            }else{
                                                //none mapping memo follow config.
                                                echo $arrEmail[$i]->memo;
                                            }
                                        } 
                                    ?>
                                    </td>
                                    <td class="uid"><?php echo $arrEmail[$i]->object ?></td>
                                    <td class="uid"><?php echo $is_sent_x[$arrEmail[$i]->is_sent] ?></td>
                                    <td class="uid"><?php echo $arrEmail[$i]->create_at ?></td>                   
                                </tr>
                            <?php } ?> 
                        </table>
                    </div>
                </div>
            </form>
        </div>

        <script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>  
        <script>
            $(document).on("change", ".selectorCheckAll", function(){
                $(this).closest("form").find(".selectorProcessThis").prop("checked", $(this).prop("checked"));
            }).on("change", ".selectorProcessThis", function(){
                if(!$(this).prop("checked")) {
                    $(this).closest("form").find(".selectorCheckAll").prop("checked", $(this).prop("checked"));
                }
            });
        </script>