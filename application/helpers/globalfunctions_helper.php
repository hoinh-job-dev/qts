<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if ( ! function_exists('datetime_2line_format'))
{
    function datetime_format( $datetime = null)
    {
        if ($datetime == null)
            return null;
        if (is_string($datetime)){
            $datetime = new DateTime($datetime);
        }
        return $datetime->format("Y-m-d H:i:s");
    }
}

if ( ! function_exists('datetime_2line_format'))
{
    function datetime_2line_format( $datetime = null)
    {
        if ($datetime == null)
            return null;
        if (is_string($datetime)){
            $datetime = new DateTime($datetime);
        }
        return $datetime->format("Y-m-d<\\b\\r/>H:i:s");
    }
}

if ( ! function_exists('money_format_qts'))
{
    function money_format_qts( $number, $precision, $floorFlag = true)
    {
        if ($floorFlag) {
            if ($precision<1){
                return floor($number);
            }
            if (strpos($number, ".") === false) {
                return $number . "." . str_repeat('0', $precision);
            } else {
                $fraction = substr($number, strpos($number, ".")+1, $precision); // calculates the decimal places to $precision length
                $fraction_len = strlen($fraction);
                if ($fraction_len < $precision){
                    $fraction = $fraction . str_repeat('0', $precision - $fraction_len);
                }
                $newDecimal = floor($number). '.' .$fraction; // reconstructs decimal with new decimal places
                return $newDecimal;
            }
        } else {
            if (strpos($number, ".") === false) {
                return $number . "." . str_repeat('0', $precision);
            } else {
                $result = round($number, $precision, PHP_ROUND_HALF_UP);
                $fraction = substr($result, strpos($number, ".") + 1, $precision); // calculates the decimal places to $precision length
                $fraction_len = strlen($fraction);
                if ($fraction_len < $precision) {
                    $result = $result . str_repeat('0', $precision - $fraction_len);
                }
                return $result;
            }
        }
    }
}

if ( ! function_exists('getPostMaxSize'))
{
    function getPostMaxSize()
    {
        $post_max_size = ini_get('post_max_size');
        switch (substr($post_max_size, -1))
        {
            case 'G':
                $post_max_size = (int) $post_max_size * 1024 * 1024 * 1024;
                break;
            case 'M':
                $post_max_size = (int) $post_max_size * 1024 * 1024;
                break;
            case 'K':
                $post_max_size = (int) $post_max_size * 1024;
                break;
        }
        return $post_max_size;
    }
}

if ( ! function_exists('get_docs_list'))
{
    function get_docs_list()
    {
        $list = array();

        /*//group list sample:
        $list[] = array(
            'group_label' => 'any_group_label',
            'group_data' => array(
                'actual_file_name' => 'any_label_for_file',
            )
        );
        */

        $list[] = array(
            'group_label' => 'プライバシーポリシー',
            'group_data' => array(
                'privacy_policy.pdf' => 'privacy_policy.pdf',
            )
        );

        // $list[] = array(
        //     'group_label' => 'マーケター規約',
        //     'group_data' => array(
        //         'Confidential.pdf' => 'Confidential.pdf',
        //     )
        // );

        $list[] = array(
            'group_label' => 'QT 運用マニュアル',
            'group_data' => array(
                'marketer20_terms.pdf' => 'marketer20_terms.pdf',
                'marketer15_terms.pdf' => 'marketer15_terms.pdf',
                'marketer10_terms.pdf' => 'marketer10_terms.pdf',
                'marketer05_terms.pdf' => 'marketer05_terms.pdf',
            )
        );

        $list[] = array(
            'group_label' => 'その他ツール',
            'group_data' => array(
                'agent_operation_manual.pdf' => 'agent_operation_manual.pdf',
                'seminarschedule.pdf' => 'seminarschedule.pdf ',
                'documentforagent.pdf' => 'documentforagent.pdf',
            )
        );

        $list[] = array(
            'group_label' => '利用規約',
            'group_data' => array(
                'termsofuse.pdf' => 'termsofuse.pdf',
            )
        );

        $list[] = array(
            'group_label' => 'リスク説明',
            'group_data' => array(
                'risk_terms.pdf' => 'risk_terms.pdf',
            )
        );

        $list[] = array(
            'group_label' => '代理店ガイダンス',
            'group_data' => array(                
                'doc_guide_agent.html' => 'doc_guide_agent.html'
            )

        );

        //docs for client view quanta wallet
        $list[] = array(
            'group_label' => '交換者のQuanta Wallet',
            'group_data' => array(
                'client_quantawallet.html' => 'Quanta Walletとは',
                'client_noteCryptoCurrency.html' => '暗号通貨の保管に関する諸注意',
                'client_backup.html' => 'セットアップ・バックアップ手順'
            )
        );

        //docs for client view redeem-info
        $list[] = array(
            'group_label' => '交換者のQNT還元サポート',
            'group_data' => array(
                'client_qntRedeemSupport.html' => 'QNT還元とは',
                'client_qntRedeemProcess.html' => 'QNT還元の流れ',
                'client_qntRedeemDoc.html' => 'QNT還元サポート資料'
            )
        );

        return $list;
    }
}

function getFileUploadErrorMessage($code) {
    $uploadErrMsg = array(
        'exceed_upload_size_limit' => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        'exceed_max_file_size' => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
        'file_upload_partially' => 'The uploaded file was only partially uploaded.',
        
        'missing_tmp_folder' => 'Missing a temporary folder.',
        'cannot_write_to_disk' => 'Failed to write file to disk.',
        'php_ext_err' => 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.',

        'exceed_post_size_limit' => 'The uploaded file exceeds the post_max_size directive in php.ini.',
        'check_write_permission' => 'Cannot save upload file. Check again the permission for docs folder.',

        'success' => 'ドキュメントが正常にアップロードされました。',
        'no_file_upload' => '添付ファイルを選択してください。',
        'empty_target_file' => 'ドキュメントの書類を選択してください。',
        'different_file_extension' => 'ファイルの拡張は間違いました。',
    );
    return @$uploadErrMsg[$code];
}

if ( ! function_exists('base_url_img'))
{
    function base_url_img($uri = '', $protocol = NULL)
    {
        $exits = file_exists($uri);
        log_message('debug', "$uri is $exits");
        if (!$exits) {
            $uri = str_replace('readimg', 'image', $uri);
        }
        return base_url($uri, $protocol);
    }
}