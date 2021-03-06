<?php

class MySQL{
   var $Host = DB_HOST;
   var $Database = DB_NAME;
   var $User = DB_USER;
   var $Password = DB_PASS;
   var $Link_ID = false;
   var $Query_ID = 0;
   var $Record = array();
   var $Row;
   var $Errno = 0;
   var $Error = '';

    public function __construct($database = false){
	if ($database != false)
		$this->Database = $database;
    }

   function connect(){
      if($this->Link_ID === false){
	 $this->Link_ID = mysqli_connect(DB_HOST, $this->User, $this->Password, $this->Database);
         if (mysqli_connect_errno()){
	    errorHandler(1, mysqli_connect_error(), 'framework/config.php', 26);
         }
      }
   }

   function query($Query_String){
	if (defined('DB_TUNNEL')){
		$opts = array('http' =>
		    array(
			'method'  => 'POST',
			'header'  => 'Content-Type: application/binary, Content-Transfer-Encoding: base64',
			'content' => base64_encode(gzcompress($Query_String))
		    )
		);
		$result = file_get_contents(DB_TUNNEL, false, stream_context_create($opts));
		$result = gzuncompress(base64_decode($result));
		$result = json_decode($result, true);
		return array('data' => $result, 'ptr' => 0);
	}
      $this->connect();
      $type = explode(' ', $Query_String);
      $type = strtoupper($type[0]);
      global $acl;
      if ((in_array($type, array('SELECT', 'DESCRIBE', 'SHOW')) && !in_array('view', $acl)) ||
		(in_array($type, array('INSERT', 'CREATE')) && !in_array('add', $acl)) ||
		(in_array($type, array('UPDATE', 'ALTER')) && !in_array('edit', $acl)) ||
		(in_array($type, array('DELETE', 'DROP', 'TRUNCATE')) && !in_array('delete', $acl))){
	 ob_clean();
	 global $lex, $user, $errorHandlerLatch;
	 $errorHandlerLatch = true;
	 //$yield = $Query_String;
	 //die($Query_String);
	 require_once 'templates/error_401.php';
      }
      $this->Query_ID = mysqli_query($this->Link_ID, $Query_String);
      $this->Row = 0;
      $this->Errno = mysqli_errno($this->Link_ID);
      $this->Error = mysqli_error($this->Link_ID);
      if (!$this->Query_ID){
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$i = 0;
		while (substr($backtrace[$i]['file'], strlen($backtrace[$i]['file'])-23) == 'interfaces/database.php')
			$i += 1;
		errorHandler(1, $this->Error.'<br/><pre>'.str_replace(array('FROM', 'WHERE', 'AND', 'ORDER'), array('<br/>FROM', '<br/>WHERE', '<br/> &nbsp; AND', '<br/>ORDER'), $Query_String).'</pre>', $backtrace[$i]['file'], $backtrace[$i]['line']);
      }
      return $this->Query_ID;
   }

   function select($cols, $table, $conditions = false, $offset = 0, $limit = 100, &$count = false, $debug = false){
	if (is_array($cols))
		$cols = implode(', ', $cols);
	$qstart = 'SELECT '.$cols;
	$qpart = ' FROM ';
	if (is_array($table)){
		$qpart .= '`'.$table[0].'`';
		$n_table = count($table);
		if ($n_table > 1)
			for ($i = 1; $i < $n_table; $i++)
				if (!is_array($table[$i]))
					$qpart .= ' LEFT JOIN `'.$table[$i].'` ON `'.$table[$i].'`.id = `'.$table[0].'`.'.$table[$i].'_id';
				else if (count($table[$i]) == 2)
					$qpart .= ' LEFT JOIN `'.$table[$i][0].'` ON `'.$table[$i][0].'`.`'.$table[$i][1].'` = `'.$table[0].'`.`'.$table[$i][2].'`';
				else
					$qpart .= ' LEFT JOIN `'.$table[$i][0].'` ON `'.$table[$i][0].'`.id = `'.$table[0].'`.`'.$table[$i][1].'`';
	}
	else
		$qpart .= '`'.$table.'`';
	if ($conditions == false){}
	else if (is_numeric($conditions))
		$qpart .= ' WHERE `'.(is_array($table) ? $table[0] : $table).'`.id = '.$conditions;
	else
		$qpart .= ' WHERE '.$conditions;
	//
	if ($count !== false){
		$count = mysqli_fetch_assoc($this->query('SELECT COUNT(*) AS cou'.$qpart));
		$count = $count['cou'];
	}
	//
	if ($debug)
		die($qstart.$qpart.' LIMIT '.$offset.', '.$limit);
	return $this->query($qstart.$qpart.' LIMIT '.$offset.', '.$limit);
   }

   function update($table, $params, $conditions = false){
	$cols = $this->query('DESCRIBE `'.$table.'`');
	$qpart = '';
	while ($col = mysqli_fetch_array($cols)){
		if (isset($params[$col[0]]) && $col[0] != 'id'){
			$val = $params[$col[0]];
			if ($qpart != ''){
				$qpart .= ', ';
			}
			$qpart .= $col[0].' = ';
			if ((is_numeric($val) && $val[0] != '0') || ((strpos($val, '+') || strpos($val, '-')) && substr($val, 0, strlen($col[0])) == $col[0])){
				$qpart .= $val;
			}
			else{
				$val = str_replace('\'', '\'\'', $val);
				$qpart .= '\''.$val.'\'';
			}
		}
	}
	if (isset($params['id']))
		if (!is_numeric($params['id']) || $params['id'][0] == '0')
			$params['id'] = '\''.$params['id'].'\'';
	if ($conditions == false)
		$sql = 'UPDATE `'.$table.'` SET '.$qpart.' WHERE id = '.$params['id'];
	else
		$sql = 'UPDATE `'.$table.'` SET '.$qpart.' WHERE '.$conditions;
	$this->query($sql);
   }

   function insert($table, $params){
	$cols = $this->query('DESCRIBE `'.$table.'`');
	$qpart1 = '';
	$qpart2 = '';
	while ($col = mysqli_fetch_array($cols)){
		if (isset($params[$col[0]])){
			$val = $params[$col[0]];
			if ($qpart1 != ''){
				$qpart1 .= ', ';
				$qpart2 .= ', ';
			}
			$qpart1 .= '`'.$col[0].'`';
			if (is_numeric($val) && $val[0] != '0'){
				$qpart2 .= $val;
			}
			else{
				$val = str_replace('\'', '\'\'', $val);
				$qpart2 .= '\''.$val.'\'';
			}
		}
	}
	$sql = 'INSERT INTO `'.$table.'`('.$qpart1.') VALUES('.$qpart2.')';
	$this->query($sql);
	return mysqli_insert_id($this->Link_ID);
   }

   function delete($table, $id){
	if (is_numeric($id))
		$sql = 'DELETE FROM `'.$table.'` WHERE id = '.$id;
	else
		$sql = 'DELETE FROM `'.$table.'` WHERE '.$id;
	$this->query($sql);
   }

   function next_record(){
      $this->Record = mysqli_fetch_array($this->Query_ID);
      $this->Row += 1;
      $this->Errno = mysqli_errno();
      $this->Error = mysqli_error();
      $stat = is_array($this->Record);
      if (!$stat){
         mysqli_free_result($this->Query_ID);
         $this->Query_ID = 0;
      }
      return $this->Record;
   }

   function num_rows(){
      return mysqli_num_rows($this->Query_ID);
   }

   function close(){
      if($this->Link_ID !== false){
         mysqli_close($this->Link_ID);
      }
   }
   
   function insert_id(){
      return mysqli_insert_id($this->Link_ID);
   }
   
   function affected_rows(){
      return mysqli_affected_rows($this->Link_ID);
   }
}

function row_assoc($resource){
	return mysqli_fetch_assoc($resource);
}

function row_array($resource){
	return mysqli_fetch_array($resource);
}

function load_schema($file){
	if (file_exists('schema/'.$file.'.json')){
		$data = json_decode(file_get_contents('schema/'.$file.'.json'), true);
		if (!$data)
			$error = 'Invalid Schema JSON format in: schema/'.$file.'.json';
	}
	else
		$error = 'Schema JSON file not found. Expected: schema/'.$file.'.json';
	if (isset($error)){
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		errorHandler(1, $error, $backtrace[0]['file'], $backtrace[0]['line']);
	}
	return $data;
}

function write_schema($file, $schema){
	$data = json_encode($schema);
	file_put_contents('schema/'.$file.'.json', $data);
}

?>