<?php
	$Page->title->subject = 'Search';

	$Page->location->append ("Hochzeit", "./");
	$Page->location->append ("R.S.V.P.", "index.php?panel=rsvp");
	$Page->location->append ("Guest Search", "index.php?panel=guest_search");
	$Page->location->append ("Results");

	$search_text = read_var ('search');

  $class_name = $Page->final_class_name ('INVITEE_QUERY', 'hochzeit/db/invitee_query.php');
  $query = new $class_name ($Page);
	$query->add_search ($search_text, 'search_text');
	$num_objects = $query->size ();

	$Page->start_display ();
?>
<div class="box">
  <div class="box-title">
			R.S.V.P. Search on "<?php echo $search_text; ?>"
	</div>
  <div class="box-body">
    <p style="text-align: center">
      We've found
      <?php if ($num_objects != 1) { ?>
      <span class="field"><?php echo $num_objects; ?></span> people that match
      <?php } else { ?>
      <span class="field"><?php echo $num_objects; ?></span> person that matches
      <?php } ?>
      your search:
    </p>
    <div style="text-align: center">
    <?php
      $class_name = $Page->final_class_name ('INVITEE_GRID', 'hochzeit/gui/invitee_grid.php');
      $grid = new $class_name ($Page);
      $grid->set_ranges (25, 1);
      $grid->set_query ($query);
      $grid->display ();
    ?>
    </div>
  </div>
</div>
<?php $Page->finish_display (); ?>