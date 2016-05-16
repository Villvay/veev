<?php

	$template_file = 'home.php';
	//if (isset($params[0]) && is_numeric($params[0]))
	/*if (is_numeric($method) || $method == '*'){
		$method = 'index';
	}

	function index($params){
		global $user;
		$data = array();
		$db = connect_database();
		//
		$page = 1;
		if (isset($params[0]) && $params[0] > 0)
			$page = $params[0];
		if (isset($params[0])){
			$data['article'] = row_assoc($db->query('SELECT id, published, title, content FROM content WHERE id = '.$params[0]));
		}
		else{
			$per_page = 5;
			$data['content'] = $db->query('SELECT id, published, title, content FROM content WHERE author = '.$user['id'].' ORDER BY published DESC LIMIT '.($per_page * ($page - 1)).', '.$per_page);
			$pages = row_array($db->query('SELECT COUNT(*) FROM content WHERE author = '.$user['id']));
			$data['pages'] = ceil($pages[0] / $per_page);
		}
		$data['page'] = $page;
		//
		$data['html_head'] = array('title' => 'News: Website Title');
		return $data;
	}*/

	// --------------------------------------------------------------------

	$content_schema = array(
						'id' 			=> array('ID', 			'table' => false, 'key' => true),
						'title' 		=> array('Title'),
						'slug' 		=> array('Slug', 		'form-width' => 50),
						'lang' 		=> array('Language', 	'form-width' => 50),
						'content' 		=> array('Content', 	'table' => false, 'display' => 'richtext'),
						'published' 	=> array('Published on', 	'form' => false),
						'slides' 		=> array('Slides', 		'display' => 'folder', 'path' => 'dashboard/images/uploads/{slug}', 'table' => false),
						'edit' 		=> array('Edit', 		'form' => false, 'cmd' => 'dashboard/edit/{key}', 'default' => true),
						'delete' 		=> array('Delete', 		'form' => false, 'cmd' => 'dashboard/delete/{key}', 'confirm' => true),
						//'view' 		=> array('View', 		'form' => false, 'cmd' => '*/{key}')
					);

	function index($params){
		global $content_schema;
		$content_schema['lang']['enum'] = list_languages();
		$data = array('schema' => $content_schema);
		$db = connect_database();
		//
		if (isset($params['title'])){
			if ($params['id'] == 'new'){
				$params['published'] = date('Y-m-d H:i:s');
				$db->insert('content', $params);
			}
			else
				$db->update('content', $params);
			flash_message('Content is saved', 'success');
			redirect('dashboard');
		}
		else{
			$per_page = 5;
			$page = 1;
			if (isset($params[0]))
				$page = $params[0];
			$data['page'] = $page;
			$data['news'] = $db->query('SELECT id, published, slug, lang, title FROM content ORDER BY published DESC LIMIT '.($per_page * ($page - 1)).', '.$per_page);
			$pages = row_array($db->query('SELECT COUNT(*) FROM content'));
			$data['pages'] = ceil($pages[0] / $per_page);
		}
		//
		$data['html_head'] = array('title' => 'Content Dashboard');
		return $data;
	}

	function add($params){
		global $content_schema, $method, $lang;
		$method = 'index';
		$content_schema['lang']['enum'] = list_languages();
		$data = array('schema' => $content_schema);
		$db = connect_database();
		//
		$data['article'] = array('id' => 'new', 'slug' => '', 'lang' => $lang, 'published' => date('Y-m-d H:i:s'), 'title' => '', 'content' => '');
		//
		$data['html_head'] = array('title' => 'Add Content');
		return $data;
	}

	function edit($params){
		global $content_schema, $method;
		$method = 'index';
		$content_schema['lang']['enum'] = list_languages();
		$data = array('schema' => $content_schema);
		$db = connect_database();
		//
		$data['article'] = row_assoc($db->query('SELECT id, slug, lang, published, title, content FROM content WHERE id = '.$params[0]));
		//
		$data['html_head'] = array('title' => 'Edit Content');
		return $data;
	}

	function delete($params){
		global $user;
		$db = connect_database();
		//
		$db->delete('content', 'author = '.$user['id'].' AND id = '.$params[0]);
		flash_message('Content is deleted', 'success');
		redirect('dashboard');
		//
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

?>