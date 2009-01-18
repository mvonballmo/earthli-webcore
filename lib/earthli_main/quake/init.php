<?php
  require_once ('webcore/init.php');

  $Page->title->group = 'dur\'s Quake III Arena';
  $Page->template_options->title = 'Q3A';
  $Page->template_options->icon = '{site_icons}products/quake';

  $Page->location->add_root_link ();

  $Page->database_options->name = 'marco';

  // if the user has a default theme, then change the default
  // for this section to use 'quake' and 'courier'

  if (! $Page->stored_theme->main_CSS_file_name)
    $Page->theme->main_CSS_file_name = '{themes}/alien';

  if (! $Page->stored_theme->font_name_CSS_file_name)
    $Page->theme->font_name_CSS_file_name = '{styles}fonts/courier';

  // add the stylesheet with the tree-display classes

  $Page->add_style_sheet ('{styles}tree.css');
?>