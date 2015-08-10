<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta name="robots" content="NOFOLLOW" />
		<meta name="viewport" content="width=device-width" />
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo BASE_URL_STATIC; ?>favicon.ico" />
		<title><?php echo isset($html_head['title']) ? $html_head['title'] : 'Admin Dashboard'; ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/basic.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/admin.css" />
		<link rel="stylesheet" href="<?php echo BASE_URL_STATIC; ?>font-awesome/css/font-awesome.min.css" />
	</head>
	<body>
		<nav>
			<div class="container">
				<h2>Admin Dashboard</h2>
<?php 	$navigation = array();
		if (isset($user) && $user['level'] > 4){
			$navigation[] = array('title' => 'Pages', 'module' => 'admin', 'method' => 'pages');
			$navigation[] = array('title' => 'Inquiry', 'module' => 'admin', 'method' => 'inquiry');
			$navigation[] = array('title' => 'Users', 'module' => 'admin', 'method' => 'users');
			$navigation[] = array('title' => 'Log Out', 'module' => 'user', 'method' => 'sign-out');
		}
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