<?php

	$template_file = 'home.php';

	function index($params){
		global $user;
		$content_schema = load_schema('content');
		$content_schema['lang']['enum'] = list_languages();
		//
		unset($content_schema['title']['form-width']);
		unset($content_schema['category']);
		//$content_schema['category']['enum'] = list_categories();
		$data = array('schema' => $content_schema);
		$db = connect_database();
		//
		if (isset($params['title'])){
			if ($params['id'] == 'new'){
				$params['published'] = date('Y-m-d H:i:s');
				$params['author'] = $user['id'];
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
			$data['news'] = $db->query('SELECT id, category, published, slug, lang, title FROM content WHERE author = '.$user['id'].' ORDER BY published DESC LIMIT '.($per_page * ($page - 1)).', '.$per_page);
			$pages = row_array($db->query('SELECT COUNT(*) FROM content'));
			$data['pages'] = ceil($pages[0] / $per_page);
		}
		//
		$data['html_head'] = array('title' => 'Content Dashboard');
		return $data;
	}

	function add($params){
		global $method, $lang;
		$method = 'index';
		$content_schema = load_schema('content');
		$content_schema['lang']['enum'] = list_languages();
		//
		unset($content_schema['title']['form-width']);
		unset($content_schema['category']);
		//$content_schema['category']['enum'] = list_categories();
		$data = array('schema' => $content_schema);
		$db = connect_database();
		//
		$data['article'] = array('id' => 'new', 'category' => 0, 'slug' => '', 'lang' => $lang, 'published' => date('Y-m-d H:i:s'), 'title' => '', 'content' => '');
		//
		$data['html_head'] = array('title' => 'Add Content');
		return $data;
	}

	function edit($params){
		global $method;
		$method = 'index';
		$content_schema = load_schema('content');
		$content_schema['lang']['enum'] = list_languages();
		//
		unset($content_schema['title']['form-width']);
		unset($content_schema['category']);
		//$content_schema['category']['enum'] = list_categories();
		$data = array('schema' => $content_schema);
		$db = connect_database();
		//
		$data['article'] = row_assoc($db->query('SELECT id, category, slug, lang, published, title, content FROM content WHERE id = '.$params[0]));
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

	/*function list_categories(){
		$db = connect_database();
		$categories = array();
		$cats = $db->select('id, title', 'category');
		while ($cat = row_assoc($cats))
			$categories[$cat['id']] = $cat['title'];
		return $categories;
	}*/

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