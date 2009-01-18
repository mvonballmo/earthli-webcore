<?php
  require_once ('webcore/init.php');

  $Page->title->group = "earthli Webcore";
  $Page->title->subject = 'Latest news';
  $Page->template_options->title = '<img src="media/images/webcore_title.png" alt="earthli WebCore">';
  $Page->template_options->icon = '{site_icons}products/webcore';

  $Page->location->add_root_link ();
  $Page->location->append ('Software', '../');

  $Page->add_style_sheet ('styles/webcore.css');
?>