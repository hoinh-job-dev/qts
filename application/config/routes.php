<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
// Agent
$route['Agent/complete'] = "Agent/complete";
$route['Agent/login'] = "Agent/login";
$route['Agent/login/home'] = "Agent/home";
$route['Agent/home'] = "Agent/home";
$route['Agent/linkAgent'] = "Agent/linkAgent";
$route['Agent/linkagent'] = "Agent/linkAgent";
$route['Agent/guideAgent'] = "Agent/guideAgent";
$route['Agent/guideagent'] = "Agent/guideAgent";
$route['Agent/confirmGuideAgent'] = "Agent/confirmGuideAgent";
$route['Agent/confirmguideagent'] = "Agent/confirmGuideAgent";
$route['Agent/makeAgentLink'] = "Agent/makeAgentLink";
$route['Agent/makeAgentlink'] = "Agent/makeAgentLink";
$route['Agent/makeagentLink'] = "Agent/makeAgentLink";
$route['Agent/makeagentlink'] = "Agent/makeAgentLink";
$route['Agent/linkClient'] = "Agent/linkClient";
$route['Agent/linkclient'] = "Agent/linkClient";
$route['Agent/makeClientLink'] = "Agent/makeClientLink";
$route['Agent/makeClientlink'] = "Agent/makeClientLink";
$route['Agent/makeclientLink'] = "Agent/makeClientLink";
$route['Agent/makeclientlink'] = "Agent/makeClientLink";
$route['Agent/viewClients'] = "Agent/viewClients";
$route['Agent/viewclients'] = "Agent/viewClients";
$route['Agent/viewCommission'] = "Agent/viewCommission";
$route['Agent/viewcommission'] = "Agent/viewCommission";
$route['Agent/viewLinks'] = "Agent/viewLinks";
$route['Agent/viewlinks'] = "Agent/viewLinks";
$route['Agent/logout'] = "Agent/logout";
$route['Agent/setCommissionBtcAddress'] = "Agent/setCommissionBtcAddress";
$route['Agent/setPassword/home'] = "Agent/home";
$route['Agent/setPassword/(:any)'] = "Agent/setPassword/$1";
$route['Agent/setpassword/(:any)'] = "Agent/setpassword/$1";
$route['Agent/ask_password'] = "Agent/ask_password";
$route['Agent/resetPasswd/(:any)'] = "Agent/resetPasswd/$1";
$route['Agent/resetpasswd/(:any)'] = "Agent/resetPasswd/$1";
$route['Agent/updatePasswd'] = "Agent/updatePasswd";
$route['Agent/updatepasswd'] = "Agent/updatePasswd";
$route['Agent/changePasswd'] = "Agent/changePasswd";
$route['Agent/changepasswd'] = "Agent/changePasswd";
$route['Agent/confirmChangePasswd'] = "Agent/confirmChangePasswd";
$route['Agent/confirmchangePasswd'] = "Agent/confirmChangePasswd";
$route['Agent/(:any)'] = "Agent/regAccount/$1";
$route['Agent/(:any)'] = "Agent/regaccount/$1";

// Client
$route['Client/login'] = "Client/login";
$route['Client/home'] = "Client/home";
$route['Client/login/quantaWallet'] = "Client/quantaWallet";
$route['Client/quantaWallet'] = "Client/quantaWallet";
$route['Client/quantawallet'] = "Client/quantaWallet";
$route['Client/redeemInfo'] = "Client/redeemInfo";
$route['Client/redeeminfo'] = "Client/redeemInfo";

$route['Client/completeOrder'] = "Client/completeOrder";
$route['Client/completeorder'] = "Client/completeOrder";
$route['Client/addOrder'] = "Client/addOrder";
$route['Client/addorder'] = "Client/addOrder";
$route['Client/viewBtcAddr'] = "Client/viewBtcAddr";
$route['Client/viewBtcaddr'] = "Client/viewBtcAddr";
$route['Client/viewbtcAddr'] = "Client/viewBtcAddr";
$route['Client/viewbtcaddr'] = "Client/viewBtcAddr";
$route['Client/viewToken'] = "Client/viewToken";
$route['Client/viewtoken'] = "Client/viewToken";
$route['Client/getUsdBtcRate'] = "Client/getUsdBtcRate";
$route['Client/getusdbtcrate'] = "Client/getUsdBtcRate";
$route['Client/viewBtcAddr/(:any)'] = "Client/viewBtcAddr/$1";
$route['Client/viewbtcaddr/(:any)'] = "Client/viewBtcAddr/$1";
$route['Client/viewBtcAddr/(:any)/(:any)'] = "Client/viewBtcAddr/$1/$2";
$route['Client/viewbtcaddr/(:any)/(:any)'] = "Client/viewBtcAddr/$1/$2";
$route['Client/icoFinish'] = "Client/icoFinish";
$route['Client/icofinish'] = "Client/icoFinish";
$route['Client/(:any)'] = "Client/regAccount/$1";
$route['Client/(:any)'] = "Client/regaccount/$1";

// Operator
$route['Operator/login/home'] = "Operator/login";

// Others
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;