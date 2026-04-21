<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <h2><?php echo $title; ?></h2>

            <!-- Begin form search -->
            <form action="<?php echo base_url('Operator/reissueToken'); ?>" method="post" class="search">

                <div class="whitebox">
                    <h3>受領書の再発行</h3>
                    <?php
                    if(isset($statusMessage) && !empty($statusMessage)) {
                        ?>
                        <div style="color: red; "><?php echo $statusMessage; ?></div>
                        <?php
                    }
                    ?>
                    
                    <input type="hidden" name="reissue_token" value="1" />

                    <div class="row">
                        <label for="search_create_from">注文番号</label>
                        <input type="text" name="order_number" value="<?php echo @$_POST['order_number'];?>" />
                    </div>

                    <div class="row">
                        <label for="search_create_from">運用管理者のパスワード</label>
                        <input type="password" name="password" />
                    </div>

                    <div class="row">
                        <label style="height: 5px; display: block;">&nbsp;</label>
                        <input type="submit" value="受領書の再発行" class="prevent-double-click" onclick="this.form.submit()"/>
                    </div>

                    <div style="clear: both;"></div>
                </div>

            </form>

        </div>

        <script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>
        <script type="text/javascript">
            
        </script>