<?php
	require_once ('diet/init.php');

  require_once ('diet/obj/dieting_statistic.php');
  require_once ('diet/forms/dieting_statistic_form.php');
  require_once ('diet/db/dieting_statistic_query.php');
  require_once ('diet/gui/dieting_statistic_grid.php');

  $form = new DIETING_STATISTIC_FORM ($Page);
  $stat = new DIETING_STATISTIC ($Page);
  $stat->user_id = $Opt_user_id;

  $form->process_new ($stat);
  if ($form->committed ())
    $Env->redirect_local ('index.php');

	$Page->template_options->title = "Diet";
	$Page->title->subject = "Dieting statistics";
	$Page->location->append ("Users");
	$Page->location->append ($Opt_user_name, "../");
	$Page->location->append ($Page->title->subject);

	/* Prepare the query with the desired date */

	$now = time ();
	$now_date = new DATE_TIME ();
	
	$today_month = date ("n", $now);
	$today_year = date ("Y", $now);

	$month = read_var ('month', $today_month);
	$year = read_var ('year', $today_year);

  if ($month == 1)
	{
		$previous_month = 12;
		$previous_year = $year - 1;
	}
	else
	{
		$previous_month = $month - 1;
		$previous_year = $year;
	}

	if ($month == 12)
	{
		$next_month = 1;
		$next_year = $year + 1;
	}
	else
	{
		$next_month = $month + 1;
		$next_year = $year;
	}

	$queried_months = ($year * 12) + $month;
	$today_months = ($today_year * 12) + $today_month;

  $actual_date = $Page->make_date_time (mktime (0, 0, 0, $month, 1, $year));
	
  $first_day = "$year-$month-01";
	$last_day = "$year-$month-" . $actual_date->last_legal_day ();

  $query = new DIETING_STATISTIC_QUERY ($Page);
  $query->set_days ($first_day, $last_day);
  $query->restrict ("user_id = $Opt_user_id");

	$Page->start_display ();

  $box = $Page->make_box_renderer ();
  $box->start_column_set ();
  $box->new_column ();
?>
  <div class="side-bar">
    <div class="side-bar-title">
      Add data
    </div>
    <div class="side-bar-body">
    <?php
      $form->display ();
    ?>
    </div>
  </div>
  <br>
  <div class="side-bar">
    <div class="side-bar-title">
      <a href="index.php?<?php echo "month=$previous_month&amp;year=$previous_year";?>"><?php echo $Page->resolve_icon_as_html ('{icons}buttons/go_to_previous', '', '16px');?></a>
      <?php
        echo date ("F", mktime (0, 0, 0, $month, 1, $year)) . ' ' . $year;

        if ($queried_months < $today_months)
        {
      ?>
      <a href="index.php?<?php echo "month=$next_month&amp;year=$next_year";?>"><?php echo $Page->resolve_icon_as_html ('{icons}buttons/go_to_next', '', '16px');?></a>
      <?php
        }
      ?>
    </div>
    <div class="side-bar-body">
    <?php
      $grid = new DIETING_STATISTIC_GRID ($Page);
      $grid->set_ranges (100, 1);
      $grid->set_query ($query);
      $grid->display ();
    ?>
    </div>
  </div>
<?php
  $box->new_column ('width: 75%; padding-left: 1em');
?>
  <div class="box">
    <div class="box-title"><?php echo $Page->title->subject; ?></div>
    <div class="box-body">
      <?php
        $stats = $query->objects ();
        if (sizeof ($stats))
        {
      ?>
      <h4>Weight vs. Time</h4>
      <div class="graph-background">
      <?php
        $stats = array_reverse ($stats);

        foreach ($stats as $s)
        {
          $w = $s->weight - $Opt_weight_base_line;
          $w *= 10;
          printf ('<img class="graph-foreground" style="width: .5em; height: %spx" src="/common/images/pixel.gif" alt="">', $w);
          echo '<img style="width: .2em; height: 1px" src="/common/images/pixel.gif" alt="">';
        }
      ?>
      </div>
      <div style="text-align: right" class="detail">
        Baseline is at <?php echo $Opt_weight_base_line; ?> lbs.
      </div>
      <h4>Body Fat vs. Time</h4>
      <div class="graph-background">
      <?php
        foreach ($stats as $s)
        {
          $f = $s->body_fat - $Opt_body_fat_base_line;
          $f *= 10;
          printf ('<img class="graph-foreground" style="width: .5em; height: %spx" src="/common/images/pixel.gif" alt="">', $f);
          echo '<img style="width: .2em; height: 1px" src="/common/images/pixel.gif" alt="">';
        }
      ?>
      </div>
      <div style="text-align: right" class="detail">
        Baseline is at <?php echo $Opt_body_fat_base_line; ?>% fat.
      </div>
      <?php } ?>
    </div>
  </div>
<?php 
  $box->finish_column_set ();
  $Page->finish_display (); 
?>