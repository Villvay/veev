<?php
	http_response_code(404);
	$html_head['title'] = '404 Not Found';
	$yield = '<h2>'.$lex['not-found'].'</h2>
			<p>'.$lex['page-not-found'].'</p>';
	include isset($template_file) ? $template_file : 'home.php';
?>
