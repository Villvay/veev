<?php
	$html_head['title'] = '401 Unauthorized';
	global $acl;
	$yield = '<h2>401 - Unauthorized</h2>
			<p>You are not permitted to do this action. Please contact your administrator.</p>';
	include isset($template_file) ? $template_file : 'home.php';
?>