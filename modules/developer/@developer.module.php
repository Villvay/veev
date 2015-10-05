<?php

	if (!isset($_SESSION['user']) && $_SESSION['user']['level'] < 5 && $method != 'log_in')
		redirect('user', 'log_in');

	$template_file = 'admin.php';

	function index($params){
		$data = array();
		//
		$data['html_head'] = array('title' => 'Developer Dashboard');
		return $data;
	}

	$table_schema = array(
			/*'Field' 	=> array('Field'),*/
			'Type' 	=> array('Type', 	'function' => '_type'),
			'Size' 	=> array('Size', 	'function' => '_type_length'),
			'Null' 	=> array('Null'),
			'Key' 	=> array('Key'),
			'Default' 	=> array('Default'),
			'Extra' 	=> array('Extra'),
		);

	function _type($row){
		$tmp = explode('(', $row['Type']);
		return $tmp[0];
	}
	function _type_length($row){
		$tmp = explode('(', $row['Type']);
		if (count($tmp) == 2)
			return str_replace(')', '', $tmp[1]);
		else
			return '';
	}

	function database($params){
		global $table_schema;
		$data = array();
		$db = connect_database();
		//
		$data['import'] = unserialize(gzinflate(file_get_contents('data/db.schema.ar.gz')));
		//print_r($file);
		//
		$tables = $db->query('SHOW tables');
		$data['tables'] = array();
		while ($table = row_array($tables)){
			$columns = $db->query('DESCRIBE `'.$table[0].'`');
			$tmp = array();
			$cname = '';
			while ($column = row_assoc($columns)){
				$field = $column['Field'];
				unset($column['Field']);
				$size = explode('(', $column['Type']);
				$column['Type'] = $size[0];
				$column['Size'] = count($size) == 2 ? str_replace(')', '', $size[1]) : '';
				$tmp[$field] = $column;
			}
			$data['tables'][$table[0]] = $tmp;
		}
		$data['schema'] = $table_schema;
		//
		if (isset($params[0]) && $params[0] == 'export'){	//	CHECK IF THERE ARE CONFLICTS
			file_put_contents('data/db.schema.ar.gz', gzdeflate(serialize($data['tables'])));
			flash_message('Database schema is exported', 'success');
			redirect('developer', 'database');
		}
		//
		$data['html_head'] = array('title' => 'Database');
		return $data;
	}

	function _create_query($table, $schema){
		$query = 'CREATE TABLE `'.$table.'` (';
		$primKey = array();
		foreach ($schema as $col => $meta){
			//print_r($meta);
			if ($meta['Key'] == 'PRI')
				$primKey[] = $col;
			$query .= "\n".'  `'.$col.'` '.$meta['Type'].
					($meta['Size'] != '' ? '('.$meta['Size'].')' : '').
					($meta['Null'] == 'NO' ? ' NOT NULL' : '').
					($meta['Extra'] == 'auto_increment' ? ' AUTO_INCREMENT' : '').
					($meta['Null'] == 'YES' ? (' DEFAULT '.($meta['Default'] == '' ? 'NULL' : '\''.$meta['Default'].'\'')) : '').',';
		}
		if (count($primKey) > 0)
			$query .= "\n".'  PRIMARY KEY (`'.implode('`,`', $primKey).'`)'."\n".')';
		else
			$query = substr($query, 0, -1).')';
		return $query;
	}

	function _drop_query($table){
		return 'DROP TABLE IF EXISTS `'.$table.'`;';
	}

?>