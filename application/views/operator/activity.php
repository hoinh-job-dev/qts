<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">
<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <h2><?php echo $title; ?></h2>
                <!-- Begin form search -->
                <form action="<?php echo base_url('Operator/activitylist'); ?>" method="post" id="search" class="search search-bank">
                    <div class="row">
                        <label for="search_id">アクティビティID</label>
                        <input type="text" name="search_id" value="<?php echo $search_id;?>">
                    </div>

                    <div class="row">
                        <label for="search_user_id">ユーザID</label>
                        <input type="text" name="search_user_id" value="<?php echo $search_user_id;?>">
                    </div>

                    <div class="row">
                        <label for="search_active_code">アクティビティ</label>
                        <select name="search_active_code">
                            <option value=""></option>
                        <?php
                            foreach ($active_codes as $active) {
                                $selected = '';
                                if($search_active_code === $active->code) {
                                    $selected = 'selected';
                                }                                                
                        ?>
                            <option value="<?php echo $active->code; ?>" <?php echo $selected;?>><?php echo $active->value; ?></option>
                        <?php
                            }
                        ?>
                        </select>
                    </div>
                    
                    <div class="row clear">
                        <label for="search_from">日時 from</label>
                        <input type="text" name="search_from" id="search_from" value="<?php echo $search_from;?>" class="date_popup">

                        
                    </div>

                    <div class="row">
                        <label for="search_to">日時 to</label>
                        <input type="text" name="search_to" id="search_to" value="<?php echo $search_to;?>" class="date_popup">
                    </div>
                    
                    <div class="row-condition-search clear">
                        <span>並べ替え: </span>
                        <label><input type="radio" name="order_by" value="activity_id"> アクティビティID</label> 
                        <label><input type="radio" name="order_by" value="uid"> ユーザID</label> 
                        <label><input type="radio" name="order_by" value="activity_code"> アクティビティ</label>
                        <label><input type="radio" name="order_by" value="create_at"> 日時</label>
                        <br>
                        <span>並び順: </span>
                        <label><input type="radio" name="order_opt" value="ASC"> 昇順</label>
                        <label><input type="radio" name="order_opt" value="DESC"> 降順</label>
                    </div>

                    <div class="row row-search clear">
                        <input type="submit" value="Search" onclick="">     
                    </div>

                    <input type="hidden" name="search" id="search" value="search">                         
                </form>
                <hr>
                <!-- End form search -->

            <div class="whitebox">
                <h3>アクティビティ</h3>
                <form action="<?php echo base_url('Operator/confirmActivitylist'); ?>" method="post" id="outputCsv"> 
                    <ul id="button">  
                         <li><input type="button" id="update" class="prevent-double-click" value="更新" onclick="this.form.submit();"></li>
                    </ul> 
                    <div id="tableHead">
                        <table>
                            <tr>
                                <th class="uid">アクティビティID</th>
                                <th class="uid">ユーザID</th>
                                <th class="name">名前</th>
                                <th class="uid">アクティビティ</th>
                                <th class="addr">対象</th>
                                <th class="addr">メモ</th>
                                <th class="date">日時</th>
                            </tr>
                        </table>
                    </div>
                    <div id="tableBody" onscroll="tableScroll();">
                        <table>
                            <?php
                            foreach ($acts as $i => $act) {
                                ?>
                                <tr>
                                    <td class="uid"><?php echo $act->activity_id; ?></td>
                                    <td class="uid"><?php echo $act->uid; ?></td>
                                    <td class="name"><?php echo $act->family_name . " " . $act->first_name; ?></td>
                                    <td class=""><?php echo $act->action; ?></td>
                                    <td class="addr"><?php echo $act->object; ?></td>
                                    <td class="addr"><input type="text" id="memo_<?php echo $i; ?>" name="memo_<?php echo $i; ?>" value="<?php echo $act->memo; ?>"></td>
                                    <td class="date"> 
                                        <?php echo $act->create_at; ?> 
                                        <input type="hidden" name="memoRow[<?php echo $i; ?>]" value="<?php echo $i; ?>" /> 
                                    </td> 
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </form> 
            </div>
        </div>

        <script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>
        <script>
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