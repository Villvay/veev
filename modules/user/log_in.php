<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/admin.css" />
<h2>Log in</h2>

<form method="post">
	<table style="width:40%;">
		<tr>
			<td>Username</td>
			<td><input type="text" name="username" /></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<label>
					<input type="checkbox" name="remember" />
					Remember me
				</label>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Log In" /></td>
		</tr>
		<tr>
			<td colspan="2">
				<a href="<?php echo BASE_URL; ?>user/reset-password/step-1">Forgot password?</a>
			</td>
		</tr>
	</table>
</form>
