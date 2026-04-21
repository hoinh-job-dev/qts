<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*****************************************
 * system
 *****************************************/

// QuantaToken
$config['sys_quantatoken'] = 1;

// site url
// Local environment and all servers refer to the same pages.
$config['site_domain'] = "http://one8-association.co.jp";
$config['use_manual'] = "http://one8-association.co.jp/qnt_agent/";
$config['selfy_manual'] = "http://one8-association.co.jp/manual/#manual_section03";


/* Directory */
//--------------------------------------------------
// local environment
//$config['img_tmp_dir'] = "/Applications/MAMP/htdocs/QT/tmpimg/";
//$config['img_read_dir'] = "/Applications/MAMP/htdocs/QT/readimg/";
//$config['kyc_host'] = "CLV_KYC";

// product, staging, dev servers
$config['img_tmp_dir'] = "/var/www/html/QT/tmpimg/";
$config['img_read_dir'] = "/var/www/html/QT/readimg/";
$config['kyc_host'] = "kyc";
$config['kyc_img_dir'] = "~/QT/img/";


/* Files */
//--------------------------------------------------
//File-path to document contain pdf file.
$config['path_pdf_doc'] = "docs/";

// private policy
//$config['doc_privacypolicy'] = "docs/privacy_policy_20160601.pdf"; // old
$config['doc_privacypolicy'] = "privacy_policy.pdf";

// terms of risk
$config['doc_seminarschedule'] = "seminarschedule.pdf";
// terms of use
$config['doc_termsofuse'] = "termsofuse.pdf";
// terms of risk
$config['doc_termsofrisk'] = "risk_terms.pdf";
// agnet operation manual
$config['doc_agentmanual'] = "agent_operation_manual.pdf";
// document for agent
$config['doc_agentnote'] = "documentforagent.pdf";



//terms of guide
$config['doc_guide_agent'] = "doc_guide_agent.html";

// csv file (and settings)
$config['delimiter'] = ",";
$config['newline'] = "\r\n";

$config['csv_home'] = "QT_userlist.csv";
$config['csv_personal'] = "QT_personallist.csv";
$config['csv_order'] = "QT_orderlist.csv";
$config['csv_rate'] = "QT_ratelist.csv";
$config['csv_receipt_issue_token'] = "QT_receipt_issue_token.csv";

$config['csv_issue_redeem_token'] = "QT_csv_issue_redeem_token_link.csv";
$config['csv_edit_token_list'] = "QT_csv_edit_token_list.csv";


/* Mail setting */
//--------------------------------------------------
$config['mailtype'] = "text";
$config['newline'] = "\n";

// mail address
// sending from qts system.
/*$config['info_email'] = "staging_info@one8-association.co.jp";
//$config['qtsmaillog_email'] = "system_log@one8-association.co.jp";
$config['qtsmaillog_email'] = "qtstest@cardano-labo.com";
// sending by hand.
$config['support_email'] = "info@zondir.top";

// mail header
//$config['mail_admin_name'] = "[Quanta]ワンエイトアソシエーション株式会社";
$config['mail_admin_name'] = "<<Staging>>[Quanta]";

$config['mail_from'] = $config['info_email'];
$config['mail_bcc'] = $config['qtsmaillog_email'];*/
/*
// mail signature
$config['mail_signature'] = "\n"
        . "\n"
        . "++++++++++++++++++++++++++++++++++++++\n"
        . "ワンエイトアソシエーション株式会社\n"
        . "〒105-0004\n"
        . "東京都港区新橋5丁目12-7\n"
        . "富永ビル4F\n"
        . "営業時間平日10：00~19：00\n"
        . "定休日 土日祝日\n"
        . "WEB： ".$config['site_domain']."\n"
        . "MAIL：".$config['support_email']."\n"
        . "LINE：@bbk7201s\n"
        . "++++++++++++++++++++++++++++++++++++++";*/


/*****************************************
 * variables
 *****************************************/
// order charge
/*$config['charge'] = 0.0375;*/ // 3.75%, 銀行振込の場合のみ、そしてオペレータの処理の範囲内なのでシステムではこのパラメータは使用しない

// exchange rate : USD/QNT
//$config['rate_usdtoken'] = 0.001; // 0.1セントで1トークン(5月)
//$config['rate_usdtoken'] = 0.0012; // 0.12セントで1トークン(6,7月)
//$config['rate_usdtoken'] = 0.0013; // 0.13セントで1トークン(8,9月)
//$config['rate_usdtoken'] = 0.0014; // 0.14セントで1トークン(10月-4Q)


/* valid period */
//--------------------------------------------------
// session of login with agents and operators
$config['session_expiration_time'] = 1800; // sec, 30分

// order by btc payment
/*$config['order_expiration_time'] = 691200;*/ // sec, 8日間

// getting btc/usd rate
$config['get_rate_interval'] = 180; // sec, 3分

/*****************************************
 * variable format
 *****************************************/
// session
$config['session_timestamp_format'] = "Y/m/d H:i:s";

// date format
$config['db_timestamp_format'] = "Y/m/d H:i:s";
$config['filename_timestamp_format'] = "YmdHis";

//
$config['fields_separator'] = '~:~';

/*****************************************
 * code list
 *****************************************/

/* 01 type code */
//--------------------------------------------------
$config['type_person'] = "01";      //個人
$config['type_company'] = "02";     //法人

/* 02 role code */
//--------------------------------------------------
$config['role_sysadmin'] = "01";    //シスアド        
$config['role_operator'] = "02";    //オペレータ
$config['role_client'] = "03";      //交換者
/*$config['role_agent20'] = "04";     //代理20%
$config['role_agent15'] = "05";     //代理15%
$config['role_agent10'] = "06";     //代理10%
$config['role_agent5'] = "07";      //代理5%*/
$config['role_ope_money'] = "21";   //*
$config['role_ope_order'] = "22";   //*
$config['role_ope_reg'] = "23";     //*

// agent role => commission (in percentage)
/*$config['role_agent_commission'] = array(
    "04" => 20, //代理20%
    "05" => 18, //代理15%
    "06" => 15, //代理10%
    "07" => 11,  //代理5%
);*/

// rules of marketers based on agent roles
$config['role_agent_doc_marketer'] = array(
    "04" => "marketer20_terms.pdf",
    "05" => "marketer15_terms.pdf",
    "06" => "marketer10_terms.pdf",
    "07" => "marketer05_terms.pdf",
);

$config['role_agent_label'] = array(
    "04" => "代理20%", //代理20%
    "05" => "代理15%", //代理15%
    "06" => "代理10%", //代理10%
    "07" => "代理5%",  //代理5%
);

// $config['error_not_operator'] = "This is not an Operator";
// $config['operator_deleted'] = "Operator was deleted successful";
$config['error_not_operator'] = "これはオペレータではありません。";
$config['operator_deleted'] = "オペレータが正常に削除されました。";

$config['operator_added'] = "オペレータが正常に追加されました。";
$config['operator_edited'] = "オペレータが正常に編集されました。";

// activity message
$config['activity_csv_user'] = '登録者一覧';
$config['activity_csv_order'] = '注文一覧';
$config['activity_csv_personal'] = '審査対象';
$config['activity_csv_receipt_issue_token'] = '受領書発行実績';

$config['activity_csv_issue_redeem_token'] = '還元コードの変更';
$config['activity_csv_edit_token_list'] = 'リディームリンク発行';

/* 03 activity code */
//--------------------------------------------------
// make link
$config['act_gen_link'] = "01";         //リンクのみ
$config['act_invalid_personal'] = "04"; //無効
// approve account
$config['act_reg_personal'] = "11";     //登録 済
$config['act_edit_personal'] = "12";    //個人情報編集
$config['act_approved'] = "21";         //承認 済
// order
$config['act_order'] = "31";            //新規注文
$config['act_addorder'] = "32";         //追加注文
// bank
$config['act_receiveby_bank'] = "33";   //銀行確認済み
$config['act_exchange_jpybtc'] = "34";  //jpy/btc換金
// Reorder confirm(BTC and Bank)
$config['act_reorder_confirm'] = "35"; //注文承認済み
$config['act_reorder_confirm_memo'] = "注文承認済み";
// btc
$config['act_notify_btcaddr'] = "37";   //btcアドレス通知
$config['act_receivedby_btc'] = "38";   //btc確認済み
$config['act_btc_amount'] = "39";       //BTCの受取
// token
$config['act_issue_token'] = "41";      //トークン発行
$config['act_edit_token'] = "43";       // Edit token
// view token
$config['act_view_token'] = "51";       //保有量確認
$config['act_order_complete'] = "52";       //注文完了

//config code 61 => exists in m_general code = 'システムウォレット作成'
//config code 62 => exists in m_general code = 'システムウォレットへ転送'

// CSV file
$config['act_csv_export'] = "71"; // CSV出力
// log session
$config['act_login'] = "91";            //ログイン
$config['act_logout'] = "92";           //ログアウト
$config['act_ask_password'] = "93";     //パスワード確認
$config['act_reset_password'] = "94";   //パスワード変更

/* 04 approval result code */
//--------------------------------------------------
$config['approval_not_selected'] = "00";        //未選択
$config['approval_approved'] = "01";            //承認
$config['approval_pending'] = "02";             //保留
$config['approval_not_approve'] = "03";         //未承認
$config['approval_invalid'] = "04";             //無効
$config['approval_expired'] = "05";             //期限切れ

// reason 
$config['approval_lack'] = "12";                //未記入あり
$config['approval_imcompleted'] = "13";         //入力不完全
$config['approval_unreadable'] = "14";          //判読不可
$config['approval_tooyoung'] = "15";            //年齢制限
$config['approval_name_mistake'] = "16";        //氏名不一致
$config['approval_birthday_mistake'] = "17";    //生年月日不一致
$config['approval_address_mistake'] = "18";     //住所不一致

/* 05 order progress code */
//--------------------------------------------------
$config['order_not_selected'] = "00";           //未選択
$config['order_invalid'] = "41";                //無効
$config['order_expired'] = "42";                //期限切れ
// bank
$config['order_orderby_bank'] = "11";           //銀行振込申込み
$config['order_notify_bankaccount'] = "12";     //銀行口座連絡済
$config['order_receiveby_bank'] = "13";         //銀行振込済
$config['order_exchange_jpybtc'] = "14";        //jpy/btc換金済
// btc
$config['order_orderby_btc'] = "21";            //BTC支払い申込み
$config['order_notify_btcaddr'] = "22";         //BTCアドレス連絡済
$config['order_receiveby_btc'] = "24";          //BTC着金済
// token
$config['order_pre_issue_token'] = "30";        //*
$config['order_issue_token'] = "31";            //QNT計算済
$config['order_send_receipt'] = "32";           //QNT発行済
$config['order_completed'] = "51";              //Order completed

// ■■■BTCの返金テーブルの状態■■■
// 返金理由の状態
$config['refund_status'] = array(
    // 2回目から送金
    'send_from_2nd' => "01",
    // トークン発行を取消する
    'token_cancel' => $config['order_invalid'],
    // 有効期限が切れる
    'expired_date' => $config['order_expired']
);
// 返金操作の状態
$config['refund_oper_status'] = array(
        // 未確認
        'unconfirm'      => '00',
        // 確認中
        'is_comfirming'  => '01',
        // 終了
        'finish'         => '02'
);
// status of send refund btc to operator
$config['refund_sent_status'] = array(
        'fail'      => '00',
        'success'  => '01'
);
$config['refund_status_text'] = array(
    // 2回目から送金
    '01' => "2回目の送金",
    // トークン発行を取消する
    $config['order_invalid'] => '無効',
    // 有効期限が切れる
    $config['order_expired'] => '有効期限切れ'
);
// 返金操作の状態
$config['refund_oper_status_text'] = array(
        // 未確認
        '00'      => '-',
        // 確認中
        '01'  => '対応中',
        // 終了
        '02'         => '返金済'
);
// status of send refund btc to operator
$config['refund_sent_status_text'] = array(
        '00'      => '失敗',
        '01'  => '成功'
);

/* 07 pay method code */
//--------------------------------------------------
$config['payby_bank'] = "01";   //銀行
$config['payby_btc'] = "02";    //BTC

/* 08 currency unit code */
//--------------------------------------------------
$config['currency_jpy'] = "01"; //JPY
$config['currency_btc'] = "02"; //BTC
$config['currency_qnt'] = "03"; //QNT
$config['currency_usd'] = "04"; //USD

$config['read_guide'] = "readGuide";

/*****************************************
 * Mail text
 *****************************************/

/* 交換者 */
//--------------------------------------------------
/*$config['mail_accept_order_subject'] = "[Quanta]ワンエイトアソシエーション株式会社　ご注文を受付ました";
$config['mail_accept_order_message'] = "様\n"
        . "\n"
        . "\n"
        . "ご注文ありがとうございます。\n"
        . "\n"
        . "現在、弊社にてコンプライアンス確認を行っております。\n"
        . "確認が完了次第、ビットコイン送信先のご連絡させていただきますので、今しばらくお待ち下さい。\n"
        . "\n"
        . "\n"
        . "※身分証および住所確認書類に不備がある場合は証明書類の追加・再提出をお願いする場合がございます。\n"
        . "\n"
        . "※交換金額が日額で4800ドル以上、または月額で24000ドル以上のQNT交換者様について\n弊社のコンプライアンス基準に則り、03-5425-4863から交換確認のお電話を差し上げます。";*/
/*
$config['mail_accept_order_message'] = "様\n"
        . "\n"
        . "\n"
        . "ご注文ありがとうございます。\n"
        . "\n"
        . "現在、弊社にてコンプライアンス確認を行っております。\n"
        . "確認が完了次第、振込先またはビットコイン送信先のご連絡させていただきますので、今しばらくお待ち下さい。\n"
        . "\n"
        . "\n"
        . "※身分証および住所確認書類に不備がある場合は証明書類の追加・再提出をお願いする場合がございます。\n"
        . "\n"
        . "※交換金額が日額で4800ドル以上、または月額で24000ドル以上のQNT交換者様について\n弊社のコンプライアンス基準に則り、03-5425-4863から交換確認のお電話を差し上げます。";
*/
/*
//--------------------------------------------------
$config['mail_approved_client_subject'] = "[Quanta]ワンエイトアソシエーション株式会社　コンプライアンス確認完了のお知らせ";
$config['mail_approved_client_message'] = "様\n"
        . "\n"
        . "\n"
        . "お待たせ致しました。\n"
        . "コンプライアンス確認作業が完了しアカウントが承認されました。\n"
        . "なお、請求書につきましては、別途メールにてご連絡致しますので、\n"
        . "今しばらくお待ち下さい。";


//--------------------------------------------------
$config['mail_notify_bankaccount_subject'] = "[Quanta]ワンエイトアソシエーション株式会社 QNT交換　ご注文確定致しました";
$config['mail_notify_bankaccount1_message'] = "様\n"
        . "\n"
        . "\n"
        . "お世話になっております。\n"
        . "ワンエイトアソシエーション株式会社で御座います。\n"
        . "\n"
        . "QNTのご注文が確定致しました。\n"
        . "下記金額のお振込みをお願い致します。\n"
        . "\n"
        . "入金は日本円のみとなります。\n"
        . "下記の【入金金額】を日本円に換算し、お振り込みお願い致します。\n"
        . "（USDの/JPYの為替レートは変動しますので、おおよその日本円換算金額をご入金ください。）\n"
        . "お振込頂いた金額に対してQNTの発行を致します。\n"
        . "\n"
        . "\n"
        . "▼振込情報\n"
        . "===========================================\n"
        . "\n"
        . "【 振 込 I D 】";
$config['mail_notify_bankaccount2_message'] = "\n"
        . "\n"
        . "\n"
        . "【注文金額】";
$config['mail_notify_bankaccount3_message'] = "USD\n"
        . "【交換手数料】";
$config['mail_notify_bankaccount4_message'] = "USD\n"
        . "【交換対象金額】";
$config['mail_notify_bankaccount5_message'] = "USD\n"
        . "\n"
        . "<USDから日本への計算参考サイト>\n"
        . "http://info.finance.yahoo.co.jp/fx/\n"
        . "\n"
        . "===========================================\n"
        . "\n"
        . "※ご入金額換算の例\n"
        . "USD/JPYの為替レートが105 USD/JPY だった場合のご入金額\n"
        . "2,000 USD(【入金金額】) × 105 USD/JPY＝ 210,000円\n"
        . "USDの/JPYの為替レートは変動しますので、大幅な差異がある\n場合は確認のお電話をさせていただく場合がございます。\n"
        . "\n"
        . "\n"
        . "▼振込先口座情報\n"
        . "===========================================\n"
        . "\n"
        . "〈金融機関〉西武信用金庫\n"
        . "〈口座種別〉原宿支店　（支店番号：111）\n"
        . "〈支店番号〉普通預金\n"
        . "〈口座番号〉2130699\n"
        . "〈振込先名〉ワンエイトアソシエーション（カ\n"
        . "\n"
        . "※ ※ ※ご 注 意 く だ さ い ※ ※ ※\n"
        . "・お振り込み時には、必ず「振込ID」を振込人名義の前にご記入下さい。\n"
        . "　例) 123456 ワンエイト ハナコ\n"
        . "・交換時に発生する送金手数料等は、お客様のご負担となります。\n"
        . "\n"
        . "============================================\n"
        . "\n"
        . "特定商取引法に基づく表示項目\n"
        . $config['site_domain']."/law/";


//--------------------------------------------------
$config['mail_notify_btcaddr_subject'] = "[Quanta]ワンエイトアソシエーション株式会社 ご注文が確定致しました";
$config['mail_notify_btcaddr_message'] = "様\n"
        . "\n"
        . "\n"
        . "お世話になっております。\n"
        . "ワンエイトアソシエーションで御座います。\n"
        . "\n"
        . "QNTのご注文が確定致しました。\n"
        . "\n"
        . "以下のURLから請求内容をご確認下さい。\n"
        . "内容に問題がございませんでしたら、ご送金のお手続きをお願い致します。\n"
        . "\n"
        . "URL：".$this->config['domain']."/QT/Client/viewBtcAddr/";


//--------------------------------------------------
$config['mail_notify_tokencode_subject'] = "[Quanta]ワンエイトアソシエーション株式会社　受領書をお送りいたします";
$config['mail_notify_tokencode_message1'] = "様\n"
        . "\n"
        . "\n"
        . "お世話になっております。\n"
        . "ワンエイトアソシエーションで御座います。\n"
        . "\n"
        . "\n"
        . "QNTの交換が完了しましたので受領書をお送り致します。\n"
        . "\n"
        . "*******************\n"
        . "この領収書はQuanta公開時にQNTを自分のウォレットに入金する\n"
        . "際にも必要となりますので大切に保管して下さい。\n"
        . "*******************\n"
        . "\n"
        . "===================\n"
        . "受領書\n"
        . "-------------------\n";
$config['mail_notify_tokencode_message2'] = "Ticket ID: ";
$config['mail_notify_tokencode_message3'] = "\n"
        . "\n"
        . "交換完了日時: ";
$config['mail_notify_tokencode_message4'] = "\n"
        . "\n"
        . "受け取ったBitcoinの総額: ";
$config['mail_notify_tokencode_message5'] = " BTC\n"
        . "適用されたUSD/BTCレート: ";
$config['mail_notify_tokencode_message6'] = " USD/BTC\n"
        . "適用されたUSD/QNTレート: ";
$config['mail_notify_tokencode_message7'] = " USD/QNT\n"
        . "交換されたQNTの総額: ";
$config['mail_notify_tokencode_message8'] = " QNT\n"
        . "\n"
        . "QNT還元用コード: ";
$config['mail_notify_tokencode_message9'] = "\n"
        . "===================";
*/

/* 代理店 - 仮登録完了 *//*
//--------------------------------------------------
$config['mail_agent_register1_subject'] = "[Quanta] 仮登録完了のご連絡";
$config['mail_agent_register1_message1'] = "様\n"
        . "\n"
        . "\n"
        . "お手続きありがとうございます。\n"
        . "仮登録が完了致しましたので、以下のURLからパスワードを設定し、本登録を完了させて下さい。\n"
        . "\n"
        . "URL : ".$this->config['domain']."/QT/Agent/setPassword/";
$config['mail_agent_register1_message2'] = "\n"
        . "\n"
        . "尚、上記のURLはパスワード設定後に無効となりますのでご注意下さい。\n"
        . "パスワード設定後にログインを行われる場合は、以下のURLをご使用下さい。\n"
        . "\n"
        . "URL : ".$this->config['domain']."/QT/Agent/login";


//--------------------------------------------------
$config['mail_approved_agent_subject'] = "[Quanta]ワンエイトアソシエーション株式会社　コンプライアンス確認完了のお知らせ";
$config['mail_approved_agent_message'] = "様\n"
        . "\n"
        . "\n"
        . "お待たせ致しました。\n"
        . "コンプライアンス確認作業が完了し、アカウントが承認されました。\n"
        . "以下URLより、ID、パスワードをご入力の上、ログインお願いいたします。\n"
        . "\n"
        . "ログインURL: ".$this->config['domain']."/QT/Agent/login\n"
        . "尚、弊社WEBサイトからもログイン可能になっております。";


// パスワード忘れ
//--------------------------------------------------
$config['mail_respond_password_subject'] = "[Quanta] パスワード変更の確認";
$config['mail_respond_password_message'] = "様\n"
        . "\n"
        . "\n"
        . "お世話になっております。\n"
        . "ワンエイトアソシエーションで御座います。\n"
        . "\n"
        . "パスワード再設定URL: ".$this->config['domain']."/QT/Agent/resetPasswd/";

// Information of email when BTC was arrived
//--------------------------------------------------
$config['mail_notify_receivedbtc'] = array(
    'subject' => "[Quanta]ワンエイトアソシエーション株式会社 BTCを受領いたしました",
    'message' => "様\n"
        . "\n"
        . "\n"
        . "お世話になっております。\n"
        . "ワンエイトアソシエーション株式会社　サポートデスクです。\n"
        . "\n"
        . "ご送付頂きましたBTCを受領いたしました。\n"
        . "誠にありがとうございました。\n"
        . "\n"
        . "▼送付情報 \n"
        . "===========================================\n\n",
    'odernumber' => "【 振 込 I D 】",
    'btcreceived' => "\n" . "【 送付額 】",
    'btcaddress' => " BTC \n" . "【 BTCアドレス 】",
    'lastmessage' => "\n\n"
        . "===========================================\n\n"
        . "後程、受領書をお送りいたしますので、\n"
        . "引き続き、よろしくお願いいたします。\n"
        . "※受領書の発行は、通常１営業日又は、2営業日ほどかかります。\n"
);

// Information of email when BTC was arrived more than 2nd time.
//--------------------------------------------------
$config['mail_notify_receivedbtc2nd'] = array(
    'subject' => "[Quanta]ワンエイトアソシエーション株式会社 BTCのウォレット相違につきまして",
    'message' => "様\n"
        . "\n"
        . "\n"
        . "お世話になっております。\n"
        . "ワンエイトアソシエーション株式会社　サポートデスクです。\n"
        . "\n"
        . "この度、ご送付いただきましたBTCにつきまして、弊社指定のBTCアドレスと\n"
        . "相違しております。QNT交換までのお手続きに関しましては、セキュリティの\n"
        . "都合上、ご指定BTCアドレス以外の受領では交換手続きにすすめません。\n"
        . "\n"
        . "交換手続きをすすめるにあたり、受領BTCを一度、交換者様へ返送させていた\n"
        . "だき、再度指定BTCアドレスへのご送付処理が必要となります。\n"
        . "\n"
        . "つきましては、本メールへ返送先BTCアドレス情報の返信をお願いいたします。\n"
        . "\n"
        . "※受信BTCの返送にかかる取引所手数料は、交換者様負担での返送処理となり\n"
        . "ますことをご了承ください。\n"
        . "\n"
        . "▼返送させていただく情報 \n"
        . "===========================================\n\n",
    'odernumber' => "【 振 込 I D 】",
    'btcreceived' => "\n" . "【返送額 】",
    'btcaddress' => "",
    'lastmessage' =>  " BTC \n\n"
        . "===========================================\n\n\n"
);*/


$config['email_blacklist_domain'] = array("docomo.ne.jp", "softbank.ne.jp", "ezweb.ne.jp");

//config for memo column of t_email_queue.
$config['memo_0'] = "Client confirm email"; //登録済み
$config['memo_1'] = "Operator: Approve client"; //交換者の承認通知
$config['memo_2'] = "Client complete order"; //注文完了の通知
$config['memo_3'] = "Auto wallet: Notify client order by BTC"; //BCTの注文でBCTアドレス通知
$config['memo_4'] = "Auto wallet: Receive BTC amount"; //BCTの注文でBCT着金
$config['memo_5'] = "Operator: Notify user for order pay by bank"; //振込の注文で送金通知
$config['memo_6'] = "Agent register complete"; //代理店の登録済み
$config['memo_7'] = "Agent ask password"; //代理店のパスワード設定
$config['memo_8'] = "Operator: Approve agent"; //代理店の承認通知
$config['memo_9'] = "Operator: confirm token"; //受領書の発行  
$config['memo_10'] = "Operator: reissue token / 受領書の再発行"; //受領書の「再」発行
$config['memo_11'] = "Operator: email cancel order";//注文の無効メール

//config is_send column for send email status of t_email_queue.
$config['is_sent_0'] = "0";  //未送信
$config['is_sent_1'] = "1";  //送信 済
$config['is_sent_2'] = "2";  //送信中
$config['is_sent_3'] = "3";  //送信 失敗

//config email redeem send info
$config['email_redeem_info'] = array(
    'protocol' => "smtp",
    'smtp_host' => "smtp.lolipop.jp",
    'smtp_port' => "587",
    'smtp_user' => "info@one8-association.co.jp",
    'smtp_pass' => "minamiji1094",
    'mailtype' => "text",
    'charset' => "UTF-8"
);

$config['mail_name_redeem'] = "Quanta";
// 現在の設定はBCCがありません。 
$config['mail_bcc_redeem'] = "system_log@one8-association.co.jp"; 

$config['edit_token_type'] = "1";
$config['redeem_token_type'] = "2";

//status of email is sent.
$config['email_sent_status'] = array(
    '1' => '済み',
    '2' => '送信中',
    '3' => '失敗'
);

//status payed of token.
$config['token_payed_status'] = array(
    '1' => '済み', 
    '2' => '失敗',
    '3' => '済み（タイムアウト）'
);

//group file for view quanta-wallet page
$config['client_quantawallet'] = "client_quantawallet.html";
$config['client_noteCryptoCurrency'] = "client_noteCryptoCurrency.html";
$config['client_backup'] = "client_backup.html";

//group file for view client redeem-info page
$config['client_qntRedeemSupport'] = "client_qntRedeemSupport.html";
$config['client_qntRedeemProcess'] = "client_qntRedeemProcess.html";
$config['client_qntRedeemDoc'] = "client_qntRedeemDoc.html";