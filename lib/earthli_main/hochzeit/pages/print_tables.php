<?php
	$num_rows = 1000;

	$Page->set_printable ();
	$Page->theme->font_size = 'small';
	$Page->start_display ();
?>
<div style="text-align: center">
<h2>Kath and Marco - September 14, 2002 - Tables</h2>
<?php
	include_once ('hochzeit/db/invitee_table_query.php');
	$query = new INVITEE_TABLE_QUERY ($Page);

	include_once ('hochzeit/gui/invitee_table_grid.php');
	$grid = new INVITEE_TABLE_GRID ($Page);
	$grid->set_ranges ($num_rows, 4);
	$grid->set_query ($query);
	$grid->display ();
?>
<p style="page-break-after: always"><img src="media/images/table_layout.gif" alt="Table Layout"></p>
</div>
<?php $Page->finish_display (); ?>