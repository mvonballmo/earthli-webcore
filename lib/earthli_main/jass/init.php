<?php
  require_once ('webcore/init.php');

  $Page->title->group = 'earthli Jass Manual';
  $Page->template_options->title = 'Jass';
  $Page->template_options->icon = '{site_icons}products/jass';

  $Page->location->add_root_link ();

  $Page->add_style_sheet ('{styles}tree.css');
  $Page->add_style_sheet ('styles/jass.css');
?>