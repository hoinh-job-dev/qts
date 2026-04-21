<!-- KYC, 個人情報 -->
<div class="whitebox">
    <h3>身分証写真</h3>
    <?php
    $files = $personal['imgfile'];
    $filenumber = (''==$files)? 0 : sizeof(explode(',',$files));
    $filearray = '';
    if (0<$filenumber) 
    { 
        ?>
        <table>
            <tr><th style="color:red;">枚数</th><td><?php echo $filenumber; ?>枚</td></tr>
        </table>

        <div style="width:100%;">
        <?php
        $filearray = explode(',',$files);
        for ($i=0; $i<$filenumber; $i++) 
        {
            echo("◎ ".($i+1)."枚目"); 
        ?>
            <div style="float:right; color:red; font-weight: bold">Delete this images? <input type="checkbox" name="deleteImages[]" value="<?php echo $filearray[$i] ?>"/>
            </div>
            <img src="<?php echo base_url_img('readimg/'.$filearray[$i]); ?>" alt="削除による欠番" style="width:100%;"></img>

        <?php } ?>
        </div>
        <br>
        <?php 
    } 
    ?>

    <table>
        <tr><th style="color:red;">追加アップロード</th><td>
                <input type="file" accept="image/*" name="photo" id="FileAttachment" class="upload"/>
                <input type="hidden" name="filenumber" value="<?php echo $filenumber; ?>">
            </td></tr>
    </table>
    <br>
    <h3>個人情報</h3>
    <table class="selectorZipcloudSearchWrapper">
        <tr>
            <tr>
                <th style="color:red;">性別</th>
                <td>
                    <input type="radio" id="sex" name="sex" value=1 <?php if(1 == $personal['sex']) echo "checked"; ?> ><label for="male">男性</label>&nbsp;&nbsp;&nbsp;
                    <input type="radio" id="sex" name="sex" value=2 <?php if(2 == $personal['sex']) echo "checked"; ?> ><label for="female">女性</label>                    
                </td>
            </tr>
            <th style="color:red;">生年月日</th>
            <td>
                <?php @list($birthYear, $birthMonth, $birthDate) = explode('-', $personal['birthday']); ?>
                <ul class="float-list select-box">
                    <li>
                        <select name="year" id="year" class="birthday" onchange="isValid_birthday();">
                            <option value="">--</option> 
                            <?php
                            date_default_timezone_set("Asia/Tokyo");
                            $date = new DateTime();
                            $this_year = intval(date("Y", $date->getTimestamp()));
                            for ($i = 0; $i < 100; $i++) {
                            	?><option value="<?php echo strval($this_year - $i); ?>" <?php echo ($this_year - $i == $birthYear ? ' selected="selected"' : NULL);?> ><?php echo strval($this_year - $i);?></option><?php
                            }
                            ?>
                        </select>
                        <span>年</span>
                    </li>
                    <li>
                        <select name="month" id="month" class="birthday" onchange="isValid_birthday();">
                            <option value="">--</option> 
                            <?php
                            for ($i = 1; $i <= 12; $i++) {
                            	?><option value="<?php echo strval( $i); ?>" <?php echo ($i == $birthMonth? ' selected="selected"' : NULL);?> ><?php echo strval($i);?></option><?php
                            }
                            ?>
                        </select>
                        <span>月</span>
                    </li>
                    <li>
                        <select name="day" id="day" class="birthday" onchange="isValid_birthday();">
                            <option value="">--</option> 
                            <?php
                            for ($i = 1; $i <= 31; $i++) {
                                ?><option value="<?php echo strval( $i); ?>" <?php echo ($i == $birthDate? ' selected="selected"' : NULL);?> ><?php echo strval($i);?></option><?php
                            }
                            ?>
                        </select>
                        <span>日</span>
                    </li>
                </ul>
                <input type="hidden" id="birthday" name="birthday" value="<?php echo $personal['birthday']; ?>">
            </td>
        </tr>
        <tr>
            <th style="color:red;">国籍</th>
            <td><input type="text" name="country" id="country" value="<?php echo $personal['country']; ?>" /></td>
        </tr>
        <tr>
            <th style="color:red;">郵便番号</th>
            <td>
                <input type="text" name="zip_code" id="zip" class="w-short selectorZipcodeValue zip_code" value="<?php echo $personal['zip_code']; ?>" />
                <input type="button" name="Search" value="住所検索" class="selectorSearchByZipcode" style="margin-left: 0; margin-top: 7px;" />
            </td>
        </tr>
        <tr>
            <th style="color:red;">都道府県</th>
            <td><input type="text" name="prefecture" id="add" class="w-short prefecture" value="<?php echo $personal['prefecture']; ?>" /></td>
        </tr>
        <tr>
            <th style="color:red;">市区郡</th>
            <td><input type="text" name="city" id="add02" class="city" value="<?php echo $personal['city']; ?>" /></td>
        </tr>
        <tr>
            <th>それ以降の住所</th>
            <td><input type="text" name="building" id="add03" class="w-short building" value="<?php echo $personal['building']; ?>" /></td>
        </tr>
    </table>

    <br>
    <h3>連絡先</h3>
    <?php
        $roleLogin = $this->CI->getSessionRole();
        $roleEdit = array(
                $this->config->item('role_sysadmin')
        );
    ?>
    <table>
        <tr>
            <th style="color:red;">Email</th>
            <td>
                <?php if(!in_array($roleLogin, $roleEdit)) {  ?>
                    <?php echo $personal['email']; ?>
                    <input type="hidden" name="email" id="mail" value="<?php echo $personal['email']; ?>"/>
                <?php }else{  ?>
                    <input type="text" name="email" id="mail" class="email" value="<?php echo $personal['email']; ?>" />
                <?php } ?>
            </td>
        </tr>
        <tr>
            <th style="color:red;">電話番号</th>
            <td><input type="text" name="tel" id="tel" class="w-short tel" value="<?php echo $personal['tel']; ?>" ></td>
        </tr>
    </table>
</div>

<script type="text/javascript">
    //select gender
    var sex = '<?php echo $sex;?>';
    if(sex) {            
       $('input[name="sex"][value="' + sex + '"]').attr('checked', 'checked');
    }
</script>