<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>

<form id="search" method="get" action="search.php?section=advanced">
	<nav class="navbar navbar-default" role="navigation">
		<div class="navbar-header">
			<a href="search.php" class="navbar-brand"><span class="luni luni-fw luni-search"></span> <?php echo $lang['Search'] ?></a>
		</div>
		<div class="collapse navbar-collapse hidden-xs" id="search-nav">
			<ul class="navbar-form navbar-right">
				<input class="btn btn-primary" type="submit" name="search" value="<?php echo $lang['Search'] ?>" accesskey="s" />
			</ul>
		</div>
	</nav>
	<div class="panel panel-default">
		<div class="panel-body">
			<fieldset class="form-inline">
				<input type="hidden"  name="action" value="search" />
				<input placeholder="<?php echo $lang['Keyword'] ?>" class="form-control" type="text" name="keywords" maxlength="100" />
				<input placeholder="<?php echo $lang['Author'] ?>"  class="form-control" id="author" type="text" name="author" maxlength="25" />
				<select class="form-control" id="search_in" name="search_in">
					<option value="0"><?php echo $lang['Message and subject'] ?></option>
					<option value="1"><?php echo $lang['Message only'] ?></option>
					<option value="-1"><?php echo $lang['Topic only'] ?></option>
				</select>
				<select class="form-control" name="sort_by">
					<option value="0"><?php echo $lang['Sort by post time'] ?></option>
					<option value="1"><?php echo $lang['Sort by author'] ?></option>
					<option value="2"><?php echo $lang['Subject'] ?></option>
					<option value="3"><?php echo $lang['Forum'] ?></option>
				</select>
				<select class="form-control" name="sort_dir">
					<option value="DESC"><?php echo $lang['Descending'] ?></option>
					<option value="ASC"><?php echo $lang['Ascending'] ?></option>
				</select>
				<select class="form-control" name="show_as">
					<option value="topics"><?php echo $lang['Topics'] ?></option>
					<option value="posts"><?php echo $lang['Show as posts'] ?></option>
				</select>
			</fieldset>
			<fieldset>
				<div class="row">
					<?php echo draw_search_forum_list(); ?>
				</div>
			</fieldset>
		</div>
	</div>
</form>