<?php

	$template_file = 'admin.php';

	/*$table_schema = array(
			'Type' 	=> array('Type', 	'function' => '_type'),
			'Size' 	=> array('Size', 	'function' => '_type_length'),
			'Null' 	=> array('Null'),
			'Key' 	=> array('Key'),
			'Default' 	=> array('Default'),
			'Extra' 	=> array('Extra'),
		);*/

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
		/*global $table_schema;
		write_schema('dbtable', $table_schema);*/
		$data = array();
		$db = connect_database();
		//
		if (isset($params['sql']) && is_array($params['sql']))
			foreach ($params['sql'] as $sql)
				if (trim($sql) != '')
					$db->query($sql);
		//
		$data['import'] = @unserialize(gzinflate(file_get_contents('data/schema.db')));
		if (!$data['import'])
			$data['import'] = array();
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
		$data['schema'] = load_schema('dbtable');
		//
		if (isset($params[0]) && $params[0] == 'export'){	//	CHECK IF THERE ARE CONFLICTS
			$result = file_put_contents('data/schema.db', gzdeflate(serialize($data['tables'])));
			if ($result)
				flash_message('Database schema is exported', 'success');
			else
				flash_message('No permission to write to data/schema.db', 'warning');
			redirect('admin/developer', 'database');
		}
		//
		$data['html_head'] = array('title' => 'Database');
		return $data;
	}

	function vcs($params){
		$data = array();
		//
		flash_message('Under Construction', 'warning');
		//
		$data['html_head'] = array('title' => 'Version Control System');
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
					($meta['Size'] != '' ? '('.$meta['Size'].')' : '').($meta['Null'] == 'NO' ? ' NOT NULL' : '').//($meta['Default'] == '' ? '' : ' DEFAULT \''.$meta['Default'].'\'').
					($meta['Extra'] == 'auto_increment' ? ' AUTO_INCREMENT' : '').($meta['Null'] == 'YES' ? (' DEFAULT '.($meta['Default'] == '' ? 'NULL' : '\''.$meta['Default'].'\'')) : '').',';
		}
		if (count($primKey) > 0)
			$query .= "\n".'  PRIMARY KEY (`'.implode('`,`', $primKey).'`)'."\n".');';
		else
			$query = substr($query, 0, -1).');';
		return $query;
	}

	function _drop_query($table){
		return 'DROP TABLE IF EXISTS `'.$table.'`;';
	}

?>