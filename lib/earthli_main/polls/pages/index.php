<?php
	require_once ('polls/init.php');

  $Page->location->add_root_link ();
  $Page->location->append ('Polls');

	$Page->start_display ();
?>
<div class="side-bar" style="float: left; width: 15em">
  <div class="side-bar-title">
    Polls
  </div>
  <div class="side-bar-body">
  <?php
    $class_name = $App->final_class_name ('POLL_GRID', 'polls/gui/poll_grid.php');
    $grid = new $class_name ($App);
    $poll_query = $App->poll_query ();
    $grid->set_ranges (10, 1);
    $grid->set_query ($poll_query);
    $grid->display ();
  ?>
  </div>
</div>
<div class="box" style="margin-left: 16em">
  <div class="box-title">
    Selected poll
  </div>
  <div class="box-body">
  <?php
    $poll_id = read_var ('id');
    if ($poll_id)
    {
      $poll =& $poll_query->object_at_id ($poll_id);
      if ($poll)
        $poll->display_results ();
    }
    else
    {
  ?>
    Click any one of the "Results" links to the left to see that poll.
  <?php
    }
  ?>
  </div>
</div>
<?php $Page->finish_display (); ?>