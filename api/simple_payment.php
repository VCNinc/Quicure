<?php
$db = mysqli_connect("localhost", "root", "M98TN97NkUxcnQTVyxrm6eHf", "quicure");
function sanitize($data) {
	global $db;
	$data = trim($data);
	$data = htmlentities($data);
	$data = mysqli_real_escape_string($db, $data);
	return $data;
}
$email = sanitize($_GET['email']);
$price = sanitize($_GET['price']);
$title = sanitize($_GET['title']);
if (!mysqli_num_rows(mysqli_query($db, "SELECT * FROM `users` WHERE `email` = '$email'")) > 0) {
	die("ERR: Account Not Found.");
} else {
	$userdata = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM `users` WHERE `email` = '$email'"));
	$price = str_replace(array(".", "$"), "", $price);
	if(!ctype_digit($price)) {
		die("ERR: Invalid Price.");
	} else {
		$owner = $userdata['email'];
		$owner_name = $userdata['first_name'];
		$owner_logo = $userdata['logo'];
		$items = json_encode(
			array(
				array(
					"name" => $title,
					"price" => $price,
					"quantity" => 1,
					"description" => ''
				)
			)
		);
		$q = mysqli_query($db, "INSERT INTO `invoices` (`owner`, `owner_name`, `owner_logo`, `tax_name`, `tax_value`, `items`) VALUES ('$owner', '$owner_name', '$owner_logo', '', '0', '$items')");
		$id = mysqli_insert_id($db);
		echo "https://quicure.com/invoice/$id";
	}
}