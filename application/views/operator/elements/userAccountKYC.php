<!-- KYC, 個人情報 -->
<div class="whitebox">
    <h3>身分証写真</h3>
    <?php
        $files = $personal['imgfile'];
        $filenumber = (''==$files)? 0 : sizeof(explode(',',$files));
        $filearray = '';
        if (0<$filenumber) { ?>
    <table>
        <tr><th style="color:red;">枚数</th><td><?php echo $filenumber; ?>枚</td></tr>
    </table>
    <div style="overflow-y:scroll;width:100%;height:400px;">
    <?php
            $filearray = explode(',',$files);
            for ($i=0; $i<$filenumber; $i++) {
                        echo("◎ ".($i+1)."枚目"); ?>
                        <img src="<?php echo base_url_img('readimg/'.$filearray[$i]); ?>" alt="削除による欠番" style="width:100%;"></img>
        <?php } ?>
    </div>
    <?php } ?>
    <br>
    <h3>個人情報</h3>
    <table>
        <tr><th style="color:red;">性別</th>
            <td><?php if($personal['sex'] == 1){
                    echo "男性";
                }else if($personal['sex'] == 2){
                    echo "女性";
                }else{
                    echo "無し";
                }?></td>            
        </tr>
        <tr><th style="color:red;">生年月日</th><td><?php echo $personal['birthday']; ?></td></tr>
        <tr><th style="color:red;">国籍</th><td><?php echo $personal['country']; ?></td></tr>
        <tr><th style="color:red;">郵便番号</th><td><?php echo $personal['zip_code']; ?></td></tr>
        <tr><th style="color:red;">都道府県</th><td><?php echo $personal['prefecture']; ?></td></tr>
        <tr><th style="color:red;">市区郡</th><td><?php echo $personal['city']; ?></td></tr>
        <tr><th style="color:red;">それ以降の住所</th><td><?php echo $personal['building']; ?></td></tr>
    </table>
    <br>
    <h3>連絡先</h3>
    <table>
        <tr><th style="color:red;">Email</th><td><?php echo $personal['email']; ?></td></tr>
        <tr><th style="color:red;">電話番号</th><td><?php echo $personal['tel']; ?></td></tr>
    </table>
</div>