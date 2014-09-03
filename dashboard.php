<?php
include('core/php/incl.php');
if(!$logged_in) {
	header("Location: index.php");
	exit();
}
function checkurs($inv) {
	global $db;
	global $userdata;
	$inv = sanitize($inv);
	$owner = $userdata['email'];
	$q = mysqli_query($db, "SELECT * FROM `invoices` WHERE `invoice_number` = '$inv' AND `owner` = '$owner'");
	return mysqli_num_rows($q) > 0;
}
if(isset($_POST['sendinv'])) {
	if(empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || empty($_POST['invno']) || !checkurs($_POST['invno'])) {
		$invsenderr = true;
	} else {
		$to = sanitize($_POST['email']);
		$owner = $userdata['email'];
		$invno = sanitize($_POST['invno']);
		mysqli_query($db, "UPDATE `invoices` SET `sent` = '1' WHERE `invoice_number` = '$invno' AND `owner` = '$owner'");
		mysqli_query($db, "UPDATE `invoices` SET `sent_to` = '$to' WHERE `invoice_number` = '$invno' AND `owner` = '$owner'");
		header("Location: dashboard");
	}
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Dashboard - Quick &amp; Secure</title>
		<meta charset="utf-8">
		<meta name="robots" content="noindex, nofollow">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="//maxcdn.bootstrapcdn.com/bootswatch/3.2.0/cosmo/bootstrap.min.css" rel="stylesheet">
		<link rel="shortcut icon" href="https://quicure.com/core/img/favicon.png">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<script src="core/js/jquery.browser.js"></script>
		<script src="core/js/jquery.autoresize.js"></script>
		<script src="core/js/dashboard.js"></script>
		<?php require_once('core/php/chat.php'); ?>
	</head>
	<body>
		<nav class="navbar navbar-default" role="navigation" style="height: 100px;">
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" style="padding: 0;" href="#">
						<img src="core/img/logo-wh.png" style="height: 100px;">
					</a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right" style="margin-top: 22.5px;">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-user"></i> Actions<span class="caret"></span></a>
							<ul class="dropdown-menu well" style="margin: 10px; border-radius: 10px; border: 3px solid #6e6d71; width: 300px;" role="menu">
								<h4 style="background: #6e6d71;margin: -21px;text-align: center;display: block;border-radius: inherit;margin-bottom: 21px;color: #fff;padding: 10px;">Your Profile</h4>
						        <p>Hello, <a href="#"><?=$userdata['first_name']?> <?=$userdata['last_name']?></a></p>
						        <li class="divider"></li>
					        </ul>
					    </li>
					    <li><a href="logout.php"><i class="glyphicon glyphicon-log-out"></i> Log Out</a></li>
					</ul>
				 </div>
			</div>
		</nav>
		<div class="container text-center">
			<div class="row">
				<div class="col-md-4 text-left">
					<div class="well">
						<h3 class="text-center">Money</h3>
						<hr>
						<p style="margin: 0;">Available</p>
						<h1 style="margin: 0 10px;"><small>$</small><?=number_format($userdata['balance'], 2, ".", ",")?></h1>
						<hr>
						<p><a href="#">Withdraw Money</a>&nbsp;&bull;&nbsp;<a href="#">Deposit Money</a>&nbsp;&bull;&nbsp;<a href="#">Currency</a></p>
					</div>

					<div class="well text-center">
						<h3>Tools</h3>
						<hr>
						<div class="row">
							<div class="col-md-6">
								<a data-toggle="collapse" data-parent="#tools" href="#invoices">
									<img class="img img-responsive" src="core/img/invoice.svg">
									<p>Invoices</p>
								</a>
							</div>
							<div class="col-md-6">
								<a data-toggle="collapse" data-parent="#tools" href="#finances">
									<img class="img img-responsive" src="core/img/finances.svg">
									<p>Finances</p>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-8">
					<div class="panel-group" id="tools">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#tools" href="#invoices">
										Invoices
									</a>
								</h4>
							</div>
							<div id="invoices" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="form-group">
										<a class="btn btn-primary btn-lg" href="new-invoice.php">New Invoice</a>
									</div>
									<?php
									if($invsenderr) {
									?>
									<div class="alert alert-danger">
										<strong>Error!</strong> We couldn't send your invoice. Check the email and try again.
									</div>
									<?php
									}
									?>
									<hr>
									<table class="table">
										<thead>
											<tr class="active">
												<td>Invoice Number</td>
												<td>Date Created</td>
												<td>Status</td>
												<td>Total</td>
												<td>Actions</td>
											</tr>
										</thead>
										<tbody>
											<?php
											$email = $userdata['email'];
											$q = mysqli_query($db, "SELECT * FROM `invoices` WHERE `owner` = '$email'");
											$first = true;
											while($inv = mysqli_fetch_assoc($q)) {
											?>
											<tr>
												<td><input type="radio" name="invoice" value="<?=$inv['invoice_number']?>">&nbsp;&nbsp;<?=$inv['invoice_number']?></td>
												<td><?=$inv['date_issued']?></td>
												<?php
												if($inv['paid']) {
												?>
												<td>PAID by <?=$inv['paid_by']?></td>
												<?php
												} else if ($inv['sent']) {
												?>
												<td>SENT to <?=$inv['sent_to']?></td>
												<?php
												} else {
												?>
												<td>CREATED</td>
												<?php
												}
												$inv['items'] = json_decode($inv['items'], true);
												$total = 0;
												foreach($inv['items'] as $item) {
													$total += $item['price'] * $item['quantity'];
												}
												$tax = $total * $inv['tax_value'] / 100;
												$grand_total = "$" . number_format($total + $tax, 2, ".", ",");
												?>
												<td><?=$grand_total?></td>
												<td>
													<?php if(!$inv['sent'] && !$inv['paid']) { ?>
													<div class="btn-group">
													  <div class="btn btn-success btn-xs" data-toggle="modal" data-target="#send-<?=$inv['invoice_number']?>">Send</div>
													  <button type="button" class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown">
													    <span class="caret"></span>
													  </button>
													  <ul class="dropdown-menu" role="menu">
													  	<li><a href="#" onclick="document.getElementById('inv-<?=$inv['invoice_number']?>').contentWindow.print();">Print</a></li>
													    <li><a href="#">Duplicate</a></li>
													    <li><a href="https://quicure.com/invoice/<?=$inv['invoice_number']?>" target="_blank">View</a></li>
													  </ul>
													</div>
													<div class="modal fade" id="send-<?=$inv['invoice_number']?>">
														<div class="modal-dialog">
															<div class="modal-content">
																<div class="modal-header">
																	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																	<h4 class="modal-title">Send Invoice</h4>
																</div>
																<div class="modal-body">
																	<form action="" method="post">
																		<input type="text" name="email" placeholder="Email" class="form-control input-lg">
																		<input type="hidden" name="invno" value="<?=$inv['invoice_number']?>">
																</div>
																<div class="modal-footer">
																		<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
																		<input type="submit" class="btn btn-success" name="sendinv" value="Send Invoice">
																	</form>
																</div>
															</div>
														</div>
													</div>
													<?php } else if($inv['paid']) { ?>
													<div class="btn-group">
													  <div class="btn btn-success btn-xs" onclick="document.getElementById('inv-<?=$inv['invoice_number']?>').contentWindow.print();">Print</div>
													  <button type="button" class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown">
													    <span class="caret"></span>
													  </button>
													  <ul class="dropdown-menu" role="menu">
													    <li><a href="#">Duplicate</a></li>
													    <li><a href="https://quicure.com/invoice/<?=$inv['invoice_number']?>" target="_blank">View</a></li>
													  </ul>
													</div>
													<?php } else { ?>
													<div class="btn-group">
													  <div class="btn btn-success btn-xs" onclick="document.getElementById('inv-<?=$inv['invoice_number']?>').contentWindow.print();">Print</div>
													  <button type="button" class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown">
													    <span class="caret"></span>
													  </button>
													  <ul class="dropdown-menu" role="menu">
													    <li><a href="#">Duplicate</a></li>
													    <li><a href="https://quicure.com/invoice/<?=$inv['invoice_number']?>" target="_blank">View</a></li>
													  </ul>
													</div>
													<?php } ?>
												</td>
												<iframe src="https://quicure.com/invoice/<?=$inv['invoice_number']?>" style="display: none;" id="inv-<?=$inv['invoice_number']?>"></iframe>
											</tr>
											<?php
											if($first) {
												$firstid = $inv['invoice_number'];
											}
											$first = false;
											}
											?>
										</tbody>
									</table>
									<div id="view" style="border-radius: 10px; border: 5px solid #6e6d71;">
										<?php
										if(mysqli_num_rows($q) == 0) {
										?>
										<h2>You have no invoices.</h2>
										<p><a href="new-invoice.php">Create one now</a> for <b>FREE!</b></p>
										<?php
										} else {
										?>
										<script>
											$(function(){
												$("input[value=<?=$firstid?>]").click();
											});
										</script>
										<?php
										}
										?>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#tools" href="#finances">
										Finances
									</a>
								</h4>
							</div>
							<div id="finances" class="panel-collapse collapse">
								<div class="panel-body">
									<h1>Finances</h1>
									<hr>
									<h3>The ultimate financial dashboard for just $6.99.</h3>
									<h1><small>Coming Soon</small></h1>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>