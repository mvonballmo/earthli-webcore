<?php

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
  
  if (isset ($folder) && $App->login->is_allowed (Privilege_set_folder, Privilege_view, $folder))
  {
    $App->set_referer ();

    /* get the folder from the folder query because the tree has
     * been built and the sub-folders have been cached.
     */
     
    $folders = $folder_query->tree ($folder->id, $folder->root_id);
    $folder = $folder_query->object_at_id ($folder->id);

    $class_name = $App->final_class_name ('FOLDER_PANEL_MANAGER', 'webcore/gui/panel.php');
    $panel_manager = new $class_name ($folder);
    $panel = $panel_manager->selected_panel ();

    $Page->location->add_folder_text ($folder, 'panel=' . $panel_manager->selected_panel_id);
    $Page->title->add_object ($folder);
    $Page->title->subject = $panel->raw_title();

    $Page->add_script_file ('{scripts}webcore_forms.js');
    
    $Page->newsfeed_options->title->group = $App->title;
    $Page->newsfeed_options->title->add_object ($folder);
    $Page->newsfeed_options->file_name = '{app}/view_folder_rss.php?id=' . $folder->id;

    $Page->start_display ();

    $box = $Page->make_box_renderer ();
    $box->start_column_set ();
    $box->new_column ('padding-right: 1em');  
?>
  <div class="side-bar">
    <div class="side-bar-title">
      <?php 
        $newsfeed_commands = $Page->newsfeed_options->make_commands($App);
        $renderer = $App->make_newsfeed_menu_renderer ();
        $renderer->display ($newsfeed_commands);
        
        echo $folder->icon_as_html ('20px') . ' ' . $folder->title_as_html (); 
      ?>
    </div>
    <div class="side-bar-body">
      <?php
        $renderer = $folder->handler_for (Handler_html_renderer);
        $options = $renderer->options ();
        $options->show_as_summary = true;
        $options->show_users = false;
        $renderer->display ($folder);

        $panel_manager->display ();

        if (! empty ($folders))
        {
          $folder_type_info = $App->type_info_for ('FOLDER');
          echo '<h4>Sub-' . $folder_type_info->plural_title . '</h4>';
          /* Make a copy (not a reference). */
          $tree = $App->make_tree_renderer ();
          include_once ('webcore/gui/folder_tree_node_info.php');
          $tree->node_info = new FOLDER_TREE_NODE_INFO ($App);
          $tree->node_info->page_args = read_vars (array ('panel', 'time_frame'));
          $tree->display ($folders);
        }
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
      $selected_panel = $panel_manager->selected_panel ();
      $form = new $class_name ($App, $search);
      $form->load_with_defaults ();
      $form->set_value ('state', $selected_panel->state);
      $form->set_value ('folder_ids', $folder->id);
      $form->set_value ('folder_search_type', Search_user_constant);
      $form->display ();
    ?>
    </div>
  </div>
<?php
    $box->new_column ('width: 75%');
?>
  <div class="box">
    <?php
      $renderer = $folder->handler_for (Handler_menu);
      $renderer->display_as_toolbar ($folder->handler_for (Handler_commands));
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
    $Page->raise_security_violation ('You are not allowed to view this folder.', $folder);
  }
?>