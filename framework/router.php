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

$headers = array_change_key_case(getallheaders(), CASE_LOWER);

//	SESSION/ACL RELATED
if (!isset($_SERVER['HTTP_AUTHENTICATION'])){
	session_start();
	ini_set('session.cookie_domain', ltrim($server_name, SUBDOMAIN.'.'));
	include 'interfaces/user_tracking.php';
	global $acl;
	$acl = array('view' => true);
	$db = connect_database();
	$login = $db->select('id, user_id, useragent, session', 'login', 'cookie = \''.$user_id.'\' AND (remember = 1 OR session = \''.session_id().'\')');
	if ($login = row_assoc($login)){
		if (!isset($_SESSION['USERAGENT'])){	//	Protect from session and cookie hijacking
			include 'interfaces/user_agent_parser.php';
			$_SESSION['USERAGENT'] = parse_user_agent($_SERVER['HTTP_USER_AGENT']);
		}
		$login['useragent'] = json_decode($login['useragent'], true);
		if ($login['useragent']['platform'] != $_SESSION['USERAGENT']['platform'] || $login['useragent']['browser'] != $_SESSION['USERAGENT']['browser']){
			$acl[] = 'delete';				//	Cookie does not match Browser and OS
			$db->delete('login', $login['id']);
		}
		else{
			$user = row_assoc($db->select('id, organization, email, username, lang, timezone, auth, groups', 'user', $login['user_id']));
			$user['auth'] = json_decode($user['auth'], true);
			acl_union($user['auth'], json_decode(PUBLIC_MODULES, true));
			if ($user['groups'] != ''){			//	Load group permissions
				$groups = $db->select('id, username, auth', 'user', 'id IN ('.$user['groups'].')');
				$user['groups'] = array();
				while ($group = row_assoc($groups)){
					$user['groups'][ $group['id'] ] = $group['username'];
					$group = json_decode($group['auth'], true);
					acl_union($user['auth'], $group);
				}
			}
		}
		if ($login['session'] != session_id()){
			$acl[] = 'edit';
			$db->update('login', array('id' => $login['id'], 'session' => session_id(), 'useragent' => json_encode($_SESSION['USERAGENT'])));
		}
	}
}
else{
	$token = base64_decode(substr($_SERVER['HTTP_AUTHENTICATION'], 7));
	$signature = strpos($token, '{');
	$signature = substr($token, 0, $signature);
	$token = substr($token, strlen($signature));
	if ($signature != base64_encode(md5($token.':'.COMMON_SALT, true)))
		die(json_encode(array('error' => '401 Unauthorized')));
	$token = json_decode($token, true);
	//
	global $acl;
	$acl = array('view' => true);
	$db = connect_database();
	$user = row_assoc($db->query('SELECT id, organization, email, username, lang, timezone, auth, groups FROM `user` WHERE id = '.$token['userid']));
	$user['auth'] = json_decode($user['auth'], true);
	acl_union($user['auth'], json_decode(PUBLIC_MODULES, true));
	if ($user['groups'] != ''){			//	Load group permissions
		$groups = $db->query('SELECT auth FROM `user` WHERE id IN ('.$user['groups'].')');
		while ($group = row_assoc($groups)){
			$group = json_decode($group['auth'], true);
			acl_union($user['auth'], $group);
		}
	}
	foreach ($user['auth'] as $key => $acl)
		if (!in_array($key, $token['scopes']))
			unset($user['auth'][$key]);
	print_r($user);
	$template_file = 'json';
	die('-- Under Construction --');
}
if (!isset($user))
	$user = array('id' => -1, 'organization' => -1, 'lang' => DEFAULT_LANGUAGE, 'timezone' => DEFAULT_TIMEZONE, 'auth' => json_decode(PUBLIC_MODULES, true));

function acl_union(&$dest, $add){
	foreach ($add as $module => $acl){
		if (isset($dest[$module])){
			foreach ($acl as $permission)
				if (!in_array($permission, $dest[$module]))
					$dest[$module][] = $permission;
		}
		else
			$dest[$module] = $acl;
	}
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

if (isset($headers['content-type']) && $headers['content-type'] == 'application/json'){
	$req_body = json_decode(file_get_contents('php://input'), true);
	foreach ($req_body as $key => $val)
		$params[$key] = $val;
}

date_default_timezone_set($user['timezone']);

//	Load languages
require_once 'render.php';
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : $user['lang'];

$lex_file = 'data/lang/'.DEFAULT_LANGUAGE.'.json';
if (file_exists('data/lang/'.$lang.'.json'))
	$lex_file = 'data/lang/'.$lang.'.json';
$lex = file_get_contents($lex_file);

if (!$lex = json_decode(substr($lex, 3), true))
	errorHandler(4, 'Invalid JSON syntax in language file', dirname(dirname(__FILE__)).'/'.$lex_file, 0);


// Include the Module
$module = str_replace('-', '_', $module);
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

function checkIfAuthorized($user, $module, $submodule = false){
	if (!isset($user['auth'][$module.($submodule==false?'':'/'.$submodule)]))
		return false;
	else
		return $user['auth'][$module.($submodule==false?'':'/'.$submodule)];
}


// Call the Method
if (function_exists($method)){
	ob_start();
	$data = $method($params);
	$method_yield = ob_get_contents();
	ob_end_clean();
	//
	if (is_array($data))
		if ($submodule)
			$yield = render_view('modules/'.$module.'/'.$submodule.'/'.$method.'.php', $data);
		else
			$yield = render_view('modules/'.$module.'/'.$method.'.php', $data);
	else
		$yield = $data;
	//
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
	$size = strlen($yield);
	if ($encoding){
		//if ($size > 2048){
		header('Content-Encoding: '.$encoding);
		print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
		$yield = gzcompress($yield, 9);
		//}
	}
	echo $yield;
	//
	$method_yield = "\n[".date('Y-m-d H:i:s').'] /'.$query_string.' '.http_response_code().' '.$size.($method_yield == '' ? '' : "\n".$method_yield."\n-------");
	if (is_writable('stdout.log'))//$method_yield != '' && 
		file_put_contents('stdout.log', $method_yield, FILE_APPEND);
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
	exit;
}

?>