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
	if (ON_ERROR == 'DISPLAY')
		$yield = print_r(array($errno, $errstr, $errfile, $errline), true);
	else if (ON_ERROR == 'LOG')
		file_put_contents('data/error.log', json_encode(array($errno, $errstr, $errfile, $errline)), FILE_APPEND);
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