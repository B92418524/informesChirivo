<?php

if(!defined("PATH"))define('PATH', dirname(__DIR__));
require_once PATH."/informes/vendor/autoload.php";

if (isset($_SESSION['username']))
{
	if (strtolower ($_SESSION['username'])=='ticsur')
	{
		error_reporting(E_ALL);
	}
}

//define('LOGIN_TYPE','login-demo');
define('LOGIN_TYPE','login-mssql');
define('ABSPATH', dirname(__FILE__));
define('MAX_PRODUCTS', 10);



if (LOGIN_TYPE=='login-mssql')
{
	if (version_compare(phpversion(), '5.4.0', '<')) {if(session_id() == '') {session_name('mstde'); session_start();}} else {if (session_status() == PHP_SESSION_NONE) {session_name('mstde'); session_start();}}

	
	if(isset($_GET['logout'])) 
	{
		unset($_SESSION['username']);
		header('Location: login.php');		
		die;
	}
	
	if(!isset($_SESSION['username']))
	{
		if (!isset($loop))
		{	
			header('Location: login.php');
			//echo 'post user>'.$_POST['username'];	
			//$_SESSION['username'] ='punto-de-venta';
		}
	}
	
	require_once(ABSPATH.'/functions/db_functions.php');
	require_once(ABSPATH.'/functions/functions.php');

	if ( isset($_POST['username']) and isset($_POST['password']) and isset($_POST['company']) )
	{
		
		if ($_POST['company']==''){$_POST['company']='1';}	// Por defecto empresa 1
		
		if ( empty($_POST['username']) or empty($_POST['password']) or empty($_POST['company']) )
		{
			echo "Indique los campos usuario, contrase&ntilde;a y empresa!";
		}
		else
		{
			$username			= $_POST['username'];
			$password			= $_POST['password'];
			$company_id			= $_POST['company'];
						
			if (login($username,$password,$company_id)===true)
			{									
				header('Location: index.php');
				die;	 
			}
		}
	}
	
	
}



?>