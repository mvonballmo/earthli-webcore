<?php
  $class_name = $Page->final_class_name ('INVITEE_QUERY', 'hochzeit/db/invitee_query.php');
  $query = new $class_name ($Page);
	$num_objects = $query->size ();
?><h3>Calendar of events</h3>
<p>Here's the calendar of known dates so far.</p>
<div style="text-align: center">
<?php
  $class_name = $Page->final_class_name ('HOCHZEIT_CALENDAR', 'hochzeit/gui/hochzeit_calendar.php');
  $calendar = new $class_name ($Page);
	$calendar->display (1);
?>
</div>