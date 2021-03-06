<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta name="robots" content="NOFOLLOW" />
		<meta name="viewport" content="width=device-width" />
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo BASE_URL_STATIC; ?>favicon.ico" />
		<title><?php echo isset($html_head['title']) ? $html_head['title'] : 'Admin Dashboard'; ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>font-awesome/css/font-awesome.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/fonts.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/basic.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/admin.css" />
		<script src="<?php echo BASE_URL_STATIC; ?>js/arc.js"></script>
	</head>
	<body>
		<nav>
			<div class="container">
				<h1><a href="<?php echo BASE_URL; ?>admin"><?php echo $lex['admin-dashboard']; ?></a></h1>
				<div class="right f-right">
<?php 	if ($user['id'] > 0){ ?>
					<a href="<?php echo BASE_URL; ?>user"><?php echo $user['username']; ?></a> &nbsp;
					<a href="<?php echo BASE_URL; ?>user/sign-out"><?php echo $lex['log-out']; ?></a>
<?php 	} ?>
				</div>
				<ul class="languages">
<?php 	$languages = list_languages();
		foreach ($languages as $code => $details){ ?>
					<li><a href="<?php echo BASE_URL; ?>language/<?php echo $code; ?>" title="<?php echo $details['title']; ?>"><img src="<?php echo BASE_URL_STATIC; ?><?php echo $details['flag']; ?>" /></a></li>
<?php 	} ?>
				</ul>
<?php 	$navigation = array();
		$navigation[] = array('title' => 'Users', 'icon' => 'fa-user', 'module' => 'admin', 'method' => 'users');
		$navigation[] = array('title' => 'Groups', 'icon' => 'fa-users', 'module' => 'admin', 'method' => 'groups');
		$navigation[] = array('title' => 'Configure', 'icon' => 'fa-dashboard', 'module' => 'admin', 'method' => 'config');
		$navigation[] = array('title' => 'Services', 'icon' => 'fa-cogs', 'module' => 'admin', 'method' => 'services');
		$navigation[] = array('title' => 'Database', 'icon' => 'fa-database', 'module' => 'admin/developer', 'method' => 'database');
		$navigation[] = array('title' => 'Logs', 'icon' => 'fa-exclamation-triangle', 'module' => 'admin', 'method' => 'logs');
		//$navigation[] = array('title' => 'Version Control', 'icon' => 'fa-upload', 'module' => 'admin/developer', 'method' => 'vcs');
		$navigation[] = array('title' => 'Pages', 'icon' => 'fa-newspaper-o', 'module' => 'admin', 'method' => 'pages');
		$navigation[] = array('title' => 'View Site', 'icon' => 'fa-globe', 'module' => 'index');
		render_navigation($navigation); ?>
			</div>
		</nav>
		<main class="container">
<?php flash_message_dump(); ?>
<?php echo $yield; ?>
		</main>
		<script src="<?php echo BASE_URL_STATIC; ?>js/script.js"></script>
	</body>
</html>