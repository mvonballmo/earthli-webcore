<?php
  require_once ('webcore/init.php');

  $Page->title->group = 'Kath and Marco - September 14, 2002';
  $Page->template_options->title = 'Hochzeit';
  $Page->template_options->icon = '{site_icons}products/hochzeit';

  $Page->location->add_root_link ();

  // Replace any of the default theme options with new defaults
  // for this page. This only changes it if the user hasn't chosen
  // a different theme.

  if (! $Page->stored_theme->main_CSS_file_name)
  {
    $Page->theme->main_CSS_file_name = '{themes}/hochzeit';
    $Page->theme->renderer_class_name = '';
  }

  if (! $Page->stored_theme->font_name_CSS_file_name)
    $Page->theme->font_name_CSS_file_name = '{styles}fonts/georgia';

  if (! $Page->stored_theme->font_size_CSS_file_name)
    $Page->theme->font_size_CSS_file_name = '{styles}core/medium';

  $Page->add_style_sheet ('{styles}tree.css');
  $Page->add_style_sheet ('styles/calendar.css');

  // switch the database

  $Page->database_options->name = 'marco';
?>
