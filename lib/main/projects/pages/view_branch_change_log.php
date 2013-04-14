<?php

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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

  $Page->title->subject = 'Change log';

  $id = read_var ('id');
  /** @var $folder_query USER_PROJECT_QUERY */
  $folder_query = $App->login->folder_query ();
  $folder = $folder_query->folder_for_branch_at_id ($id);

  if (isset ($folder))
  {
    $branch_query = $folder->branch_query ();
    /** @var $branch BRANCH */
    $branch = $branch_query->object_at_id ($id);
  }

  if (isset ($branch) && $App->login->is_allowed (Privilege_set_folder, Privilege_view, $branch))
  {
    $Page->title->add_object ($folder);
    $Page->title->add_object ($branch);

    $Page->location->add_folder_link ($folder, 'panel=releases');
    $Page->location->add_object_link ($branch, 'panel=releases', $App->resolve_file('{app_icons}buttons/new_branch'));
    $Page->location->append ($Page->title->subject, '', $App->resolve_file ('{app_icons}buttons/change_log', ''));

    $printable = read_var ('printable');

    if ($printable)
    {
      $Page->set_printable ();
    }

    $Page->start_display ();

    $show_all = read_var ('show_all', true);
    $show_description = read_var ('show_description', true);
    $show_date = read_var ('show_date', true);
    $show_user = read_var ('show_user', true);

    if ($printable)
    {
?>
<h1>
  <?php
    echo $folder->title_as_link ();
    echo $App->display_options->object_separator;
    echo $branch->title_as_link ();
  ?>
</h1>
<?php
    }
    else
    {
?>
<div class="top-box">
  <div class="button-content">
  <?php
  $class_name = $App->final_class_name ('BRANCH_CHANGE_LOG_COMMANDS', 'projects/cmd/change_log_commands.php');
  /** @var $commands COMMANDS */
  $commands = new $class_name ($App);
  $renderer = $App->make_menu_renderer ();
  $renderer->set_size(Menu_size_full);
  $renderer->display ($commands);
  ?>
  </div>
</div>
<div class="box">
  <div class="box-body">
<?php
    }

    if (! $show_all)
    {
      $entry_query = $branch->entry_query ();
      $entry_query->add_select ('IFNULL(ctob.branch_time_applied, jtob.branch_time_closed) as time_to_use');
      $entry_query->add_table ($App->table_names->components . ' comp', 'comp.id = entry.component_id', 'LEFT');
      $entry_query->restrict ('((ctob.branch_applier_id <> 0) OR (jtob.branch_closer_id <> 0)) AND NOT ((ctob.branch_applier_id <> 0) AND (jtob.branch_closer_id <> 0))');
      $entry_query->restrict ('etob.branch_release_id = 0');
      $entry_query->set_order ('comp.title ASC, entry.kind, time_to_use DESC');
      $entries = $entry_query->objects ();
    }
    else
    {
      $entries = array ();
    }

    if ($show_all)
    {
      $rel_query = $branch->release_query ();
      $rel_query->restrict_by_op ('rel.state', array (Locked, Shipped), Operator_in);
      $rels = $rel_query->objects ();
    }
    else
    {
      $rels = array ();
    }

    $not_used = array ();

    $class_name = $App->final_class_name ('CHANGE_LOG', 'projects/gui/change_log.php');
    /** @var $change_log CHANGE_LOG */
    $change_log = new $class_name ($App);
    $change_log->show_description = $show_description;
    $change_log->show_date = $show_date;
    $change_log->show_user = $show_user;
    $change_log->display ($entries, $not_used, $rels);

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
    $Page->raise_security_violation ('You are not allowed to view this branch.');
  }
?>
