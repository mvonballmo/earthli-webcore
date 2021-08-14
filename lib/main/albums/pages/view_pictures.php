<?php

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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

/** @var APPLICATION $App */
$folder_query = $App->login->folder_query ();
  /** @var ALBUM $folder */
  $folder = $folder_query->object_at_id (read_var ('id'));

  if (isset ($folder)
      && $App->login->is_allowed (Privilege_set_folder, Privilege_view, $folder)
      && $App->login->is_allowed (Privilege_set_entry, Privilege_view, $folder))
  {
    $App->set_referer ();

    /** @var THEMED_PAGE $Page */
    $Page->title->add_object ($folder);
    $Page->location->add_folder_link ($folder);

    $first_day = read_var ('first_day');
    $last_day = read_var ('last_day');

    $first_day = urldecode ($first_day);
    $last_day = urldecode ($last_day);

    $first_day = $App->make_date_time ($first_day, Date_time_iso);
    $last_day = $App->make_date_time ($last_day, Date_time_iso);
    $calendar = read_var ('calendar');
    $journal_id = read_var ('journal');
    $page_number = read_var ('page_number');

    $df = $first_day->formatter ();
    $df->show_CSS = false;

    if ($first_day->equals ($last_day, Date_time_date_part))
      $Page->title->subject = 'Pictures from ' . $folder->format_date ($first_day, $df);
    else
      $Page->title->subject = 'Pictures from ' . $folder->format_date ($first_day, $df) . ' - ' . $folder->format_date ($last_day, $df);

    if ($calendar)
    {
      $Page->location->append ("Calendar", "view_calendar.php?id=$folder->id", '{icons}buttons/calendar');
    }

    if ($journal_id)
    {
      $journal_query = $folder->entry_query ();
      $journal = $journal_query->object_at_id ($journal_id);
      $Page->location->append ($journal->title, "view_journal.php?id=$journal->id&amp;calendar=$calendar&amp;page_number=$page_number");
    }

    if ($calendar)
      $Page->location->append ($folder->format_date ($first_day));
    else
      $Page->location->append ("Pictures");

    /** @var ALBUM_ENTRY_QUERY $pic_query */
    $pic_query = $folder->entry_query ();
    $pic_query->set_type ('picture');
    $pic_query->set_days ($first_day->as_iso (), $last_day->as_iso ());

    /** @var PICTURE $first_picture */
    $first_picture = $pic_query->first_object ();

    if ($first_picture)
    {
      $Page->social_options->enabled = true;
      $first_picture->set_social_options($Page->social_options);
    }

    $Page->start_display ();
?>
<div class="main-box">
  <div class="grid-content">
  <?php
    $class_name = $Page->final_class_name ('PICTURE_GRID', 'albums/gui/picture_grid.php');
    /** @var PICTURE_GRID $grid */
    $grid = new $class_name ($Page);
    $grid->set_page_size (Default_page_size);
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