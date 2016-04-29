<?php

	$template_file = 'home.php';
	//if (isset($params[0]) && is_numeric($params[0]))
	if (is_numeric($method) || $method == '*'){
		$method = 'index';
	}

	function index($params){
		$data = array();
		$db = connect_database();
		//
		$page = 1;
		if (isset($params[0]) && $params[0] > 0)
			$page = $params[0];
		if (isset($params[0])){
			$data['article'] = row_assoc($db->query('SELECT id, published, title, content FROM news WHERE id = '.$params[0]));
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