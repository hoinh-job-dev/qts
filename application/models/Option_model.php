<?php

class Option_model extends MY_model {

    // 
    const T_OPTION = 't_options';

    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Tokyo");
    }

    /*****************************************
     * 
     *****************************************/

    public function updateOption($arrData, $arrConds = array(), $limit = NULL) {
        if(!is_array($arrData) || empty($arrData)) {
            log_message('debug', 'WARN: Update option / update data is empty');
            return false;
        }
        foreach($arrData as $field => $value) {
            $this->token_db->set($field, $value);
        }
        if(is_array($arrConds) && !empty($arrConds)) {
            foreach($arrConds as $field => $value) {
                $this->token_db->where($field, $value);
            }
        }
        if((int) $limit > 0) {
            $this->token_db->limit((int) $limit);
        }
        $result = $this->token_db->update(self::T_OPTION);
        log_message('debug', $this->token_db->last_query());
        // echo $this->token_db->last_query(); echo "<br><br>"; var_dump($result); exit;
        return $result;
    }

    public function getOptions($foreign_id, $tab_id = NULL) {
        $this->token_db->select('*');
        $this->token_db->from(self::T_OPTION);
        $this->token_db->where('foreign_id', $foreign_id);
        if((int) $tab_id > 0) {
            $this->token_db->where('tab_id', $tab_id);
        }
        $this->token_db->order_by('order', 'ASC');
        $result = $this->token_db->get();
        return !empty($result) ? $result->result_array() : array();
    }

    public function getPairs($foreign_id)
    {
        $_arr = $this->getOptions($foreign_id);
        $arr = array();
        foreach ($_arr as $row)
        {
            $key = $row['key'];
            $value = $row['value'];
            switch ($row['type'])
            {
                case 'enum':
                    list(, $value) = explode("::", $value);
                    break;
                case 'bool':
                    list(, $value) = explode("::", $value);
                    $value = ((int) $value === 1);
                    break;
                case 'float':
                    $value = (float) $value;
                break;
                case 'int':
                    $value = (int) $value;
                break;
            }
            // parse option that is organized as an array
            $group_separator = '_ARRAY_';
            if(strpos($key, $group_separator) !== false) {
                list($group_key, $group_item_key) = explode($group_separator, $key);
                if(!isset($arr[$group_key])) {
                    $arr[$group_key] = array();
                }
                $arr[$group_key][$group_item_key] = $value;
            }
            else {
                $arr[$key] = $value;
            }
        }
        return $arr;
    }

}
