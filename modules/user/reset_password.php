<h2>Reset Password - Step <?php echo $step; ?></h2>

<form method="post">

<?php if ($step == 1){ ?>

	<p>Enter your username or email address to send a code to reset your password.</p>
	<input type="email" name="email" />
	<input type="submit" value="Reset" />

<?php }else if ($step == 2){ ?>

	<p>Enter the code you received on email to reset your password.</p>
	<input type="text" name="code" />
	<input type="submit" value="Reset" />

<?php }else if ($step == 3){ ?>

	<p>Enter your new password and confirm.</p>
	<table>
		<tr>
			<td>Password: </td><td><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td>Confirm Password: </td><td><input type="password" name="password_conf" /></td>
		<tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Reset" /></td>
		<tr>
	</table>

<?php } ?>

</form>