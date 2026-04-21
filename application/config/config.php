<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
| List setting for QTS
|   1.Newtwork protocol
|   2.Host name
|   3.Password md5 flag
|   4.Enable/Disable Auto-banking
|   5.Auto wallet servers and configurations
|   6.The blockheight
|■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
*/
// ■ 1.Newtwork protocol
// Product server uses HTTPS. And other environments use HTTP.
//$config['protocol'] = "https://";
$config['protocol'] = "http://";
// ■ 2.Host name
//$config['server_host'] = 'members.one8-association.co.jp';
//$config['server_host'] = 'staging.one8-association.co.jp';
//$config['server_host'] = 'dev.one8-association.co.jp';

$config['server_host'] = 'localhost';
// ■ 3.Password md5 flag
$config['is_hash_password'] = true;

$config['password_salt'] = 'QTS_S41t_';

$config['base_url'] = $config['protocol'] . $config['server_host'] . '/QT';

// domain name
$config['domain'] = $config['protocol'] . $config['server_host'];

// ■ 4.Enable/Disable Auto-banking
/*$config['enable_banking'] = true;

// ■ 5.Auto wallet servers and configurations
$config['testnet_mode'] = true;
if($config['testnet_mode'] === true) {
	// Block Explorer URL to synchronize block
	$config['block_explorer_url'] = 'http://blocktest.altaapps.io:3002/api';

	// Auto wallet API server URL to send to refund, hot, cold, operator commission wallet.
	//$config['wallet-server'] = 'http://bws.altaapps.io:5555/api';	// DEV server
	$config['wallet-server'] = 'http://139.162.56.20:5555/api';	// DEV server for Auto banking
	//$config['wallet-server'] = 'http://localhost:5555/api';	// local server

	// Auto wallet API server URL to allow operator to send commission to agencies.
	//$config['ope-wallet-server'] = 'http://bws.altaapps.io:5556/api';	// DEV server
	$config['ope-wallet-server'] = 'http://139.162.56.20:5556/api';	// DEV server for Auto banking
	//$config['ope-wallet-server'] = 'http://localhost:5556/api';	// local server
}
else {
	// Block Explorer URL to synchronize block
	$config['block_explorer_url'] = 'http://blockbtc.altaapps.io/insight-api';

	// Auto wallet API server URL to send to refund, hot, cold, operator commission wallet.
	$config['wallet-server'] = 'http://bws.altaapps.io:6666/api';	// DEV server
	//$config['wallet-server'] = 'http://139.162.56.20:6666/api';	// DEV server for Auto banking
	//$config['wallet-server'] = 'http://localhost:6666/api';	// local server

	// Auto wallet API server URL to allow operator to send commission to agencies.
	$config['ope-wallet-server'] = 'http://bws.altaapps.io:6667/api';	// DEV server
	//$config['ope-wallet-server'] = 'http://139.162.56.20:6667/api';	// DEV server for Auto banking
	//$config['ope-wallet-server'] = 'http://localhost:6667/api';	// local server
}*/

/*
|--------------------------------------------------------------------------
| The blockheight
| The start blockheight we want to begin synchronize
|--------------------------------------------------------------------------
*/
// ■ 6.The blockheight
/*$config['start_blockheight'] = 949059;*/

/*
|--------------------------------------------------------------------------
| Percentage of BTC amount that will be sent to Hot and Cold BTC address
| Please note that SUM of hot_btc_rate and cold_btc_rate MUST BE less then or equal to 80%, because Agent take 20%.
|--------------------------------------------------------------------------
*/
/*$config['hot_btc_rate'] = 30;	// in percentage
$config['cold_btc_rate'] = 50;	// in percentage
$config['commission_btc_rate'] = 20;	// in percentage
$config['special_commission_btc_rate'] = 0;	// in percentage
*/

$config['limit_records'] = 10;	// Number of records will be processed when send HOT, COLD, COMMISSION, REFUND

// ■ 7.Show/Hide radio buttons on create agent link when logged-in as Agent
/*$config['show_agent_role_radio'] = false;*/


/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
|
| Typically this will be your index.php file, unless you've renamed it to
| something else. If you are using mod_rewrite to remove the page set this
| variable so that it is blank.
|
*/
$config['index_page'] = '';

/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
|
| This item determines which server global should be used to retrieve the
| URI string.  The default setting of 'REQUEST_URI' works for most servers.
| If your links do not seem to work, try one of the other delicious flavors:
|
| 'REQUEST_URI'    Uses $_SERVER['REQUEST_URI']
| 'QUERY_STRING'   Uses $_SERVER['QUERY_STRING']
| 'PATH_INFO'      Uses $_SERVER['PATH_INFO']
|
| WARNING: If you set this to 'PATH_INFO', URIs will always be URL-decoded!
*/
$config['uri_protocol']	= 'REQUEST_URI';

/*
|--------------------------------------------------------------------------
| URL suffix
|--------------------------------------------------------------------------
|
| This option allows you to add a suffix to all URLs generated by CodeIgniter.
| For more information please see the user guide:
|
| https://codeigniter.com/user_guide/general/urls.html
*/
$config['url_suffix'] = '';

/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
|
| This determines which set of language files should be used. Make sure
| there is an available translation if you intend to use something other
| than english.
|
*/
//$config['language']	= 'english';
$config['language']	= 'japanese';

/*
|--------------------------------------------------------------------------
| Default Character Set
|--------------------------------------------------------------------------
|
| This determines which character set is used by default in various methods
| that require a character set to be provided.
|
| See http://php.net/htmlspecialchars for a list of supported charsets.
|
*/
$config['charset'] = 'UTF-8';

/*
|--------------------------------------------------------------------------
| Enable/Disable System Hooks
|--------------------------------------------------------------------------
|
| If you would like to use the 'hooks' feature you must enable it by
| setting this variable to TRUE (boolean).  See the user guide for details.
|
*/
$config['enable_hooks'] = FALSE;

/*
|--------------------------------------------------------------------------
| Class Extension Prefix
|--------------------------------------------------------------------------
|
| This item allows you to set the filename/classname prefix when extending
| native libraries.  For more information please see the user guide:
|
| https://codeigniter.com/user_guide/general/core_classes.html
| https://codeigniter.com/user_guide/general/creating_libraries.html
|
*/
$config['subclass_prefix'] = 'MY_';

/*
|--------------------------------------------------------------------------
| Composer auto-loading
|--------------------------------------------------------------------------
|
| Enabling this setting will tell CodeIgniter to look for a Composer
| package auto-loader script in application/vendor/autoload.php.
|
|	$config['composer_autoload'] = TRUE;
|
| Or if you have your vendor/ directory located somewhere else, you
| can opt to set a specific path as well:
|
|	$config['composer_autoload'] = '/path/to/vendor/autoload.php';
|
| For more information about Composer, please visit http://getcomposer.org/
|
| Note: This will NOT disable or override the CodeIgniter-specific
|	autoloading (application/config/autoload.php)
*/
$config['composer_autoload'] = FALSE;

/*
|--------------------------------------------------------------------------
| Allowed URL Characters
|--------------------------------------------------------------------------
|
| This lets you specify which characters are permitted within your URLs.
| When someone tries to submit a URL with disallowed characters they will
| get a warning message.
|
| As a security measure you are STRONGLY encouraged to restrict URLs to
| as few characters as possible.  By default only these are allowed: a-z 0-9~%.:_-
|
| Leave blank to allow all characters -- but only if you are insane.
|
| The configured value is actually a regular expression character group
| and it will be executed as: ! preg_match('/^[<permitted_uri_chars>]+$/i
|
| DO NOT CHANGE THIS UNLESS YOU FULLY UNDERSTAND THE REPERCUSSIONS!!
|
*/
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';

/*
|--------------------------------------------------------------------------
| Enable Query Strings
|--------------------------------------------------------------------------
|
| By default CodeIgniter uses search-engine friendly segment based URLs:
| example.com/who/what/where/
|
| By default CodeIgniter enables access to the $_GET array.  If for some
| reason you would like to disable it, set 'allow_get_array' to FALSE.
|
| You can optionally enable standard query string based URLs:
| example.com?who=me&what=something&where=here
|
| Options are: TRUE or FALSE (boolean)
|
| The other items let you set the query string 'words' that will
| invoke your controllers and its functions:
| example.com/index.php?c=controller&m=function
|
| Please note that some of the helpers won't work as expected when
| this feature is enabled, since CodeIgniter is designed primarily to
| use segment based URLs.
|
*/
$config['allow_get_array'] = TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';

/*
|--------------------------------------------------------------------------
| Error Logging Threshold
|--------------------------------------------------------------------------
|
| You can enable error logging by setting a threshold over zero. The
| threshold determines what gets logged. Threshold options are:
|
|	0 = Disables logging, Error logging TURNED OFF
|	1 = Error Messages (including PHP errors)
|	2 = Debug Messages
|	3 = Informational Messages
|	4 = All Messages
|
| You can also pass an array with threshold levels to show individual error types
|
| 	array(2) = Debug Messages, without Error Messages
|
| For a live site you'll usually only enable Errors (1) to be logged otherwise
| your log files will fill up very fast.
|
*/
$config['log_threshold'] = 2;

/*
|--------------------------------------------------------------------------
| Error Logging Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| application/logs/ directory. Use a full server path with trailing slash.
|
*/
$config['log_path'] = 'application/logs/';

/*
|--------------------------------------------------------------------------
| Log File Extension
|--------------------------------------------------------------------------
|
| The default filename extension for log files. The default 'php' allows for
| protecting the log files via basic scripting, when they are to be stored
| under a publicly accessible directory.
|
| Note: Leaving it blank will default to 'php'.
|
*/
$config['log_file_extension'] = '.log';

/*
|--------------------------------------------------------------------------
| Log File Permissions
|--------------------------------------------------------------------------
|
| The file system permissions to be applied on newly created log files.
|
| IMPORTANT: This MUST be an integer (no quotes) and you MUST use octal
|            integer notation (i.e. 0700, 0644, etc.)
*/
$config['log_file_permissions'] = 0666;

/*
|--------------------------------------------------------------------------
| Date Format for Logs
|--------------------------------------------------------------------------
|
| Each item that is logged has an associated date. You can use PHP date
| codes to set your own date formatting
|
*/
$config['log_date_format'] = 'Y-m-d H:i:s';

/*
|--------------------------------------------------------------------------
| Error Views Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| application/views/errors/ directory.  Use a full server path with trailing slash.
|
*/
$config['error_views_path'] = '';

/*
|--------------------------------------------------------------------------
| Cache Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| application/cache/ directory.  Use a full server path with trailing slash.
|
*/
$config['cache_path'] = '';

/*
|--------------------------------------------------------------------------
| Cache Include Query String
|--------------------------------------------------------------------------
|
| Whether to take the URL query string into consideration when generating
| output cache files. Valid options are:
|
|	FALSE      = Disabled
|	TRUE       = Enabled, take all query parameters into account.
|	             Please be aware that this may result in numerous cache
|	             files generated for the same page over and over again.
|	array('q') = Enabled, but only take into account the specified list
|	             of query parameters.
|
*/
$config['cache_query_string'] = FALSE;

/*
|--------------------------------------------------------------------------
| Encryption Key
|--------------------------------------------------------------------------
|
| If you use the Encryption class, you must set an encryption key.
| See the user guide for more info.
|
| https://codeigniter.com/user_guide/libraries/encryption.html
|
*/
$config['encryption_key'] = '806deb5a285e769a9471fa8e73393eab';

/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
|
| 'sess_driver'
|
|	The storage driver to use: files, database, redis, memcached
|
| 'sess_cookie_name'
|
|	The session cookie name, must contain only [0-9a-z_-] characters
|
| 'sess_expiration'
|
|	The number of SECONDS you want the session to last.
|	Setting to 0 (zero) means expire when the browser is closed.
|
| 'sess_save_path'
|
|	The location to save sessions to, driver dependent.
|
|	For the 'files' driver, it's a path to a writable directory.
|	WARNING: Only absolute paths are supported!
|
|	For the 'database' driver, it's a table name.
|	Please read up the manual for the format with other session drivers.
|
|	IMPORTANT: You are REQUIRED to set a valid save path!
|
| 'sess_match_ip'
|
|	Whether to match the user's IP address when reading the session data.
|
|	WARNING: If you're using the database driver, don't forget to update
|	         your session table's PRIMARY KEY when changing this setting.
|
| 'sess_time_to_update'
|
|	How many seconds between CI regenerating the session ID.
|
| 'sess_regenerate_destroy'
|
|	Whether to destroy session data associated with the old session ID
|	when auto-regenerating the session ID. When set to FALSE, the data
|	will be later deleted by the garbage collector.
|
| Other session cookie settings are shared with the rest of the application,
| except for 'cookie_prefix' and 'cookie_httponly', which are ignored here.
|
*/
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'ci_session';
$config['sess_expiration'] = 3600;
$config['sess_expire_on_close'] = TRUE;
$config['sess_save_path'] = NULL;
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
|
| 'cookie_prefix'   = Set a cookie name prefix if you need to avoid collisions
| 'cookie_domain'   = Set to .your-domain.com for site-wide cookies
| 'cookie_path'     = Typically will be a forward slash
| 'cookie_secure'   = Cookie will only be set if a secure HTTPS connection exists.
| 'cookie_httponly' = Cookie will only be accessible via HTTP(S) (no javascript)
|
| Note: These settings (with the exception of 'cookie_prefix' and
|       'cookie_httponly') will also affect sessions.
|
*/
$config['cookie_prefix']	= '';
$config['cookie_domain']	= '';
$config['cookie_path']		= '/';
$config['cookie_secure']	= FALSE;
$config['cookie_httponly'] 	= FALSE;

/*
|--------------------------------------------------------------------------
| Standardize newlines
|--------------------------------------------------------------------------
|
| Determines whether to standardize newline characters in input data,
| meaning to replace \r\n, \r, \n occurrences with the PHP_EOL value.
|
| This is particularly useful for portability between UNIX-based OSes,
| (usually \n) and Windows (\r\n).
|
*/
$config['standardize_newlines'] = FALSE;

/*
|--------------------------------------------------------------------------
| Global XSS Filtering
|--------------------------------------------------------------------------
|
| Determines whether the XSS filter is always active when GET, POST or
| COOKIE data is encountered
|
| WARNING: This feature is DEPRECATED and currently available only
|          for backwards compatibility purposes!
|
*/
$config['global_xss_filtering'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cross Site Request Forgery
|--------------------------------------------------------------------------
| Enables a CSRF cookie token to be set. When set to TRUE, token will be
| checked on a submitted form. If you are accepting user data, it is strongly
| recommended CSRF protection be enabled.
|
| 'csrf_token_name' = The token name
| 'csrf_cookie_name' = The cookie name
| 'csrf_expire' = The number in seconds the token should expire.
| 'csrf_regenerate' = Regenerate token on every submission
| 'csrf_exclude_uris' = Array of URIs which ignore CSRF checks
*/
$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array();

/*
|--------------------------------------------------------------------------
| Output Compression
|--------------------------------------------------------------------------
|
| Enables Gzip output compression for faster page loads.  When enabled,
| the output class will test whether your server supports Gzip.
| Even if it does, however, not all browsers support compression
| so enable only if you are reasonably sure your visitors can handle it.
|
| Only used if zlib.output_compression is turned off in your php.ini.
| Please do not use it together with httpd-level output compression.
|
| VERY IMPORTANT:  If you are getting a blank page when compression is enabled it
| means you are prematurely outputting something to your browser. It could
| even be a line of whitespace at the end of one of your scripts.  For
| compression to work, nothing can be sent before the output buffer is called
| by the output class.  Do not 'echo' any values with compression enabled.
|
*/
$config['compress_output'] = FALSE;

/*
|--------------------------------------------------------------------------
| Master Time Reference
|--------------------------------------------------------------------------
|
| Options are 'local' or any PHP supported timezone. This preference tells
| the system whether to use your server's local time as the master 'now'
| reference, or convert it to the configured one timezone. See the 'date
| helper' page of the user guide for information regarding date handling.
|
*/
$config['time_reference'] = 'local';

/*
|--------------------------------------------------------------------------
| Rewrite PHP Short Tags
|--------------------------------------------------------------------------
|
| If your PHP installation does not have short tag support enabled CI
| can rewrite the tags on-the-fly, enabling you to utilize that syntax
| in your view files.  Options are TRUE or FALSE (boolean)
|
| Note: You need to have eval() enabled for this to work.
|
*/
$config['rewrite_short_tags'] = FALSE;

/*
|--------------------------------------------------------------------------
| Reverse Proxy IPs
|--------------------------------------------------------------------------
|
| If your server is behind a reverse proxy, you must whitelist the proxy
| IP addresses from which CodeIgniter should trust headers such as
| HTTP_X_FORWARDED_FOR and HTTP_CLIENT_IP in order to properly identify
| the visitor's IP address.
|
| You can use both an array or a comma-separated list of proxy addresses,
| as well as specifying whole subnets. Here are a few examples:
|
| Comma-separated:	'10.0.1.200,192.168.5.0/24'
| Array:		array('10.0.1.200', '192.168.5.0/24')
*/
$config['proxy_ips'] = '';


/*
|--------------------------------------------------------------------------
| Default id of auto process
|--------------------------------------------------------------------------
|
| The id of auto process
|
*/
$config['AUTO_ID'] = 999999999;

// Message below are optional
$config['hot_wallet_message'] = 'Send to HOT wallet';	// Message to send along with HOT BTC amount
$config['cold_wallet_message'] = 'Send to COLD wallet';	// Message to send along with COLD BTC amount
$config['hot_cold_wallet_general_message'] = 'Send to HOT & COLD wallet';	// General Message to send along with HOT & COLD BTC amount
$config['refund_wallet_message'] = 'Send to REFUND wallet';	// Message to send along with REFUND BTC amount
$config['special_wallet_message'] = 'Send to SPECIAL wallet';	// Message to send along with SPECIAL BTC amount
$config['operator_wallet_message'] = 'Send commision to OPERATOR wallet';	// Message to send along with OPERATOR commission BTC amount
$config['agent_wallet_message'] = 'Send commision to each Agent';	// Message to send to each agent address along with BTC amount to agent
$config['agent_wallet_general_message'] = 'Send commision to Agent';	// General Message to send along with BTC amount to agent

/*
|--------------------------------------------------------------------------
| Auto wallet response code
|--------------------------------------------------------------------------
*/
$config['auto_wallet_response'] = array(
	'success' => 'BTC has been sent successfully.',
	'fail' => 'Failed to send. Unknow response.',

	'ADDRESS_IS_EMPTY' => 'Address is empty.',
	'BALANCE_NOT_ENOUGH_OR_LOCKED' => 'Not enough balance or the balance has been locked cause of previous transaction has not been resolved.',
	'CANNOT_CHECK_BALANCE' => 'Cannot get balance before send.',
	'CANNOT_CREATE_ADDRESS' => 'Cannot create address.',
	'CANNOT_CREATE_PROPOSAL_TX' => 'Cannot create proposal transaction.',
	'CANNOT_GET_BALANCE' => 'Cannot get balance.',
	'CANNOT_GET_STATUS' => 'Cannot get wallet status.',
	'CANNOT_IMPORT_SEED_FROM_MNEMONIC' => 'Cannot import wallet. Check again the mnemonic and password (if any).',
	'CANNOT_PULISH_TX_PROPOSAL' => 'Cannot publish proposal transaction.',
	'CANNOT_RESTORE_WALLET' => 'Cannot restore wallet from seed. Check again the seed or password (if any).',
	'CANNOT_SIGN' => 'Cannot sign proposal transaction.',
	'COLD_BTC_ADDR_NOT_SET' => 'COLD BTC address is not set',
	'EMPTY_SEED' => 'The seed (passphrase) is empty.',
	'EXCEPTION' => 'Cannot comunicate with server. Unknow error.',
	'HOT_BTC_ADDR_NOT_SET' => 'HOT BTC address is not set',
	'INVALID_ADDRESS' => 'Address is invalid.',
	'INVALID_AMOUNT' => 'Amount is zero or negative.',
	'INVALID_BWC_INSTANCE' => 'Bitcoin Wallet Client instance has not been created.',
	'INVALID_COLD_BTC_ADDR' => 'Invalid COLD BTC address.',
	'INVALID_COLD_WALLET_BTC_AMOUNT' => 'Invalid COLD wallet BTC amount.',
	'INVALID_HOT_BTC_ADDR' => 'Invalid HOT BTC address.',
	'INVALID_HOT_WALLET_BTC_AMOUNT' => 'Invalid HOT wallet BTC amount.',
	'INVALID_NUM_OF_ADDRESS' => 'Invalid number of addresses to create.',
	'INVALID_REFUND_BTC_ADDR' => 'Invalid REFUND BTC address.',
	'INVALID_REQUEST' => 'Invalid request.',
	'INVALID_REQUEST_DATA' => 'Invalid request data.',
	'INVALID_REQUEST_NUMBER' => 'Number of addresses is zero or nagative.',
	'MISSING_AMOUNT' => 'Missing the amount to process.',
	'MISSING_COLD_WALLET_BTC_AMOUNT' => 'Missing COLD wallet BTC amount.',
	'MISSING_HOT_WALLET_BTC_AMOUNT' => 'Missing HOT wallet BTC amount.',
	'MISSING_REQUEST_DATA' => 'Missing request data.',
	'NOT_SUPPORT_NOW' => 'Process failed because some of feature is not supported.',
	'NO_ADDRESS_CREATED' => 'Cannot create any address.',
	'NO_VALID_DATA' => 'There is no valid data item.',
	'NO_WALLET_LOADED' => 'Cannot load wallet from seed. Check again the seed or password (if any).',
	'REFUND_BTC_ADDR_NOT_SET' => 'REFUND BTC address is not set.',
	'REQUEST_DATA_IS_EMPTY' => 'Request data is empty.',
	'SEED_IS_EMPTY' => 'Missing wallet seed.',
	'SIGN_ERROR' => 'Failed to sign proposal transaction.',
	'SOCKET_ALREADY_STARTED' => 'Socket already started on another copayer of this wallet.',
	'WALLET_NOT_LOADED' => 'No wallet available to send.',
	'WRONG_NUMBER_OF_SEED_WORDS' => 'Cannot load wallet from seed. Number of words in seed should be 12 words.',
	'COMMISSION_BTC_ADDR_NOT_SET' => 'Commission BTC address is not set.',
	'INVALID_COMMISSION_BTC_ADDR' => 'Invalid Commission BTC address.',
	'MISSING_COMMISSION_WALLET_BTC_AMOUNT' => 'Missing Commission BTC amount.',
	'INVALID_COMMISSION_WALLET_BTC_AMOUNT' => 'Commission BTC amount is zero or nagative.',
	'SPECIAL_COMMISSION_BTC_ADDR_NOT_SET' => 'Special Commission BTC address is not set.',
	'INVALID_SPECIAL_COMMISSION_BTC_ADDR' => 'Invalid Special Commission BTC address.',
	'MISSING_SPECIAL_COMMISSION_WALLET_BTC_AMOUNT' => 'Missing Special Commission BTC amount.',
	'INVALID_SPECIAL_COMMISSION_WALLET_BTC_AMOUNT' => 'Special Commission BTC amount is zero or nagative.',
);

/*
|--------------------------------------------------------------------------
| Values for checking order
|--------------------------------------------------------------------------
*/
/*$config['amount_rules'] = array(
	'min_amount' => 1000,
	'max_amount' => 4800,
	'monthly_amount' => 24000,
	'dif_percentage' => 5,	// in percentage
	'num_days' => 30,
    'diff_percent_check' =>10, // check if amount different more than 10% or not
);*/

$config['fields_separator'] = '~:~';
