<?php
  $class_name = $Page->final_class_name ('INVITEE_QUERY', 'hochzeit/db/invitee_query.php');
  $query = new $class_name ($Page);
	$reception = read_var ('reception');
	$picnic = read_var ('picnic');
	$panel = 'guest_list';
	$sort_by_date = read_var ('sort_by_date');
	$day = read_var ('day');
	if ($reception)
		$query->restrict ("reception = 1");
	if ($picnic)
		$query->restrict ("picnic = 1");
	if ($sort_by_date)
		$query->set_order ('time_registered DESC');
	if ($day)
		$query->set_days ("2002-$day 00:00:00", "2002-$day 23:59:59");
	$num_objects = $query->size ();
?>
<?php
	if ($reception && $picnic)
	{
?>
<h3>Guest List (<?php echo $num_objects; ?> people)</h3>
<p>This is the list of guests that are coming to both the picnic and the reception.</p>
<?php
	}
	else
	{
		if ($picnic)
		{
?>
<h3>Picnic Guest List (<?php echo $num_objects; ?> people)</h3>
<p>This is the list of guests that are coming to the picnic.</p>
<?php
		}

		if ($reception)
		{
?>
<h3>Reception Guest List (<?php echo $num_objects; ?> people)</h3>
<p>This is the list of guests that are coming to the reception.</p>
<?php
		}
	}

	if (! $reception && ! $picnic)
	{
		if ($day)
		{
?>
<h3><?php echo $num_objects; ?> people R.S.V.P.d on <?php echo $day; ?>-2002</h3>
<p>This is list of guests that registered on <?php echo $day; ?>-2002.</p>
<?php
		}
		else
		{
?>
<h3>Full Guest List (<?php echo $num_objects; ?> people)</h3>
<p>This is the full list of invited guests. It shows whether they have accepted or rejected the invitation and when they did so.</p>
<?php
		}
	}
?>
<?php
  $class_name = $Page->final_class_name ('INVITEE_GRID', 'hochzeit/gui/invitee_grid.php');
  $grid = new $class_name ($Page);
  $grid->set_ranges ($num_rows, 1);
	$grid->set_query ($query);
	$grid->display ();
?>