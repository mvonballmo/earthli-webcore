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

    function format_dates_for_title ($use_CSS)
    {
      global $folder;
      global $first_day;
      global $last_day;

      $formatter = $first_day->formatter ();
      $formatter->show_CSS = $use_CSS;
      $fmt_first_day = $folder->format_date ($first_day, $formatter);
      $fmt_last_day = $folder->format_date ($last_day, $formatter);

      if ($first_day->equals ($last_day))
        return 'Journal Entries from ' . $fmt_first_day;
      else
        return 'Journal Entries from ' . $fmt_first_day . ' - ' . $fmt_last_day;
    }

    $Page->title->subject = format_dates_for_title (false);
    $Page->location->append ("Calendar", "view_calendar.php?id=$folder->id");    
    $Page->location->append ($folder->format_date ($first_day));

    $Page->start_display ();
?>
<div class="box">
  <div class="box-title">
    <?php echo format_dates_for_title (true); ?>
  </div>
  <div class="box-body">
  <?php
    $jrnl_query = $folder->entry_query ();
    $jrnl_query->set_type ('journal');
    $jrnl_query->set_days ($first_day->as_iso (), $last_day->as_iso ());
    
    $iso_first_day = $first_day->as_iso ();
    $iso_last_day = $last_day->as_iso ();

    $class_name = $App->final_class_name ('JOURNAL_GRID', 'albums/gui/journal_grid.php');
    $grid = new $class_name ($App);
    $grid->set_ranges (15, 1);
    $grid->set_query ($jrnl_query);
    $grid->display ();
  ?>
  </div>
</div>
<?php
    $Page->finish_display ();
  }
  else
    $Page->raise_security_violation ('You are not allowed to view journal entries for this folder.', $folder);
?>