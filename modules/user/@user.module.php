<?php

	$template_file = 'home.php';

	function index($params){
		$data = array();
		//
		if (!isset($_SESSION['user']) && $method != 'log_in')
			redirect('user', 'log_in');
		//
		$data['html_head'] = array('title' => 'User Dashboard');
		return $data;
	}

	// --------------------------------------------------------------------

	$blog_schema = array(
						'id' 			=> array('ID', 		'table' => false, 'key' => true),
						'title' 			=> array('Title'),
						'content' 		=> array('Content', 	'table' => false, 'display' => 'richtext'),
						'published' 		=> array('Published on', 'form' => false),
						'edit' 			=> array('Edit', 		'form' => false, 'cmd' => 'user/blog/edit/{key}', 'default' => true),
						'delete' 		=> array('Delete', 	'form' => false, 'cmd' => 'user/delete/edit/{key}', 'default' => true),
						'view' 		=> array('View', 		'form' => false, 'cmd' => 'bloh/*/{key}', 'default' => true)
					);

	function blog($params){
		global $blog_schema;
		$data = array('schema' => $blog_schema);
		$db = connect_database();
		//
		if (isset($params['title'])){
			if ($params['id'] == 'new'){
				$params['published'] = date('Y-m-d H:i:s');
				$db->insert('blog', $params);
			}
			else
				$db->update('blog', $params);
			flash_message('Blog article is saved', 'success');
			redirect('user', 'blog');
		}
		else if (isset($params[1])){
			if ($params[0] == 'edit')
				$data['article'] = mysql_fetch_assoc($db->query('SELECT id, published, title, content FROM blog WHERE id = '.$params[1]));
			else if ($params[0] == 'add')
				$data['article'] = array('id' => 'new', 'published' => date('Y-m-d H:i:s'), 'title' => '', 'content' => '');
			else if ($params[0] == 'delete'){
				$db->delete('blog', $params[1]);
				flash_message('Blog article is deleted', 'success');
				redirect('user', 'blog');
			}
		}
		else{
			$per_page = 5;
			$page = 1;
			if (isset($params[0]))
				$page = $params[0];
			$data['page'] = $page;
			$data['blog'] = $db->query('SELECT id, published, title FROM blog ORDER BY published DESC LIMIT '.($per_page * ($page - 1)).', '.$per_page);
			$pages = mysql_fetch_array($db->query('SELECT COUNT(*) FROM blog'));
			$data['pages'] = ceil($pages[0] / $per_page);
		}
		//
		$data['html_head'] = array('title' => 'Blog: Admin Dashboard');
		return $data;
	}

	// --------------------------------------------------------------------

	function images($params){
		global $template_file;
		$template_file = '';
		$data = array();
		//
		$data['path'] = implode('/', $params);
		$files_path = STATIC_FILES_ROOT.$data['path'].'/';
		if (!is_dir($files_path))
			mkdir($files_path, 0775, true);
		//
		if (isset($_FILES['file']) && isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] != ''){
			include 'interfaces/image_magic.php';
			move_uploaded_file($_FILES['file']['tmp_name'], $files_path.$_FILES['file']['name']);
			$new_name = time().'.jpg';
			$image = load_image($files_path.$_FILES['file']['name']);
			save_image($image, $files_path.$new_name, 'jpg');
			//
			$thumbnail = thumbnail($image, 160);
			save_image($thumbnail, $files_path.'thumb_'.$new_name, 'jpg');
			//
			unlink($files_path.$_FILES['file']['name']);
			imagedestroy($image);
			imagedestroy($thumbnail);
		}
		//
		$data['files'] = array();
		if ($dh = opendir($files_path)){
			while (($file = readdir($dh)) !== false){
				if ($file == '.' || $file == '..' || is_dir($files_path.'/'.$file)){
				}
				else if (substr($file, 0, 6) == 'thumb_'){
					$filemtime = filemtime($files_path.'/'.$file);
					$type = strtolower(substr($file, strrpos($file, '.')+1));
					$file_size = filesize($files_path.'/'.$file);
					$data['files'][] = array('type' => $type, 'file_size' => intval($file_size / 10.24)/100, 'created_at' => $filemtime, 'thumb' => $file, 'name' => substr($file, 6));
				}
			}
			closedir($dh);
		}
		return $data;
	}

	// --------------------------------------------------------------------

	function log_in($params){
		if (isset($params['username'])){
			$db = connect_database();
			$user = $db->query('SELECT id, cid, username, `password`, level, timezone FROM `user` WHERE username = \''.$params['username'].'\'');
			if ($user = mysql_fetch_assoc($user)){
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