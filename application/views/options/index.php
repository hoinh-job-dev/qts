<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <?php
            if(isset($access_deny)) {
                ?>
                <div class="whitebox">
                    <i>You do not have permission to access this page</i>
                </div>
                <?php
            }
            else {
                ?><h2><?php echo $title; ?></h2><?php
                if(isset($grouped_arr) && is_array($grouped_arr) && count($grouped_arr) > 0) {
                    $this->load->helper('sanitize');
                    ?>

                    <style type="text/css">
                        /* jqueryui fix */
                        .ui-widget {
                            font-family: inherit;
                            font-size: inherit;
                        }
                        .ui-widget input, .ui-widget select, .ui-widget textarea, .ui-widget button {
                            font-family: inherit;
                            font-size: inherit;
                        }
                        img.ui-datepicker-trigger {
                            cursor: pointer;
                        }
                        .ui-tabs .ui-tabs-panel {
                            padding: 10px 0;
                            overflow: hidden;
                        }
                        div.ui-tabs{
                            padding: 0;
                        }
                        div.ui-widget-content{
                            background: none;
                            border: none;
                        }
                        div.ui-tabs ul li{
                            padding: 5px 10px !important;
                        }

                        tr.table-row-odd td{
                            padding: 5px 10px;
                        }
                        tr.table-row-odd td textarea{
                            height: 250px;
                        }
                        div#tableHead table th.option_label_column, 
                        div#tableBody table td.option_label_column{
                            width: 30% !important;
                            word-break: normal;
                        }
                        div#tableHead table th.option_value_column, 
                        div#tableBody table td.option_value_column{
                            width: 70% !important;
                        }
                        div#tableBody table td input[type="text"]{
                            height: auto;
                        }
                        .group_header {
                            margin: 20px 0 0 0;
                            background-color: #003366;
                            color: #fff;
                            padding: 2px 5px;
                            width: 100%;
                        }
                    </style>

                    <?php $active = " ui-tabs-active ui-state-active"; ?>
                    <div class="ui-tabs ui-widget ui-widget-content ui-corner-all">
                        <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
                            <li class="ui-state-default ui-corner-top<?php echo @$tab_id != '1' ? NULL : $active; ?>">
                                <a href="<?php echo base_url('Options/index/1'); ?>">一般設定</a>
                            </li>
                            <li class="ui-state-default ui-corner-top<?php echo @$tab_id != '2' ? NULL : $active; ?>">
                                <a href="<?php echo base_url('Options/index/2'); ?>">Wallet自動化</a>
                            </li>
                            <li class="ui-state-default ui-corner-top<?php echo @$tab_id != '3' ? NULL : $active; ?>">
                                <a href="<?php echo base_url('Options/index/3'); ?>">Eメール</a>
                            </li>
                        </ul>
                    </div>

                    <form action="<?php echo base_url('Options/update'); ?>" method="post" id="update">
                        <input type="hidden" name="options_update" value="1" />
                        <input type="hidden" name="tab_id" value="<?php echo @$tab_id; ?>" />

                        <div class="whitebox">
                            <!-- <h3>一般に設定</h3> -->
                            <?php
                            if(isset($statusMessage) && !empty($statusMessage)) {
                                ?>
                                <div style="color: red; "><?php echo $statusMessage; ?></div>
                                <?php
                            }
                            ?>
                            <ul id="button">
                                <li><input type="button" id="update" value="Update" onclick="return submitStatus();"></li>
                            </ul>
                            <div id="tableHead">
                                <table >
                                    <tr>
                                        <th class="option_label_column">Option</th>
                                        <th class="option_value_column">Value</th>
                                    </tr>
                                </table>
                            </div>
                            <div id="tableBody" onscroll="tableScroll();">
                                <?php
                                foreach($group_order as $group_index => $group_key) {
                                    $o_arr = $grouped_arr[$group_key];
                                    ?>
                                    <div class="group_header"><?php echo @$group_label[$group_key]; ?></div>
                                    <table>
                                    <?php 
                                    for ($i = 0; $i < count($o_arr); $i++) {
                                        if (!in_array($o_arr[$i]['tab_id'], array(1,2,3)) ||
                                            (int) $o_arr[$i]['is_visible'] === 0 ||
                                            $o_arr[$i]['tab_id'] != @$tab_id)
                                        {
                                            continue;
                                        }

                                        $rowClass = NULL;
                                        $rowStyle = NULL;
                                        if (in_array($o_arr[$i]['key'], array('smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass')))
                                        {
                                            $rowClass = " boxSmtp";
                                            $rowStyle = "display: none";
                                            switch (@$this->config->item('send_email_method'))
                                            {
                                                case 'smtp':
                                                    $rowStyle = NULL;
                                                    break;
                                            }
                                        }
                                        ?>
                                        <tr class="table-row-odd<?php echo $rowClass; ?>" style="<?php echo $rowStyle; ?>">
                                            <td class="option_label_column">
                                                <?php 
                                                echo $o_arr[$i]['description']; 

                                                // show tokens for email templates
                                                $search_key = '_ARRAY_message';
                                                $tmp_key = substr($o_arr[$i]['key'], 0, strlen($o_arr[$i]['key']) - strlen($search_key));
                                                if($o_arr[$i]['key'] == $tmp_key.$search_key) {
                                                    $supportTokens = $this->CI->getSupportEmailTokens($tmp_key);
                                                    if(!empty($supportTokens)) {
                                                        echo "<br /><br />Available email tokens: <br />" . implode("<br />", $supportTokens);
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="option_value_column">
                                                <?php
                                                switch ($o_arr[$i]['type'])
                                                {
                                                    case 'string':
                                                        ?><input type="text" name="value-<?php echo $o_arr[$i]['type']; ?>-<?php echo $o_arr[$i]['key']; ?>" class="form-field w200" value="<?php echo Sanitize::html($o_arr[$i]['value']); ?>" /><?php
                                                        break;
                                                    case 'text':
                                                        ?><textarea name="value-<?php echo $o_arr[$i]['type']; ?>-<?php echo $o_arr[$i]['key']; ?>" class="form-field"><?php echo Sanitize::html($o_arr[$i]['value']); ?></textarea><?php
                                                        break;
                                                    case 'int':
                                                        ?><input type="text" name="value-<?php echo $o_arr[$i]['type']; ?>-<?php echo $o_arr[$i]['key']; ?>" class="form-field w60<?php echo $o_arr[$i]['key'] == 'o_products_per_page' ? ' positiveNumber' : ' field-int';?>" value="<?php echo Sanitize::html($o_arr[$i]['value']); ?>" /><?php
                                                        break;
                                                    case 'float':
                                                        switch ($o_arr[$i]['key'])
                                                        {
                                                            default:
                                                                ?><input type="text" name="value-<?php echo $o_arr[$i]['type']; ?>-<?php echo $o_arr[$i]['key']; ?>" class="form-field field-float w60" value="<?php echo Sanitize::html($o_arr[$i]['value']); ?>" /><?php
                                                        }
                                                        break;
                                                    case 'enum':
                                                        ?><select name="value-<?php echo $o_arr[$i]['type']; ?>-<?php echo $o_arr[$i]['key']; ?>" class="form-field">
                                                        <?php
                                                        $default = explode("::", $o_arr[$i]['value']);
                                                        $enum = explode("|", $default[0]);
                                                        
                                                        $enumLabels = array();
                                                        if (!empty($o_arr[$i]['label']) && strpos($o_arr[$i]['label'], "|") !== false)
                                                        {
                                                            $enumLabels = explode("|", $o_arr[$i]['label']);
                                                        }
                                                        
                                                        foreach ($enum as $k => $el)
                                                        {
                                                            if ($default[1] == $el)
                                                            {
                                                                ?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo array_key_exists($k, $enumLabels) ? Sanitize::html($enumLabels[$k]) : Sanitize::html($el); ?></option><?php
                                                            } else {
                                                                ?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo array_key_exists($k, $enumLabels) ? Sanitize::html($enumLabels[$k]) : Sanitize::html($el); ?></option><?php
                                                            }
                                                        }
                                                        ?>
                                                        </select>
                                                        <?php
                                                        break;
                                                    case 'bool':
                                                        ?><input type="checkbox" name="value-<?php echo $o_arr[$i]['type']; ?>-<?php echo $o_arr[$i]['key']; ?>"<?php echo $o_arr[$i]['value'] == '1|0::1' ? ' checked="checked"' : NULL; ?> value="1|0::1" /><?php
                                                        break;
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php 
                                    } 
                                    ?>
                                    </table>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </form>

                    <script type="text/javascript" src="<?php echo base_url('js/operator/operate.js'); ?>"></script>

                    <script type="text/javascript">

                        (function ($, undefined) {
                            $(function () {
                                "use strict";

                                $(document).on("change", "select[name='value-enum-send_email_method']", function (e) {
                                    switch ($("option:selected", this).val()) {
                                        case 'mail|smtp::mail':
                                            $(".boxSmtp").hide();
                                            break;
                                        case 'mail|smtp::smtp':
                                            $(".boxSmtp").show();
                                            break;
                                    }
                                });

                            });
                        })(jQuery);

                    </script>

                    <?php
                }
            }
            ?>
        </div>
