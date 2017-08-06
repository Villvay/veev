<?php

	$template_file = 'home.php';

	$users_schema = array(
						'password' 	=> array('Password', 	'table' => false, 'display' => 'password', 'form-width' => '50'),
						'password_conf' => array('Confirm Password', 'table' => false, 'display' => 'password', 'form-width' => '50'),
						'email' 		=> array('Email'),
						'timezone' 	=> array('Time Zone', 	'form-width' => '50'),
						'lang' 		=> array('Language', 	'form-width' => '50'),
					);

	$logins_schema = array(
						'id' 			=> array('ID', 		'table' => false, 'key' => true),
						'useragent' 	=> array('Browser &amp; OS', 'function' => '_browser_os'),
						'ip' 			=> array('Location', 	'function' => '_location_by_ip'),
						'last_login' 	=> array('Last Login', 	'display' => 'calendar+clock'),
						'log_out' 		=> array('Log Out', 	'cmd' => 'user/remote-logout/{key}')
					);

	function index($params){
		global $method, $user;
		if ($user['id'] == -1 && $method != 'log_in')
			redirect('user', 'log_in');
		//
		global $users_schema, $logins_schema, $acl;
		$users_schema['timezone']['enum'] = json_decode(file_get_contents('data/timezones.json'));
		$db = connect_database();
		//
		if (isset($params['email'])){
			if ($params['password'] == '[encrypted]')
				unset($params['password']);
			else if ($params['password'] != $params['password_conf'])
				flash_message('Password confirmation does not match.', 'error');
			else
				$params['password'] = md5($params['password'].':'.COMMON_SALT);
			$params['id'] = $user['id'];
			$params['reset_code'] = '';
			$acl['edit'] = true;
			$db->update('user', $params);
			flash_message('Settings are updated.', 'success');
			redirect('user');
		}
		//
		$users_schema['lang']['enum'] = list_languages();
		$user['password'] = '[encrypted]';
		$user['password_conf'] = '';
		$data = array('schema' => $users_schema, 'logins_schema' => $logins_schema, 'user' => $user);
		$data['logins'] = $db->query('SELECT id, cookie, remember, ip, last_login, useragent FROM login WHERE user_id = '.$user['id'].' ORDER BY last_login DESC');
		//
		$data['html_head'] = array('title' => 'My Account');
		return $data;
	}
	function _browser_os($row){
		global $user_id;
		$row['useragent'] = json_decode($row['useragent'], true);
		$browser_icons = array('Chrome' => 'chrome.png', 'Firefox' => 'firefox.png', 'MSIE' => 'internet_explorer.png', 'IEMobile' => 'internet_explorer.png', 'Opera' => 'opera.png', 'Opera Next' => 'opera.png', 'Safari' => 'safari.png', 'OTHER' => 'browser.png');
		$os_icons = array('Windows' => 'screen_aqua.png', 'Linux' => 'screen_lensflare.png', 'Apple' => 'screen_aurora_snowleopard.png', 'Android' => 'android.png', 'Chrome OS' => 'screen_lensflare.png', 'iPhone' => 'iphone.png', 'PlayStation' => 'game_controller.png', 'OTHER' => 'screen_windows.png');
		if (isset($browser_icons[$row['useragent']['browser']]))
			$output = '<img src="'.BASE_URL_STATIC.'icons/'.$browser_icons[$row['useragent']['browser']].'" title="'.$row['useragent']['browser'].'" />';
		else
			$output = '<img src="'.BASE_URL_STATIC.'icons/'.$browser_icons['OTHER'].'" title="'.$row['useragent']['browser'].'" />';
		//
		$output .= ' <small>'.$row['useragent']['version'].'</small>';
		if (isset($os_icons[$row['useragent']['platform']]))
			$output .= ' on <img src="'.BASE_URL_STATIC.'icons/'.$os_icons[$row['useragent']['platform']].'" title="'.$row['useragent']['platform'].'" />';
		else
			$output .= ' on <img src="'.BASE_URL_STATIC.'icons/'.$os_icons['OTHER'].'" title="'.$row['useragent']['platform'].'" />';
		//
		$output .= '<br/>';
		if ($row['cookie'] == $user_id)
			$output .= 'This Device';
		if ($row['remember'] == 1)
			$output .= ' &nbsp; Remember';
		return $output;
	}
	function _location_by_ip($row){
		$details = @file_get_contents('http://api.ipinfodb.com/v3/ip-city/?key=43ccf11857d2bb9eda6c6891f62346852d9b4e4bb0a4e9ace6bbc91e1b2326a8&ip='.$row['ip']);
		if ($details === false)
			return $row['ip'];
		$details = explode(';', $details);
		if ($details[4] == '-')
			return '<img src="'.BASE_URL_STATIC.'icons/applications.png" title="localhost" /> localhost';
		else
			return '<img src="'.BASE_URL_STATIC.'icons/flags/flag_'.strtolower(str_replace(' ', '_', $details[4])).'.png" title="'.$details[4].'" /> '.$details[5].' '.$details[6];
	}

	function remote_logout($params){
		global $acl, $user;
		$db = connect_database();
		$acl[] = 'delete';
		$db->query('DELETE FROM login WHERE user_id = '.$user['id'].' AND id = '.$params[0]);
		redirect('user');
	}

	function reset_password($params){
		$data = array('step' => 1);
		if (isset($params['email']) && $params['email'] != ''){
			$db = connect_database();
			$user = $db->query('SELECT id, email FROM `user` WHERE email = \''.$params['email'].'\' OR username = \''.$params['email'].'\'');
			if ($user = row_assoc($user)){
				global $ip, $acl;
				include 'interfaces/email.php';
				$reset_code = base64_encode(sha1(microtime().'-'.$ip, true));
				send_email($user['email'], array('code' => $reset_code), 'password', 'Reset your password');
				$acl[] = 'edit';		//	Override Access Control for unauthorized user - special case
				$db->update('user', array('id' => $user['id'], 'reset_code' => $reset_code));
				redirect('user', 'reset-password/step-2');
			}
			else
				flash_message('The email address you have entered is not registered.', 'error');
		}
		else if (isset($params['code']) && $params['code'] != ''){
			$db = connect_database();
			$code = $db->query('SELECT id, email FROM `user` WHERE reset_code = \''.$params['code'].'\'');
			if ($code = row_assoc($code)){
				$_SESSION['PASSWORD_RESET_USER_ID'] = $code['id'];
				redirect('user', 'reset-password/step-3');
			}
			else{
				flash_message('The reset code you have entered is invalid or expired.', 'error');
				//redirect('user', 'reset-password/step-2');
			}
		}
		else if (isset($_SESSION['PASSWORD_RESET_USER_ID']) && isset($params['password']) && $params['password'] != ''){
			if ($params['password'] == $params['password_conf']){
				global $acl;
				$db = connect_database();
				$acl[] = 'edit';		//	Override Access Control for unauthorized user - special case
				$db->update('user', array('id' => $_SESSION['PASSWORD_RESET_USER_ID'], 'password' => md5($params['password'].':'.COMMON_SALT), 'reset_code' => ''));
				unset($_SESSION['PASSWORD_RESET_USER_ID']);
				flash_message('Your password is updated. You can log-in with the new password now', 'success');
				redirect('user', 'log-in');
			}
			else{
				flash_message('The password confirmation you have entered does not match.', 'error');
			}
		}
		if ($params[0] == 'step-1')
			$data['step'] = 1;
		else if ($params[0] == 'step-2')
			$data['step'] = 2;
		else if ($params[0] == 'step-3')
			$data['step'] = 3;
		//
		$data['html_head'] = array('title' => 'Reset Password - Step '.$data['step']);
		return $data;
	}

	// --------------------------------------------------------------------

	function log_in($params){
		if (isset($params['username'])){
			$db = connect_database();
			$user = $db->query('SELECT id, organization, email, username, `password`, lang, timezone, auth, reset_code FROM `user` WHERE username = \''.$params['username'].'\'');
			if ($user = row_assoc($user)){
				if ($user['password'] == md5($params['password'].':'.COMMON_SALT)){
					unset($user['password']);
					//$user['auth'] = array_merge(json_decode(PUBLIC_MODULES, true), json_decode($user['auth'], true));
					//$_SESSION['user'] = $user;
					if ($user['reset_code'] != '')
						flash_message('Someone has requested to reset your password. We recommend you change your password now.', 'warning');
					//
					global $user_id, $acl, $ip;
					include 'interfaces/user_agent_parser.php';
					$login = $db->query('SELECT id FROM login WHERE cookie = \''.$user_id.'\''); // user_id = '.$user['id'].' OR
					$loginRec = array('remember' => isset($params['remember']) ? 1 : 0, 'user_id' => $user['id'],
									'session' => session_id(), 'ip' => $ip, 'last_login' => date('Y-m-d H:i:s'),
									'useragent' => json_encode(parse_user_agent($_SERVER['HTTP_USER_AGENT'])));
					if ($login = row_assoc($login)){
						$acl[] = 'edit';
						$loginRec['id'] = $login['id'];
						$db->update('login', $loginRec);
					}
					else{
						$acl[] = 'add';
						$loginRec['cookie'] = $user_id;
						$db->insert('login', $loginRec);
					}
					//
					setcookie('user_id', $user_id, time()+(3600*24*365*5), PATH);
					if (isset($_SESSION['REDIRECT_AFTER_SIGNIN']) && $user['reset_code'] == ''){
						$tmp = $_SESSION['REDIRECT_AFTER_SIGNIN'];
						unset($_SESSION['REDIRECT_AFTER_SIGNIN']);
						header('location:'.(substr($tmp, 0, strlen(BASE_URL)) == BASE_URL ? '' : BASE_URL).$tmp);
					}
					else
						redirect('user', 'index');
				}
			}
			flash_message('Wrong username or password.', 'error');
		}
		$data['html_head'] = array('title' => 'Log In');
		return $data;
	}

	function sign_out($params){
		global $user, $user_id, $acl;
		$db = connect_database();
		$acl[] = 'delete';
		$db->query('DELETE FROM login WHERE user_id = '.$user['id'].' OR cookie = \''.$user_id.'\'');
		//unset($_SESSION['user']);
		redirect('user', 'log_in');
	}

	// --------------------------------------------------------------------

	//	oAuth authentication/authorization endpoint
	function oauth2($params){
		global $user;
		$data = array();
		$db = connect_database();
		if (isset($params['code']) && isset($params['redirect']))
			header('location:'.$params['redirect'].'?code='.$params['code']);
		else if (isset($params[0])){
			if ($params[0] == 'token'){
				global $template_file;
				$template_file = 'json';
				if (!isset($params['code']))
					return json_encode(array('error' => 'Authorization code not defined'));
				else if (!isset($params['client_secret']))
					return json_encode(array('error' => 'Client Secret not defined'));
				else if (!file_exists('data/oauthtokenstmp/'.$params['code'].'.token'))
					return json_encode(array('error' => 'Token expired or invalid'));
				else{
					$clientid = explode('-', $params['code']);
					$app = row_assoc($db->select('*', 'app', 'clientid = \''.$clientid[0].'\''));
					if ($app['secret'] != $params['client_secret'])
						return json_encode(array('error' => 'Client Secret is invalid'));
					else{
						$token = file_get_contents('data/oauthtokenstmp/'.$params['code'].'.token');
						unlink('data/oauthtokenstmp/'.$params['code'].'.token');
						return json_encode(array('access_token' => $token));
					}
				}
			}
			else{
				if ($user['id'] < 0){
					$_SESSION['REDIRECT_AFTER_SIGNIN'] = $_SERVER['REQUEST_URI'];
					redirect('user', 'log-in');
				}
				else if ($params[0] == 'authorize'){
					if (!isset($params['redirect_uri']))
						flash_message('Redirect URI not defined', 'error');
					else if (!isset($params['client_id']))
						flash_message('Client ID not defined', 'error');
					else if (!$data['app'] = row_assoc($db->select('*', 'app', 'clientid = \''.$params['client_id'].'\'')))
						flash_message('Application with Client ID '.$params['client_id'].' not found', 'error');
					else{
						$data['app']['urls'] = explode("\n", $data['app']['urls']);
						if (!in_array($params['redirect_uri'], $data['app']['urls'])){
							flash_message('Given redirect URI is not authorized', 'error');
							unset($data['app']);
						}
						else{
							$data['app']['scopes'] = explode(' ', $params['scope']);
							$data['app']['redirect'] = $params['redirect_uri'];
							$auth = json_encode(array('clientid' => $params['client_id'], 'userid' => $user['id'], 'scopes' => $data['app']['scopes'], 'timestamp' => time()));
							$data['app']['code'] = $params['client_id'].'-'.str_replace(array('/', '+', '='), '', base64_encode(sha1($auth.':'.COMMON_SALT)));
							$result = file_put_contents('data/oauthtokenstmp/'.$data['app']['code'].'.token', base64_encode(base64_encode(md5($auth.':'.COMMON_SALT, true)).$auth));
							$data['html_head'] = array('title' => $data['app']['title']);
							if (!$result)
								flash_message('No permission to write temp oauth token', 'error');
						}
					}
				}
			}
		}
		//
		if (!isset($data['html_head']))
			$data['html_head'] = array('title' => 'Authorize');
		return $data;
	}

	//	This is example code to connect to an instance of above oAuth endpoint
	function oauth_client($params){
		$client_id = 'EC33850714FF828FF530C9C42E7CEC0C';
		$client_secret = 'RUMzMzg1MDcxNEZGODI4RkY1MzBDOUM0MkU3Q0VDMEM';
		if (isset($params[0]) && $params[0] == 'step-1')
			header('location:http://localhost/veev/user/oauth2/authorize/?response_type=code'.
					'&client_id='.$client_id.
					'&redirect_uri='.BASE_URL.'user/oauth_client/'.
					'&scope=user+dashboard&nonce='.time());
		else if (isset($params['code'])){
			$opts = array('http' =>
					array(
						'method'  => 'POST',
						'header'  => 'Content-type: application/x-www-form-urlencoded',
						'content' => http_build_query(
							array(
								'code' => $params['code'],
								'client_secret' => $client_secret
							)
						)
					)
				);
			$result = file_get_contents('http://localhost/veev/user/oauth2/token', false, stream_context_create($opts));
			return $result;
		}
		else if (isset($params['error'])){
			return $params['error'];
		}
	}

	// --------------------------------------------------------------------

?>