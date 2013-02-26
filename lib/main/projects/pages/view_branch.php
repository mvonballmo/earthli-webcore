<?php

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
  $folder = $folder_query->folder_for_branch_at_id ($id);

  if (isset ($folder))
  {
    $branch_query = $folder->branch_query ();
    $branch = $branch_query->object_at_id ($id);
  }

  if (isset ($branch) && $App->login->is_allowed (Privilege_set_branch, Privilege_view, $branch))
  {
    $App->set_referer ();

    $class_name = $App->final_class_name ('PROJECT_BRANCH_PANEL_MANAGER', 'projects/gui/project_panel.php');
    $panel_manager = new $class_name ($branch);
    $panel = $panel_manager->selected_panel ();

    $Page->title->add_object ($folder);
    $Page->title->add_object ($branch);
    $Page->title->subject = $panel->raw_title();

    $Page->newsfeed_options->title->group = $App->title;
    $Page->newsfeed_options->title->add_object ($folder);
    $Page->newsfeed_options->title->add_object ($branch);
    $Page->newsfeed_options->file_name = '{app}/view_branch_rss.php?id=' . $branch->id;

    $Page->location->add_folder_link ($folder, 'panel=' . $panel_manager->selected_panel_id);
    $Page->location->add_object_text ($branch);

    $Page->start_display ();
    $box = $Page->make_box_renderer ();
    $box->start_column_set ();
    $box->new_column_of_type ('left-column');
?>
  <div class="side-bar">
    <div class="side-bar-title">
      <?php
        if ($branch->locked ())
        {
          echo $branch->state_as_icon ('20px') . ' ';
        }
        echo $branch->title_as_html (); ?>
    </div>
    <div class="side-bar-body">
    <?php
      $renderer = $branch->handler_for (Handler_html_renderer);
      $options = $renderer->options ();
      $options->show_as_summary = true;
      $options->show_users = false;
      $renderer->display ($branch);

      $panel_manager->display ();
    ?>
    </div>
  </div>
  <br>
  <div class="side-bar">
    <div class="side-bar-title">
      Search
    </div>
    <div class="side-bar-body">
    <?php
      $class_name = $App->final_class_name ('EXECUTE_SEARCH_FORM', 'webcore/forms/execute_search_form.php');
      $search = null;
      $form = new $class_name ($App, $search);
      $form->load_with_defaults ();
      $form->set_value ('folder_ids', $folder->id);
      $form->display ();
    ?>
    </div>
  </div>
<?php
    $box->new_column_of_type ('right-column');
?>
  <div class="box">
    <?php
      $renderer = $App->make_menu_renderer ();
      $renderer->display_as_toolbar ($branch->handler_for (Handler_commands));
    ?>
    <div class="box-title">
      <?php echo $panel->raw_title (); ?>
    </div>
    <?php if ($panel->uses_time_selector) { ?>
    <div class="menu-bar-top" style="text-align: center">
      <?php $panel_manager->display_time_menu (); ?>
    </div>
    <?php } ?>
    <div class="box-body">
      <?php $panel->display (); ?>
    </div>
    <?php
      if ($panel->num_objects () && $panel->uses_time_selector)
      {
        // don't show the bottom selector if there are no objects
    ?>
      <div class="menu-bar-bottom" style="text-align: center">
        <?php $panel_manager->display_time_menu (); ?>
      </div>
    <?php
      }
    ?>
  </div>
<?php
    $box->finish_column_set ();
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to view branches in this folder.', $folder);
  }
?>