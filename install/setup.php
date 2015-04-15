<?php

session_start();

if(!isset($_SESSION['luna_install_check']))
	die('Installation access denied.');

if (isset($_POST['form_sent'])) {
	$_SESSION['luna_install_database'] = true;
	header('Location: essential.php');
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
						<h3 class="masthead-brand"><span class="luna-brand">Luna</span>Initialization</h3>
                    </div>
                    <div class="inner cover">
						<form class="form-horizontal" method="post" action="setup.php">
							<input type="hidden" name="form_sent" value="1" />
							<div class="panel panel-default">
								<div class="panel-body">
									<fieldset>
										<div class="form-group">
											<label class="col-sm-3 control-label">Database<span class="help-block">What database do you want to use?</span></label>
											<div class="col-sm-9">
												<select class="form-control" name="req_db_type">
													<option value="1" selected="selected">MySQL</option>
													<option value="2" selected="selected">MySQLi</option>
													<option value="3" selected="selected">MySQL InnoDB</option>
													<option value="4" selected="selected">MySQLi InnoDB</option>
													<option value="5" selected="selected">SQLite</option>
													<option value="6" selected="selected">PostgreSQL</option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Server hostname<span class="help-block">Where's the server?</span></label>
											<div class="col-sm-9">
												<input type="text" class="form-control" name="req_db_host" value="" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Name<span class="help-block">The database name</span></label>
											<div class="col-sm-9">
												<input id="req_db_name" type="text" class="form-control" name="req_db_name" value="" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Username<span class="help-block">Your database username</span></label>
											<div class="col-sm-9">
												<input type="text" class="form-control" name="db_username" value="" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Password</label>
											<div class="col-sm-9">
												<input type="password" class="form-control" name="db_password" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Prefix<span class="help-block">Set for more Luna installation in this database</span></label>
											<div class="col-sm-9">
												<input id="db_prefix" type="text" class="form-control" name="db_prefix" value="" />
											</div>
										</div>
									</fieldset>
								</div>
							</div>
							<p class="lead">
								<button class="btn btn-lg btn-default" type="submit" name="install">Install Luna</button>
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