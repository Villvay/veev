<?php

$errorHandlerLatch = false;
if (!defined('DEBUG_BACKTRACE_IGNORE_ARGS'))
	define('DEBUG_BACKTRACE_IGNORE_ARGS', 0);

function errorHandler($errno = false, $errstr, $errfile = false, $errline = false/*, $errcontext*/){
	global $errorHandlerLatch;
	if ($errorHandlerLatch)
		return;
	$errorHandlerLatch = true;
	set_include_path(dirname(dirname(__FILE__)).'/');
	require_once dirname(__FILE__).'/render.php';
	//
	/*if (substr($errfile, strlen($errfile)-23) == 'interfaces/database.php'){
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		print_r($backtrace);
		die();
	}*/
	if (substr($errfile, strlen($errfile)-20) == 'framework/render.php'){
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
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
		$yield = ($errno == false ? '' : '<b>'.$errno.'</b>: ').$errstr.($errfile == false ? '' : '<br/>'.$errfile.($errline == false ? '' : '<br/>line ['.$errline.']'));
	}
	else if (ON_ERROR == 'LOG')
		file_put_contents('data/'.date('Y-m-d').'-error.log', json_encode(array('errno' => $errno, 'errstr' => $errstr, 'errfile' => $errfile, 'errline' => $errline)).",\n", FILE_APPEND);
	else if (ON_ERROR == 'EMAIL'){
		global $server_name;
		include 'interfaces/email.php';
		send_email(ADMIN_EMAIL, array('errno' => $errno, 'errstr' => $errstr, 'errfile' => $errfile, 'errline' => $errline), 'error', 'Error in Veev app at '.$server_name, false);//, array($errfile) // Attached the file with error to the email
	}
	//
	global $lex, $user;
	if ($errno == false && $errfile == false && $errline == false){
		$yield = '<pre>'.$yield.'</pre>';
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

set_error_handler('errorHandler', E_ALL);
//set_exception_handler('exceptionHandler');
register_shutdown_function(
	function(){
		global $yield, $errorHandlerLatch;
		if ($errorHandlerLatch)
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