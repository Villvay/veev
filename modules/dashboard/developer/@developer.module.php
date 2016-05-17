<?php

	$template_file = 'home.php';

	$app_schema = array(
			'id' 		=> array('id', 				'key' => true),
			'title' 	=> array('Title'),
			'clientid' 	=> array('Client ID',			'display' => 'readonly'),
			'secret' 	=> array('Client Secret',		'display' => 'readonly'),
			'urls' 	=> array('Authorized URLs', 	'display' => 'textarea', 'tip' => 'Enter URLs one per line'),
			'view' 	=> array('Details', 			'form' => false, 'cmd' => 'dashboard/developer/{key}'),
		);

	function developer($params){
		global $app_schema, $user;
		$data = array('schema' => $app_schema);
		$db = connect_database();
		//
		$data['apps'] = $db->query('SELECT id, title, clientid FROM app WHERE owner = '.$user['id']);
		//
		$data['html_head'] = array('title' => 'Database');
		return $data;
	}

	function create_app($params){
		global $app_schema, $user;
		$data = array('schema' => $app_schema);
		//
		print_r($params);
		//die();
		//
		$cid = strtoupper(md5(microtime()));
		$data['app'] = array('id' => 'new', 'title' => '', 'clientid' => $cid, 'secret' => base64_encode($cid), 'urls' => '');
		//
		$data['html_head'] = array('title' => 'Create an App');
		return $data;
	}

?>