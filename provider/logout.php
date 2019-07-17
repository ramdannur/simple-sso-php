<?php
error_reporting(1);
session_start();
include("./helper/pdo.class.crud.php");

function doLogout($token)
{
	$db = new crud();
		
	$tb_token = "token";

	$sql_token = json_decode($db->delete($tb_token,array('access_token'=>$token)));

	return $sql_token;

}


if (!empty($_GET['token'])) {
	$token = $_GET['token'];
}else{
	$token = $_SESSION['token'];
}

doLogout($token);

session_destroy();

?>

Sign out success!!

<br>
<br>
<a href="login.php">< Back</a>