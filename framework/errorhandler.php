<?php

$errorHandlerLatch = false;
if (!defined('DEBUG_BACKTRACE_IGNORE_ARGS'))
	define('DEBUG_BACKTRACE_IGNORE_ARGS', 0);

function errorHandler($errno = false, $errstr, $errfile = false, $errline = false/*, $errcontext*/){
	global $errorHandlerLatch;
	if ($errorHandlerLatch)
		return;
	$errorHandlerLatch = true;
	header('Content-Type: text/html');
	set_include_path(dirname(dirname(__FILE__)).'/');
	require_once dirname(__FILE__).'/render.php';
	//
	//$backtrace = false;
	$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	if (substr($errstr, 0, 16) == 'Missing argument' && class_exists('ReflectionFunction')){
		$p0 = strpos($errstr, ' ', 18);
		$arg = substr($errstr, 17, $p0-17);
		$p0 = strpos($errstr, 'for', 10);
		$p0 = strpos($errstr, ' ', $p0);
		$p1 = strpos($errstr, '(', $p0);
		$p0 = substr($errstr, $p0+1, $p1-$p0-1);
		$parameters = new ReflectionFunction($p0);
		$parameters = $parameters->getParameters();
		//for render_form(), called in /var/www/html/veev/modules/admin/errors.php on line 6
		$errstr = 'Missing argument '.$arg.' ['.$parameters[$arg-1]->name.'] for ['.$p0.']';
		//$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$errfile = $backtrace[1]['file'];
		$errline = $backtrace[1]['line'];
	}
	if (substr($errfile, strlen($errfile)-20) == 'framework/render.php'){
		//$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$i = 0;
		while (substr($backtrace[$i]['file'], strlen($backtrace[$i]['file'])-20) == 'framework/render.php')
			$i += 1;
		if ($errstr == 'Undefined variable: key')
			$errstr = 'Define a key field on the schema';
		else
			$errstr = 'Render/Schema Error: '.$errstr;
		$errfile = $backtrace[$i]['file'];
		$errline = $backtrace[$i]['line'];
	}
	ob_clean();
	$yield = '';
	//
	if (ON_ERROR == 'DISPLAY'){
		$errfile = substr($errfile, strlen(__FILE__)-26);
		$yield = ($errno == false ? '' : '<b>'.$errno.'</b>: ').$errstr.($errfile == false ? '' : '<br/>'.$errfile.($errline == false ? '' : '<br/>line ['.$errline.']').'<br/><small>'.print_r($backtrace, true).'</small>');
	}
	else if (ON_ERROR == 'LOG'){
		global $user, $params;
		$adata = array('useragent' => $_SESSION['USERAGENT'], 'user_id' => $user['id'], 'params' => $params);
		if ($backtrace == false)
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$error_data = base64_encode(gzdeflate(json_encode(array('message' => $errstr, 'backtrace' => $backtrace, 'additional_data' => $adata)), 9));
		//
		$errfile = substr($errfile, strlen(__FILE__)-26);
		$hash = str_replace(array('/', '='), array('-', '_'), base64_encode(gzdeflate($errno.':'.$errfile.':'.$errline)));
		//if (!file_exists('data/errors/'.$hash.'.log'))
		//	file_put_contents('data/errors/'.$hash.'.log', $error_data."\n");
		file_put_contents('data/errors/'.$hash.'.log', time().':'.$error_data."\n", FILE_APPEND);
	}
	else if (ON_ERROR == 'EMAIL'){
		global $server_name;
		include 'interfaces/email.php';
		send_email(ADMIN_EMAIL, array('errno' => $errno, 'errstr' => $errstr, 'errfile' => $errfile, 'errline' => $errline, 'backtrace' => $backtrace), 'error', 'Error in Veev app at '.$server_name, false);//, array($errfile) // Attached the file with error to the email
	}
	//
	if ($errno < 3)
		return false;
	global $lex, $user;
	if ($errno == false && $errfile == false && $errline == false){
		require_once 'templates/home.php';
	}
	else
		require_once 'templates/error_500.php';
	die();
}

/*function exceptionHandler($e){
	print_r($e);
	die();
}*/

//*/
set_error_handler('errorHandler', E_ALL);
/*/
//set_exception_handler('exceptionHandler');
register_shutdown_function(
	function(){
		global $yield, $errorHandlerLatch, $template_file;
		//echo '['.$yield.']';
		if ($errorHandlerLatch || $template_file == 'json')
			die();//$errorHandlerLatch = true;
		else if ($yield == ''){
			global $lex, $user;
			$yield = strip_tags(ob_get_contents()).' ';
			ob_end_clean();
			if ($yield == ' ')
				die();
			$errline = strpos($yield, 'on line')+8;
			if ($errline == 8)
				errorHandler(0, $yield, '', '');
			else{
				$errfile = strpos($yield, ' in ')+4;
				$errno = strpos($yield, ':');
				$errstr = $yield; // trim(substr($yield, $errno+1, $errfile-$errno-5));
				$errno = trim(substr($yield, 0, $errno));
				$errfile = trim(substr($yield, $errfile, strpos($yield, ' ', $errfile)-$errfile));
				$errline = trim(substr($yield, $errline, strpos($yield, ' ', $errline)-$errline));
				errorHandler($errno, $errstr, $errfile, $errline);
			}
			die();
		}
	});
//*/

if (ON_ERROR == 'DISPLAY'){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}
else if (ON_ERROR == 'IGNORE'){
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(0);
}
/*else{
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(0);
}*/

if (!function_exists('http_response_code')){
	function http_response_code($newcode = NULL){
		static $code = 200;
		if ($newcode !== NULL){
			header('X-PHP-Response-Code: '.$newcode, true, $newcode);
			if (!headers_sent())
				$code = $newcode;
		}
		return $code;
	}
}

?>