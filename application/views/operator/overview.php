<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

        <section class="l-contents cf">
        <div class="l-main js-height">
            <div class="l-page-">
                <h2><?php echo $title;?></h2>
                    <!-- Begin form search -->
                    <form action="<?php echo base_url('Operator/home'); ?>" method="post" id="search" class="search">
                        <fieldset>
                                <div class="row">
                                    <label for="search[userid]">ユーザID</label>
                                    <input type="text" name="search[uid]" id="search[uid]" value="<?php echo $search->uid;?>">
                                </div>
                                <div class="row">
                                    <label for="role">ロール </label>
                                    <select name="search[role]" id="search[role]">
                                    <option value=""></option>
                                    <?php
                                        foreach ($roles as $role) {     
                                            $selected = '';
                                            if($search->role === $role->code) {
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
                                    <label for="search[type]">種別 </label>
                                    <select name="search[type]" id="search[type]">
                                        <option value=""></option>
                                    <?php
                                        foreach ($types as $type) {
                                            $selected = '';
                                            if($search->type === $type->code) {
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
                                    <label for="search_email">Email </label>
                                    <input type="text" name="search[email]" id="search[email]" value="<?php echo $search->email;?>">
                                </div>
                                <div class="row row-order">
                                    <span>並べ替え: </span>
                                    <label><input type="radio" name="search[order_by]" value="uid" <?php if ($search->order_by=='uid') echo "checked"; ?>> ユーザID</label>
                                    <label><input type="radio" name="search[order_by]" value="role" <?php if ($search->order_by=='role') echo "checked"; ?>> ロール</label>
                                    <label><input type="radio" name="search[order_by]" value="type" <?php if ($search->order_by=='type') echo "checked"; ?>> 種別</label>
                                    <label><input type="radio" name="search[order_by]" value="email" <?php if ($search->order_by=='email') echo "checked"; ?>> Email</label>
									<br>
                                    <span>並び順: </span>
                                    <label><input type="radio" name="search[order_opt]" value="ASC"  <?php if ($search->order_opt=='ASC') echo "checked"; ?>> 昇順</label>
                                    <label><input type="radio" name="search[order_opt]" value="DESC"  <?php if ($search->order_opt=='DESC') echo "checked"; ?>> 降順</label>
                                </div>
                                <div class="row row-search">
                                    <input type="submit" value="Search" onclick="">
                                </div>

                        </fieldset>                   
                    </form>
                    <hr>
                    <!-- End form search -->

                <div class="whitebox" id="table">
                    <h3>登録者</h3>
                    <?php if ($this->CI->check_export_csv_permission()){ ?>
                        <ul id="button">
                                <!--<li><input type="button" id="update" value="CSV出力" onclick="var form=$('#search');var fcsv=form.clone()[0]; fcsv.action='outputCsv_home';fcsv.submit();"></li>-->
                            <input type="button" id="update" value="CSV出力" onclick="exportCsvFromSearch('outputCsv_home');">
                            <div style="float:right; padding-right: 5%;">
                                <form action="<?php echo base_url('Operator/outputCsv_home2'); ?>" method="post" id="outputCsv">
                                        <input type="submit" id="update" value="20%エージェントツリー">
                                </form>
                            </div>
                        </ul>
                    <?php } ?>
                    <div id="tableHead_main">
                        <table>
                            <tr>
                                <th class="status">ステータス</th>
                                <th class="uid">ユーザID</th>
                                <th class="uid">個人情報<br>ID</th>
                                <th class="role">ロール</th>
                                <th class="type">種別</th>
                                <th class="name">姓 名</th>
                            </tr>
                        </table>
                    </div>
                    <div id="tableHead_sub" onscroll="document.getElementById('tableBody_sub').scrollLeft = document.getElementById('tableHead_sub').scrollLeft;">
                        <table>
                            <tr>
                                <th class="email">メールアドレス</th>
                                <th class="tel">電話番号</th>
                                <th class="date">生年月日</th>
                                <th class="zip">郵便<br>番号</th>
                                <th class="zip">都道<br>府県</th>
                                <th class="addr">市区郡</th>
                                <th class="addr">それ以降の住所</th>
                                <th class="zip">国籍</th>
                                <th class="filename">身分証</th>  
                            </tr>
                        </table>
                    </div>
                    <div id="tableBody_main" onscroll="document.getElementById('tableBody_sub').scrollTop = document.getElementById('tableBody_main').scrollTop;">
                        <table>
                        <?php
                        foreach ($users as $user) {
                        ?>
                            <tr>
                                <td class="status"><?php echo $user['status']; ?></td>
                                <td class="uid"><a href="<?php echo base_url('Operator/viewUserDetail/').'/'.$user['uid']; ?>" style="color:blue;text-decoration:underline;"><?php echo $user['uid']; ?></a></td>
                                <td class="uid"><?php echo $user['pid']; ?></td>
                                <td class="role"><?php echo $user['role']; ?></td>
                                <td class="type"><?php echo $user['type']; ?></td>
                                <td class="name"><?php echo $user['name']; ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        </table>
                   </div>
                    <div id="tableBody_sub" onscroll="document.getElementById('tableHead_sub').scrollLeft = document.getElementById('tableBody_sub').scrollLeft;document.getElementById('tableBody_main').scrollTop = document.getElementById('tableBody_sub').scrollTop;">
                        <table>
                        <?php
                        foreach ($users as $user) {
                        ?>
                            <tr>
                                <td class="email"><?php echo $user['email']; ?></td>
                                <td class="tel"><?php echo $user['tel']; ?></td>
                                <td class="date"><?php echo $user['birthday']; ?></td>
                                <td class="zip"><?php echo $user['zip']; ?></td>
                                <td class="zip"><?php echo $user['prefecture']; ?></td>
                                <td class="addr"><?php echo $user['city']; ?></td>
                                <td class="addr"><?php echo $user['building']; ?></td>
                                <td class="zip"><?php echo $user['country']; ?></td>
                                <td class="filename"><?php echo $user['imgfile']; ?></td>   
                            </tr>
                        <?php
                        }
                        ?>
                        </table>
                   </div>
                   <div style="clear: both;"></div>
                </div>
            </div>

<script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>