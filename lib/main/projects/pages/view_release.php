<?php

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
  /** @var $folder_query USER_PROJECT_QUERY */
  $folder_query = $App->login->folder_query ();
  $folder = $folder_query->folder_for_release_at_id ($id);

  if (isset ($folder))
  {
    $rel_query = $folder->release_query ();
    /** @var $release RELEASE */
    $release = $rel_query->object_at_id ($id);
  }

  if (isset ($release) && $App->login->is_allowed (Privilege_set_release, Privilege_view, $release))
  {
    $App->set_referer ();

    $branch = $release->branch ();

    $class_name = $App->final_class_name ('PROJECT_RELEASE_PANEL_MANAGER', 'projects/gui/project_panel.php');
    /** @var $panel_manager PROJECT_RELEASE_PANEL_MANAGER */
    $panel_manager = new $class_name ($release);
    $panel = $panel_manager->selected_panel ();

    $Page->title->add_object ($folder);
    $Page->title->add_object ($branch);
    $Page->title->add_object ($release);
    $Page->title->subject = $panel->num_objects() . ' ' . $panel->raw_title ();

    $Page->newsfeed_options->title->group = $App->title;
    $Page->newsfeed_options->title->add_object ($folder);
    $Page->newsfeed_options->title->add_object ($branch);
    $Page->newsfeed_options->title->add_object ($release);
    $Page->newsfeed_options->file_name = '{app}/view_release_rss.php?id=' . $release->id;

    $Page->location->add_folder_link ($folder, 'panel=' . $panel_manager->selected_panel_id);
    $Page->location->add_object_link ($branch, 'panel=' . $panel_manager->selected_panel_id, $App->resolve_file('{app_icons}buttons/new_branch'));

    $Page->location->add_object_text ($release, $App->resolve_file('{app_icons}buttons/new_release'), '');
    $Page->location->append($Page->title->subject);

    $Page->start_display ();
?>
<div class="top-box">
<?php
    $box = $Page->make_box_renderer ();
    $box->start_column_set ();

    /** @var $renderer OBJECT_RENDERER */
    $renderer = $release->handler_for (Handler_html_renderer);
    $options = $renderer->options ();
    $options->show_as_summary = true;
    $options->show_users = false;

    $text = $renderer->display_to_string ($release);

    if ($text)
    {
      $box->new_column_of_type ('description-box');

      echo $text;
    }

    $box->new_column_of_type ('contents-box');

    echo '<h4>Contents</h4>';
    echo '<div class="panels">';

    $panel_manager->display ();

    echo '</div>';

    $box->new_column_of_type ('tools-box');

    echo '<h4>Search</h4>';

    $class_name = $App->final_class_name ('EXECUTE_SEARCH_FORM', 'webcore/forms/execute_search_form.php');
    $search = null;
    /** @var $form EXECUTE_SEARCH_FORM */
    $form = new $class_name ($App, $search);
    $form->load_with_defaults ();
    $form->set_value ('folder_ids', $folder->id);
    $form->display ();

    $box->finish_column_set ();
?>
</div>
  <div class="main-box">
    <div class="menu-bar-top">
      <?php
      if ($panel->uses_time_selector)
      {
        $panel_manager->display_time_menu ();
      }
      $pager = $panel->get_pager();

      if ($pager)
      {
        $pager->pages_to_show = 0;
        $pager->display();
      }

      $grid = $panel->get_grid();
      if ($grid)
      {
        $grid->show_pager = false;
      }

      /** @var MENU_RENDERER $renderer */
      $renderer = $release->handler_for (Handler_menu);
      $renderer->set_size(Menu_size_standard);
      $renderer->num_important_commands = 2;
      /** @var COMMANDS $commands */
      $commands = $release->handler_for(Handler_commands);
      $renderer->display($commands);
      ?>
    </div>
    <?php
    $panel->display ();

    if ($panel->num_objects ())
    {
      // don't show the bottom selector if there are no objects
      ?>
      <div class="menu-bar-bottom">
        <?php
        if ($panel->uses_time_selector)
        {
          $panel_manager->display_time_menu ();
        }
        if ($pager)
        {
          $pager->pages_to_show = 5;
          $pager->display(true);
        }
        ?>
      </div>
    <?php
    }
    ?>
  </div>
<?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to view this release.', $folder);
  }
?>