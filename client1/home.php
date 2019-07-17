<?php
    session_start();
    error_reporting(1);

    $MySecretKey = 'CyberLabs';

    // Set not logged in by default
    $token = $_GET['token'];
    $sig = $_GET['sig'];

      // See if they have the right signature
    if(!empty($token) && !$_SESSION['token']) // Someone trying to log in?
    {
      if (hash_equals(hash_hmac('sha256', $token, $MySecretKey), $sig)) {
        $_SESSION['token'] = $token;
      }

    }

    function requestData($url, $method = "GET", $postdata = null){
        // create curl resource 
        $ch = curl_init($url); 
        
        $headers = array(
            'Accept: application/json',
        );

        if ($method == "POST") {
            curl_setopt_array($ch, array(
                CURLOPT_POST  => 1,
                CURLOPT_HTTPHEADER  => $headers,
                CURLOPT_POSTFIELDS  => $postdata,
                CURLOPT_RETURNTRANSFER  =>true,
                CURLOPT_VERBOSE     => 1
            ));
        }else{
            curl_setopt_array($ch, array(
                CURLOPT_HTTPGET  => 1,
                CURLOPT_HTTPHEADER  => $headers,
                CURLOPT_RETURNTRANSFER  =>true,
                CURLOPT_VERBOSE     => 1
            ));
        }


        // $output contains the output string 
        $output = curl_exec($ch);
        // close curl resource to free up system resources 
        curl_close($ch);      

        return $output;
    }

    $user = "";
    if($_SESSION['token']) {
      $url = "http://127.0.0.1/cek_token.php?token=".$_SESSION['token'];

      $respon = json_decode(requestData($url, "GET"));
      $user = $respon->data;

      if ($respon->code != 200) {
        unset($_SESSION['token']);

      }
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
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">Client 1</a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav navbar-right">
          <?php
         if($_SESSION['token']) {
          ?>
            <li><a href="logout.php">Logout</a></li>
          <?php
          }else{
          ?>
            <li><a href='http://127.0.0.1/login.php?redirect_url=http://127.0.0.2/home.php'>Login</a></li>
          <?php
          }
          ?>
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav>
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <br>
      <center>
      <h1>App Client 1</h1>
      <br>
      <?php
      if($_SESSION['token']){

        ?>
        <br>
        <strong>Nama : </strong>
        <br>
        <?php echo $user->nama ?>
        <br>
        <br>

        <strong>Email : </strong>
        <br>
        <?php echo $user->email ?>
        <br>
        <br>

        <strong>Secret Key : </strong>
        <br>
        <?php echo $user->secret_key ?>
        <br>
        <br>

        <strong>Token : </strong>
        <br>
        <?php echo $_SESSION['token'] ?>

        <?php

       }else{
      	?>

          Anda belum login!

      	<?php
      }
      // function hash_equals($a, $b) {
      //     $key = mcrypt_create_iv(128, MCRYPT_DEV_URANDOM);
      //     return hash_hmac('sha512', $a, $key) === hash_hmac('sha512', $b, $key);
      // }
      ?>
      </center>
    </div>
  </div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="./asset/js/bootstrap.min.js"></script>
</body>
</html>