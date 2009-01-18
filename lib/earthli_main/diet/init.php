<?php
  require_once ('webcore/init.php');

  $Page->title->group = 'earthli Dieting Statistics';
  $Page->template_options->title = 'Diet';
  $Page->template_options->icon = '{site_icons}products/diet';
  $Page->database_options->name = $Opt_data_base_name;

  $Page->location->add_root_link ();
?>