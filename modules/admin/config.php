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
						<td><input type="text" name="base_url_static" value="<?php echo BASE_URL_STATIC; ?>" /></td>
					</tr>
					<tr>
						<td>STATIC_FILES_ROOT</td>
						<td><input type="text" name="static_files_root" value="<?php echo $static_files_root; ?>" /></td>
					</tr>
					<tr>
						<td>BACKEND_SERVICE_PORT</td>
						<td><input type="text" name="backend_service_port" value="<?php echo $backend_service_port; ?>" /></td>
					</tr>
				</table>
				<br/>
				<h3>Instance</h3>
				<label>
					<input type="radio" name="instance" value="development"<?php echo $instance == 'development' ? ' checked' : ''; ?> />
					Development
				</label>
				<label>
					<input type="radio" name="instance" value="testing"<?php echo $instance == 'testing' ? ' checked' : ''; ?> />
					Testing
				</label>
				<label>
					<input type="radio" name="instance" value="production"<?php echo $instance == 'production' ? ' checked' : ''; ?> />
					Production
				</label>
				<br/><br/>
				<h3>OAuth</h3>
				<table>
					<tr>
						<td>GOOGLE_CLIENT_ID</td>
						<td><input type="text" name="google_client_id" value="<?php echo $google_client_id; ?>" /></td>
					</tr>
					<tr>
						<td>GOOGLE_SECRET</td>
						<td><input type="text" name="google_secret" value="<?php echo $google_secret; ?>" /></td>
					</tr>
					<tr>
						<td>FACEBOOK_CLIENT_ID</td>
						<td><input type="text" name="facebook_client_id" value="<?php echo $facebook_client_id; ?>" /></td>
					</tr>
					<tr>
						<td>FACEBOOK_SECRET</td>
						<td><input type="text" name="facebook_secret" value="<?php echo $facebook_secret; ?>" /></td>
					</tr>
				</table>
				<br/>
				<h3>Database</h3>
				<table>
					<tr>
						<td>DB_HOST</td>
						<td><input type="text" name="db_host" value="<?php echo $db_host; ?>" /></td>
					</tr>
					<tr>
						<td>DB_NAME</td>
						<td><input type="text" name="db_name" value="<?php echo $db_name; ?>" /></td>
					</tr>
					<tr>
						<td>DB_USER</td>
						<td><input type="text" name="db_user" value="<?php echo $db_user; ?>" /></td>
					</tr>
					<tr>
						<td>DB_PASS</td>
						<td><input type="text" name="db_pass" value="<?php echo $db_pass; ?>" /></td>
					</tr>
				</table>
			</td>
			<td>&nbsp;&nbsp;</td>
			<td>
				<h3>Configuration Files</h3>
				.htaccess<br/>
				<textarea name="htaccess" rows="4"><?php echo $htaccess; ?></textarea>
				<br/><br/>
				config.php<br/>
				<textarea name="config" rows="34"><?php echo $config; ?></textarea>
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
			'<'+'?php\n//	Paths\n'+
			'define (\'PROTOCOL\', \''+document.config.protocol.value+'\');\n'+
			'define (\'PORT\', \''+document.config.port.value+'\');\n'+
			'define (\'PATH\', \''+document.config.path.value+'\');\n'+
			'define (\'BASE_URL_STATIC\', \''+document.config.base_url_static.value+'\');\n'+
			'define (\'STATIC_FILES_ROOT\', \''+document.config.static_files_root.value+'\');\n'+
			'define (\'BACKEND_SERVICE_PORT\', '+document.config.backend_service_port.value+');\n'+
			'\n// OAuth\n'+
			'define (\'GOOGLE_CLIENT_ID\', \''+document.config.google_client_id.value+'\');\n'+
			'define (\'GOOGLE_SECRET\', \''+document.config.google_secret.value+'\');\n'+
			'define (\'FACEBOOK_CLIENT_ID\', \''+document.config.facebook_client_id.value+'\');\n'+
			'define (\'FACEBOOK_SECRET\', \''+document.config.facebook_secret.value+'\');\n'+
			'\n// Security\n'+
			'define (\'PUBLIC_MODULES\', \'{"index":["view"], "user":["view"]'+(document.config.instance.value == 'development' ? ', "admin":["view", "add", "edit", "delete"]' : '')+'}\');\n'+
			'define (\'COMMON_SALT\', \''+(document.config.instance.value == 'development' ? '' : 'NaCl')+'\'); // Changing this later will invalidate all user passwords.\n'+
			'\n// Logging\n'+
			'define (\'ON_ERROR\', \''+(document.config.instance.value == 'production' ? 'LOG' : 'DISPLAY')+'\');//{DISPLAY, LOG, IGNORE, EMAIL}\n'+
			'define (\'DATABASE_LOGGING\', \''+(document.config.instance.value == 'production' ? 'OFF' : 'ON')+'\');\n'+
			'define (\'PROFILING\', \''+(document.config.instance.value == 'production' ? 'OFF' : 'ON')+'\');\n'+
			'\n// Localization\n'+
			'define (\'DEFAULT_TIMEZONE\', \'UTC\');\n'+
			'define (\'DEFAULT_LANGUAGE\', \'en\');\n'+
			'\n// Database\n'+
			'define (\'DB_HOST\', \''+document.config.db_host.value+'\');\n'+
			'define (\'DB_NAME\', \''+document.config.db_name.value+'\');\n'+
			'define (\'DB_USER\', \''+document.config.db_user.value+'\');\n'+
			'define (\'DB_PASS\', \''+document.config.db_pass.value+'\');\n'+
			'\n'+
			'?>';
	}
	for (var i = 0; i < document.config.elements.length-2; i++){
		document.config.elements[i].onkeyup = generate_files;
		document.config.elements[i].onchange = generate_files;
	}
	document.config.path.onchange = document.config.static_files_root.onchange = function(){
		if (this.value.substring(this.value.length-1) != '/')
			this.value += '/';
		generate_files();
	};
<?php if (!isset($_POST['htaccess']) && !isset($_POST['config'])){ ?>
	generate_files();
<?php } ?>
</script>
<style>
	textarea{
		width:100%;
	}
</style>
