<?php
	$party = read_var ('party');
	$changed = read_var ('changed');
	$search_text = read_var ('search_text');

	$Page->location->append ("Hochzeit", "./");
	$Page->location->append ("R.S.V.P.", "index.php?panel=rsvp");
	$Page->location->append ("Guest List", "index.php?panel=guest_list");
	$Page->location->append ("Party \"$party\"");

  $class_name = $Page->final_class_name ('INVITEE_QUERY', 'hochzeit/db/invitee_query.php');
  $query = new $class_name ($Page);
	$query->restrict ("party = '$party'");
	$num_objects = $query->size ();

	$Page->start_display ();
?>
<div>
	<div class="box-title">
		Party "<?php echo $party; ?>"
	</div>
	<div class="box-body">
    <p style="text-align: center">
    <?php if ($changed) {?>
      Thank you for registering!
    <?php } else { ?>
      There
      <?php if ($num_objects != 1) { ?>
      are <span class="field"><?php echo $num_objects; ?></span> people
      <?php } else { ?>
      is <span class="field"><?php echo $num_objects; ?></span> person
      <?php } ?>
      in this party. Click 'Change R.S.V.P.' to change your status.
    <?php } ?>
    </p>
    <?php
      $class_name = $Page->final_class_name ('INVITEE_GRID', 'hochzeit/gui/invitee_grid.php');
      $grid = new $class_name ($Page);
      $grid->show_links = FALSE;
      $grid->set_ranges (25, 1);
      $grid->set_query ($query);
      $grid->display ();
    ?>
    <div style="text-align: center; margin-top: 1em">
    <?php
      $renderer = $Page->make_controls_renderer ();
      echo $renderer->button_as_html ('Change R.S.V.P.', "change_rsvp.php?party=$party", '{icons}buttons/edit');
      if ($changed)
        echo $renderer->button_as_html ('Continue', "index.php?panel=guest_list&amp;sort_by_date=1", '{icons}buttons/go_to_next');
      else
        echo $renderer->button_as_html ('Back to guest list', "index.php?panel=guest_list&amp;sort_by_date=1", '{icons}buttons/go_to_previous');
    ?>
    </div>
  </div>
</div>
<?php $Page->finish_display (); ?>