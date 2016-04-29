<?php
	$html_head['title'] = '404 Not Found';
	$yield = '<h2>404 - Not Found</h2>
			<p>The page you requested was not found.</p>';
	include isset($template_file) ? $template_file : 'home.php';
?>