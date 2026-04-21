<div style="margin-top:10px">
    <table class="table-dialog" >
        <tr>
            <th class="longname">Date Time</th>
            <th class="name">注文番号  </th>
            <th class="name">代理店<br/>ID  </th>
            <th class="filename">代理店<br/>氏名</th>
            <th class="longname">コミッション金額<br/>(BTC)</th>
            <th class="btc_address">BTCアドレス</th>
        </tr>

        <?php foreach(@$data as $dataRow) { ?>
            <tr>
                <td class="longname">
                    <?php if (isset($dataRow->closed_date) && !empty($dataRow->closed_date)) { ?>
                            <?php echo datetime_format($dataRow->closed_date); ?>
                    <?php } else { echo '-'; } ?>

                </td>
                <td class="uid">
                    <?php echo $dataRow->order_number ?>
                </td>
                <td class="uid">
                    <?php echo $dataRow->agent_uid ?>
                </td>
                <td class="filename">
                    <?php if ($dataRow->company_name_kana !=null or $dataRow->company_name != null) { echo $dataRow->company_name_kana. "<br/>" .$dataRow->company_name;} echo $dataRow->family_name." ".$dataRow->first_name; ?>
                </td>
                <td class="longname">
                    <?php echo $dataRow->amount ?>
                </td>
                <td class="btc_address">
                    <?php echo $dataRow->btc_address ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>