<?php

session_start();

if (isset($_POST['form_sent'])) {
	$_SESSION['luna_finished'] = true;
	header('Location: success.php');
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Update Luna</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link href="install.css" rel="stylesheet">
    </head>
    <body class="default">
        <div class="site-wrapper">
            <div class="site-wrapper-inner">
                <div class="cover-container">
                    <div class="masthead clearfix">
						<h3 class="masthead-brand"><span class="luna-brand">Luna</span>Preview<span class="luna-brand">Update</span></h3>
                    </div>
                    <div class="inner cover">
						<form method="post" action="update.php">
							<input type="hidden" name="form_sent" value="1" />
                        	<h1 class="cover-heading">There's an update available to be installed</h1>
							<p class="lead">
								<button class="btn btn-lg btn-default" type="submit" name="update">Update</button>
							</p>
						</form>
                    </div>
                </div>
            </div>
        </div>
		<script src="http://code.jquery.com/jquery-2.1.3.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    </body>
</html>