<?php

$config['access_roles'] = array(
    'operator' => array(
        'viewpersonal' =>  array()
        ,'confirmpersonalstatus' =>  array()
        ,'reorderconfirm' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money'],
            $this->config['role_ope_order']
        )
        ,'inputbanking' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money'],
        )
        ,'confirmBanking' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money'],
        )
        ,'inputexchangedbtc' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'confirmexchangedbtc' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'viewbankbtc' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'maketoken' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'confirmtoken' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'makeclosedorder' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'confirmclosedorder' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'viewapprovedtoken' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'confirmapprovedtoken' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'viewcommissions' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'confirmcommissionstatus' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'viewrefunds' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'confirmrefunds' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'home' =>  array()
        ,'orderlist' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money'],
            $this->config['role_ope_order']
        )
        ,'confirmorder' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money'],
            $this->config['role_ope_order']
        )
        ,'receiptissuetoken' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'listcommissions' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'activitylist' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator']
        )
        ,'confirmactivitylist' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator']
        )
        ,'viewrate' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money']
        )
        ,'insertrate' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator']
        )
        ,'linkagent' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator']
        )
        ,'operatormanagement' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator']
        )
        ,'reissuetoken' =>  array(
            $this->config['role_sysadmin']
        )
        ,'inputbtcaddr' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money'],
            $this->config['role_ope_order']
        )
        ,'confirmbtcaddr' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
            $this->config['role_ope_money'],
            $this->config['role_ope_order']
        )
        ,'uploaddocs' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator']
        )
        ,'confirmuploaddocs' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator']
        )
        ,'outputcsv' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator']
        )
        , 'manageoptions' => array(
            $this->config['role_sysadmin']
        )
        ,'resendemail' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator']
        )
        ,'edittokenlist' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator']
        )
        ,'issueredeemtoken' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator']
        )
        ,'deleteEmailRedeem' =>  array(
            $this->config['role_sysadmin'],
            $this->config['role_operator'],
        )
    ));
