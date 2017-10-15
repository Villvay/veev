<?php

	$template_file = 'home.php';

	function language($params){
		$languages = list_languages();
		if (isset($languages[$params[0]]))
			$_SESSION['lang'] = $params[0];
		header('location:'.$_SERVER['HTTP_REFERER']);
	}
	if (in_array($method, array('index', 'home', 'about', 'privacy_policy', 'terms_of_use', 'contact'))){
		$page = $method == 'index' ? 'home' : $method;
		$method = '_generic_page';
	}

	//	background process
	function bgproc_example($params){
		$bg = new background();
		$data = array();
		//$data['stat'] = $bg->process('index', 'example', array());
		return $data;
	}
	function _bgstat($params){
		global $template_file;
		$template_file = '';
		//
		$bg = new background();
		$data = $bg->status($params[0]);
		return json_encode($data);
	}

	function _generic_page($params){
		global $lex, $lang, $page;
		$db = connect_database();
		//
		$content = $db->query('SELECT title, content FROM content WHERE lang = \''.$lang.'\' AND slug = \''.str_replace('_', '-', $page).'\'');
		if ($data = row_assoc($content)){}
		else
			$data = array('title' => $lex['not-found'], 'content' => '<p>'.$lex['translation-not-found'].'</p>');
		//
		$data['page'] = $page;
		$data['html_head'] = array('title' => $data['title'], 'description' => shorten_string($data['title'], 250));
		return $data;
	}

?>