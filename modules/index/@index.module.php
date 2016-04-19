<?php

	$template_file = 'home.php';

	function language($params){
		$languages = list_languages();
		if (isset($languages[$params[0]]))
			$_SESSION['lang'] = $params[0];
		header('location:'.$_SERVER['HTTP_REFERER']);
	}
	if (in_array($method, array('index', 'about', 'privacy_policy', 'terms_of_use', 'contact'))){
		$page = $method == 'index' ? 'home' : $method;
		$method = '_generic_page';
	}

	function _generic_page($params){
		global $lex, $lang, $page;
		$db = connect_database();
		//
		$content = $db->query('SELECT title, content FROM content WHERE lang = \''.$lang.'\' AND slug = \''.$page.'\'');
		if ($data = row_assoc($content)){}
		else
			$data = array('title' => $lex['not-found'], 'content' => $lex['translation-not-found']);
		//
		$data['page'] = $page;
		$data['html_head'] = array('title' => $data['title'], 'description' => shorten_string($data['title'], 250));
		return $data;
	}

?>