<?php
	http_response_code(500);
	$html_head['title'] = '500 Internal Server Error';
	global $acl;
	$yield = '<h2>500 Internal Server Error</h2>
			<p>The server encountered an error while processing your request.</p>'.
			(ON_ERROR == 'DISPLAY' ? '<pre>'.$yield.'</pre>' : '');
	include isset($template_file) ? $template_file : 'home.php';
?>
