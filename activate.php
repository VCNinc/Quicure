<?php
require_once('core/php/incl.php');
if(empty($_SESSION['email'])) {
	header("Location: index.php");
	exit();
} else {
	if($userdata['active'] != 0) {
		header("Location: index.php");
		exit();
	}
}
if(!empty($_POST)) {
	if($_POST['email-key'] != $userdata['email_key']) {
	 	$email_fail = true;
	} else {
	 	$email = $userdata['email'];
	 	mysqli_query($db, "UPDATE `users` SET `active` = '1' WHERE `email` = '$email'");
		header("Location: index.php");
		exit();
	}

}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Activate | Quicure - Quick &amp; Secure</title>
		<meta charset="utf-8">
		<meta name="robots" content="noindex, nofollow">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="//maxcdn.bootstrapcdn.com/bootswatch/3.2.0/cosmo/bootstrap.min.css" rel="stylesheet">
		<link href="core/css/style.css" rel="stylesheet" type="text/css">
		<link href="core/css/font-awesome.css" rel="stylesheet" type="text/css">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,800,700,800' rel='stylesheet' type='text/css'>
		<link rel="shortcut icon" href="https://quicure.com/core/img/favicon.png">
		<?php require_once('core/php/chat.php'); ?>
	</head>
	<body style="background: #25b695; margin-bottom: 60px;">
		<?php include('core/php/header.php'); ?>
		<div class="front_header">
			<div class="container clearfix text-center">
				<h1 style="color: #fff">Check your Email!</h1>
				<h3 style="color: #fff; margin: 10px;">Before you create your first invoice, you need to activate your email address.</h3>
			</div>
		</div>
		<div class="container text-center">
			<form action="" method="post">
				<div class="form-group">
					<label for="key">We've just sent you a 32-character activation code. Please enter that below.</label>
					<input type="text" id="key" class="form-control input-lg" name="email-key" placeholder="Activation Code">
				</div>
				<?php
					if(isset($email_fail)) {
				?>
				<div class="alert alert-danger" style="">
					<strong>Check again!</strong> The code should look like this: <?=md5("Quicure");?>
				</div>
				<?php
					}
				?>
				<input type="submit" class="btn btn-primary btn-lg btn-block" value="Activate">
			</form>
		</div>
		<?php
		require_once('core/php/footer.php');
		?>
	</body>
</html>