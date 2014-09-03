<?php
require_once("core/php/incl.php");
if(!empty($_POST)) {
	if(empty($_POST['email']) || empty($_POST['password']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$missing_data = true;
	} else if(!email_exists($_POST['email'])) {
		$wrong_data = true;
	} else if(!password_verify($_POST['password'], email_to_password($_POST['email']))) {
		$wrong_data = true;
	} else {
		$_SESSION['email'] = $_POST['email'];
		header("Location: index.php");
	}
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Login | Quicure - Quick &amp; Secure</title>
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
				<h1 style="color: #fff">Sign in to</h1>
				<h3 style="color: #fff; margin: 0;">Send Invoices &bull; Track Profits &bull; Pay Employees</h3>
			</div>
		</div>
		<div class="container text-center">
			<form action="" method="post">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="glyphicon glyphicon-envelope"></i>
						</span>
						<input type="text" name="email" placeholder="Email" class="form-control input-lg">
					</div>
				</div>
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="glyphicon glyphicon-lock"></i>
						</span>
						<input type="password" name="password" placeholder="Password" class="form-control input-lg">
					</div>
				</div>
				<?php
				if(isset($missing_data)) {
				?>
				<div class="alert alert-danger">
					<p><strong>Error!</strong> Please enter a valid email and password.</p>
				</div>
				<?php
				} else if (isset($wrong_data)) {
				?>
				<div class="alert alert-danger">
					<p><strong>Error!</strong> The details provided do not match an account in our database.</p>
				</div>
				<?php
				}
				?>
				<input type="submit" class="btn btn-primary btn-lg btn-block" value="Login">
			</form>
		</div>
		<?php
		require_once('core/php/footer.php');
		?>
	</body>
</html>