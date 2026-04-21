<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">
<div class="l-order ">
    <section class="order-main">
        <h1 style="font-size: x-large;"><?php echo $title; ?></h1>
       
        <?php if (!empty($arrFieldError)) { ?>
            <div class ="whitebox" style="color:#ff6666; padding: 15px;">
                <?php echo "入力したデータはエラーがあります"; 
                    foreach($arrFieldError as $key => $fieldError) {
                    ?>
                    <p class="error_link" style="margin-left:15px;" for="<?php echo $key ?>" ><?php echo $fieldError ?></p>
                    <?php
                }?>
            </div>
        <?php } ?>

        </br>
        <div class="whitebox">
            <form action="<?php echo base_url('Client/completeOrder'); ?>" method="post" id="order">
                                <?php
                                    $isErr = isset($isError)? $isError : '00';
                                    switch ($isErr) {
                                    case '10':
                                        echo "<p>===============================<br>"
                                        ." 注文を行うためにはメールアドレスの登録が必要です。<br>"
                                        ."===============================</p>";
                                        break;
                                    default:
                                    }
                                ?>
                <h2 style="margin-top:20px;">連絡先</h2>
                <input type="hidden" value="addOrderPost" name="addOrderPost">
                <p>
                    <label for="mail">メールアドレス</label>
                    <input type="text" name="email" id="email" value="<?php if(@$userPostData['email'] != '') echo $userPostData['email'] ?>">
                </p>

                <h2>支払い方法</h2>
                <p>
                    <label for="pay">支払い方法</label>
                    <select name="pay_method" id="pay" class="w-short">
                        <?php if ($this->config->item('enable_banking') === true) { ?><option value="<?php echo $this->config->item('payby_bank'); ?>">銀行振込</option><?php } ?>
                        <option value="<?php echo $this->config->item('payby_btc'); ?>">ビットコイン</option>
                    </select>
                </p>
                <p>
                    <label for="money">交換希望額</label>
                    <input type="text" name="amount" id="amount" value="<?php if(@$userPostData['amount'] != '') echo $userPostData['amount'] ?>" class="w-medule" <span class="right">USD</span>
                </p>

                <h2>規約への同意</h2>
                <?php
                $doc_absolute_dir = FCPATH . $this->config->item('path_pdf_doc');
                $search_doc_ext = ".pdf";
                $replace_doc_ext = ".html";
                ?>

                <!-- <div class="terms-container">
                    <section class="terms-content" data-end_reached="F">
                        <?php include_once str_replace($search_doc_ext, $replace_doc_ext, $doc_absolute_dir . $this->config->item('doc_termsofuse')); ?>
                    </section>
                    <p>
                        <label id="rule_agreement_label" class="rule_agreement">
                            <input type="checkbox" id="rule_agreement" name="rule_agreement" class="terms-checkbox" <?php echo !isset($userPostData['rule_agreement']) ? ' disabled="disabled"' : ' checked="checked"'; ?> />&nbsp;
                            内容に同意する
                        </label>
                    </p>
                </div> -->

                <p>
                    <span class="txt-blue">
                        <a href="<?php echo base_url($this->config->item('path_pdf_doc') . $this->config->item('doc_termsofuse'));  ?>" target="_blank" tabindex="-1">利用規約を読む</a>
                    </span>
                    <label id="rule_agreement_label"  class="rule_agreement" >
                        <input type="checkbox" id="rule_agreement" name="rule_agreement" <?php echo !isset($userPostData['rule_agreement']) ? '' : ' checked="checked"'; ?> 
                        />&nbsp;内容に同意する
                    </label>
                </p>

                <!-- <div class="terms-container">
                    <section class="terms-content" data-end_reached="F">
                        <?php include_once str_replace($search_doc_ext, $replace_doc_ext, $doc_absolute_dir . $this->config->item('doc_termsofrisk')); ?>
                    </section>
                    <p>
                        <label id="risk_agreement_label" class="risk_agreement">
                            <input type="checkbox" id="risk_agreement" name="risk_agreement" class="terms-checkbox" <?php echo !isset($userPostData['risk_agreement']) ? ' disabled="disabled"' : ' checked="checked"'; ?> />&nbsp;
                            内容に同意する
                        </label>
                    </p>
                </div> -->

                <p>
                    <span class="txt-blue">
                        <a href="<?php echo base_url($this->config->item('path_pdf_doc') . $this->config->item('doc_termsofrisk')); ?>" target="_blank" tabindex="-1">リスク説明を読む</a>
                    </span>
                    <label id="risk_agreement_label" class="risk_agreement" >
                        <input type="checkbox" id="risk_agreement" name="risk_agreement" <?php echo !isset($userPostData['risk_agreement']) ? '' : ' checked="checked"'; ?> 
                        />&nbsp;内容に同意する
                    </label>
                </p>
                <!-- <ul class="order-btnlist float-list">
                    <li><input type="button" class="confirm btn-force-disable selectorConfirmRegister" disabled="disable" value="confirm" onclick="return submitOrder();"></li>
                </ul> -->      
                <ul class="order-btnlist float-list">
                    <li><input type="button" class="next prevent-double-click" value="confirm" id="confirm" onclick="return submitOrder();"></li>
                </ul>           
            </form>
        </div><!-- / .whitebox -->

<script type="text/javascript" src="<?php echo base_url('js/client/addOrder.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/regUser.js'); ?>"></script> 
<script type="text/javascript">
    $(document).ready(function () {               
        $('.error_link').click(function(e){
            var id = "#" + $(e.target).attr('for');
            $(id).focus();
        });

        $('.error_link').each(function(index){
           var id = "#" + $(this).attr('for');
           $(id).addClass('outline-red');
       });
    });
</script> 