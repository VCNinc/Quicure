<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Order Complete | Quicure - Quick &amp; Secure</title>
		<meta charset="utf-8">
		<meta name="robots" content="noindex, nofollow">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<link rel="shortcut icon" href="https://quicure.com/core/img/favicon.ico" type="image/x-icon">
	</head>
	<body>
		<div class="container text-center">
			<div class="jumbotron">
				<h1><span style="margin-top: 10px;">Processing your payment...</span> <img style="display: inline-block;" src="https://quicure.com/core/img/loading.gif"></h1>
			</div>				
		</div>
		<script>
		setTimeout(function() {
			window.location = "https://quicure.com/invoice/<?=$_GET['q']?>";
		}, 2000);
		</script>
	</body>
</html>