<?php
session_start();
include 'vendor/autoload.php';
include 'recaptchalib.php';
$db = mysqli_connect("localhost", "root", "************************", "quicure");
if(!empty($_SESSION['email'])) {
	$email = $_SESSION['email'];
	$userdata = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM `users` WHERE `email` = '$email'"));
	if($userdata['active'] == 0 && $_SERVER['SCRIPT_NAME'] != "/activate.php") {
		header("Location: activate.php");
	}
	$logged_in = true;
} else {
	$logged_in = false;
}
function sanitize($data) {
	global $db;
	$data = trim($data);
	$data = htmlentities($data);
	$data = mysqli_real_escape_string($db, $data);
	return $data;
}
function email_exists($email) {
	global $db;
	$email = sanitize($email);
	return (mysqli_num_rows(mysqli_query($db, "SELECT * FROM `users` WHERE `email` = '$email'")) > 0);
}
function invoice_exists($invoice) {
	global $db;
	$invoice = sanitize($invoice);
	return (mysqli_num_rows(mysqli_query($db, "SELECT * FROM `invoices` WHERE `invoice_number` = '$invoice'")) > 0);
}
function email_to_password($email) {
	global $db;
	$email = sanitize($email);
	$q = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM `users` WHERE `email` = '$email'"));
	return $q['password'];
}
function start_page($title) {
	echo '<!DOCTYPE html>
	<html lang="en">
	<head>
		<title>' . $title . ' | Quicure - Quick &amp; Secure</title>
		<meta charset="utf-8">
		<meta name="robots" content="noindex, nofollow">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="//maxcdn.bootstrapcdn.com/bootswatch/3.2.0/cosmo/bootstrap.min.css" rel="stylesheet">
		<link href="core/css/style.css" rel="stylesheet" type="text/css">
		<link href="core/css/font-awesome.css" rel="stylesheet" type="text/css">
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,800,700,800" rel="stylesheet" type="text/css">
		<link rel="shortcut icon" href="https://quicure.com/core/img/favicon.png">
	</head>
	<body style="background: #25b695; margin-bottom: 60px;">';
}
function end_page() {
	echo '</body></html>';
}