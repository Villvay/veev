<?php

	$template_file = 'admin.php';

	function index($params){
		$data = array();
		//
		$data['html_head'] = array('title' => 'Admin Dashboard');
		return $data;
	}

	// --------------------------------------------------------------------

	function config($params){
		if (!is_writable('.htaccess'))
			flash_message('Apache cannot write `.htaccess` file', 'warning');
		if (!is_writable('framework/config.php'))
			flash_message('Apache cannot write `framework/config.php` file', 'warning');
		if (!is_writable('data/schema.db'))
			flash_message('Apache cannot write to `data` directory', 'warning');
		if (!is_writable('static/uploads/test'))
			flash_message('Apache cannot write to `static/uploads` directory', 'warning');
		//
		if (isset($params['htaccess']) && isset($params['config'])){
			$data = $params;
			//
			//	Try to connect to database server with db-name
			$conn = mysqli_connect($params['db_host'], $params['db_user'], $params['db_pass'], $params['db_name']);
			if (!$conn){
				//	Maybe we need to create a new database
				$conn = mysqli_connect($params['db_host'], $params['db_user'], $params['db_pass']);
				if (!$conn)
					flash_message('Database credentials are wrong.', 'error');
				else{
					mysqli_query($conn, 'CREATE DATABASE `'.$params['db_name'].'`');
					mysqli_query($conn, 'USE `'.$params['db_name'].'`');
				}
			}
			//	If database connection succesful
			if (!!$conn){
				//	Create database schema from script
				if (file_exists('database.sql')){
					$lines = file('database.sql');
					$query = '';
					foreach ($lines as $line)
						if (substr($line, 0, 2) == '--' || (substr($line, 0, 2) == '/*' && (substr($line, -4, 3) == '*/;' || substr($line, -3) == '*/;')) || trim($line) == ''){}
						else{
							$query .= $line;
							if (substr($line, -1) == ';' || substr($line, -2, 1) == ';'){
								mysqli_query($conn, $query);
								$query = '';
							}
						}
				}
				//
				//	Write .htaccess and config file
				file_put_contents('.htaccess', $params['htaccess']);
				$result = file_put_contents('framework/config.php', $params['config']);
				if (!$result)
					flash_message('Cannot write config file. Please manually upload .htaccess and framework/config.php', 'error');
				else
					header('location:http://'.$data['server'].$data['path']);
			}
		}
		else{
			$data = array();
			$data['protocol'] = 'http';
			$data['port'] = '';
			$data['path'] = rtrim($_SERVER['REQUEST_URI'], 'configure.php');
			$data['static_files_root'] = './static/';
			$data['backend_service_port'] = rand(49152, 65535);
			//
			$data['db_host'] = 'localhost';
			$data['db_name'] = str_replace('/', '', $data['path']);
			$data['db_user'] = 'root';
			$data['db_pass'] = '';
			//
			$data['google_client_id'] = '';
			$data['google_secret'] = '';
			$data['facebook_client_id'] = '';
			$data['facebook_secret'] = '';
			//
			$data['htaccess'] = '';
			$data['config'] = '';
			$data['instance'] = ($_SERVER['SERVER_NAME'] == 'localhost' ? 'development' : 'production');
		}
		$data['server'] = $_SERVER['SERVER_NAME'];
		//
		$data['html_head'] = array('title' => 'Setup Veev Site/Web-app');
		return $data;
	}

	// --------------------------------------------------------------------

	function services($params){
		$data = array();
		//
		$data['pid'] = file_get_contents('data/service.pid') - 1;
		if (isset($params[0])){
			if ($params[0] == 'start'){
				$proc = proc_open('cd framework'."\n".'sh run.sh '.BACKEND_SERVICE_PORT,
					[0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
				sleep(1);
			}
			else if ($params[0] == 'stop'){
				passthru('kill '.$data['pid']);
				unlink('data/service.pid');
			}
			redirect('admin', 'services');
		}
		$data['result'] = file_get_contents('http://127.0.0.1:'.BACKEND_SERVICE_PORT.'/');
		//
		$data['html_head'] = array('title' => 'Services: Admin Dashboard');
		return $data;
	}

	// --------------------------------------------------------------------

	function logs($params){
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

	//$pages_index = array('home' => 'Home Page', 'about' => 'About Us', 'contact' => 'Contact Us');

	function pages($params){
		$db = connect_database();
		$pages_schema = load_schema('content');
		$pages_schema['lang']['enum'] = list_languages();
		$pages_schema['edit']['cmd'] = 'admin/pages/edit/{key}';
		$pages_schema['delete']['cmd'] = 'admin/pages/delete/{key}';
		//
		$pages_schema['category']['enum'] = array();
		$cats = $db->select('id, title', 'category');
		while ($cat = row_assoc($cats))
			$pages_schema['category']['enum'][$cat['id']] = $cat['title'];
		//
		$data = array('schema' => $pages_schema);
		//
		if (isset($params['content'])){
			if ($params['id'] == 'new')
				$db->insert('content', $params);
			else
				$db->update('content', $params);
			flash_message('Content is saved', 'success');
			redirect('admin', 'pages');
		}
		else if (isset($params[1])){
			if ($params[0] == 'delete'){
				$db->delete('content', $params[1]);
				redirect('admin', 'pages');
			}
			else if ($params[0] == 'edit'){
				$content = $db->select('id, category, slug, lang, title, content', 'content', $params[1]);
				$data['content'] = row_assoc($content);
			}
			else if ($params[0] == 'page'){
				//$data['page'] = $params[0];
			}
		}
		else if (isset($params[0])){
			if ($params[0] == 'add'){
				global $lang;
				$data['content'] = array('id' => 'new', 'category' => 0, 'slug' => '', 'lang' => $lang, 'title' => '', 'content' => '');
			}
		}
		else
			$data['pages'] = $db->select('id, category, slug, lang, title, published', 'content');
		//
		$data['html_head'] = array('title' => 'Pages: Admin Dashboard');
		return $data;
	}

	function categories($params){
		$category_schema = array('id' => array('ID', 'key'=>true, 'display' => 'readonly'), 'title' => array('Title'), 'edit' => array('Edit', 'cmd' => 'admin/categories/{key}'), 'delete' => array('Delete', 'confirm' => true, 'cmd' => 'admin/categories/delete/{key}'));//load_schema('category');
		$data = array('schema' => $category_schema);
		$db = connect_database();
		//
		if (isset($params[1]) && $params[0] == 'delete')
			$db->delete('category', $params[1]);
		else if (isset($params[0])){
			if (isset($params['id'])){
				if ($params['id'] == 'new')
					$db->insert('category', $params);
				else
					$db->update('category', $params);
				flash_message('Category is saved', 'success');
				redirect('admin', 'categories');
			}
			else{
				$category = $db->select('id, title', 'category', 'id = \''.$params[0].'\'');
				if ($category = row_assoc($category))
					$data['category'] = $category;
				else
					$data['category'] = array('id' => 'new', 'title' => '');
			}
		}
		else
			$data['categories'] = $db->select('id, title', 'category');
		//
		$data['html_head'] = array('title' => 'Categories: Admin Dashboard');
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
				$acl = array_diff($acl, array('full'));
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