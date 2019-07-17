<?php
error_reporting(1);
session_start();
include("./helper/pdo.class.crud.php");

class controller{

	private $db;
	private $tb_user;
	private $tb_host;
	private $tb_token;

	public function __construct(){

		$this->db = new crud();
		$this->tb_user = "user";
		$this->tb_host = "host";
		$this->tb_token = "token";

	}

	function authenticate($email, $pass){

		$sql_login = json_decode($this->db->select($this->tb_user,"*","email ='".$email."' and password ='".$pass."'", null, null, "1"));

		if (count($sql_login->stand)) {
			foreach($sql_login->stand as $idx => $user){

				$accessToken = $this->generateToken(60);
				$secretKey = $user->secret_key;

				// Make sure we're redirecting somewhere safe
				$appUrl = $_GET['redirect_url'];

				// save token
				$_SESSION['token'] = $accessToken;

				$this->saveToken($accessToken, $user->id);

				
				if (!empty($appUrl)) {

					$this->loginClient($appUrl, $accessToken, $secretKey);

				}
				
		  	}
		}else{
			echo "<script>alert('Email atau Password salah!!!')</script>";
		}

	}

	function loginClient($appUrl, $accessToken, $secretKey)
	{

		// Generate signature from authentication info + secret key
		$sig = hash_hmac(
		    'sha256',
		     $accessToken,
		     $secretKey
		);

		if (substr( $appUrl, 0, 4 ) != "http") {
			$source = parse_url("http://".$appUrl);
		}else{
			$source = parse_url($appUrl);
		}

		$sql_host = json_decode($this->db->select($this->tb_host,"*","nama ='".$source['host']."'"));

		if(count($sql_host->stand)){
		  $target = 'http://'.$source['host'].$source['path'];

			// Send the authenticated user back to the originating site
			header('Location: '.$target.'?'.
			    'token='.$accessToken.
			    '&sig='.$sig);

		}else{
			echo "<strong>Forbidden!!!</strong><br>";
		}

	}

	function remove_http($url) {
	   $disallowed = array('http://', 'https://');
	   foreach($disallowed as $d) {
	      if(strpos($url, $d) === 0) {
	         return str_replace($d, '', $url);
	      }
	   }
	   return $url;
	}

	function generateToken($length = 16)
	{
	    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	    return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
	}

	function saveToken($token, $user_id)
	{

		return $this->db->insert($this->tb_token, array("access_token" => $token, "user_id" => $user_id));

	}

	function getUserByToken($token)
	{

		$sql_token = json_decode($this->db->select($this->tb_token,"*","access_token ='".$token."'"));

		$sql_token = json_decode($this->db->select($this->tb_user,"*","id ='".$sql_token->stand['0']->user_id."'"));

		return $sql_token;

	}

}

$class = new controller();

if (isset($_POST['btn_submit'])) {
	$class->authenticate($_POST['inp_email'], md5($_POST['inp_password']));
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple SSO System</title>
    <link rel="stylesheet" href="css/main.css">
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="./asset/css/bootstrap.min.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="./asset/css/bootstrap-theme.min.css">
</head>
<body>
 <center>
 </center>
<div class="kotak-tengah">
	<center>
		<h1>APP PROVIDER</h1>
		<br>
	</center>
	<div class="kotak-login">
	<br>
	<br>
		<div class="row">
		 		<?php
				if($_SESSION['token']){

					$accessToken = $_SESSION['token'];
		
					$user = $class->getUserByToken($accessToken)->stand['0'];

					$appUrl = $_GET['redirect_url'];
					$secretKey = $user->secret_key;

					if (!empty($appUrl)) {
						$class->loginClient($appUrl, $accessToken, $secretKey);
					}

				// $sql_user = json_decode($this->db->select($this->tb_user,"*","email ='".$email."' and password ='".$pass."'", null, null, "1"));

				// 	if (count($sql_login->stand)) {
				// 		foreach($sql_login->stand as $idx => $user){

					?>
				 	<div class="col-md-6 col-md-offset-3">
				 		<div class="well">
						  <h2>Data Login</h2>
					 		<br>
					 		<strong>Id : </strong><?php echo $user->id ?>
					 		<br>
					 		<strong>Nama : </strong><?php echo $user->nama ?>
					 		<br>
					 		<strong>Email : </strong><?php echo $user->email ?>
					 		<br>
					 		<strong>Secret Key : </strong><?php echo $user->secret_key ?>
					 		<br>
					 		<strong>Token : </strong><?php echo $_SESSION['token'] ?>
					 		<br>
					 		<br>
					 		<a href="logout.php">LOGOUT</a>
						</div>
					</div>
					<?php
				}else{
					?>
				 	<div class="col-md-4 col-md-offset-4">
				 		<div class="well">
							 <center>
							  <form method="post">
							  <h2>Login Form</h2>
							  <br>
							  <div class="form-group">
							    <label for="exampleInputEmail1">Email address</label>
							    <input type="email" class="form-control" name="inp_email" placeholder="Email" required>
							  </div>
							  <div class="form-group">
							    <label for="exampleInputEmail1">Password</label>
							    <input type="password" class="form-control" name="inp_password" placeholder="Password" required>
							  </div>
							  <button type="submit" name="btn_submit" class="btn btn-primary btn-lg btn-block">Login</button>
							  <br>
							  </form>
							 </center>
						</div>
					</div>
				<?php
				}	
				?>
		 	<br>
	 	</div>
	</div>
</div>
<center class="copyright">Simple SSO System</center>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="./asset/js/bootstrap.min.js"></script>
</body>
</html>