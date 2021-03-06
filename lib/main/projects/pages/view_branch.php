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
    /** @var $panel_manager PROJECT_BRANCH_PANEL_MANAGER */
    $panel_manager = new $class_name ($branch);
    $panel = $panel_manager->selected_panel ();

    $Page->title->add_object ($folder);
    $Page->title->add_object ($branch);
    $Page->title->subject = $panel->num_objects() . ' ' . $panel->raw_title();

    $Page->newsfeed_options->title->group = $App->title;
    $Page->newsfeed_options->title->add_object ($folder);
    $Page->newsfeed_options->title->add_object ($branch);
    $Page->newsfeed_options->file_name = '{app}/view_branch_rss.php?id=' . $branch->id;

    $Page->location->add_folder_link ($folder, 'panel=' . $panel_manager->selected_panel_id);

    if ($branch->locked ())
    {

      $icon = $App->resolve_file($branch->state_icon_url(), '');
    }
    else
    {
      $icon = '';
    }

    $Page->location->add_object_text ($branch, $App->resolve_file('{app_icons}buttons/new_branch'));
    $Page->location->append ($Page->title->subject);

    $Page->start_display ();
?>
    <div class="top-box">
      <div class="columns text-flow">
        <?php
        /** @var $renderer OBJECT_RENDERER */
        $renderer = $branch->handler_for (Handler_html_renderer);
        $options = $renderer->options ();
        $options->show_as_summary = true;
        $options->show_users = false;
        $text = $renderer->display_to_string ($branch);

        if ($text)
        {
          ?>
          <div class="left-sidebar">
            <?php echo $text; ?>
          </div>
          <?php
        }

        ?>
        <div>
          <h4>Contents</h4>
          <div class="panels">
            <?php $panel_manager->display (); ?>
          </div>
        </div>
        <div>
          <h4>Search</h4>
          <div class="form-content">
            <?php
            $class_name = $App->final_class_name ('EXECUTE_SEARCH_FORM', 'webcore/forms/execute_search_form.php');
            $search = null;
            /** @var $form EXECUTE_SEARCH_FORM */
            $form = new $class_name ($App, $search);
            $form->load_with_defaults ();
            $form->set_value ('folder_ids', $folder->id);
            $form->display ();
            ?>
          </div>
        </div>
      </div>
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
        $renderer = $branch->handler_for (Handler_menu);
        $renderer->set_size(Menu_size_standard);
        $renderer->num_important_commands = 2;
        /** @var COMMANDS $commands */
        $commands = $branch->handler_for(Handler_commands);
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
    $Page->raise_security_violation ('You are not allowed to view branches in this folder.', $folder);
  }
?>