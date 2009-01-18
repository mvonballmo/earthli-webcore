<div class="menu-bar-top">
<?php
	$sort_by_date = read_var ('sort_by_date');
	if ($sort_by_date)
	{
?>
	<a href="index.php?panel=guest_list&amp;reception=<?php echo read_var ('reception'); ?>&amp;picnic=<?php echo read_var ('picnic'); ?>">Sort by name</a> |
<?php
	}
	else
	{
?>
	<a href="index.php?panel=guest_list&amp;reception=<?php echo read_var ('reception'); ?>&amp;picnic=<?php echo read_var ('picnic'); ?>&amp;sort_by_date=1">Sort by date</a> |
<?php
	}
?>
	<a href="print_preview.php?reception=<?php echo read_var ('reception'); ?>&amp;picnic=<?php echo read_var ('picnic'); ?>&amp;sort_by_date=<?php echo $sort_by_date; ?>">
		<?php echo $Page->resolve_icon_as_html ('{icons}buttons/print', 'Print', '16px'); ?>
	</a>
</div>
<div style="text-align: center">
<?php
	$num_rows = 25;
	include ('hochzeit/pages/guest_list_body.php');
?>
</div>