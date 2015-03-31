<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Luna / Edge</title>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/edge.css" rel="stylesheet">
	</head>
	<body>
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">Luna</a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
						<li><a href="#">Link</a></li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#">Action</a></li>
								<li><a href="#">Another action</a></li>
								<li><a href="#">Something else here</a></li>
								<li class="divider"></li>
								<li><a href="#">Separated link</a></li>
								<li class="divider"></li>
								<li><a href="#">One more separated link</a></li>
							</ul>
						</li>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#">Back</a></li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">User <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#">Profile</a></li>
								<li><a href="#">Settings</a></li>
								<li class="divider"></li>
								<li><a href="#">Help</a></li>
								<li><a href="#">Support</a></li>
								<li class="divider"></li>
								<li><a href="#">Log out</a></li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-2">
					<div class="list-group">
						<a class="list-group-item" href="index.php">Backstage</a>
					</div>
					<div class="list-group">
						<a class="list-group-item" href="settings.php">Settings</a>
						<a class="list-group-item" href="features.php">Features</a>
					</div>
					<div class="list-group">
						<a class="list-group-item" href="about.php">About Luna</a>
					</div>
				</div>
				<div class="col-xs-10">
					<h1>Welcome back, Edge!</h1>
					<div class="row">
						<div class="col-sm-7">
							<div class="row">
								<div class="col-xs-12">
									<div class="panel panel-default">
										<div class="panel-heading">
											<h3 class="panel-title">Reports</h3>
										</div>
										<table class="table">
											<thead>
												<tr>
													<th>Reported by</th>
													<th>Time</th>
													<th>Message</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>Aero</td>
													<td>21:05 31.03.'15</td>
													<td>This is just not polite in any meaning of the word.</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="panel panel-default">
										<div class="panel-heading">
											<h3 class="panel-title">Statistics</h3>
										</div>
										<table class="table">
											<thead>
												<tr>
													<th>Posts</th>
													<th>Topics</th>
													<th>Users</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>1.421</td>
													<td>389</td>
													<td>426</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="panel panel-default">
										<div class="panel-heading">
											<h3 class="panel-title">Statistics</h3>
										</div>
										<div class="panel-body">
											<a class="btn btn-default btn-block" href="http://getluna.org/edge.php">About Edge</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-5">
							<h2>Admin notes</h2>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
	</body>
</html>