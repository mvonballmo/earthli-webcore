<?php
  require_once ('webcore/init.php');

  $Page->title->group = 'earthli Software';
  $Page->template_options->title = 'Software';
  $Page->template_options->icon = '{site_icons}products/software';

  $Page->location->add_root_link ();

  $Webcore_version = '2.7.0';
  $Webcore_release_date = new DATE_TIME ('2005-12-12 12:00:00');
  $f = $Webcore_release_date->formatter ();
  $f->type = Date_time_format_short_date;
  $Webcore_release_link = $Page->resolve_file ('{root}/projects/view_release_change_log.php?id=70');

  $Page->add_style_sheet ('{root}software/styles/webcore.css');
?>