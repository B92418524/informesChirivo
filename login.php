<?php

$loop=true;

require_once('config.php');

if(isset($_SESSION['username']))
{
	header('Location: index.php');
}

if (defined(LOGIN_TYPE)){echo 'Error'; die;}

echo get_header('login');

echo get_login(LOGIN_TYPE);

echo get_footer();

?>
