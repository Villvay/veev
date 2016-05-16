<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta name="robots" content="INDEX,FOLLOW" />
		<meta name="viewport" content="width=device-width" />
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo BASE_URL_STATIC; ?>favicon.ico" />
		<title><?php echo isset($html_head['title']) ? $html_head['title'] : $lex['title']; ?></title>
		<meta property="og:title" content="<?php echo isset($html_head['title']) ? $html_head['title'] : $lex['title']; ?>" />
		<meta property="og:site_name" content="Website Name" />
<?php 	if (isset($html_head['description'])){ ?>
		<meta name="description" content="<?php echo $html_head['description']; ?>" />
		<meta property="og:description" content="<?php echo $html_head['description']; ?>" />
<?php 	} ?>
		<link rel="stylesheet" href="<?php echo BASE_URL_STATIC; ?>font-awesome/css/font-awesome.min.css" />
		<link rel="stylesheet" href="<?php echo BASE_URL_STATIC; ?>css/fonts.css" />
		<link rel="stylesheet" href="<?php echo BASE_URL_STATIC; ?>css/basic.css" />
		<link rel="stylesheet" href="<?php echo BASE_URL_STATIC; ?>css/admin.css" />
	</head>
	<body>
		<nav>
			<div class="container">
				<h1><a href="<?php echo BASE_URL; ?>"><?php echo $lex['title']; ?></a></h1>
				<div class="right f-right">
<?php 	if ($user['id'] > 0){ ?>
					<a href="<?php echo BASE_URL; ?>user"><?php echo $user['username']; ?></a> &nbsp;
					<a href="<?php echo BASE_URL; ?>user/sign-out"><?php echo $lex['log-out']; ?></a>
<?php 	}else{ ?>
					<a href="<?php echo BASE_URL; ?>user/log-in"><?php echo $lex['log-in']; ?></a>
<?php 	} ?>
				</div>
				<ul class="languages">
<?php 	$languages = list_languages();
		foreach ($languages as $code => $details){ ?>
					<li><a href="<?php echo BASE_URL; ?>language/<?php echo $code; ?>" title="<?php echo $details['title']; ?>"><img src="<?php echo BASE_URL_STATIC; ?><?php echo $details['flag']; ?>" /></a></li>
<?php 	} ?>
				</ul>
				<a id="hamberger"></a>
<?php 	$navigation = array(
			array('title' => $lex['about'], 'icon' => 'fa-info-circle', 'method' => 'about'),
			array('title' => $lex['contact'], 'icon' => 'fa-envelope-o', 'method' => 'contact'));
		$navigation[] = array('title' => $lex['my-content'], 'icon' => 'fa-newspaper-o', 'module' => 'dashboard');
		$navigation[] = array('title' => 'Developer', 'icon' => 'fa-code', 'module' => 'dashboard', 'method' => 'developer');
		$navigation[] = array('title' => $lex['admin-dashboard'], 'icon' => 'fa-dashboard', 'module' => 'admin');
		render_navigation($navigation); ?>
			</div>
		</nav>
		<main class="container">
<?php flash_message_dump(); ?>
<?php echo $yield; ?><br class="clear-both" /><br/>
		</main>
		<footer>
			<div class="container">
				&copy; From Year - <?php echo date('Y'); ?> All rights reserved &nbsp; | &nbsp; Company Name
				<div class="right f-right">
					<a href="<?php echo BASE_URL; ?>privacy-policy"><?php echo $lex['privacy-policy']; ?></a>
					&nbsp; | &nbsp;
					<a href="<?php echo BASE_URL; ?>terms-of-use"><?php echo $lex['terms-of-use']; ?></a>
					&nbsp; | &nbsp;
					<a href="<?php echo BASE_URL; ?>contact"><?php echo $lex['contact']; ?></a>
				</div>
				<ul class="social-links">
					<li><a href="https://www.linkedin.com/company/example" target="_blank"><img src="<?php echo BASE_URL_STATIC; ?>icons/socialmedia/social_linked_in.png" /></a></li>
					<li><a href="https://plus.google.com/333" target="_blank"><img src="<?php echo BASE_URL_STATIC; ?>icons/socialmedia/social_google.png" /></a></li>
				</ul>
			</div>
		</footer>
		<script src="<?php echo BASE_URL_STATIC; ?>js/script.js"></script>
	</body>
</html>