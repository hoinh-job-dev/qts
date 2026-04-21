<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $query_builder variables lets you determine whether or not to load
| the query builder class.
*/

$db['token'] = array(
        'hostname' => 'localhost',
        'username' => 'lotte_token_user',
        'password' => 'lotte_token',
        'database' => 'LotteToken',
        'dbdriver' => 'mysqli',
        'dbprefix' => '',
        'cache_on' => FALSE,
        'char_set' => 'utf8',
        'dbcollat' => 'utf8_general_ci'
    );
// local localhost
// staging 52.196.100.143
$db['kyc'] = array(
        'hostname' => 'localhost',
        'username' => 'kyc_user',
        'password' => 'kyc',
        'database' => 'Kyc',
        'dbdriver' => 'mysqli',
        'dbprefix' => '',
        'cache_on' => FALSE,
        'char_set' => 'utf8',
        'dbcollat' => 'utf8_general_ci'
    );

$active_group = 'token';