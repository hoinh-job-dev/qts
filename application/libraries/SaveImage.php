<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class SaveImage {

    private $CI = null;

    public function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->model('ImgFile_model', 'img');
        date_default_timezone_set("Asia/Tokyo");
    }

    // ファイルを追加する
    public function makeImgFile($formname, $personal_id, $num) {
        log_message('debug', '>> makeImgFile');

        // 一時ファイルを取得する
        $tempfilename = isset($_FILES[$formname]) ? $_FILES[$formname]['tmp_name'] : null;
        log_message('debug', '>> img | tempfilename=' . $tempfilename);
        if (null == $tempfilename) {
            return;
        }
        // ファイル名を作成する
        $date = new DateTime();
        $now = date($this->CI->config->item('filename_timestamp_format'), $date->getTimestamp());
        $filename = "pid" . $personal_id . "_" . $num . "_" . $now;
        // アップロードされた画像データを保存する
        $tmpImgType = exif_imagetype($tempfilename);
        log_message('debug', '>> img | tmpImgType=' . $tmpImgType);
        switch ($tmpImgType) {
            case IMAGETYPE_GIF:
                log_message('debug', 'IMAGETYPE GIF');
                $src = imagecreatefromgif($tempfilename);
                $filename = $filename . ".gif";
                imagegif($src, $this->CI->config->item('img_tmp_dir') . $filename);
                imagedestroy($src);
                break;
            case IMAGETYPE_JPEG:
                log_message('debug', 'IMAGETYPE JPEG');
                $src = imagecreatefromjpeg($tempfilename);
                $filename = $filename . ".jpg";
                imagejpeg($src, $this->CI->config->item('img_tmp_dir') . $filename);
                imagedestroy($src);
                break;
            case IMAGETYPE_PNG:
                log_message('debug', 'IMAGETYPE PNG');
                $src = imagecreatefrompng($tempfilename);
                $filename = $filename . ".png";
                imagepng($src, $this->CI->config->item('img_tmp_dir') . $filename);
                imagedestroy($src);
                break;
            default:
                log_message('debug', 'IMAGETYPE Others = ' . $tmpImgType);
                return '';
        }

        // ファイル転送後に削除
        $from = $this->CI->config->item('img_tmp_dir') . $filename;
        log_message('debug', '>> img | ls=' . shell_exec("ls -la " . $from));

        $to = $this->CI->config->item('img_read_dir');
        //$to = "ubuntu@52.196.152.251:".$this->CI->config->item('kyc_img_dir');

        $cp = "cp " . $from . " " . $to;
        //$cp = "sudo scp -pq -i ~/.ssh/CLV_KYC.pem ".$from." ".$to;
        log_message('debug', '>> img | copy' . $cp);

        shell_exec("sudo chmod 777 " . $from);
        shell_exec($cp);
        //shell_exec("sudo rm ".$from);

        $pdata = array(
            'personal_id' => $personal_id,
            'imgfile' => $filename
        );
        $this->CI->img->insert_filename($pdata);

        return $filename;
    }

    // ファイルを読み込む
    public function readImageFile($filename) {
        
    }

    // ファイルを削除して追加する
    public function updateImageFile($addfilename, $delfilename) {
        
    }

}
