<?php
include ('core/php/incl.php');
if(!$logged_in) {
	header("location: index.php");
}
if(isset($_FILES['logo'])) {
	if($_FILES['logo']['type'] != "image/png" && $_FILES['logo']['type'] != "image/jpeg") {
		$invalid_file = true;
	} else if ($_FILES['logo']['size'] > 1024000) {
		$invalid_file = true;
	} else {
		$logodata = "data:" . $_FILES['logo']['type'] . ";base64,";
		$logodata .= base64_encode(file_get_contents($_FILES['logo']['tmp_name']));
		$logodata = mysqli_real_escape_string($db, $logodata);
		$email = $userdata['email'];
		mysqli_query($db, "UPDATE `users` SET `logo` = '$logodata' WHERE `email` = '$email'") or die(mysqli_error($db));
		header("Location: new-invoice.php");
	}
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Logo | Quicure - Quick &amp; Secure</title>
		<meta charset="utf-8">
		<meta name="robots" content="noindex, nofollow">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<link rel="shortcut icon" href="https://quicure.com/core/img/favicon.ico" type="image/x-icon">
	</head>
	<body>
		<div class="container text-center">
			<div class="jumbotron">
				<?php
				if(empty($userdata['logo'])) {
				?>
				<h1>Please upload a logo.</h1>
				<p>(It usually makes for a better user experience...)</p>
				<?php
				} else {
				?>
				<img class="img img-responsive" src="<?=$userdata['logo']?>">
				<p>Thanks for uploading your logo. You can update it if you want.</p>
				<?php
				}
				?>
				<?php
				if (isset($invalid_file)) {
				?>
				<div class="alert alert-danger">
					<p>Please choose a PNG or JPG file (less than 1MB).</p>
				</div>
				<?php
				}
				?>
				<form action="" method="post" enctype="multipart/form-data" id="form">
					<input style="display: inline-block;" type="file" name="logo" id="logo">
				</form>
			</div>
		</div>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script>
		$(function(){
			$("#logo").change(function(){
				$("#form").submit();
			});
		})
		</script>
	</body>
</html>