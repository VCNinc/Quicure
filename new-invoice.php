<?php
require_once('core/php/incl.php');
if(!$logged_in) {
	header("Location: index.php");
	exit();
}

if(isset($_POST['uploadlogo'])) {
	
} else if(!empty($_POST)) {
	$owner = $userdata['email'];
	$owner_name = $userdata['first_name'] . " " . $userdata['last_name'];
	$owner_logo = mysqli_real_escape_string($db, $userdata['logo']);
	$date_issued = Date("F jS, Y");
	$tax_name = sanitize($_POST['tax_name']);
	$tax_value = sanitize(str_replace("%", "", $_POST['tax_percent']));
	$items = array();
	for($i = 1; $i <= $_POST['items']; $i++) {
		if(!empty($_POST['item-' . $i . '-name']) && !empty($_POST['item-' . $i . '-unit-price']) && !empty($_POST['item-' . $i . '-quantity'])) {
			$items[] = array(
				"name" => sanitize($_POST['item-' . $i . '-name']),
				"price" => sanitize($_POST['item-' . $i . '-unit-price']),
				"quantity" => sanitize($_POST['item-' . $i . '-quantity']),
				"description" => sanitize($_POST['item-' . $i . '-description'])
			);
		}
	}
	$items = json_encode($items);
	mysqli_query($db, "INSERT INTO `invoices` (`owner`, `owner_name`, `owner_logo`, `tax_name`, `tax_value`, `items`, `date_issued`) VALUES ('$owner', '$owner_name', '$owner_logo', '$tax_name', '$tax_value', '$items', '$date_issued')");
	$q = mysqli_insert_id($db);
	header("Location: invoice/$q&authored");
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Invoice | Quicure - Quick &amp; Secure</title>
		<meta charset="utf-8">
		<meta name="robots" content="noindex, nofollow">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="//maxcdn.bootstrapcdn.com/bootswatch/3.2.0/cosmo/bootstrap.min.css" rel="stylesheet">
		<link rel="shortcut icon" href="https://quicure.com/core/img/favicon.ico" type="image/x-icon">
	</head>
	<body style="background: #f0f0ee;">
		<style>
		.container {
			position: relative;
			width: 750px;
			padding: 50px;
			margin: 0 auto;
			background-color: #fff;
			-webkit-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2), inset 0 0 50px rgba(0, 0, 0, 0.1);
			-moz-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2), inset 0 0 50px rgba(0, 0, 0, 0.1);
			box-shadow: 0 0 5px rgba(0, 0, 0, 0.2), inset 0 0 50px rgba(0, 0, 0, 0.1);
			margin-top: 50px;
		}

		.ribbon-wrapper-green {
		  width: 220px;
		  height: 188px;
		  overflow: hidden;
		  position: absolute;
		  top: -3px;
		  left: -3px;
		}

		.ribbon-green {
		  z-index: 10000000000;
		  font-size: 2em;
		  font: bold 22px Sans-Serif;
		  color: #fff !important;
		  text-align: center;
		  text-shadow: rgba(255,255,255,0.5) 0px 1px 0px;
		  -webkit-transform: rotate(-45deg);
		  -moz-transform:    rotate(-45deg);
		  -ms-transform:     rotate(-45deg);
		  -o-transform:      rotate(-45deg);
		  position: relative;
		  padding: 7px 0;
		  left: -63px;
		  top: 65px;
		  width: 280px;
		  background-color: red;
		  background-image: -webkit-gradient(linear, left top, left bottom, from(red), to(DarkRed)); 
		  background-image: -webkit-linear-gradient(top, red, DarkRed); 
		  background-image:    -moz-linear-gradient(top, red, DarkRed); 
		  background-image:     -ms-linear-gradient(top, red, DarkRed); 
		  background-image:      -o-linear-gradient(top, red, DarkRed); 
		  color: #6a6340;
		  -webkit-box-shadow: 0px 0px 3px rgba(0,0,0,0.3);
		  -moz-box-shadow:    0px 0px 3px rgba(0,0,0,0.3);
		  box-shadow:         0px 0px 3px rgba(0,0,0,0.3);
		}

		.ribbon-green:before, .ribbon-green:after {
		  content: "";
		  border-top:   3px solid #6e8900;   
		  border-left:  3px solid transparent;
		  border-right: 3px solid transparent;
		  position:absolute;
		  bottom: -3px;
		}

		.ribbon-green:before {
		  left: 0;
		}
		.ribbon-green:after {
		  right: 0;
		}
		</style>
		<div class="container" style="padding: 50px;">
			<form action="" method="post">
			<div class="row">
				<div class="col-sm-7 text-center">
					<?php
						if(!empty($userdata['logo'])) {
					?>
					<a href="add-logo.php"><img style="max-width: 100%; padding: 37px; max-height: 250px;" src="<?=$userdata['logo']?>"></a>
					<?php
						} else {
					?>
					<a href="add-logo.php" class="btn btn-success btn-lg" style="margin-top: 37px;">+ Add Logo</a>
					<?php
						}
					?>
				</div>
				<div class="col-sm-5">
					<table class="table">
						<tbody>
							<tr>
								<td>Invoice Number</td>
								<td>XXXXXX</td>
							</tr>
							<tr>
								<td>Date Issued</td>
								<td><?=Date("F jS, Y")?></td>
							</tr>
							<tr class="active">
								<td>Amount Due</td>
								<td>$0.00</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<table class="table table-bordered" id="items">
				<tbody>
					<tr class="active">
						<th>Item</th>
						<th>Price for One</th>
						<th>Quantity</th>
						<th>Total</th>
					</tr>
					<tr>
						<td><input type="text" name="item-1-name" placeholder="Item #1" class="form-control input"></td>
						<td><input type="text" name="item-1-unit-price" placeholder="10.00" class="form-control input"></td>
						<td><input type="text" name="item-1-quantity" placeholder="4" class="form-control input"></td>
						<td rowspan="2" id="input-1-total">$0.00</td>
					</tr>
					<tr>
						<td colspan="3"><input type="text" name="item-1-description" placeholder="Description (Optional)" class="form-control input"></td>
					</tr>
				</tbody>
			</table>
			<button type="button" class="btn btn-lg btn-block" id="additem">+ Add Item #2</button>
			<input type="hidden" name="items" value="1" id="itemcount">
			<div class="row">
				<div class="col-sm-7" style="padding: 13px;">
					<button type="submit" class="btn btn-primary btn-lg btn-block">Create Invoice</button>
				</div>
				<div class="col-sm-5">
					<table class="table">
						<tbody>
							<tr>
								<td><input type="text" name="tax_name" class="form-control input" placeholder="No Tax"></td>
								<td><input type="text" name="tax_percent" class="form-control input" placeholder="0.00%"></td>
							</tr>
							<tr class="active">
								<td>Amount Due</td>
								<td>$0.00</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			</form>
		</div>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="core/js/newinvoice.js"></script>
	</body>
</html>