<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="col-sm-3 profile-nav">
<?php
    generate_me_menu('view');
?>
</div>
<div class="col-sm-9 col-profile">
	<?php if (file_exists('z.txt') && ($luna_config['o_reading_list'] == '1')) { ?>
	<h1>Reading list</h1>
	<table class="table">
		<thead>
			<tr>
				<th>Topic</th>
				<th>Forum</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="3">No reading list items</td>
			</tr>
		</tbody>
	</table>
	<?php } ?>
	<h1>Activity feed</h1>
</div>