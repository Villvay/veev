<?php
//	This script loads minimally required modules to
//	set-up Veev web application instance.
//
include 'framework/render.php';
include 'modules/admin/@admin.module.php';
date_default_timezone_set('UTC');

$lex = file_get_contents('data/lang/en.json');
$lex = json_decode(substr($lex, 3), true);
$lex['title'] = 'Setup Veev Site/Web-app';

$data = config($_POST);
define ('BASE_URL', $data['protocol'].'://'.$data['server'].$data['path']);
define ('BASE_URL_STATIC', $data['protocol'].'://'.$data['server'].$data['path'].'static/');
$yield = render_view('modules/admin/config.php', $data);
$yield = render_template('home.php', $yield, $lex['title']);

die($yield);
?>