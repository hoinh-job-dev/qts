<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">
<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-view-client">
            <h2><?php echo $title;?></h2>
            <!-- Begin form search -->
            <form action="<?php echo base_url('Agent/viewClients'); ?>" method="post" id="search" class="search" style="width:98%; margin:10px;">
                <fieldset>
                    <div class="bigrow row ">
                        <label for="family_name">交換者姓</label>
                        <input type="text" name="search[family_name]" value="<?php echo $search->family_name; ?>" />
                    </div>
                    <div class="row bigrow">
                        <label for="first_name">交換者名</label>
                        <input type="text" name="search[first_name]" value="<?php echo $search->first_name; ?>" />
                    </div>
                    <div class="row bigrow " style="padding:7px; float:right;">
                        <label for="search"></label>
                        <input type="submit" value="Search" onclick="">
                    </div>
                </fieldset>
            </form>
            <!-- end form search -->

            <div class="whitebox">
                <h3>交換者一覧</h3>
                <style>
                    li.user {
                        margin-bottom: 10px;
                    }
                    table.user {
                        border-top: 1px solid #e4e3e3;
                        border-left: 1px solid #e4e3e3;
                        width: 100%;
                    }
                    td.user {
                        border-right: 1px solid #e4e3e3;
                        border-bottom: 1px solid #e4e3e3;
                        padding : 3px;
                        word-wrap: break-word;
                    }
                    td.head {
                        width: 100px;
                    }
                </style>
                <ul>
                    <?php
                    foreach ($clients as $client) {
                        ?>
                        <li class="user"><table class="user">
                                <tr><td class="head user">種別</td><td class="body user"><?php echo $client->type; ?></td></tr>
                                <tr><td class="head user">名称</td><td class="body user"><?php if ('02'==$client->type) { echo $client->compamy_name_kana."<br>".$client->compamy_name."<br>";} echo $client->family_name_kana." ".$client->first_name_kana."<br>".$client->family_name." ".$client->first_name; ?></td></tr>
                                <tr><td class="head user">状態</td><td class="body user"><?php echo $client->status; ?></td></tr>
                            </table></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div><!-- /.l-page-home -->