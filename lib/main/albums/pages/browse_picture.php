<?php

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

This file is part of earthli Albums.

earthli Albums is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Albums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Albums; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Albums, visit:

http://www.earthli.com/software/webcore/albums

****************************************************************************/

  $folder_query = $App->login->folder_query ();
  $folder = $folder_query->object_at_id (read_var ('id'));

  if (isset ($folder) && $App->login->is_allowed (Privilege_set_folder, Privilege_modify, $folder))
  {
    $Page->title->add_object ($folder);
    $Page->title->subject = 'Browse for picture';

    $Page->template_options->header_visible = false;
    $Page->template_options->footer_visible = false;
    $Page->add_script_file ('{scripts}webcore_forms.js');    
    $Page->start_display ();
?>
<div class="box">
  <div class="box-body">
    <h2>
      Choose cover picture for <?php echo $folder->title_as_link (); ?>
    </h2>
    <p class="notes">Click a picture below to select it. Click "Clear" to select
      no cover picture.</p>
    <?php
      $controls_renderer = $App->make_controls_renderer ();
      echo '<p>';
      echo $controls_renderer->javascript_button_as_html ('Clear', "picker.select_value ('0')", '{icons}buttons/delete' );
      echo $controls_renderer->javascript_button_as_html ('Cancel', "window.close()", '{icons}buttons/close' );
      echo '</p>';

      $pic_query = $folder->entry_query ();
      $pic_query->set_type ('picture');
      
      $class_name = $App->final_class_name ('SIMPLE_PICTURE_GRID', 'albums/gui/simple_picture_grid.php');
      $grid = new $class_name ($App);
      $grid->set_ranges (8, 3);
      $grid->set_query ($pic_query);
      $grid->display ();
    ?>
  </div>
</div>
<?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to edit this folder.', $folder);
  }
?>
