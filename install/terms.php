<?php

session_start();

if(!isset($_SESSION['luna_install_check']))
	die('Installation access denied.');

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Install Luna</title>
        <link href="../include/css/bootstrap.min.css" rel="stylesheet">
        <link href="install.css" rel="stylesheet">
    </head>
    <body class="default">
        <div class="site-wrapper">
            <div class="site-wrapper-inner">
                <div class="cover-container">
                    <div class="masthead clearfix">
						<h3 class="masthead-brand"><span class="luna-brand">Luna</span>Terms<span class="visible-xs-block"></span><span class="luna-brand">&amp;</span>Lincense</h3>
                    </div>
                    <div class="inner cover">
<textarea class="form-control" rows="20">These are the terms for using Luna.

That's it, continue.</textarea><br />
                        <p class="lead">
                            <a href="setup.php" class="btn btn-lg btn-default">Agree</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <script src="../include/js/jquery.js"></script>
        <script src="../include/js/bootstrap.min.js"></script>
    </body>
</html>