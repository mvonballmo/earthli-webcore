<?php

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli WebCore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

  $Page->title->subject = 'Theme Settings';
  $Page->template_options->title = 'Settings';
  $Page->template_options->settings_url = '';
  
  $Page->location->add_root_link ();
  $Page->location->append ('Settings');

  $theme_query = $Page->theme_query ();
  $themes = $theme_query->objects ();

  include_once ('webcore/forms/theme_selector_form.php');
  $form = new THEME_SELECTOR_FORM ($Page, $themes);
  $form->process_plain ();
  $form->load_with_defaults ();

  $Page->start_display ();
?>
<div class="box">
  <div class="box-title"><?php echo $theme_query->size (); ?> Themes</div>
  <div class="box-body" style="text-align: center">
    <p>Adjust your font and theme settings in the form below. Select <span class="reference">[default]</span>
      to restore the site default for that setting.</p>
    <?php $form->display (); ?>
    <p>You can also switch themes with the samples below. Click a screenshot below to switch to that theme.</p>
    <?php
      $class_name = $Page->final_class_name ('THEME_GRID', 'webcore/gui/theme_grid.php');
      $grid = new $class_name ($Page);
      $grid->is_chooser = true;
      $grid->set_ranges (5, 3);
      $grid->set_query ($theme_query);
      $grid->display ();
    ?>
  </div>
</div>
<?php
  $Page->finish_display ();
?>