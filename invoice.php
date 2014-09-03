<?php
require_once('core/php/incl.php');
$q = sanitize($_GET['q']);
$query = mysqli_query($db, "SELECT * FROM `invoices` WHERE `invoice_number` = '$q'");
if(mysqli_num_rows($query) == 0) {
	die("INVOICE NOT FOUND");
}
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
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Invoice | Quicure - Quick &amp; Secure</title>
		<meta charset="utf-8">
		<meta name="robots" content="noindex, nofollow">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<link rel="shortcut icon" href="https://quicure.com/core/img/favicon.ico" type="image/x-icon">
	</head>
	<body <?php if(!isset($_GET['frameless'])) {echo 'style="background: #f0f0ee;"';} ?>>
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

		.atd {
			position: relative;
			width: 750px;
			margin: 0 auto;
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
		  -xs--top:   3px solid #6e8900;   
		  -xs--left:  3px solid transparent;
		  -xs--right: 3px solid transparent;
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
		<?php
		if(isset($_GET['authored'])) {
		?>
		<div class="atd">
			<div class="alert alert-success">
				<div class="row">
					<div class="col-md-8">
						<h3><strong>Tada!</strong> Your invoice has been published.</h3>
					</div>
					<div class="col-md-4">
						<a style="margin-top: 8px" class="btn btn-primary btn-lg btn-block" href="https://quicure.com/dashboard">Return to Dashboard</a>
				</div>
			</div>
		</div>
		<?php } ?>
		<?php if(!isset($_GET['frameless'])) { ?>
		<div class="container" style="padding: 50px;">
		<?php } ?>
			<div class="row">
				<?php
				if($invoice['paid']) {
				?>
       			<div class="ribbon-wrapper-green"><div class="ribbon-green">PAID</div></div>
       			<?php
       			} else if ($invoice['escrow']) {
       			?>
       			<div class="ribbon-wrapper-green"><div class="ribbon-green">ESCROW</div></div>
       			<?php
       			}
       			?>
				<div class="col-xs-7 text-center">
					<?php
					if(!empty($invoice['owner_logo'])) {
					?>
						<img style="max-width: 100%; padding: 37px; max-height: 250px;" src="<?=$invoice['owner_logo']?>">
					<?php
					} else {
					?>
						<h1><?=$invoice['owner_name']?></h1>
					<?php
					}
					?>
				</div>
				<div class="col-xs-5">
					<table class="table">
						<tbody>
							<tr>
								<td>Invoice Number</td>
								<td><?=$invoice['invoice_number']?></td>
							</tr>
							<tr>
								<td>Date Issued</td>
								<td><?=$invoice['date_issued']?></td>
							</tr>
							<tr class="active">
								<td>Amount Due</td>
								<td><?="$" . number_format($grand_total, 2, ".", ",")?></td>
							</tr>
						</tbody>
					</table>
					<?php
					if($invoice['escrow']) {
					?>
					<p>THIS IS AN ESCROW PAYMENT. Pay now, release the funds when you're satisfied with the service.</p>
					<?php
					}
					?>
				</div>
			</div>
			<table class="table table--xs-ed">
				<tbody>
					<tr class="active">
						<th>Item</th>
						<th>Price for One</th>
						<th>Quantity</th>
						<th>Total</th>
					</tr>
				<?php
					foreach($invoice['items'] as $item) {
				?>
					<tr>
						<td><?=$item['name']?></td>
						<td><?="$" . number_format($item['price'], 2, ".", ",")?></td>
						<td><?=$item['quantity']?></td>
						<td<?php if(!empty($item['description'])) {echo ' rowspan="2"';} ?>><?="$". number_format(($item['price'] * $item['quantity']), 2, ".", ",")?></td>
					</tr>
					<?php if(!empty($item['description'])) { ?>
					<tr>
						<td colspan="3" class="text-center"><?=$item['description']?></td>
					</tr>
					<?php } ?>
				<?php
					}
				?>
				</tbody>
			</table>
			<div class="row">
				<div class="col-xs-7" style="padding: 13px;">
					<?php if(!$invoice['paid']) { ?>
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="pay">
						<input type="hidden" name="cmd" value="_cart">
						<input type="hidden" name="business" value="admin@quicure.com">
						<input type="hidden" name="currency_code" value="USD">
						<input type="hidden" name="upload" value="1">
						<input type="hidden" name="item_name_1" value="Payment to <?=$invoice['owner_name']?>">
						<input type="hidden" name="amount_1" value="<?=$grand_total?>">
						<input type="hidden" name="item_name_2" value="Transaction Charges">
						<input type="hidden" name="amount_2" value="<?=$fees?>">
						<input type="hidden" name="custom" value="<?=$invoice['invoice_number']?>">
						<input type="hidden" name="notify_url" value="https://quicure.com/paypal-ipn">
						<input type="hidden" name="return" value="https://quicure.com/return/<?=$q?>">
						<input type="submit" class="btn btn-primary btn-lg btn-block" value="Pay Now with PayPal">
					</form>
					<?php } else { ?>
					<div class="btn btn-danger btn-lg btn-block">Paid by <?=$invoice['paid_by']?></div>
					<?php } ?>
				</div>
				<div class="col-xs-5">
					<table class="table">
						<tbody>
							<?php
							if($invoice['tax_value'] != 0) {
							?>
							<tr>
								<td><?=$invoice['tax_name']?> (<?=$invoice['tax_value']?>%)</td>
								<td><?="$".number_format($tax, 2, '.', ',')?></td>
							</tr>
							<?php
							}
							?>
							<tr class="active">
								<td>Amount Due</td>
								<td><?="$" . number_format($grand_total, 2, ".", ",")?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		<?php if(!isset($_GET['frameless'])) { ?>
		</div>
		<?php } ?>
	</body>
</html>