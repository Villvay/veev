<?php

	$template_file = 'home.php';

	$pages_schema = array(
						'id' 			=> array('ID', 			'table' => false, 'key' => true),
						'password' 	=> array('Password', 	'table' => false, 'display' => 'password', 'form-width' => '50'),
						'password_conf' => array('Confirm Password', 'table' => false, 'display' => 'password', 'form-width' => '50'),
						'email' 		=> array('Email'),
						'timezone' 	=> array('Time Zone', 	'form-width' => '50'),
						'lang' 		=> array('Language', 	'form-width' => '50'),
					);

	function index($params){
		if (!isset($_SESSION['user']) && $method != 'log_in')
			redirect('user', 'log_in');
		//
		global $users_schema, $user;
		$data = array();
		$users_schema['lang']['enum'] = list_languages();
		$data = array('schema' => $users_schema);//, 'data' => $user
		//
		//flash_message('Under Construction', 'warning');
		//
		$data['html_head'] = array('title' => 'My Account');
		return $data;
	}

	// --------------------------------------------------------------------

	function log_in($params){
		if (isset($params['username'])){
			$db = connect_database();
			$user = $db->query('SELECT id, cid, username, `password`, lang, timezone FROM `user` WHERE username = \''.$params['username'].'\'');
			if ($user = row_assoc($user)){
				if ($user['password'] == md5($params['password'].':NaCl')){
					unset($user['password']);
					$_SESSION['user'] = $user;
					redirect('user', 'index');
				}
			}
			flash_message('Wrong username or password.', 'error');
		}
		$data['html_head'] = array('title' => 'Log In');
	}

	function sign_out($params){
		unset($_SESSION['user']);
		redirect('user', 'log_in');
	}

	// --------------------------------------------------------------------

?>