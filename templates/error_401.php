<?php
	http_response_code(401);
	$html_head['title'] = '401 Unauthorized';
	global $acl;
	$yield = '<h2>401 Unauthorized</h2>
			<p>You are not authorized for this action. Please contact your administrator.</p>'.
			'<pre>'.print_r($acl, true).'</pre>';
	include isset($template_file) ? $template_file : 'home.php';
	die();
?>
