<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod'])
	header("Location: login.php");
$action = isset($_GET['action']) ? $_GET['action'] : null;
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Update']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('backstage', 'about');
	
	?>
<div class="row">
	<div class="col-sm-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Navigation</h3>
			</div>
			<div class="list-group">
				<a href="#brand" class="list-group-item"><span class="fa fa-fw fa-bold"></span> Editor</a>
				<a href="#board" class="list-group-item"><span class="fa fa-fw fa-align-justify"></span> Board</a>
				<a href="#backstage" class="list-group-item"><span class="fa fa-fw fa-dashboard"></span> Backstage</a>
				<a href="#theme" class="list-group-item"><span class="fa fa-fw fa-paint-brush"></span> Themes</a>
				<a href="#others" class="list-group-item">Other improvements and notes</a>
				<a href="#updates" class="list-group-item">Updates</a>
			</div>
		</div>
	</div>
	<div class="col-sm-9">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">About Luna 1.1 Bittersweet Shimmer</h3>
			</div>
			<div class="panel-body panel-about">
				<a id="brand"></a><h3><span class="fa fa-fw fa-bold"></span> Editor</h3>
				<div class="row">
					<div class="col-sm-6">
						<h4>WYSIWYG</h4>
						<p>We've had our previous editor for a while, but it was time for a massive upgrade. Bittersweet Shimmer introduces a WYSIWYG editor, helping you to make posts easier.</p>
					</div>
				</div>
				<a id="board"></a><h3><span class="fa fa-fw fa-align-justify"></span> Board</h3>
				<div class="row">
					<div class="col-sm-6">
						<h4></h4>
						<p></p>
					</div>
				</div>
				<a id="backstage"></a><h3><span class="fa fa-fw fa-dashboard"></span> Backstage</h3>
				<div class="row">
					<div class="col-sm-6">
						<h4></h4>
						<p></p>
					</div>
				</div>
				<a id="theme"></a><h3><span class="fa fa-fw fa-paint-brush"></span> Themes</h3>
				<div class="row">
					<div class="col-sm-6">
						<h4>Theme Engine 6.1</h4>
						<p>After the major revamp from Aero, Bittersweet Shimmer continues to improve our themes with a more simplified system, better support for accents, night mode and so much more.</p>
					</div>
				</div>
				<a id="others"></a><h3>Other improvements and notes</h3>
				<div class="row">
					<div class="col-sm-6">
						<h4>Packages</h4>
						<p><b>Core</b> has been updated from version 1.0.4275 to 1.1.4289.</p>
					</div>
					<div class="col-sm-6">
						<h4>Bugfixes</h4>
						<p>0 bugs have been fixed.</p>
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<p>Luna is developed by the <a href="http://getluna.org/">Luna Group</a>. Copyright 2013-2015. Released under the GPLv3 license.</p>
			</div>
		</div>
	</div>
</div>
<?php

require 'footer.php';
