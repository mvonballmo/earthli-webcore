<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
  $folder =& $folder_query->object_at_id (read_var ('id'));

  if (isset ($folder) && $App->login->is_allowed (Privilege_set_folder, Privilege_view, $folder))
  {
    $App->set_referer ();
    
    $Page->title->add_object ($folder);
    $Page->title->subject = 'Calendar';
    $Page->location->add_folder_link ($folder->parent_folder (), '', 'view_calendar.php');
    $Page->location->add_object_link ($folder);
    $Page->location->append ($Page->title->subject);

    $Page->add_style_sheet ('{app_styles}calendar');
    $Page->start_display ();
?>
<div class="box">
  <div class="box-title">
    <?php echo $folder->title_as_html (); ?> - Calendar
  </div>
  <?php
    $menu = $App->make_menu ();
    $menu->renderer->content_mode = Menu_show_all_as_buttons;
    $menu->renderer->alignment = Menu_align_right;
    $menu->append ('Explorer', "view_explorer.php?id=$folder->id", '{icons}/buttons/explorer');
    $menu->append ('Album', "view_folder.php?id=$folder->id", '{app_icons}/buttons/new_album');
    $menu->display_as_toolbar ();
  ?>
  <div class="box-body">
  <?php
    $calendar = 1;
    
    $class_name = $App->final_class_name ('ALBUM_CALENDAR', 'albums/gui/album_calendar.php');
    $cal = new $class_name ($folder);
    $cal->display ();
  ?>
  </div>
</div>
<?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to view the calendar for this folder.', $folder);
  }
?>