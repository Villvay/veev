<?php

// Self cunfiguration
if (file_exists('configure.php') && (!file_exists('.htaccess') || !file_exists('framework/config.php')))
	require_once 'configure.php';

// Load Configurations
require_once 'framework/config.php';

// Route The Request Query String to Module & View
require_once 'framework/router.php';

?>