<?php

	function index($params){
		$db = connect_database();
		$query = gzuncompress(base64_decode(file_get_contents('php://input')));
		$data = $db->query($query);
		$dat = array();
		while ($row = row_assoc($data))
			$dat[] = $row;
		$data = base64_encode(gzcompress(json_encode($dat)));
		die($data);
	}

?>