<?
error_reporting(1);
session_start();
include("./helper/pdo.class.crud.php");

function cekToken($token)
{
	$db = new crud();
		
	$tb_token = "token";
	$tb_user = "user";

	$sql_token = json_decode($db->select($tb_token,"*","access_token ='".$token."'"));
	
	if (count($sql_token->stand)) {

		return json_encode(array(
			'code' => 200,
			'msg' => "Success",
			'data' => json_decode($db->select($tb_user,"nama, email, secret_key","id ='".$sql_token->stand['0']->user_id."'"))->stand['0']
		));;

	}else{
		return json_encode(array(
			'code' => 400,
			'msg' => "Unauthorized"
		));
	}


}

if (!empty($_GET['token'])) {
	echo cekToken($_GET['token']);	
}else{
	echo json_encode(array(
		'code' => 400,
		'msg' => "Invalid"
	));
}


?>