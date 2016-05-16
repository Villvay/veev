<?php

	//	This module is meant to be a submodule. You may move this inside admin module without any change.
	$template_file = 'home.php';

	$app_schema = array(
			'id' 		=> array('id', 				'table' => false, 'key' => true),
			'title' 	=> array('Title'),
			'clientid' 	=> array('Client ID',			'display' => 'readonly'),
			'secret' 	=> array('Client Secret',		'table' => false, 'display' => 'readonly'),
			'urls' 	=> array('Authorized URLs', 	'table' => false, 'display' => 'textarea', 'tip' => 'Enter each URL one under another in seperate lines'),
			'status' 	=> array('Status',			'form' => false, 'enum' => array(0 => 'Inactive', 1 => 'Active', -1 => 'Pending Deletion')),
			'edit' 	=> array('Details', 			'form' => false, 'cmd' => $module.'/developer/app/{key}'),
			'delete' 	=> array('Delete', 			'form' => false, 'cmd' => $module.'/developer/delete-app/{key}', 'confirm' => true),
		);

	function developer($params){
		global $app_schema, $user;
		$data = array('schema' => $app_schema);
		$db = connect_database();
		//
		$data['apps'] = $db->query('SELECT id, title, clientid, status FROM app WHERE owner = '.$user['id']);
		//
		$data['html_head'] = array('title' => 'My Apps');
		return $data;
	}

	function create_app($params){
		global $app_schema, $user, $module;
		$data = array('schema' => $app_schema);
		//
		if (isset($params['id'])){
			$db = connect_database();
			unset($params['id']);
			$params['owner'] = $user['id'];
			$db->insert('app', $params);
			redirect($module, 'developer');
		}
		//
		$cid = strtoupper(md5(microtime()));
		$csecret = str_replace(array('/', '='), '', base64_encode($cid));
		$data['app'] = array('id' => 'new', 'title' => '', 'clientid' => $cid, 'secret' => $csecret, 'urls' => '');
		//
		$data['html_head'] = array('title' => 'Create an App');
		return $data;
	}

	function app($params){
		global $app_schema, $user, $module;
		$data = array('schema' => $app_schema);
		$db = connect_database();
		//
		if (isset($params['id'])){
			$db->update('app', $params);
			redirect($module, 'developer');
		}
		//
		if (!$data['app'] = row_assoc($db->select('*', 'app', 'id = '.$params[0].' AND owner = '.$user['id'])))
			redirect($module, 'developer');
		//
		$data['html_head'] = array('title' => $data['app']['title']);
		return $data;
	}

	function delete_app($params){
		global $user, $module;
		$db = connect_database();
		$db->delete('app', 'id = '.$params[0].' AND owner = '.$user['id']);
		redirect($module, 'developer');
	}

?>