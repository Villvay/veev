<?php
header('Content-type: text/html; charset=utf-8');
$db_connection = false;


// RESOLVE REQUEST URL
$server_name = $_SERVER['SERVER_NAME'];
define ('BASE_URL', PROTOCOL.'://'.$server_name.(PORT==''?'':':'.PORT).PATH);
//define ('BASE_URL', (defined(PROTOCOL)?PROTOCOL:'http').'://'.$server_name.(defined(PORT) && PORT==''? '':':'.PORT).PATH);

$subdomain = explode('.', $server_name);
if (count($subdomain) > 2)
	define ('SUBDOMAIN', $subdomain[0]);
else
	define ('SUBDOMAIN', '');

$query_string = substr($_SERVER['REQUEST_URI'], strlen(PATH));

$params = explode('/', $query_string);
$params_count = count($params);


//	SESSION RELATED
include 'interfaces/user_tracking.php';
//ini_set('session.cookie_domain', ltrim($server_name, SUBDOMAIN.'.'));
session_start();
if (isset($_SESSION['user']))
	$user = $_SESSION['user'];
else
	$user = array('id' => -1, 'cid' => -1, 'lang' => 'en', 'timezone' => 'UTC', 'auth' => json_decode(PUBLIC_MODULES, true));

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : $user['lang'];
if (file_exists('data/lang/'.$lang.'.json'))
	$lex = file_get_contents('data/lang/'.$lang.'.json');
else
	$lex = file_get_contents('data/lang/'.$user['lang'].'.json');
$lex = json_decode(substr($lex, 3), true);

date_default_timezone_set($user['timezone']);


// Determine the Module
$module = array_shift($params);
if ($module == '')
	$module = 'index';


// Determine the Method
if ($params_count < 2)
	$method = 'index';
else
	$method = array_shift($params);
if ($method == '')
	$method = 'index';


// Append GET, POST to params
foreach ($_POST as $key => $val)
	$params[$key] = $val;

require_once 'render.php';

$module = str_replace('-', '_', $module);


// Include the Module
$submodule = false;
if (file_exists('modules/'.$module.'/@'.$module.'.module.php')){
	require_once 'modules/'.$module.'/@'.$module.'.module.php';
	if (is_dir('modules/'.$module.'/'.$method)){
		$submodule = $method;
		$method = array_shift($params);
		$method = str_replace('-', '_', $method);
		if ($method == '')
			$method = $submodule;
		/*if (file_exists('modules/'.$module.'/@'.$submodule.'.module.php'))require_once 'modules/'.$module.'/@'.$submodule.'.module.php';else*/
		if (file_exists('modules/'.$module.'/'.$submodule.'/@'.$submodule.'.module.php'))
			require_once 'modules/'.$module.'/'.$submodule.'/@'.$submodule.'.module.php';
	}
}
else{
	array_unshift($params, $method);
	$method = $module;
	$module = 'index';
	require_once 'modules/index/@index.module.php';
}
$method = str_replace('-', '_', $method);


// Call the Method
if (function_exists($method)){
	$data = $method($params);
	if ($submodule)
		$yield = render_view('modules/'.$module.'/'.$submodule.'/'.$method.'.php', $data);
	else
		$yield = render_view('modules/'.$module.'/'.$method.'.php', $data);
	if (isset($template_file) && $template_file != '' && (!isset($params['format']) || $params['format'] != 'js'))
		$yield = render_template($template_file, $yield, (isset($data['html_head']) ? $data['html_head'] : false));
	// =================
	global $HTTP_ACCEPT_ENCODING;
	if (strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false)
		$encoding = 'x-gzip';
	else if (strpos($HTTP_ACCEPT_ENCODING, 'gzip') !== false)
		$encoding = 'gzip';
	else
		$encoding = false;
	if ($encoding){
		$size = strlen($yield);
		if ($size > 2048){
			header('Content-Encoding: '.$encoding);
			print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
			$yield = gzcompress($yield, 9);
		}
	}
	echo $yield;
}
else{
	require_once 'templates/error_404.php';
}


function connect_database($database = false){
	global $db_connection;
	if ($db_connection == false){
		include 'interfaces/database.php';
	}
	$db_connection = new MySQL($database);
	return $db_connection;
}

if ($db_connection != false){
	$db_connection->close();
}


function redirect($module, $method = false, $params = false, $redirect_after = false){
	if ($redirect_after != false)
		$_SESSION['REDIRECT_AFTER_SIGNIN'] = $redirect_after;
	$params_list = '';
	if ($params)
		foreach($params as $par)
			$params_list .= '/'.$par;
	if(!$method)
		$method = 'index';
	header('location:'.BASE_URL.$module.'/'.$method.$params_list);
	die();
}

?>