<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT">
    <meta name="robots" content="noindex">

    <title>ワンエイトアソシエーション</title>

    <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url('js/build/jquery-ui/jquery-ui.min.css'); ?>" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url('css/style.css'); ?>" />
    <?php if ('agent'==$role || 'operator'==$role || 'client'==$role) { ?>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url('css/page.css'); ?>" />
    <?php } else { ?>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url('css/top.css'); ?>" />
    <?php } ?>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url('css/common.css'); ?>" />

    <script type="text/javascript">
        var BASE_URL = '<?php echo base_url(); ?>',
            CONTROLLER = '<?php echo $this->router->fetch_class(); ?>',
            TESTNET_MODE = <?php echo $this->config->item('testnet_mode') === true ? 'true' : 'false'; ?>;
    </script>
    <script type="text/javascript" src="<?php echo base_url('js/jquery.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('js/build/jquery-ui/jquery-ui.min.js'); ?>"></script>
    <script src="<?php echo base_url('js/build/jquery.datetimepicker.full.min.js'); ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('css/jquery.datetimepicker.css'); ?>"/>
    <script type="text/javascript" src="<?php echo base_url('js/common/header.js'); ?>"></script>
</head>
<body class='l-bg'>
    <?php
    $role_agent_arr = array_keys($this->config->item('role_agent_commission'));
    $lowest_agent_role = $role_agent_arr[count($role_agent_arr)-1];
    ?>
    <?php if ('agent'==$role || 'operator'==$role || 'client'==$role ) { ?>
    <header class="l-header">
        <div class="header-logo">
                <a href="<?php if ('agent'==$role) { echo base_url('Agent/home'); } else if ('operator'==$role) { echo base_url('Operator/home'); } ?>"><h1><?php echo $title;?></h1></a>
        </div>
    </header><!-- /.l-header -->
    <?php } ?>
    <?php $roleLogin = $this->CI->getSessionRole(); ?>
    <!--begin client-->
    <?php if ('client'==$role) { ?>
    <nav class="l-nav">
        <div class="navstrip">
            <label for="menu-toggle" id="accordion_sp"><img src="<?php echo base_url('img/layout/nav/marvelous_w640px_menu.png'); ?>" width="30" height="22.5" alt="メニュー"></label>
            <input type="checkbox" id="menu-toggle"/>
            <ul class="nav-list" id="accordion_sp_in">
                <?php 
                // Agent list
                $roleAgentList = $this->config->item('role_agent_commission');
                // List agent can create child agent  
                $createAuthenList = array();
                foreach ($roleAgentList as $key => $value) {
                    array_push($createAuthenList, $key);
                }
                
                array_pop($createAuthenList); 
                $MobiAgentMenu = array(                           
                    array(
                        'text' => 'Quanta Wallet','method' => 'quantaWallet','isBtcSys' => true,
                    ),
                    array(
                        'text' => "QNT還元サポート",'method' => 'redeemInfo','isBtcSys' => true,
                    )
                ); ?>

                <?php
                foreach ($MobiAgentMenu as $menuItem){
                    $menuItem = (object) $menuItem;
                    if ($menuItem->isBtcSys == true) { ?>
                        <li>
                            <?php
                                if ($menuItem->method==$current_menu) {
                                    $activeClass = "active";
                                } else {
                                    $activeClass = "";
                                }
                            ?>
                            <a href="<?php echo base_url("Client/$menuItem->method"); ?>" class="<?php echo $activeClass ?>"> 
                                <?php echo $menuItem->text ?>   
                            </a>
                        </li>
                    <?php 
                    } 
                } ?>
            </ul>
        </div>
    </nav><!-- /.l-nav -->
    <section>
    <?php } ?>
    <!--end client-->
    <!--begin agent-->
    <?php if ('agent'==$role) { ?>
    <nav class="l-nav">
        <div class="navstrip">
            <label for="menu-toggle" id="accordion_sp"><img src="<?php echo base_url('img/layout/nav/marvelous_w640px_menu.png'); ?>" width="30" height="22.5" alt="メニュー"></label>
            <input type="checkbox" id="menu-toggle"/>
            <ul class="nav-list" id="accordion_sp_in">
                <?php 
                // Agent list
                $roleAgentList = $this->config->item('role_agent_commission');
                // List agent can create child agent  
                $createAuthenList = array();
                foreach ($roleAgentList as $key => $value) {
                    array_push($createAuthenList, $key);
                }
                // Last agent can't create child agent
                array_pop($createAuthenList); 
                $MobiAgentMenu = array(                           
                    array(
                        'text' => '交換者リンク作成','method' => 'linkClient','isBtcSys' => true, 'width' => '20', 'height' => '20', 'img' => 'img/layout/nav/marvelous_w640px_menu_icon_02.png',
                        'roles' => array()
                    ),
                    array(
                        'text' => '代理店リンク作成','method' => 'linkAgent','isBtcSys' => true, 'width' => '20', 'height' => '20', 'img' => 'img/layout/nav/marvelous_w640px_menu_icon_03.png',
                        'roles' => $createAuthenList
                    ),
                    array(
                        'text' => '交換者一覧','method' => 'viewClients','isBtcSys' => true, 'width' => '20', 'height' => '20', 'img' => 'img/layout/nav/marvelous_w640px_menu_icon_04.png',
                        'roles' => array()
                    ),
//                    array(
//                        'text' => 'コミッション','method' => 'viewCommission','isBtcSys' => true, 'width' => '20', 'height' => '20', 'img' => 'img/layout/nav/marvelous_w640px_menu_icon_05.png',
//                        'roles' => array()
//                    ),
                    array(
                        'text' => 'リンク一覧','method' => 'viewLinks','isBtcSys' => true,  'width' => '20', 'height' => '20', 'img' => 'img/layout/nav/marvelous_w640px_menu_icon_06.png',
                        'roles' => array()
                    )
                    // ,
                    // array(
                    //     'text' => '代理店ガイダンス','method' => 'guideAgent','isBtcSys' => true,  'width' => '20', 'height' => '20', 'img' => 'img/layout/nav/marvelous_w640px_menu_icon_03.png',
                    //     'roles' => array()
                    // )                             
                ); ?>
                <li><a href="<?php echo base_url('Agent/home'); ?>" <?php if ("home"==$current_menu) {echo "class='active'";} ?>><i><img src="<?php echo base_url('img/layout/nav/marvelous_w640px_menu_icon_01.png'); ?>" width="20" height="20" alt=""></i>アカウント情報</a></li>
                <?php 
                $loginData = $this->CI->getLoginData(); 
                $this->load->model('User_model', 'user');
                $user = $this->user->get_user_by_userhash($loginData['user_hash']);     
                foreach ($MobiAgentMenu as $menuItem){
                    $menuItem = (object) $menuItem;

                    if ($this->config->item('act_approved')<=intval($userstatus)  
                        && (isset($menuItem->roles) == false || empty($menuItem->roles) || in_array($roleLogin, $menuItem->roles))
                         && ("" != $user->btc_address)
                         && ($this->config->item('read_guide') == $user->rsv_char_2)
                    ){                          
                        if ($menuItem->method==$current_menu) {
                            $activeClass = "active";
                        } else {
                            $activeClass = "";
                        }                            
                    ?>
                        <?php if ("linkClient"==$current_menu && $lowest_agent_role!=$agentrank) { ?>
                        <li>
                            <a href="<?php echo base_url("Agent/$menuItem->method"); ?>" class="<?php echo $activeClass ?>"> 
                                <i><img src="<?php echo base_url($menuItem->img); ?>" width="<?php echo $menuItem->width; ?>" height="<?php echo $menuItem->height; ?>" alt="">
                                </i>   
                                <?php echo $menuItem->text ?>      
                            </a>
                        </li>
                        <?php }elseif("viewLinks"==$current_menu && "07"!=$agentrank){ ?>
                            <li>
                                <a href="<?php echo base_url("Agent/$menuItem->method"); ?>"  class="<?php echo $activeClass ?>"> 
                                    <i><img src="<?php echo base_url($menuItem->img); ?>" width="<?php echo $menuItem->width; ?>" height="<?php echo $menuItem->height; ?>" alt="">
                                    </i>   
                                    <?php echo $menuItem->text ?>      
                                </a>
                            </li>
                        <?php }else{ ?>
                            <li>
                                <a href="<?php echo base_url("Agent/$menuItem->method"); ?>"  class="<?php echo $activeClass ?>"> 
                                    <i><img src="<?php echo base_url($menuItem->img); ?>" width="<?php echo $menuItem->width; ?>" height="<?php echo $menuItem->height; ?>" alt="">
                                    </i>   
                                    <?php echo $menuItem->text ?>      
                                </a>
                            </li>
                        <?php } ?>
                        <?php
                    }
                } ?>
                <li><a href="javascript: void(0);" onclick="logout();">ログアウト</a></li>
            </ul>
        </div>
    </nav><!-- /.l-nav -->
    <section>
    <?php } ?>
    <!--end agent-->
    <!--begin operator-->
    <?php if ('operator'==$role) { ?>
    <nav class="l-nav">
        <div class="navstrip">
            <label for="menu-toggle" id="accordion_sp"><img src="<?php echo base_url('img/layout/nav/marvelous_w640px_menu.png'); ?>" width="30" height="22.5" alt="メニュー"></label>
            <input type="checkbox" id="menu-toggle"/>
            <ul class="nav-list" id="accordion_sp_in">
            <?php 
            $mobiOpeMenu = array(
                            array(
                                'text' => '審査対象一覧','method' => 'viewPersonal','isBtcSys' => true,
                            ),
                            array(
                                'text' => '注文審査対象','method' => 'reOrderConfirm','isBtcSys' => true,
                            ),
                            array(
                                'text' => '入金待ち一覧','method' => 'inputBanking','isBtcSys' => true,
                            ),
                            array(
                                'text' => 'JPY/BTC換金選択','method' => 'inputExchangedBtc','isBtcSys' => false,
                            ),
                            array(
                                'text' => 'JPY/BTC換金結果照会','method' => 'viewBankBtc','isBtcSys' => false,
                            ),
                            array(
                                'text' => '受領書発行対象','method' => 'makeToken','isBtcSys' => true,
                            ),
                            array(
                                'text' => '締め処理','method' => 'makeClosedOrder','isBtcSys' => true,
                            ),
                            array(
                                'text' => 'BTC送信対象','method' => 'viewApprovedToken','isBtcSys' => true,
                            ),
                            array(
                                'text' => 'コミッション審査対象','method' => 'viewCommissions','isBtcSys' => true,
                            ),
                            array(
                                'text' => '返金対象','method' => 'viewRefunds','isBtcSys' => true,
                            ),
                            array(
                                'text' => '登録者一覧','method' => 'home','isBtcSys' => true,
                            ),
                            array(
                                'text' => '注文実績','method' => 'orderlist','isBtcSys' => true,
                            ),
                            array(
                                'text' => '受領書発行実績','method' => 'receiptIssueToken','isBtcSys' => true,
                            ),
                            // BTC送信実績
                            array(
                                'text' => 'コミッション実績','method' => 'listCommissions','isBtcSys' => true,
                            ),
                            array(
                                'text' => 'アクティビティ','method' => 'activitylist','isBtcSys' => true,
                            ),
                            array(
                                'text' => '換金レート','method' => 'viewRate','isBtcSys' => true,
                            ),
                            array(
                                'text' => 'レート入力','method' => 'insertRate','isBtcSys' => true,
                            ),
                            array(
                                'text' => ($this->config->item('role_agent_label')[$role_agent_arr[0]]) . 'リンク発行','method' => 'linkAgent','isBtcSys' => true,
                            ),
                            array(
                                'text' => 'アカウント権限設定','method' => 'operatorManagement','isBtcSys' => true,
                            ),
                            array(
                                'text' => '受領書の再発行','method' => 'reissueToken','isBtcSys' => true,
                            ),
                            // array('text' => '請求用BTCアドレス登録','method' => 'inputBtcAddr','isBtcSys' => true,
                            // ),
                            array(
                                'text' => 'ドキュメントアップロード','method' => 'uploadDocs','isBtcSys' => true,
                            ),
                            array(
                                'text' => '環境の任意設定','method' => 'manageOptions','isBtcSys' => true,
                            ),
                            array(
                                'text' => 'パスワード変更','method' => 'changePasswd','isBtcSys' => true,
                            ),
                            array(
                                'text' => 'メールの再送信','method' => 'resendEmail','isBtcSys' => true,
                            ),
                            array(
                                'text' => '還元コードの変更','method' => 'editTokenList','isBtcSys' => true,
                            ),
                            array(
                                'text' => 'リディームリンク発行','method' => 'issueRedeemToken','isBtcSys' => true,
                            )
                        );
            ?>

            <?php
            $access_roles = $this->config->item('access_roles')['operator'];
            foreach ($mobiOpeMenu as $menuItem){
                $menuItem = (object) $menuItem;
                $roles = @$access_roles[strtolower($menuItem->method)];
                if (($menuItem->isBtcSys == true || $this->config->item('enable_banking') === true)  
                    && (isset($roles) == false || empty($roles) || in_array($roleLogin, $roles))
                ) {
                ?>
                    <li>
                        <a href="<?php echo base_url("Operator/$menuItem->method"); ?>" <?php if ($menuItem->method==$current_menu) {echo "class='active'";} ?>>
                            <?php if('linkAgent' != $menuItem->method){echo $menuItem->text; }else{ echo @$menuItem->preText . $menuItem->text;}  ?>
                        </a>
                    </li>
                <?php
                }
            }
            ?>
            <!--<li><a href="<?php echo base_url('docs/operator_flow.html'); ?>" target="_blank">[オペレータ マニュアル概要]</a></li>-->
            <li><a href="javascript: void(0);" onclick="logout();">ログアウト</a></li>
            </ul>
        </div>
    </nav><!-- /.l-nav -->
    <?php } ?>
    <!--operator-->