<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

  $id = read_var ('id');
  $folder_query = $App->login->folder_query ();
  $folder = $folder_query->folder_for_release_at_id ($id);
  
  if (isset ($folder))
  {
    $rel_query = $folder->release_query ();
    $release = $rel_query->object_at_id ($id);
  }

  if (isset ($release) && $App->login->is_allowed (Privilege_set_release, Privilege_view, $release))
  {
    $App->set_referer ();

    $branch = $release->branch ();

    $Page->title->add_object ($folder);
    $Page->title->add_object ($branch);
    $Page->title->add_object ($release);
    $Page->title->subject = 'Change log';

    $Page->location->add_folder_link ($folder, 'panel=releases');
    $Page->location->add_object_link ($branch, 'panel=releases');
    $Page->location->add_object_link ($release);
    $Page->location->append ('Change log');

    $printable = read_var ('printable');

    if ($printable)
    {
      $Page->set_printable ();
    }

    $Page->start_display ();

    $show_description = read_var ('show_description', true);
    $show_date = read_var ('show_date', true);
    $show_user = read_var ('show_user', true);

    if ($printable)
    {
?>
<h1 style="text-align: center">
  <?php
    echo $folder->title_as_link ();
    echo $App->display_options->object_separator;
    echo $branch->title_as_link ();
    echo $App->display_options->object_separator;
    echo $release->title_as_link ();
  ?>
</h1>
<?php
    }
    else
    {
?>
<div class="box">
  <div class="box-title">
  <?php
    echo ($App->title_bar_icon ('{app_icons}buttons/change_log') . ' ' .
          $folder->title_as_html () .
          $App->display_options->object_separator .
          $branch->title_as_html () .
          $App->display_options->object_separator .
          $release->title_as_html ());
  ?> change log
  </div>
  <?php
    $class_name = $App->final_class_name ('CHANGE_LOG_COMMANDS', 'projects/cmd/change_log_commands.php');
    $commands = new $class_name ($App);
    $renderer = $App->make_menu_renderer ();
    $renderer->num_important_commands = 1;
    $renderer->display_as_toolbar ($commands);
  ?>
  <div class="box-body">
<?php
    }

    echo $release->description_as_html ();

    $entry_query = $release->entry_query ();
    $entry_query->add_select ('IFNULL(ctob.branch_time_applied, jtob.branch_time_closed) as time_to_use');
    $entry_query->add_table ($App->table_names->components . ' comp', 'comp.id = entry.component_id', 'LEFT');
    $entry_query->restrict ('(ctob.branch_applier_id <> 0) OR (jtob.branch_closer_id <> 0)');
    $entry_query->set_order ('comp.title ASC, entry.kind, time_to_use DESC');
    $entries = $entry_query->objects ();

    $not_used = array ();

    $class_name = $App->final_class_name ('CHANGE_LOG', 'projects/gui/change_log.php');
    $change_log = new $class_name ($App);
    $change_log->show_description = $show_description;
    $change_log->show_date = $show_date;
    $change_log->show_user = $show_user;
    $change_log->display_release ($entries, $not_used, $release);

    if (! $printable)
    {
?>
  </div>
</div>
<?php
    }

    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to view this release.');
  }
?>