<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta name="robots" content="INDEX,FOLLOW" />
		<meta name="viewport" content="width=device-width" />
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo BASE_URL_STATIC; ?>favicon.ico" />
		<title><?php echo isset($html_head['title']) ? $html_head['title'] : $lex[$lang]['title']; ?></title>
		<meta property="og:title" content="<?php echo isset($html_head['title']) ? $html_head['title'] : $lex[$lang]['title']; ?>" />
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
				<h1><a href="<?php echo BASE_URL; ?>"><?php echo $lex[$lang]['title']; ?></a></h1>
				<div class="right f-right">
<?php 	if (isset($user)){ ?>
					<a><?php echo $user['username']; ?></a> &nbsp;
					<a href="<?php echo BASE_URL; ?>user/sign-out">Log Out</a>
<?php 	} ?>
				</div>
				<a id="hamberger"></a>
				<ul class="nav">
					<li <?php echo $method == 'index' ? 'class="current"' : ''; ?>>
						<a href="<?php echo BASE_URL; ?>"><i class="fa fa-home"></i> <?php echo $lex[$lang]['home']; ?></a>
					</li>
					<li <?php echo $module == 'index' && $method == 'blog' ? 'class="current"' : ''; ?>>
						<a href="<?php echo BASE_URL; ?>blog"><i class="fa fa-newspaper-o"></i> <?php echo $lex[$lang]['blog']; ?></a>
					</li>
					<li <?php echo $method == 'about' ? 'class="current"' : ''; ?>>
						<a href="<?php echo BASE_URL; ?>about"><i class="fa fa-info-circle"></i> <?php echo $lex[$lang]['about']; ?></a>
					</li>
					<li <?php echo $method == 'contact' ? 'class="current"' : ''; ?>>
						<a href="<?php echo BASE_URL; ?>contact"><i class="fa fa-envelope-o"></i> <?php echo $lex[$lang]['contact']; ?></a>
					</li>
<?php 	if (isset($user)){ ?>
					<li <?php echo $module == 'user' && $method == 'blog' ? 'class="current"' : ''; ?>>
						<a href="<?php echo BASE_URL; ?>user/blog"><i class="fa fa-newspaper-o"></i> <?php echo $lex[$lang]['my-blog']; ?></a>
					</li>
<?php 	} ?>
				</ul>
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
					<a href="<?php echo BASE_URL; ?>privacy_policy">Privacy Policy</a>
					&nbsp; | &nbsp;
					<a href="<?php echo BASE_URL; ?>terms_of_use">Terms of Use</a>
					&nbsp; | &nbsp;
					<a href="<?php echo BASE_URL; ?>contact"><?php echo $lex[$lang]['contact']; ?></a>
				</div>
				<ul class="social-links">
					<li><a href="https://www.linkedin.com/company/example" target="_blank"><img src="<?php echo BASE_URL_STATIC; ?>linkedin.png" /></a></li>
					<li><a href="https://plus.google.com/333" target="_blank"><img src="<?php echo BASE_URL_STATIC; ?>googleplus.png" /></a></li>
				</ul>
			</div>
		</footer>
		<script src="<?php echo BASE_URL_STATIC; ?>js/script.js"></script>
	</body>
</html>