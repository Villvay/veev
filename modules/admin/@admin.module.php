<?php

	$template_file = 'admin.php';

	function index($params){
		$data = array();
		//
		$data['html_head'] = array('title' => 'Admin Dashboard');
		return $data;
	}

	// --------------------------------------------------------------------

	//$pages_index = array('home' => 'Home Page', 'about' => 'About Us', 'contact' => 'Contact Us');

	function pages($params){
		$pages_schema = load_schema('page');
		$pages_schema['lang']['enum'] = list_languages();
		$data = array('schema' => $pages_schema);
		$db = connect_database();
		//
		if (isset($params[0])){
			$data['page'] = $params[0];
			//
			$found = false;
			$content = $db->select('slug, lang, title, content', 'content', 'slug = \''.$params[0].'\'');
			if ($content = row_assoc($content)){
				$data['content'] = $content;
				$found = true;
			}
			//
			if (isset($params['content'])){
				if ($found)
					$db->update('content', $params, 'slug = \''.$params['slug'].'\'');
				else
					$db->insert('content', $params);
				flash_message('Content is saved', 'success');
				redirect('admin', 'pages');
			}
		}
		else
			$data['pages'] = $db->select('slug, lang, title', 'content');
		//
		$data['html_head'] = array('title' => 'Pages: Admin Dashboard');
		return $data;
	}

	// --------------------------------------------------------------------

	function errors($params){
		$directory = scandir('./data/errors/');
		$files = array();
		foreach ($directory as $file)
			if (!in_array($file, array('.', '..')))
				$files[$file] = filemtime('./data/errors/'.$file);
		arsort($files);
		$errors = array();
		foreach ($files as $file => $mtime){
			$file = explode(':', gzinflate(base64_decode(str_replace(array('-', '_'), array('/', '='), substr($file, 0, -4)))));
			$errors[] = array('time' => beautify_datetime($mtime), 'level' => $file[0], 'file' => $file[1], 'line' => $file[2]);
		}
		$data['errors'] = $errors;
		$data['html_head'] = array('title' => 'Errors: Admin Dashboard');
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

	function users($params){
		global $user;
		$users_schema = load_schema('user');
		$users_schema['lang']['enum'] = list_languages();
		$data = array('schema' => $users_schema);
		$db = connect_database();
		//
		$data['users'] = $db->query('SELECT * FROM `user` WHERE organization = '.$user['organization'].' AND password != \'[GROUP]\'');
		//
		$data['html_head'] = array('title' => 'User Accounts');
		return $data;
	}

	function _add_edit_user($params){
		global $user, $method;
		$users_schema = load_schema('user');
		$method = '_add_edit_user';
		$users_schema['timezone']['enum'] = json_decode(file_get_contents('data/timezones.json'));
		$users_schema['lang']['enum'] = list_languages();
		//
		$db = connect_database();
		$users_schema['groups']['enum'] = array();
		$groups = $db->query('SELECT id, username FROM `user` WHERE organization = '.$user['organization'].' AND password = \'[GROUP]\'');
		while ($group = row_assoc($groups))
			$users_schema['groups']['enum'][$group['id']] = $group['username'];
		//
		$data = array('schema' => $users_schema);
		//
		$data['schema']['auth']['enum'] = _subfolders('./modules/');
		return $data;
	}
	function _subfolders($path){
		$dh = opendir($path);
		$data = array();
		$public_modules = array_merge(array('.', '..'));
		while (($file = readdir($dh)) !== false){
			if (!in_array($file, $public_modules) && is_dir($path.$file)){
				$data[] = array($file, _subfolders($path.$file.'/'));
			}
		}
		closedir($dh);
		return $data;
	}

	function add_user($params){
		global $user;
		$data = _add_edit_user($params);
		$data['a_user'] = array('id' => 'new', 'username' => '', 'password' => '', 'email' => '', 'groups' => '', 'timezone' => $user['timezone'], 'lang' => $user['lang'], 'auth' => '{}');
		//
		$data['html_head'] = array('title' => 'Add User Account');
		return $data;
	}

	function edit_user($params){
		global $user;
		$data = _add_edit_user($params);
		$db = connect_database();
		//
		$data['a_user'] = row_assoc($db->query('SELECT * FROM `user` WHERE organization = '.$user['organization'].' AND id = '.$params[0]));
		$data['a_user']['password'] = '[encrypted]';
		//
		$data['html_head'] = array('title' => 'Edit User Account');
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
		if (isset($params['auth']))
			foreach ($params['auth'] as $module => $acl){
				//unset($acl['full']);
				$acl = array_diff($acl, ['full']);
				$auth[$module] = array_keys($acl);
			}
		$params['auth'] = json_encode($auth);
		$params['groups'] = isset($params['groups']) ? implode(', ', array_flip($params['groups'])) : '';
		//
		if ($params['id'] == 'new'){
			unset($params['id']);
			$params['organization'] = $user['organization'];
			$db->insert('user', $params);
		}
		else
			$db->update('user', $params);
		redirect('admin', 'users');
	}

	// --------------------------------------------------------------------

	function groups($params){
		global $user;
		$users_schema = load_schema('user');
		unset($users_schema['password']);
		unset($users_schema['email']);
		unset($users_schema['timezone']);
		unset($users_schema['lang']);
		$users_schema['edit']['cmd'] = 'admin/edit-group/{key}';
		$data = array('schema' => $users_schema);
		$db = connect_database();
		//
		$data['groups'] = $db->query('SELECT * FROM `user` WHERE organization = '.$user['organization'].' AND password = \'[GROUP]\'');
		//
		$data['html_head'] = array('title' => 'User Groups');
		return $data;
	}

	function _add_edit_group($params){
		global $method;
		$users_schema = load_schema('user');
		$method = '_add_edit_group';
		$users_schema['username'][0] = 'Group Name';
		unset($users_schema['username']['form-width']);
		unset($users_schema['password']);
		unset($users_schema['email']);
		unset($users_schema['timezone']);
		unset($users_schema['lang']);
		$data = array('schema' => $users_schema);
		//
		$data['schema']['auth']['enum'] = _subfolders('./modules/');
		return $data;
	}

	function add_group($params){
		global $user;
		$data = _add_edit_group($params);
		$data['a_user'] = array('id' => 'new', 'username' => '', 'auth' => '{}');
		//
		$data['html_head'] = array('title' => 'Add User Group');
		return $data;
	}

	function edit_group($params){
		global $user;
		$data = _add_edit_group($params);
		$db = connect_database();
		$data['a_user'] = row_assoc($db->query('SELECT * FROM `user` WHERE organization = '.$user['organization'].' AND id = '.$params[0]));
		//
		$data['html_head'] = array('title' => 'Edit User Group');
		return $data;
	}

	function save_group($params){
		global $user;
		$db = connect_database();
		$params['password'] = '[GROUP]';
		//
		$auth = array();
		foreach ($params['auth'] as $module => $acl){
			$acl = array_diff($acl, ['full']);
			$auth[$module] = array_keys($acl);
		}
		$params['auth'] = json_encode($auth);
		//
		if ($params['id'] == 'new'){
			unset($params['id']);
			$params['organization'] = $user['organization'];
			$db->insert('user', $params);
		}
		else
			$db->update('user', $params);
		redirect('admin', 'groups');
	}

	// --------------------------------------------------------------------

?>