<?php

	$template_file = 'home.php';

	function language($params){
		if (in_array($params[0], array('en', 'zh', 'de', 'ru', 'fr')))
			$_SESSION['lang'] = $params[0];
		header('location:'.$_SERVER['HTTP_REFERER']);
	}

	function index($params){
		global $lang;
		$db = connect_database();
		//
		$content = $db->query('SELECT title, content FROM content WHERE lang = \''.$lang.'\' AND slug = \'home\'');
		if ($data = row_assoc($content)){}
		else
			$data = array('title' => '404: Not Found', 'content' => 'The page you requested is not available in the language you have selected.');
		//
		$data['html_head'] = array('title' => $data['title'], 'description' => shorten_string($data['title'], 250));
		return $data;
	}
	function home($params){
		redirect('index');
	}

	function about($params){
		global $lang;
		$db = connect_database();
		//
		$content = $db->query('SELECT title, content FROM content WHERE lang = \''.$lang.'\' AND slug = \'about\'');
		if ($data = row_assoc($content)){}
		else
			$data = array('title' => '404: Not Found', 'content' => 'The page you requested is not available in the language you have selected.');
		//
		$data['html_head'] = array('title' => $data['title'], 'description' => shorten_string($data['title'], 250));
		return $data;
	}

	function privacy_policy($params){
		global $lang;
		$db = connect_database();
		//
		$content = $db->query('SELECT title, content FROM content WHERE lang = \''.$lang.'\' AND slug = \'privacy-policy\'');
		if ($data = row_assoc($content)){}
		else
			$data = array('title' => '404: Not Found', 'content' => 'The page you requested is not available in the language you have selected.');
		//
		$data['html_head'] = array('title' => $data['title'], 'description' => shorten_string($data['title'], 250));
		return $data;
	}

	function terms_of_use($params){
		global $lang;
		$db = connect_database();
		//
		$content = $db->query('SELECT title, content FROM content WHERE lang = \''.$lang.'\' AND slug = \'terms-of-use\'');
		if ($data = row_assoc($content)){}
		else
			$data = array('title' => '404: Not Found', 'content' => 'The page you requested is not available in the language you have selected.');
		//
		$data['html_head'] = array('title' => $data['title'], 'description' => shorten_string($data['title'], 250));
		return $data;
	}

	function contact($params){
		global $lang;
		$db = connect_database();
		//
		$content = $db->query('SELECT title, content FROM content WHERE lang = \''.$lang.'\' AND slug = \'contact\'');
		if ($data = row_assoc($content)){}
		else
			$data = array('title' => '404: Not Found', 'content' => 'The page you requested is not available in the language you have selected.');
		//
		$data['html_head'] = array('title' => $data['title'], 'description' => shorten_string($data['title'], 250));
		return $data;
	}

	function news($params){
		$data = array();
		$db = connect_database();
		//
		$page = 1;
		if (isset($params[0]) && $params[0] > 0)
			$page = $params[0];
		if (isset($params[1])){
			$data['article'] = row_assoc($db->query('SELECT id, published, title, content FROM news WHERE id = '.$params[1]));
		}
		else{
			$per_page = 5;
			$data['news'] = $db->query('SELECT id, published, title, content FROM news ORDER BY published DESC LIMIT '.($per_page * ($page - 1)).', '.$per_page);
			$pages = row_array($db->query('SELECT COUNT(*) FROM news'));
			$data['pages'] = ceil($pages[0] / $per_page);
		}
		$data['page'] = $page;
		//
		$data['html_head'] = array('title' => 'News: Website Title');
		return $data;
	}

?>