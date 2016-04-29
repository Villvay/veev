<?php

	global $project_number, $api_key;
	$project_number = '';
	$api_key = '';

	function push_notification($to_token, $message, $url = false){
		global $project_number, $api_key;
		//
		$data = '{
  "data": {
    "message": "'.$message.'"'.($url != false ? ',
    "url" : "'.$url.'"' : '').'
  },
  "registration_ids":["'.$to_token.'"]
}';
		//
		$opts = array(
			'http'=>array(
				'method' => 'POST',
				'header' => 'Content-Type: application/json'."\r\n".
						'Authorization: key='.$api_key."\r\n",
				'content' => $data
			)
		);
		$context = stream_context_create($opts);
		//
		$result = file_get_contents('https://android.googleapis.com/gcm/send', false, $context);
		return $result;
	}
?>