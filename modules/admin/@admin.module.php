<?php

	if (!isset($_SESSION['user']) && $_SESSION['user']['level'] < 5 && $method != 'log_in')
		redirect('user', 'log_in');

	$template_file = 'admin.php';

	function index($params){
		$data = array();
		//
		$data['html_head'] = array('title' => 'Admin Dashboard');
		return $data;
	}

	// --------------------------------------------------------------------

	$pages_index = array('home' => 'Home Page', 'about' => 'About Us', 'contact' => 'Contact Us');
	$pages_schema = array(
						'slug' 		=> array('Title', 		'key' => true, 'table' => false),
						'title' 		=> array('Title'),
						'lang' 		=> array('Language', 	'enum' => list_languages()),
						'content' 		=> array('Content', 	'display' => 'richtext', 'table' => false),
						'slides' 		=> array('Slides', 		'display' => 'folder', 'path' => 'user/images/uploads/{slug}', 'table' => false),
						'edit' 		=> array('Edit', 		'form' => false, 'cmd' => 'admin/pages/{key}', 'default' => true),
						'view' 		=> array('View', 		'form' => false, 'cmd' => '{key}')
					);
	function pages($params){
		global $pages_schema;
		$data = array('schema' => $pages_schema);
		$db = connect_database();
		//
		if (isset($params[0])){
			$data['page'] = $params[0];
			//
			$found = false;
			$content = $db->query('SELECT slug, lang, title, content FROM content WHERE slug = \''.$params[0].'\'');
			if ($content = row_assoc($content)){
				$data['content'] = $content;
				$found = true;
			}
			//
			if (isset($params['en'])){
				if ($found)
					$db->update('content', array('ch' => $params['ch'], 'en' => $params['en']), 'slug = \''.$params['slug'].'\'');
				else
					$db->insert('content', $params);
				flash_message('Content is saved', 'success');
				redirect('admin', 'pages');
			}
		}
		else
			$data['pages'] = $db->query('SELECT slug, lang, title FROM content');
		//
		$data['html_head'] = array('title' => 'Pages: Admin Dashboard');
		return $data;
	}

	// --------------------------------------------------------------------

	function inquiry($params){
		$data = array();
		//
		flash_message('Under Construction', 'warning');
		//
		$data['html_head'] = array('title' => 'Inquiry: Admin Dashboard');
		return $data;
	}

	// --------------------------------------------------------------------

	$user_levels = array(1 => 'Basic User', 2 => 'Super User', 3 => 'Manager', 5 => 'Admin', 8 => 'Super Admin');
	$users_schema = array(
						'id' 			=> array('ID', 			'table' => false, 'key' => true),
						'username' 	=> array('Username', 		'form-width' => '50'),
						'password' 	=> array('Password', 		'table' => false, 'display' => 'password', 'form-width' => '50'),
						'email' 		=> array('Email'),
						'timezone' 	=> array('Time Zone', 		'form-width' => '50'),
						'lang' 		=> array('Language', 		'form-width' => '50'),
						'auth' 		=> array('Authorized Modules', 'enum' => array(), 'table' => false, 'form' => false),
						'edit' 		=> array('Edit', 			'form' => false, 'cmd' => 'admin/edit-user/{key}', 'default' => true)
					);

	function users($params){
		global $users_schema, $user;
		$users_schema['lang']['enum'] = list_languages();
		$data = array('schema' => $users_schema);
		$db = connect_database();
		//
		$data['users'] = $db->query('SELECT * FROM `user` WHERE cid = '.$user['cid']);
		//
		$data['html_head'] = array('title' => 'User Accounts');
		return $data;
	}

	function _add_edit_user($params){
		global $users_schema, $method;
		$method = '_add_edit_user';
		$users_schema['timezone']['enum'] = json_decode(file_get_contents('data/timezones.json'));
		$users_schema['lang']['enum'] = list_languages();
		$data = array('schema' => $users_schema);
		//
		$data['schema']['auth']['enum'] = _subfolders('./modules/');
		return $data;
	}
	function _subfolders($path){
		$dh = opendir($path);
		$data = array();
		$public_modules = array_merge(array('.', '..'), array_keys(json_decode(PUBLIC_MODULES, true)));
		while (($file = readdir($dh)) !== false){
			if (!in_array($file, $public_modules) && is_dir($path.$file)){
				$data[] = array($file, _subfolders($path.$file.'/'));
			}
		}
		closedir($dh);
		return $data;
	}

	function edit_user($params){
		global $user;
		$data = _add_edit_user($params);
		$db = connect_database();
		//
		$data['a_user'] = row_assoc($db->query('SELECT * FROM `user` WHERE cid = '.$user['cid'].' AND id = '.$params[0]));
		$data['a_user']['password'] = '[encrypted]';
		//
		$data['html_head'] = array('title' => 'Edit User Account');
		return $data;
	}

	function add_user($params){
		global $user;
		$data = _add_edit_user($params);
		$data['a_user'] = array('id' => 'new', 'username' => '', 'password' => '', 'email' => '', 'timezone' => $user['timezone'], 'lang' => $user['lang'], 'auth' => '{}');
		//
		$data['html_head'] = array('title' => 'Add User Account');
		return $data;
	}

	function save_user($params){
		global $user;
		$db = connect_database();
		if ($params['password'] == '[encrypted]')
			unset($params['password']);
		else
			$params['password'] = md5($params['password'].':'.COMMON_SALT);
		//
		$auth = array();
		foreach ($params['auth'] as $module => $acl){
			unset($acl['full']);
			$auth[$module] = array_keys($acl);
		}
		$params['auth'] = json_encode($auth);
		//
		if ($params['id'] == 'new'){
			unset($params['id']);
			$params['cid'] = $user['cid'];
			$db->insert('user', $params);
		}
		else
			$db->update('user', $params);
		redirect('admin', 'users');
	}

?>