<?php

$errorHandlerLatch = false;

function errorHandler($errno, $errstr, $errfile, $errline/*, $errcontext*/){
	global $errorHandlerLatch;
	if ($errorHandlerLatch)
		return;
	$errorHandlerLatch = true;
	set_include_path(dirname(dirname(__FILE__)).'/');
	//
	if (substr($errfile, strlen($errfile)-21) == '/framework/render.php'){
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$errstr = 'Render/Schema Error: '.$errstr;
		$errfile = $backtrace[1]['file'];
		$errline = $backtrace[1]['line'];
	}
	ob_clean();
	$yield = '';
	//
	if (ON_ERROR == 'DISPLAY'){
		$errfile = substr($errfile, strlen(__FILE__)-26);
		$yield = '<b>'.$errno.'</b>: '.$errstr.'<br/>'.$errfile.'<br/>line ['.$errline.']';
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
			die();
		else if ($yield == ''){
			global $lex, $user;
			$yield = strip_tags(ob_get_contents()).' ';
			ob_end_clean();
			$errline = strpos($yield, 'on line')+8;
			$errfile = strpos($yield, ' in ')+4;
			$errno = strpos($yield, ':');
			$errstr = $yield; // trim(substr($yield, $errno+1, $errfile-$errno-5));
			$errno = trim(substr($yield, 0, $errno));
			$errfile = trim(substr($yield, $errfile, strpos($yield, ' ', $errfile)-$errfile));
			$errline = trim(substr($yield, $errline, strpos($yield, ' ', $errline)-$errline));
			errorHandler($errno, $errstr, $errfile, $errline);
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

?>