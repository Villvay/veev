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
		$data = array();
		if (isset($params[0]) && $params[0] != 'index'){
			global $template_file;
			$template_file = '';
			//
			//	Object to communicate with b ackground processes
			$bg = new background();
			//
			//	Starts a new background process
			if ($params[0] == 'start'){
				$data = $bg->process('index', 'example', array());
				$_SESSION['background-job-id'] = $data['jobId'];
				return json_encode($data);
			}
			//
			//	Get status of a background process
			else if ($params[0] == 'status'){
				$data = $bg->status($_SESSION['background-job-id']);//$params[1]
				return json_encode($data, JSON_PRETTY_PRINT);
			}
		}
		return $data;
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