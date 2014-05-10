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

  $folder_query = $App->login->folder_query ();
  /** @var $folder FOLDER */
  $folder = $folder_query->object_at_id (read_var ('id'));
  
  if (isset ($folder) && $App->login->is_allowed (Privilege_set_folder, Privilege_view, $folder))
  {
    $App->set_referer ();

    /* get the folder from the folder query because the tree has
     * been built and the sub-folders have been cached.
     */
     
    $folders = $folder_query->tree ($folder->id, $folder->root_id);
    $folder = $folder_query->object_at_id ($folder->id);

    $class_name = $App->final_class_name ('FOLDER_PANEL_MANAGER', 'webcore/gui/panel.php');
    /** @var $panel_manager FOLDER_PANEL_MANAGER */
    $panel_manager = new $class_name ($folder);
    $panel = $panel_manager->selected_panel ();

    $Page->title->add_object ($folder);
    $Page->title->subject = $panel->num_objects () . ' ' . $panel->raw_title();

    $Page->location->add_folder_text ($folder, 'panel=' . $panel_manager->selected_panel_id);
    $Page->location->append($Page->title->subject);

    $Page->add_script_file ('{scripts}webcore_forms.js');
    
    $Page->newsfeed_options->title->group = $App->title;
    $Page->newsfeed_options->title->add_object ($folder);
    $Page->newsfeed_options->file_name = '{app}/view_folder_rss.php?id=' . $folder->id;

    $Page->start_display ();
?>
<div class="top-box">
  <?php
  $box = $Page->make_box_renderer ();
  $box->start_column_set ();

  /** @var $renderer OBJECT_RENDERER */
  $renderer = $folder->handler_for (Handler_html_renderer);
  $options = $renderer->options ();
  $options->show_as_summary = true;
  $options->show_users = false;

  $text = $renderer->display_to_string ($folder);

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

  if (! empty ($folders))
  {
    $box->new_column_of_type('sub-folders-box');

    $folder_type_info = $App->type_info_for ('FOLDER');

    echo '<h4>Sub-' . $folder_type_info->plural_title . '</h4>';

    $tree = $App->make_tree_renderer ();
    include_once ('webcore/gui/folder_tree_node_info.php');
    $tree->node_info = new FOLDER_TREE_NODE_INFO ($App);
    $tree->node_info->page_args = read_vars (array ('panel', 'time_frame'));
    $tree->display ($folders);
  }

  $box->new_column_of_type ('tools-box');

  echo '<h4>Search</h4>';

  $class_name = $App->final_class_name ('EXECUTE_SEARCH_FORM', 'webcore/forms/execute_search_form.php');
  $search = null;
  $selected_panel = $panel_manager->selected_panel ();
  /** @var $form EXECUTE_SEARCH_FORM */
  $form = new $class_name ($App, $search);
  $form->load_with_defaults ();
  $form->set_value ('state', $selected_panel->state);
  $form->set_value ('folder_ids', $folder->id);
  $form->set_value ('folder_search_type', Search_user_constant);
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
    $renderer = $folder->handler_for (Handler_menu);
    $renderer->set_size(Menu_size_standard);
    $renderer->num_important_commands = 2;
    /** @var COMMANDS $commands */
    $commands = $folder->handler_for(Handler_commands);
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
    $Page->raise_security_violation ('You are not allowed to view this folder.', $folder);
  }
?>