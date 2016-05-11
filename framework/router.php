<?php
header('Content-type: text/html; charset=utf-8');
header('X-Powered-By: Veev by Vishva@Villvay.com');
$db_connection = false;


// RESOLVE REQUEST URL
$server_name = $_SERVER['SERVER_NAME'];
define ('BASE_URL', PROTOCOL.'://'.$server_name.(PORT==''?'':':'.PORT).PATH);
//define ('BASE_URL', (defined(PROTOCOL)?PROTOCOL:'http').'://'.$server_name.(!defined(PORT) || PORT==''? '':':'.PORT).PATH);

$subdomain = explode('.', $server_name);
if (count($subdomain) > 2)
	define ('SUBDOMAIN', $subdomain[0]);
else
	define ('SUBDOMAIN', '');

$query_string = substr($_SERVER['REQUEST_URI'], strlen(PATH));
if (strpos($query_string, '?') !== false)
	$query_string = substr($query_string, 0, strpos($query_string, '?'));

$params = explode('/', $query_string);
$params_count = count($params);


//	SESSION/ACL RELATED
session_start();
ini_set('session.cookie_domain', ltrim($server_name, SUBDOMAIN.'.'));
include 'interfaces/user_tracking.php';
global $acl;
$acl = array('view' => true);
$db = connect_database();
$login = $db->query('SELECT id, user_id, useragent, session FROM login WHERE cookie = \''.$user_id.'\' AND (remember = 1 OR session = \''.session_id().'\')');
if ($login = row_assoc($login)){
	if (!isset($_SESSION['USERAGENT'])){	//	Protect from session and cookie hijacking
		include 'interfaces/user_agent_parser.php';
		$_SESSION['USERAGENT'] = parse_user_agent($_SERVER['HTTP_USER_AGENT']);
	}
	$login['useragent'] = json_decode($login['useragent'], true);
	if ($login['useragent']['platform'] != $_SESSION['USERAGENT']['platform'] || $login['useragent']['browser'] != $_SESSION['USERAGENT']['browser']){
		$acl['delete'] = true;				//	Cookie does not match Browser and OS
		$db->query('DELETE FROM login WHERE id = '.$login['id']);
	}
	else{
		$user = row_assoc($db->query('SELECT id, cid, email, username, `password`, lang, timezone, auth FROM `user` WHERE id = '.$login['user_id']));
		$user['auth'] = array_merge(json_decode(PUBLIC_MODULES, true), json_decode($user['auth'], true));
	}
	if ($login['session'] != session_id()){
		$acl['edit'] = true;
		$db->update('login', array('id' => $login['id'], 'session' => session_id(), 'useragent' => json_encode($_SESSION['USERAGENT'])));
	}
}
if (!isset($user))
	$user = array('id' => -1, 'cid' => -1, 'lang' => DEFAULT_LANGUAGE, 'timezone' => DEFAULT_TIMEZONE, 'auth' => json_decode(PUBLIC_MODULES, true));

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : $user['lang'];
if (file_exists('data/lang/'.$lang.'.json'))
	$lex = file_get_contents('data/lang/'.$lang.'.json');
else
	$lex = file_get_contents('data/lang/'.DEFAULT_LANGUAGE.'.json');
$lex = json_decode(substr($lex, 3), true);

date_default_timezone_set($user['timezone']);

function checkIfAuthorized($user, $module, $submodule = false){
	if (!isset($user['auth'][$module.($submodule==false?'':'/'.$submodule)]))
		return false;
	else
		return array_flip($user['auth'][$module.($submodule==false?'':'/'.$submodule)]);
}


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
foreach ($_GET as $key => $val)
	$params[$key] = $val;

require_once 'render.php';

$module = str_replace('-', '_', $module);


// Include the Module
$submodule = false;
if (file_exists('modules/'.$module.'/@'.$module.'.module.php')){
	$acl = checkIfAuthorized($user, $module);
	if ($acl !== false){
		require_once 'modules/'.$module.'/@'.$module.'.module.php';
		//
		if (is_dir('modules/'.$module.'/'.$method)){
			$submodule = $method;
			$method = array_shift($params);
			$method = str_replace('-', '_', $method);
			if ($method == '')
				$method = $submodule;
			/*if (file_exists('modules/'.$module.'/@'.$submodule.'.module.php'))require_once 'modules/'.$module.'/@'.$submodule.'.module.php';else*/
			if (file_exists('modules/'.$module.'/'.$submodule.'/@'.$submodule.'.module.php')){
				$acl = checkIfAuthorized($user, $module, $submodule);
				if ($acl !== false)
					require_once 'modules/'.$module.'/'.$submodule.'/@'.$submodule.'.module.php';
			}
		}
	}
	if ($acl === false)
		require_once 'templates/error_401.php';
}
else{
	$acl = checkIfAuthorized($user, 'index');
	if ($acl !== false){
		array_unshift($params, $method);
		$method = $module;
		$module = 'index';
		require_once 'modules/index/@index.module.php';
	}
	else
		require_once 'templates/error_401.php';
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