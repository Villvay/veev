<?php

date_default_timezone_set('UTC');
$protocol = isset($_POST['protocol']) ? $_POST['protocol'] : 'http';
$port = isset($_POST['port']) ? $_POST['port'] : '';
$path = isset($_POST['path']) ? $_POST['path'] : rtrim($_SERVER['REQUEST_URI'], 'configure.php');
$server = $_SERVER['SERVER_NAME'];
$lex = file_get_contents('data/lang/en.json');
$lex = json_decode(substr($lex, 3), true);
$lex['title'] = 'Setup Veev Site/Web-app';
include 'framework/render.php';
if (isset($_POST['htaccess']) && isset($_POST['config'])){
	error_reporting(E_ERROR);
	//
	$conn = mysqli_connect($_POST['db_host'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_name']);
	if (!$conn){
		$conn = mysqli_connect($_POST['db_host'], $_POST['db_user'], $_POST['db_pass']);
		if (!$conn)
			flash_message('Database credentials are wrong.', 'error');
		else{
			mysqli_query($conn, 'CREATE DATABASE `'.$_POST['db_name'].'`');
			mysqli_query($conn, 'USE `'.$_POST['db_name'].'`');
		}
	}
	if (!!$conn){
		if (file_exists('database.sql')){
			$lines = file('database.sql');
			$query = '';
			foreach ($lines as $line)
				if (substr($line, 0, 2) == '--' || (substr($line, 0, 2) == '/*' && (substr($line, -4, 3) == '*/;' || substr($line, -3) == '*/;')) || trim($line) == ''){}
				else{
					$query .= $line;
					if (substr($line, -1) == ';' || substr($line, -2, 1) == ';'){
						mysqli_query($conn, $query);
						$query = '';
					}
				}
		}
		//
		file_put_contents('.htaccess', $_POST['htaccess']);
		$result = file_put_contents('framework/config.php', $_POST['config']);
		if (!$result)
			flash_message('Cannot write config file. Please manually upload .htaccess and framework/config.php', 'error');
		else
			header('location:http://'.$server.$path);
	}
}

define ('BASE_URL', 'http://'.$_SERVER['SERVER_NAME'].rtrim($_SERVER['REQUEST_URI'], 'configure.php'));
define ('BASE_URL_STATIC', 'http://'.$_SERVER['SERVER_NAME'].rtrim($_SERVER['REQUEST_URI'], 'configure.php').'static/');
ob_start();
?>
<h2>Configuration</h2>
<?php flash_message_dump(); ?>
<form name="config" method="post">
	<input type="hidden" name="server" value="<?php echo $server; ?>" />
	<p>You are seeing this because you need to configure your web application.</p>
	<p>If you do not understand, just hit "Set up Veev" with these default configurations; which should work for most scenarios.</p>
	<table width="100%">
		<tr>
			<td valign="top">
				<h3>Paths</h3>
				<table>
					<tr>
						<td>PROTOCOL</td>
						<td><input type="text" name="protocol" value="<?php echo $protocol; ?>" /></td>
					</tr>
					<tr>
						<td>PORT</td>
						<td><input type="text" name="port" value="<?php echo $port; ?>" /></td>
					</tr>
					<tr>
						<td>PATH</td>
						<td><input type="text" name="path" value="<?php echo $path; ?>" /></td>
					</tr>
					<tr>
						<td>BASE_URL_STATIC</td>
						<td><input type="text" name="base_url_static" value="<?php echo isset($_POST['base_url_static']) ? $_POST['base_url_static'] : $protocol.'://'.$server.$path.'static/'; ?>" /></td>
					</tr>
					<tr>
						<td>STATIC_FILES_ROOT</td>
						<td><input type="text" name="static_files_root" value="<?php echo isset($_POST['static_files_root']) ? $_POST['static_files_root'] : './static/'; ?>" /></td>
					</tr>
				</table>
				<br/>
				<h3>Database</h3>
				<table>
					<tr>
						<td>DB_HOST</td>
						<td><input type="text" name="db_host" value="<?php echo isset($_POST['db_host']) ? $_POST['db_host'] : 'localhost'; ?>" /></td>
					</tr>
					<tr>
						<td>DB_NAME</td>
						<td><input type="text" name="db_name" value="<?php echo isset($_POST['db_name']) ? $_POST['db_name'] : str_replace('/', '', $path); ?>" /></td>
					</tr>
					<tr>
						<td>DB_USER</td>
						<td><input type="text" name="db_user" value="<?php echo isset($_POST['db_user']) ? $_POST['db_user'] : 'root'; ?>" /></td>
					</tr>
					<tr>
						<td>DB_PASS</td>
						<td><input type="text" name="db_pass" value="<?php echo isset($_POST['db_pass']) ? $_POST['db_pass'] : ''; ?>" /></td>
					</tr>
				</table>
				<?php /* br/>
				<h3>32k Settings</h3>
				<table>
					<tr>
						<td>32K_APP_ID</td>
<?php
$thirty2k_app_id = isset($_POST['32k_app_id']) ? $_POST['32k_app_id'] : base64_encode(sha1(time().rand(1000000, 9999999), true));
$thirty2k_secret = base64_encode(md5($thirty2k_app_id.'NaCl', true));
?>
						<td><input type="text" name="32k_app_id" value="<?php echo $thirty2k_app_id; ?>" /></td>
					</tr>
					<tr>
						<td>32K_SECRET</td>
						<td><input type="text" name="32k_secret" value="<?php echo $thirty2k_secret; ?>" /></td>
					</tr>
				</table */ ?>
			</td>
			<td>&nbsp;&nbsp;</td>
			<td>
				<h3>Configuration Files</h3>
				.htaccess<br/>
				<textarea name="htaccess" rows="5"><?php echo isset($_POST['htaccess']) ? $_POST['htaccess'] : ''; ?></textarea>
				<br/><br/>
				config.php<br/>
				<textarea name="config" rows="13"><?php echo isset($_POST['config']) ? $_POST['config'] : ''; ?></textarea>
				<br/><br/>
				<input type="submit" value="Set up Veev" />
			</td>
		</tr>
	</table>
	<p><b>N.B:</b> If you would see a 500 internal server error right after setting up, you may need to enable mod_rewrite apache module on server.</p>
	<p>Once you set up on live, remove this 'configure.php' file from the site root. It is not a big problem if you leave it though..</p>
</form>
<script>
	function generate_files(){
		document.config.base_url_static.value = document.config.protocol.value+'://'+document.config.server.value+(document.config.port.value==''?'':':'+document.config.port.value)+document.config.path.value+'static/';
		document.config.htaccess.value =
			'RewriteEngine On\n'+
			'RewriteBase '+document.config.path.value+'\n'+
			'RewriteCond %{REQUEST_URI} !^'+document.config.path.value+(document.config.static_files_root.value.substring(2))+'\n'+
			'RewriteRule (.*)$ index.php [L]\n';
		//
		document.config.config.value =
			'<'+'?php\n'+
			'define (\'PROTOCOL\', \''+document.config.protocol.value+'\');\n'+
			'define (\'PORT\', \''+document.config.port.value+'\');\n'+
			'define (\'PATH\', \''+document.config.path.value+'\');\n'+
			'define (\'BASE_URL_STATIC\', \''+document.config.base_url_static.value+'\');\n'+
			'define (\'STATIC_FILES_ROOT\', \''+document.config.static_files_root.value+'\');\n'+
			'\n'+
			'// Database settings\n'+
			'define (\'DB_HOST\', \''+document.config.db_host.value+'\');\n'+
			'define (\'DB_NAME\', \''+document.config.db_name.value+'\');\n'+
			'define (\'DB_USER\', \''+document.config.db_user.value+'\');\n'+
			'define (\'DB_PASS\', \''+document.config.db_pass.value+'\');\n'+
			'\n'+
			'?>';
	}
	for (var i = 0; i < document.config.elements.length; i++){
		document.config.elements[i].onkeyup = generate_files;
		document.config.elements[i].onchange = generate_files;
	}
	document.config.path.onchange = document.config.static_files_root.onchange = function(){
		if (this.value.substring(this.value.length-1) != '/')
			this.value += '/';
		generate_files();
	};
	/*document.config.protocol.onkeyup = document.config.protocol.onchange = document.config.port.onkeyup = document.config.port.onchange = function(){
		generate_files();
	};*/
<?php if (!isset($_POST['htaccess']) && !isset($_POST['config'])){ ?>
	generate_files();
<?php } ?>
</script>
<!--style>
	/*input[type="text"]{
		width:300px;
	}
	textarea{
		width:500px;
	}
	tr td:first-child{
		width:162px;
	}*/
</style-->
<?php
$yield = ob_get_contents();
ob_end_clean();
$yield = render_template('home.php', $yield, array('title' => 'Configure Veev Website'));
die($yield); ?>
