<?php
if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
else if (isset($_SERVER['REMOTE_ADDR']))
	$ip = $_SERVER['REMOTE_ADDR'];
else if (isset($_SERVER['HTTP_CLIENT_IP']))
	$ip = $_SERVER['HTTP_CLIENT_IP'];
//
if (!isset($user_id)){
	if (isset($_COOKIE['user_id'])){
		$user_id = $_COOKIE['user_id'];
	}
	else{
		$ip = explode('.', $ip);
		$ip = sha1(microtime() * ($ip[3] + ($ip[2]*4) + ($ip[1]*8) + ($ip[0]*16)));
		$user_id = strtoupper($ip);
		setcookie('user_id', $user_id, time()+(3600*24*365*5));
	}
}
?>