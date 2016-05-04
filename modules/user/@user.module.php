<?php

	$template_file = 'home.php';

	$users_schema = array(
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
		//
		if (isset($params['email'])){
			$db = connect_database();
			if ($params['password'] == '[encrypted]')
				unset($params['password']);
			else if ($params['password'] != $params['password_conf'])
				flash_message('Password confirmation does not match.', 'error');
			else
				$params['password'] = md5($params['password'].':'.COMMON_SALT);
			$params['id'] = $user['id'];
			$db->update('user', $params);
			flash_message('Settings are updated. Please log out and log in to apply.', 'success');
		}
		//
		$users_schema['lang']['enum'] = list_languages();
		$user['password'] = '[encrypted]';
		$user['password_conf'] = '';
		$data = array('schema' => $users_schema, 'user' => $user);
		//
		$data['html_head'] = array('title' => 'My Account');
		return $data;
	}

	// --------------------------------------------------------------------

	function log_in($params){
		if (isset($params['username'])){
			$db = connect_database();
			$user = $db->query('SELECT id, cid, email, username, `password`, lang, timezone, auth FROM `user` WHERE username = \''.$params['username'].'\'');
			if ($user = row_assoc($user)){
				if ($user['password'] == md5($params['password'].':'.COMMON_SALT)){
					unset($user['password']);
					$user['auth'] = json_decode($user['auth'], true);
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