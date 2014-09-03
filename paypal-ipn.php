<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include('core/php/ipnlistener.php');
include('core/php/incl.php');
$listener = new IpnListener();
$listener->use_sandbox = false;
$listener->use_ssl = true;
$listener->use_curl = true;
$postdata = json_encode($_POST);
try {
	$listener->requirePostMethod();
	$verified = $listener->processIpn();
} catch (Exception $e) {
	$message = $e->getMessage();
	mysqli_query($db, "INSERT INTO `purchases` (`postdata`, `status`, `message`) VALUES ('$postdata', 'ERROR', '$message')");
}

if(isset($verified)) {
	if($verified) {
		if($_POST['payment_status'] != 'Completed') {
			mysqli_query($db, "INSERT INTO `purchases` (`postdata`, `status`, `message`) VALUES ('$postdata', 'INVALID', 'Payment Not Completed')");
			exit();
		}

		if($_POST['receiver_email'] != 'admin@quicure.com') {
			mysqli_query($db, "INSERT INTO `purchases` (`postdata`, `status`, `message`) VALUES ('$postdata', 'INVALID', 'Email Does Not Match')");
			exit();
		}

		if($_POST['mc_currency'] != 'USD') {
			mysqli_query($db, "INSERT INTO `purchases` (`postdata`, `status`, `message`) VALUES ('$postdata', 'INVALID', 'Currency is not USD')");
			exit();
		}

		if(!invoice_exists($_POST['custom'])) {
			mysqli_query($db, "INSERT INTO `purchases` (`postdata`, `status`, `message`) VALUES ('$postdata', 'INVALID', 'Invoice Not Found')");
			exit();
		}

		$q = sanitize($_POST['custom']);
		$query = mysqli_query($db, "SELECT * FROM `invoices` WHERE `invoice_number` = '$q'");
		$invoice = mysqli_fetch_assoc($query);
		$invoice['items'] = json_decode($invoice['items'], true);
		$total = 0;
		foreach($invoice['items'] as $item) {
			$total += $item['price'] * $item['quantity'];
		}
		$tax = $total * $invoice['tax_value'] / 100;
		$grand_total = $total + $tax;
		function get_fees($amt) {
			$charge_flat = 0.3;
			$charge_percent = 0.019;
			return (($amt * $charge_percent) + $charge_flat + (($amt + $charge_flat) * $charge_percent));
		}
		$fees = round(get_fees($grand_total), 2);
		$expected_gross = $grand_total + $fees;

		if($_POST['mc_gross'] != $expected_gross) {
			$err = "MC_GROSS (" . $_POST['mc_gross'] . ") DID NOT MATCH EXPECTED (" . $expected_gross . ")";
			mysqli_query($db, "INSERT INTO `purchases` (`postdata`, `status`, `message`) VALUES ('$postdata', 'INVALID', '$err')");
			exit();
		}

		$paid_by = sanitize($_POST['payer_email']);
		mysqli_query($db, "UPDATE `invoices` SET `paid` = '1' WHERE `invoice_number` = $q");
		mysqli_query($db, "INSERT INTO `purchases` (`postdata`, `status`, `message`) VALUES ('$postdata', 'VALID', 'Invoice #$q Successfully Paid! Collected $$fees')");
		mysqli_query($db, "UPDATE `invoices` SET `paid_by` = '$paid_by' WHERE `invoice_number` = $q");
	} else {
		mysqli_query($db, "INSERT INTO `purchases` (`postdata`, `status`, `message`) VALUES ('$postdata', 'INVALID', 'PayPal Rejected Request')");
	}
}