<div style="margin-top:10px">
    <table class="table-dialog full-width" >
        <tr>  
            <th class="uid">注文番号</th>
            <th class="uid">代理店 UID</th>
            <th class="longname">代理店名称</th>
            <th class="uid">ランク</th>
            <th class="filename">コミッション金額BTC</th>
            <th class="btc_address">BTCアドレス</th>  
        </tr>

        <?php foreach(@$data as $dataRow) { ?>
            <tr>                
                <td class="uid">
                    <?php echo $dataRow->order_number ?>
                </td>
                <td class="uid">
                    <?php echo $dataRow->agent_uid ?>
                </td>
                <td class="filename">
                    <?php if ($dataRow->company_name_kana != null or $dataRow->company_name != null) { 
                        echo $dataRow->company_name_kana . "<br/>" . $dataRow->company_name; }
                        echo $dataRow->family_name . " " . $dataRow->first_name; ?>
                </td>
                <td class="uid">
                    <?php if (isset($dataRow->role)) { 
                        echo $arrRole[$dataRow->role]; 
                    }else echo "-" ?>
                </td>
                <td class="amount">                
                    <?php echo money_format_qts($dataRow->amount, 8) ?>
                </td>
                <td class="uid">
                    <?php echo $dataRow->btc_address ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>