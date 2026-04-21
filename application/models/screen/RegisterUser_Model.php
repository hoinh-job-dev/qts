<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class RegisterUser_Model extends MY_Model
{

    public $user_hash;
    public $personal_id;
    // アカウント種別
    public $type = '';
    // 会社名
    public $company_name = '';
    // 会社名(カナ)
    public $company_name_kana = '';
    // 姓
    public $family_name;
    // 姓(カナ)
    public $family_name_kana;
    // 名
    public $first_name;
    // 名(カナ)
    public $first_name_kana;
    // 生年月日
    public $birthday;

    // 郵便番号
    public $zip_code1;
    public $zip_code2;
    public $zip_code;
    //都道府県
    public $prefecture;
    // 市区郡
    public $city;
    // それ以降の住所
    public $building;
    // 国籍
    public $country;
    // 電話番号
    public $tel1;
    public $tel2;
    public $tel3;
    public $tel;
    // メールアドレス
    public $email;
    // メールアドレス(確認用の再入力)
    public $email2;

    public $sex=0;

    // 支払い方法
    public $pay_method;
    // 予定金額
    public $amount;

    // 個人情報保護方針を読む
    public $personal_agreement;

    // 内容に同意する
    public $rule_agreement;
    // リスク説明を読む
    public $risk_agreement;

    public $photo1;
    public $photo2;
    public $photo3;
    public $photo4;

    public $files;

    public $btc_address;
    public $comment;

    public $currentPassword;
    public $newPassword;
    public $confirmNewPassword;

    public $deleteImages;

    public static $max_file_size = 37000000;
    public function __construct($data=array())
    {
        parent::__construct($data);

        if ($this->rule_agreement == 'on')
            $this->rule_agreement = 1;
        else
            $this->rule_agreement = 0;

        if ($this->personal_agreement == 'on')
            $this->personal_agreement = 1;
        else
            $this->personal_agreement = 0;
    }

    public function getRulesAgent($files = null,  $isAgent = true){
        $rules = $this->getRules($files, $isAgent);
        return $rules;
    }

    public function getRulesClient($files = null, $isAgent = false){
        $rules = $this->getRules($files, $isAgent);
        return $rules;
    }

    public function getRules($files = null, $isAgent){
        $this->files = $files;
        $rules = array();
        if ($this->type == $this->config->item('type_company')) {
            $rules['company_name'] = array(
                'title' => '「会社名」',
                'rule_list' => array(
                    'required', 'max_length[40]'
                ));
        } else {
            $rules['first_name'] = array(
                'title' => '「名」',
                'rule_list' => array(
                    'required',
                    'max_length[20]'
                ));
            $rules['family_name'] = array(
                'title' => '「姓」',
                'rule_list' => array(
                    'required',
                    'max_length[20]'
                ));
        }

        $email_rule_callback = $isAgent ? 'callback_emailAgentAlreadyExists' : 'callback_emailClientAlreadyExists';
        $rules = array_merge( $rules, array(
            'birthday' => array(
                'title' => '「生年月日」',
                'rule_list' => array(
                    'required'
                )
            ),
            'zip_code' => array(
                'title' => '「郵便番号」',
                'rule_list' => array(
                    'required',
                    'max_length[10]'
                )
            ),
            'prefecture' => array(
                'title' => '「都道府県」',
                'rule_list' => array(
                    'required',
                    'max_length[10]'
                )
            ),
            'city' => array(
                'title' => '「市区郡」',
                'rule_list' => array(
                    'required',
                    'max_length[30]'
                )
            ),
            'building' => array(
                'title' => '「それ以降の住所」',
                'rule_list' => array(
                    'required',
                    'max_length[40]'
                )
            ),
            'country' => array(
                'title' => '「国籍」',
                'rule_list' => array(
                    'required',
                    'max_length[10]'
                )
            ),
            'tel' => array(
                'title' => '「電話番号」',
                'rule_list' => array(
                    'required',
                    'regex_match[/[0-9 \-\+]*/]',
                    'max_length[15]'
                )
            ),
            'email' => array(
                'title' => '「メールアドレス」',
                'rule_list' => array(
                    'required',
                    'valid_email',
                    'max_length[50]',
                    $email_rule_callback,
                    'email_black_list'
                )
            ),
            'email2' => array(
                'title' => '「メールアドレス(確認用の再入力)」',
                'rule_list' => array(
                    'required',
                    'valid_email',
                    'matches[email]'
                )
            ),
        ));
        if (!$isAgent) {
            $rules = array_merge( $rules, array(
                'amount' =>  array(
                    'title' => '「予定金額」',
                    'rule_list' => array(
                        'required',
                        'greater_than_equal_to[1000]',
                    )
                )
            ));
        }

        $titles = array(
            "photo1" => "「画像」",
            "photo2" => "「画像を2」",
            "photo3" => "「画像を3」",
            "photo4" => "「画像を4」",
        );
        if ($files != null) {
            foreach($files as $key => $file){
                $rule_list = array();

                if (($key == 'photo1') and empty($file['name'])){
                    $rule_list = array('required');
                } else if (!empty($file['name'])){
                    $filename = $file['name'];
                    $regex = "([^\\s]+(\\.(?i)(jpg|jpeg|png|gif|bmp))$)";

                    if (!preg_match($regex, $filename))
                        $rule_list = array_merge($rule_list, array(
                            'callback_imageExtensionError'
                        ));
                    if ($file['size'] > self::$max_file_size){
                        $rule_list = array_merge($rule_list, array(
                            'callback_imageFileSizeError'
                        ));
                    }
                }
                $rules[$key] = array(
                    'title' => $titles[$key],
                    'rule_list' => $rule_list
                );
            }
        }

        $rules = array_merge( $rules, array(
            'personal_agreement' =>  array(
                'title' => '「個人情報保護方針を読む」',
                'rule_list' => array(
                    'required',
                )
            ),
            'rule_agreement' =>  array(
                'title' => '「内容に同意する」',
                'rule_list' => array(
                    'required',
                )
            )
        ));
        if (!$isAgent){
            $rules['risk_agreement'] =  array(
                'title' => '「リスク説明を読む」',
                'rule_list' => array(
                    'required',
                )
            );
        }

        //var_dump($rules);die;
        return $rules;
    }



    public function getPersonalData(){
        $pdata = array(
            'family_name' => $this->family_name,
            'first_name' => $this->first_name,
            'company_name' => $this->company_name,
            'family_name_kana' => $this->family_name_kana,
            'first_name_kana' => $this->first_name_kana,
            'company_name_kana' => $this->company_name_kana,
            'birthday' => $this->birthday,
            'email' => $this->email,
            'tel' => $this->tel,
            'zip_code' => $this->zip_code,
            'country' => $this->country,
            'prefecture' => $this->prefecture,
            'city' => $this->city,
            'building' => $this->building,
            'personal_agreement' => $this->personal_agreement
        );
        return $pdata;
    }

    public function getUserData(){
        $udata = array(
            'personal_id' => $this->personal_id,
            'type' => $this->type,
            'email' => $this->email,
            'family_name' => $this->family_name,
            'first_name' => $this->first_name,
            'company_name' => $this->company_name,
            'family_name_kana' => $this->family_name_kana,
            'first_name_kana' => $this->first_name_kana,
            'company_name_kana' => $this->company_name_kana,
            'status' => $this->config->item('act_reg_personal')
        );
        return $udata;
    }

    /*
    *Return field define rule for addOrder
    */
    public function getRulesAddOrder(){
        $rules = array(
            'email' => array(
                'title' => 'メールアドレス',
                'rule_list' => array(
                    'required',
                    'valid_email',
                    'max_length[50]'                    
                )
            ),
            'amount' =>  array(
                'title' => '「予定金額」',
                'rule_list' => array(
                    'required',
                    'greater_than_equal_to[1000]',
                )
            ),
            'rule_agreement' =>  array(
                'title' => '「内容に同意する」',
                'rule_list' => array(
                    'required',
                )
            ),
            'risk_agreement' =>  array(
                'title' => '「リスク説明を読む」',
                'rule_list' => array(
                    'required',
                )
            )
        );
        return $rules;
    }

    public function getPersonalData_OpeEdit($operator_uid, $now){
        $pdata = array(
            'family_name' => $this->family_name,
            'first_name' => $this->first_name,
            //'company_name' => $this->company_name,
            'family_name_kana' => $this->family_name_kana,
            'first_name_kana' => $this->first_name_kana,
            //'company_name_kana' => $this->company_name_kana,
            'birthday' => $this->birthday,
            'email' => $this->email,
            'sex' => $this->sex,
            'tel' => $this->tel,
            'zip_code' => $this->zip_code,
            'country' => $this->country,
            'prefecture' => $this->prefecture,
            'city' => $this->city,
            'building' => $this->building,
            'personal_agreement' => 1,
            'update_by' => $operator_uid,
            'update_at' => $now
        );
        return $pdata;
    }

    public function getUserData_OpeEdit($operator_uid, $now){
        $udata = array(
            'personal_id' => $this->personal_id,
            //'type' => $this->type,
            'email' => $this->email,
            'family_name' => $this->family_name,
            'first_name' => $this->first_name,
            //'company_name' => $this->company_name,
            'family_name_kana' => $this->family_name_kana,
            'first_name_kana' => $this->first_name_kana,
            //'company_name_kana' => $this->company_name_kana,
            'btc_address' => $this->btc_address,
            'comment' => $this->comment,
            'update_by' => $operator_uid,
            'update_at' => $now
        );
        return $udata;
    }

    public function getRulesOpeEdit(){
        $rules = array(
            'family_name' => array(
                'title' => '「姓」',
                'rule_list' => array(
                    'required',
                    'max_length[20]'
                )
            ),
            'first_name' => array(
                'title' => '「名」',
                'rule_list' => array(
                    'required',
                    'max_length[20]'
                )
            ),
            'btc_address' => array(
                'title' => '「BTCアドレス」',
                'rule_list' => array(
                    'check_btc_address'
                )
            ),
            'birthday' => array(
                'title' => '「生年月日」',
                'rule_list' => array(
                    'required'
                )
            ),
            'country' => array(
                'title' => '「国籍」',
                'rule_list' => array(
                    'required',
                    'max_length[10]'
                )
            ),
            'zip_code' => array(
                'title' => '「郵便番号」',
                'rule_list' => array(
                    'required',
                    'max_length[10]'
                )
            ),
            'prefecture' => array(
                'title' => '「都道府県」',
                'rule_list' => array(
                    'required',
                    'max_length[10]'
                )
            ),
            'city' => array(
                'title' => '「市区郡」',
                'rule_list' => array(
                    'required',
                    'max_length[30]'
                )
            ),
            'building' => array(
                'title' => '「それ以降の住所」',
                'rule_list' => array(
                    'required',
                    'max_length[40]'
                )
            ),
        );
        $rules = array_merge( $rules, array(
            'email' => array(
                'title' => 'メールアドレス',
                'rule_list' => array(
                    'required',
                    'valid_email',
                    'max_length[50]',
                    'emailAlreadyExists',
                    //'email_black_list',
                )
            )
        ));
        $rules['tel'] = array(
            'title' => '「電話番号」',
            'rule_list' => array(
                'required',
                'regex_match[/[0-9 \-\+]*/]',
                'max_length[15]'
            )
        );
        return $rules;
    }

    /*
    *Return fields define rule for change password
    */
    public function getRulesChangePassword(){
        $rules = array(
            'currentPassword' => array(
                'title' => '現在のパスワード',
                'rule_list' => array(                    
                    'required'
                )
            ),
            'newPassword' =>  array(
                'title' => 'パスワード',
                'rule_list' => array(                   
                    'required',
                    'min_length[6]',
                    'max_length[25]',
                    'is_password_strong'
                )
            ),
            'confirmNewPassword' =>  array(
                'title' => 'パスワード(確認用の再入力)',
                'rule_list' => array(                    
                    'required',
                    'matches[newPassword]'
                )
            )
        );
        return $rules;
    }

    /*
    *Return field define rule for guide agent
    */
    public function getRulesGuideAgent(){
        $rules = array(
            'rule_agreement' =>  array(
                    'title' => '「内容に同意する」',
                    'rule_list' => array(
                        'required',
                    )
                )
        );
        return $rules;
    }

}