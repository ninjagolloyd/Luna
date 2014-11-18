<?php

session_start();

if(version_compare(PHP_VERSION, '5.2', '<')) {
    $check['php'] = false;
    $check['php_version'] = PHP_VERSION;
    $check['php_css'] = 'danger';
} else {
    $check['php'] = true;
    $check['php_version'] = PHP_VERSION;
    $check['php_css'] = 'success';
}

$config_chmods = substr(decoct(fileperms("../config.php")), -3);
if($config_chmods < '666') {
    $check['config_chm'] = false;
    $check['config_chm_value'] = $config_chmods;
    $check['config_chm_css'] = 'danger';
} else {
    $check['config_chm'] = true;
    $check['config_chm_value'] = $config_chmods;
    $check['config_chm_css'] = 'success';
}

$avatars_chmods = substr(decoct(fileperms("../img/avatars/")), -3);
if($config_chmods < '666') {
    $check['avatars_chm'] = false;
    $check['avatars_chm_value'] = $avatars_chmods;
    $check['avatars_chm_css'] = 'danger';
} else {
    $check['avatars_chm'] = true;
    $check['avatars_chm_value'] = $avatars_chmods;
    $check['avatars_chm_css'] = 'success';
}

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
<?php
if($check['php'] === true && $check['config_chm'] === true && $check['avatars_chm'] === true) {
     $_SESSION['luna_install_check'] = true;
?>
    <body class="ready">
        <div class="site-wrapper">
            <div class="site-wrapper-inner">
                <div class="cover-container">
                    <div class="masthead clearfix">
						<h3 class="masthead-brand"><span class="luna-brand">Luna</span>Preview<span class="luna-brand">setup</span></h3>
                    </div>
                    <div class="inner cover">
                        <h1 class="cover-heading">You can do anything</h1>
                        <p class="lead">
                            <a href="terms.php" class="btn btn-lg btn-default">Let's get started</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <script src="../include/js/jquery.js"></script>
        <script src="../include/js/bootstrap.min.js"></script>
    </body>
<?php } else { ?>
    <body class="error">
        <div class="site-wrapper">
            <div class="site-wrapper-inner">
                <div class="cover-container">
                    <div class="masthead clearfix">
						<h3 class="masthead-brand"><span class="luna-brand">Luna</span>Preview<span class="luna-brand">setup</span></h3>
                    </div>
                    <div class="inner cover">
                        <h1 class="cover-heading">You can't do anything</h1>
                        <p class="lead btn-group">
                            <a href="#" data-toggle="modal" data-target="#errors" class="btn btn-lg btn-default">Show errors</a>
                            <a href="index.php" class="btn btn-lg btn-default">Check again</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
		<div class="modal fade modal-form" id="errors" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-xs">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Errors</h4>
					</div>
					<table class="table">
						<thead>
							<tr>
								<th></th>
								<th>Required</th>
								<th>Your system</th>
							</tr>
						</thead>
						<tr>
							<td>PHP Version</td>
							<td>5.2.0</td>
							<td class="<?php echo $check['php_css']; ?>"><?php echo $check['php_version']; ?></td>
						</tr>
						<tr>
							<td>Chmod <code>/config.php</code></td>
							<td>666</td>
							<td class="<?php echo $check['config_chm_css']; ?>"><?php echo $check['config_chm_value']; ?></td>
						</tr>
						<tr>
							<td>Chmod <code>/img/avatar/</code></td>
							<td>666</td>
							<td class="<?php echo $check['avatars_chm_css']; ?>"><?php echo $check['avatars_chm_value']; ?></td>
						</tr>
					</table>
					<div class="modal-footer">
						<a href="index.php" class="btn btn-lg btn-default">Check again</a>
					</div>
				</div>
			</div>
		</div>
        <script src="../include/js/jquery.js"></script>
        <script src="../include/js/bootstrap.min.js"></script>
    </body>
<?php } ?>
</html>