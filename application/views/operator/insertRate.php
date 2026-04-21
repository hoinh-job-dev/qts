<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">
<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-link-agent">
            <h2><?php echo $title;?></h2>

            <form action="<?php echo base_url('Operator/insertRate'); ?>" method="post" id="search" class="search">
                <?php if ($updated) { ?>
                    <div style="color:green; padding:10px;">
                        レートが更新されました。
                    </div>
                <?php } ?>
                <div class="row">
                    <label for="from">FROM</label>
                    <select name="from">
                        <option value="BTC">BTC</option>
                        <option value="USD">USD</option>
                        <option value="JPY">JPY</option>
                    </select>
                </div>
                <div class="row">
                    <label for="to">TO</label>
                    <select name="to">
                        <option value="USD">USD</option>
                        <option value="BTC">BTC</option>
                        <option value="JPY">JPY</option>
                    </select>
                </div>
                <div class="row">
                    <label for="rate">レート</label>
                    <input name="rate" type="text" />
                </div>

                <div class="row">
                    <input type="submit" value="submit" name="submit" style="margin-top: 26px;width: 221px;"/>
                </div>
            </form>
            <hr>
            <div class="whitebox" style="float:left">
                <table class="table-data">
                    <tr>
                        <th class="data-uid">
                            ID
                        </th>
                        <th class="data-uid">
                            FROM
                        </th>
                        <th class="data-uid">
                            TO
                        </th>
                        <th class="data-rate">
                            レート
                        </th>
                        <th class="name" >
                            作成者
                        </th>
                        <th class="date">
                            作成日
                        </th>
                    </tr>

                    <?php
                        foreach (@$rates as $rate){
                            $rate =(object) $rate;
                        ?>
                            <tr>
                            <td class="data-uid">
                                <?php echo $rate->id ?>
                            </td>
                            <td class="data-uid">
                                <?php echo $rate->to ?>
                            </td>
                            <td class="data-uid">
                                <?php echo $rate->from ?>
                            </td>
                            <td class="data-rate">
                                <?php echo $rate->rate ?>
                            </td>
                            <td class="name" style="padding-left:10px;">
                                <?php if ($rate->create_by >0){ echo $rate->family_name . ' ' . $rate->first_name; } else {echo "自動化取得";} ?>
                            </td>
                            <td class="date">
                                <?php echo $rate->create_at ?>
                            </td>
                            </tr>
                    <?php
                        }
                    ?>
                </table>
            </div>
        </div>
