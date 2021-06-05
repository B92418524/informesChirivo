<?php

#starts a new session
session_start();

#includes a database connection
require_once('db_functions.php');

if (isset($_POST['username']) and isset($_POST['password'])) {
	if ( empty($_POST['username']) or empty($_POST['password']) ) {
		echo "Indique los campos usuario y contrase&ntilde;a!";
	} else {
		$username = $_POST['username'];
		$password = $_POST['password'];
	
		login($username,$password);
	}
}
?>



<div style="text-align:center; margin-top:20px;">
	<form name="log" action="test-login.php" method="post">
		Username: <input class="form" type="text" name="username"><br />
		Password: <input class="form" type="password" name="password"><br />
		<input name="submit" type="submit" value="Submit">
	</form>
</div>
