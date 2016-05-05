<?php

function errorHandler($errno, $errstr, $errfile, $errline/*, $errcontext*/){
	if (substr($errfile, strlen($errfile)-21) == '/framework/render.php'){
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$errstr = 'Render Error: '.$errstr;
		$errfile = $backtrace[1]['file'];
		$errline = $backtrace[1]['line'];
	}
	ob_clean();
	$yield = '';
	//
	if (ON_ERROR == 'DISPLAY'){
		$errfile = substr($errfile, strlen(__FILE__)-26);
		$yield = '<b>'.$errno.': '.$errstr.'</b><br/>'.$errfile.': '.$errline;
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

set_error_handler('errorHandler', E_ALL);

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

?>