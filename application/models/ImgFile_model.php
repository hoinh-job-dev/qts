<?php

class ImgFile_model extends CI_Model {

    private $CI = null;

    // テーブル名
    const T_IMAGE = 't_imgfile';
    const VALID = 0;
    const INVALID = 1;

    private $kyc_db = null;

    public function __construct() {
        parent::__construct();
        $this->kyc_db = $this->load->database('kyc', true);
        $this->CI = & get_instance();
    }

    /*****************************************
     * 登録時
     *****************************************/

    /**
     *
     * @param type $data
     * @return type
     */
    public function insert_filename($data) {
        // 新規レコードを作成
        $this->kyc_db->set('system_key', $this->config->item('sys_quantatoken'));
        $result = $this->kyc_db->insert(self::T_IMAGE, $data);
        log_message('debug', $this->kyc_db->last_query());
        // echo $this->kyc_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    /**
     * delete_image function: change delete_flag to INVALID (1)
     * @param array String : list images will be deleted
     * @return boolean
     */
    public function delete_images($images){
        log_message('debug', 'ImgFile_model::delete_images');
        $this->kyc_db->where_in('imgfile', $images);
        $this->kyc_db->update(self::T_IMAGE, array('delete_flag' => self::INVALID));
        foreach ($images as $image){
            $filepath = $this->CI->config->item('img_read_dir') . $image;
            log_message('debug', 'ImgFile_model::delete_image ' . $filepath);
            shell_exec("rm -f " . $filepath);
        }
        return true;
    }
}
