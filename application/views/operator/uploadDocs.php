<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/operator/view.css'); ?>">

<style type="text/css">
    table.tblUpload{
        width: 100%;
    }
    .tblUpload td{
        padding-bottom: 15px;
    }
    .tblUpload td.label{
        padding-bottom: 5px;
    }
    .tblUpload a{
        color: blue;
    }
    .tblUpload a:hover{
        text-decoration: none;
    }
</style>
<section class="l-contents cf">
    <div class="l-main js-height">
        <div class="l-page-">
            <h2><?php echo $title; ?></h2>

            <form action="<?php echo base_url('Operator/confirmUploadDocs'); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="upload_document" value="1" />
                <div class="whitebox">
                    <h3>ドキュメントアップロード</h3>
                    <?php
                    if(isset($statusMessage) && !empty($statusMessage)) {
                        ?>
                        <div style="color: red; "><?php echo $statusMessage; ?></div>
                        <br />
                        <?php
                    }
                    $uploadPath = dirname(BASEPATH) . "/" . $this->config->item('path_pdf_doc');
                    $uploadUrl = base_url($this->config->item('path_pdf_doc')) . '/';
                    $fileMissingErr = " (無し)";
                    ?>
                    <table class="tblUpload">
                        <tr>
                            <td class="label">ドキュメント</td>
                        <tr>
                            <td>
                                <select name="targetFile">
                                    <option value="">-選択してください-</option>
                                    <?php
                                    if(isset($docsList) && !empty($docsList)) {
                                        foreach($docsList as $group_id => $group) {
                                            ?>
                                            <optgroup label="<?php echo @$group['group_label']; ?>">
                                                <?php
                                                $groupData = @$group['group_data'];
                                                if(!empty($groupData)) {
                                                    foreach($groupData as $file_name => $file_label) {
                                                        $selected = FALSE;
                                                        $fileExists = file_exists($uploadPath . $file_name) && is_file($uploadPath . $file_name);
                                                        $fileViewUrl = NULL;
                                                        if($fileExists) {
                                                            $fileViewUrl = $uploadUrl . $file_name;
                                                            $selected = @$uploadedDoc == md5($file_name);
                                                        }
                                                        else {
                                                            $file_label .= $fileMissingErr;
                                                        }
                                                        ?><option value="<?php echo @$file_name; ?>" <?php echo $selected ? ' selected="selected"' : NULL; ?> data-file_view_url="<?php echo $fileViewUrl; ?>"><?php echo @$file_label; ?></option><?php
                                                    }
                                                }
                                                ?>
                                            </optgroup>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="selectorViewFile">
                                    <a href="javascript: void(0);" target="_blank">★アップロードしたファイルを確認するため、クリックしてください。</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">ファイル</td>
                        </tr>
                        <tr>
                            <td>
                                <input type="file" name="uploadFile" style="border: 1px solid #efefef; width: 100%;" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="submit" value="アップロード" />
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>

<script type="text/javascript">
    function onUploadFileChanged() {
        var $selectorViewFile = $(document).find('.selectorViewFile'),
            $selectedOption = $(document).find("select[name='targetFile']").find("option:selected");
        if($selectorViewFile.length > 0 && $selectedOption.length > 0) {
            $selectorViewFile.find("a").attr("href", "javascript: void(0);");
            $selectorViewFile.hide();
            var url = $selectedOption.attr('data-file_view_url');
            if(typeof url == 'string' && url.length > 0) {
                $selectorViewFile.find("a").attr("href", url);
                $selectorViewFile.show();
            }
        }
    }

    $(document).on("change", "select[name='targetFile']", function(){
        onUploadFileChanged();
    });

    $(document).ready(function() {
        onUploadFileChanged();
    });
</script>