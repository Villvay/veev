<?php

	$template_file = 'home.php';

	function language($params){
		if (in_array($params[0], array('en', 'ch')))
			$_SESSION['lang'] = $params[0];
		header('location:'.$_SERVER['HTTP_REFERER']);
	}

	function index($params){
		global $lang;
		$data = array();
		$db = connect_database();
		//
		$content = $db->query('SELECT en, ch FROM content WHERE stub = \'home\'');
		if ($content = mysql_fetch_assoc($content)){
			$data['content'] = $content[$lang];
		}
		//
		$data['html_head'] = array('title' => 'Home: Website Title',
							'description' => '');
		return $data;
	}
	function home($params){
		redirect('index');
	}

	function about($params){
		global $lang;
		$data = array();
		$db = connect_database();
		//
		$content = $db->query('SELECT en, ch FROM content WHERE stub = \'about\'');
		if ($content = mysql_fetch_assoc($content)){
			//$data['ch'] = $content['ch'];
			//$data['en'] = $content['en'];
			$data['content'] = $content[$lang];
		}
		//
		$data['html_head'] = array('title' => 'About Us: Website Title');
		return $data;
	}

	function contact($params){
		global $lang;
		$data = array();
		$db = connect_database();
		//
		$content = $db->query('SELECT en, ch FROM content WHERE stub = \'contact\'');
		if ($content = mysql_fetch_assoc($content)){
			//$data['ch'] = $content['ch'];
			//$data['en'] = $content['en'];
			$data['content'] = $content[$lang];
		}
		//
		$data['html_head'] = array('title' => 'Contact Us: Website Title');
		return $data;
	}

	function blog($params){
		$data = array();
		$db = connect_database();
		//
		$page = 1;
		if (isset($params[0]) && $params[0] > 0)
			$page = $params[0];
		if (isset($params[1])){
			$data['article'] = mysql_fetch_assoc($db->query('SELECT id, published, title, content FROM blog WHERE id = '.$params[1]));
		}
		else{
			$per_page = 5;
			$data['blog'] = $db->query('SELECT id, published, title, content FROM blog ORDER BY published DESC LIMIT '.($per_page * ($page - 1)).', '.$per_page);
			$pages = mysql_fetch_array($db->query('SELECT COUNT(*) FROM blog'));
			$data['pages'] = ceil($pages[0] / $per_page);
		}
		$data['page'] = $page;
		//
		$data['html_head'] = array('title' => 'Blog: Website Title');
		return $data;
	}

?>