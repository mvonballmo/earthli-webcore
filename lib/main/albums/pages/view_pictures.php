<?php

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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

  if (isset ($folder)
      && $App->login->is_allowed (Privilege_set_folder, Privilege_view, $folder)
      && $App->login->is_allowed (Privilege_set_entry, Privilege_view, $folder))
  {
    $App->set_referer ();

    $Page->title->add_object ($folder);
    $Page->location->add_folder_link ($folder);

    $first_day = $App->make_date_time (read_var ('first_day'), Date_time_iso);
    $last_day = $App->make_date_time (read_var ('last_day'), Date_time_iso);
    $calendar = read_var ('calendar');
    $journal = read_var ('journal');
    $page_number = read_var ('page_number');

    $df = $first_day->formatter ();
    $df->show_CSS = false;

    if ($first_day->equals ($last_day, Date_time_date_part))
      $Page->title->subject = 'Pictures from ' . $folder->format_date ($first_day, $df);
    else
      $Page->title->subject = 'Pictures from ' . $folder->format_date ($first_day, $df) . ' - ' . $folder->format_date ($last_day, $df);

    if ($calendar)
      $Page->location->append ("Calendar", "view_calendar.php?id=$folder->id");

    if ($journal)
    {
      $jrnl_query = $folder->entry_query ();
      $jrnl = $jrnl_query->object_at_id ($journal);
      $Page->location->append ($jrnl->title, "view_journal.php?id=$jrnl->id&amp;calendar=$calendar&amp;page_number=$page_number");
    }

    if ($calendar)
      $Page->location->append ($folder->format_date ($first_day));
    else
      $Page->location->append ("Pictures");

    $Page->start_display ();
?>
<div class="box">
  <div class="box-title">
    <?php echo $Page->title->subject; ?>
  </div>
  <div class="box-body">
  <?php
    $pic_query = $folder->entry_query ();
    $pic_query->set_type ('picture');
    $pic_query->set_days ($first_day->as_iso (), $last_day->as_iso ());

    $first_day = read_var ('first_day');
    $last_day = read_var ('last_day');

    $class_name = $Page->final_class_name ('PICTURE_GRID', 'albums/gui/picture_grid.php');
    $grid = new $class_name ($Page);
    $grid->set_ranges (3, 3);
    $grid->set_query ($pic_query);
    $grid->display ();
  ?>
  </div>
</div>
<?php
    $Page->finish_display ();
  }
  else
    $Page->raise_security_violation ('You are not allowed to view pictures for this folder.', $folder);
?>