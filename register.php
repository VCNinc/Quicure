<?php
require_once("core/php/incl.php");

$first_name_required = false;
$first_name_invalid = false;
$last_name_required = false;
$last_name_invalid = false;
$email_required = false;
$email_invalid = false;
$email_registered = false;
$country_invalid = false;
$state_invalid = false;
$password_invalid = false;
$password_no_match = false;

if(isset($_POST['register'])) {
	if(empty($_POST['first_name'])) {
		$first_name_required = true;
	} else if (!ctype_alpha($_POST['first_name']) || strlen($_POST['first_name']) > 255) {
		$first_name_invalid = true;
	}
	if(empty($_POST['last_name'])) {
		$last_name_required = true;
	} else if (!ctype_alpha($_POST['last_name']) || strlen($_POST['first_name']) > 255) {
		$last_name_invalid = true;
	}
	if(empty($_POST['email'])) {
		$email_required = true;
	} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || strlen($_POST['email']) > 255) {
		$email_invalid = true;
	} else if (email_exists($_POST['email'])) {
		$email_registered = true;
	}
	if(empty($_POST['country']) || $_POST['country'] == "Select Country") {
		$country_invalid = true;
	}
	if(empty($_POST['state']) || $_POST['state'] == "Select State") {
		$state_invalid = true;
	}
	if(empty($_POST['password']) || strlen($_POST['password']) < 6) {
		$password_invalid = true;
	}
	if(empty($_POST['password_confirm']) || $_POST['password'] != $_POST['password_confirm']) {
		$password_no_match = true;
	}
	if(!($first_name_required || $first_name_invalid || $email_required || $email_invalid || $email_registered || $country_invalid || $state_invalid || $password_invalid || $password_no_match)) {
		$first_name = sanitize($_POST['first_name']);
		$last_name = sanitize($_POST['last_name']);
		$email = sanitize($_POST['email']);
		$email_key = md5(uniqid(rand(), true));
		$country = sanitize($_POST['country']);
		$state = sanitize($_POST['state']);
		$password = password_hash($_POST['password'], PASSWORD_DEFAULT, array("cost" => 12));
		mysqli_query($db, "INSERT INTO `users` (`first_name`, `last_name`, `email`, `email_key`, `country`, `state`, `password`) VALUES ('$first_name', '$last_name', '$email', '$email_key', '$country', '$state', '$password')") or die(mysqli_error($db));
		
		$mail = new PHPMailer;

		$mail->isSMTP();
		$mail->SMTPAuth = true;

		$mail->Host = "smtp.gmail.com";
		$mail->Username = "admin@quicure.com";
		$mail->Password = "VCNinc213!";
		$mail->SMTPSecure = "ssl";
		$mail->Port = 465;

		$mail->From = "admin@quicure.com";
		$mail->FromName = "Quicure Admin";
		$mail->addReplyTo("admin@quicure.com", "Quicure Admin");

		$mail->addAddress($email, $first_name . $last_name);

		$mail->isHTML(true);

		$mail->Subject = "Confirm your Quicure Account";

		$mail->Body = "<h1>Quicure</h1><h4>Quick &amp; Secure</h4><hr><p>Thanks for registering your account. To complete your registration, please enter the following confirmation code:</p><h2>$email_key</h2><p>If you did not register for Quicure with this email, please simply ignore this message. Thank you!</p><p>Quicure.</p>";
		$mail->AltBody = "Welcome to Quicure! Thanks for registering your account. To complete your registration, please enter the following confirmation code:\r\n\r\n$email_key\r\n\r\nIf you did not register for Quicure with this email, please simply ignore this message. Thank you!\r\n\r\nQuicure.";

		$mail->send();

		$_SESSION['email'] = $email;

		header("Location: index.php");
	}
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Register | Quicure - Quick &amp; Secure</title>
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
				<h1 style="color: #fff">Register for Free</h1>
				<h3 style="color: #fff; margin: 0;">Send Invoices &bull; Track Profits &bull; Pay Employees</h3>
			</div>
		</div>
		<div class="container text-center">
			<form action="" method="post">
					<div class="form-group<?php if($first_name_required || $first_name_invalid) {echo ' has-error';} ?>">
						<?php
							if($first_name_required) {
						?>
						<label for="first_name" class="control-label">Please enter your company's name.</label>
						<?php
							} else if ($first_name_invalid) {
						?>
						<label for="first_name" class="control-label">That's not a valid company name.</label>
						<?php
							}
						?>
						<input type="text" name="first_name" placeholder="Company Name" class="form-control input-lg" value="<?=$_POST['first_name']?>" id="first_name">
					</div>
				<hr>
				<div class="form-group<?php if($email_required || $email_invalid || $email_registered) {echo ' has-error';} ?>">
					<?php
						if($email_required) {
					?>
					<label for="email" class="control-label">Please enter your email address.</label>
					<?php
						} else if ($email_invalid) {
					?>
					<label for="email" class="control-label">That's not a valid email address.</label>
					<?php
						} else if ($email_registered) {
					?>
					<label for="email" class="control-label">That email address is already registered. Are you sure you don't want to <a href="login.php">login</a> instead?</label>
					<?php
						}
					?>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="glyphicon glyphicon-envelope"></i>
						</span>
						<input type="email" name="email" placeholder="Email" class="form-control input-lg" value="<?=$_POST['email']?>" id="email">
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group<?php if($country_invalid) {echo ' has-error';} ?>">
							<?php
								if($country_invalid) {
							?>
							<label for="country" class="control-label">Please select the country you currently live in.</label>
							<?php
								}
							?>
							<select name="country" onchange="print_state('state',this.selectedIndex);" id="country" class="form-control input-lg"></select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group<?php if($state_invalid) {echo ' has-error';} ?>">
							<?php
								if($state_invalid) {
							?>
							<label for="state" class="control-label">Please select the state you currently live in.</label>
							<?php
								}
							?>
							<select name="state" id="state" class="form-control input-lg"></select>
						</div>
					</div>
				</div>
				<hr>
				<div class="form-group<?php if($password_invalid) {echo ' has-error';} ?>">
					<?php
						if($password_invalid) {
					?>
					<label for="password" class="control-label">Please choose a valid password (at least 6 characters).</label>
					<?php
						}
					?>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="glyphicon glyphicon-lock"></i>
						</span>
						<input type="password" name="password" placeholder="Password" class="form-control input-lg">
					</div>
				</div>
				<div class="form-group<?php if($password_no_match) {echo ' has-error';} ?>">
					<?php
						if($password_no_match) {
					?>
					<label for="password_confirm" class="control-label">Please re-type your password for confirmation.</label>
					<?php
						}
					?>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="glyphicon glyphicon-lock"></i>
						</span>
						<input type="password" name="password_confirm" placeholder="Password (Again)" class="form-control input-lg">
					</div>
				</div>
				<input type="submit" class="btn btn-primary btn-lg btn-block" name="register" value="Register">
			</form>
		</div>
		<?php
		require_once('core/php/footer.php');
		?>
		<script src="core/js/countries.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script language="javascript">
			print_country("country");
		<?php if (!empty($_POST['country'])) { ?>
			$("#country").val('<?=$_POST['country']?>');
			print_state('state', document.getElementById("country").selectedIndex);
		<?php } ?>
		<?php if (!empty($_POST['state'])) { ?>
			$("#state").val('<?=$_POST['state']?>');
		<?php } ?>
		</script>
	</body>
</html>