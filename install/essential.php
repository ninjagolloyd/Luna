<?php

session_start();

if(!isset($_SESSION['luna_install_database']))
	die('Installation access denied.');

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
        <title>Install Luna</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link href="install.css" rel="stylesheet">
    </head>
    <body class="default">
        <div class="site-wrapper">
            <div class="site-wrapper-inner">
                <div class="cover-container">
                    <div class="masthead clearfix">
						<h3 class="masthead-brand"><span class="luna-brand">Luna</span>Essential</h3>
                    </div>
                    <div class="inner cover">
						<form class="form-horizontal" method="post" action="essential.php">
							<input type="hidden" name="form_sent" value="1" />
							<div class="panel panel-default">
								<div class="panel-body">
									<fieldset>
										<div class="form-group">
											<label class="col-sm-3 control-label">Username<span class="help-block">Tell me your name</span></label>
											<div class="col-sm-9">
												<input type="text" class="form-control" name="req_username" value="" maxlength="25" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Password<span class="help-block">At least 6 characters long</span></label>
											<div class="col-sm-9">
												<div class="row">
													<div class="col-sm-6">
														<input id="req_password1" type="password" class="form-control" name="req_password1" />
													</div>
													<div class="col-sm-6">
														<input type="password" class="form-control" name="req_password2" />
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Email</label>
											<div class="col-sm-9">
												<input id="req_email" type="text" class="form-control" name="req_email" value="" maxlength="80" />
											</div>
										</div>
									</fieldset>
									<hr />
									<fieldset>
										<div class="form-group">
											<label class="col-sm-3 control-label">Board title</label>
											<div class="col-sm-9">
												<input id="req_title" type="text" class="form-control" name="req_title" value="" maxlength="255" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Board description</label>
											<div class="col-sm-9">
												<input id="desc" type="text" class="form-control" name="desc" value="" maxlength="255" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Board URL<span class="help-block">No trailing slash<span></label>
											<div class="col-sm-9">
												<input id="req_base_url" type="text" class="form-control" name="req_base_url" value="" maxlength="100" />
											</div>
										</div>
									</fieldset>
								</div>
							</div>
							<p class="lead">
								<button class="btn btn-lg btn-default" type="submit" name="account">Create account</button>
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