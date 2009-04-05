<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli WebCore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

  /*
     $show_only_root_folders - Set this to True to prevent the index from loading
                               all the folders in the entire application. With a lot
                               of folders, this is a big performance hit.
  */

  $App->set_referer ();

  $folder_query = $App->login->folder_query ();

  if (! empty ($show_only_root_folders))
  {
    $folder = $folder_query->object_at_id ($App->root_folder_id);
    $folder_query->restrict ('parent_id = ' . $App->root_folder_id);

    if (! empty($sort_by_modified))
    {
      $sub_query = '(SELECT entry.time_modified FROM ' . $App->table_names->entries . ' entry WHERE entry.owner_id = fldr.owner_id ORDER BY entry.time_modified DESC LIMIT 1) as entry_time_modified';

      $folder_query->add_select ($sub_query);
      $folder_query->add_order ('entry_time_modified DESC', true);
    }

    $folders = $folder_query->tree ();
  }
  else
  {
    $folders = $folder_query->root_tree ($App->root_folder_id);
    $folder = $folder_query->object_at_id ($App->root_folder_id);
  }
  
  $folder_query->clear_restrictions();

  if (! sizeof ($folders) && $App->login->is_anonymous ())
  {
    $Env->redirect_local ('log_in.php');
  }

  $class_name = $App->final_class_name ('INDEX_PANEL_MANAGER', 'webcore/gui/panel.php');
  $panel_manager = new $class_name ($App, $folders);
  $panel = $panel_manager->selected_panel ();

  $Page->location->add_root_link (false);
  $Page->location->append ($App->short_title);

  $Page->title->subject = $panel->raw_title ();

  $Page->newsfeed_options->title->subject = $App->title;
  $Page->newsfeed_options->file_name = '{app}/index_rss.php';

  $Page->start_display ();

  $box = $Page->make_box_renderer ();
  $box->start_column_set ();
  $box->new_column ('padding-right: 1em');
?>
  <div class="side-bar">
    <div class="side-bar-title">
      <?php
        if (isset ($folder))
        {
          echo $folder->title_as_html ();
        }
        else
        {
          echo $App->title;
        }
      ?>
    </div>
    <div class="side-bar-body">
    <?php
      if (isset ($folder))
      {
        $renderer = $folder->handler_for (Handler_html_renderer);
        $options = $renderer->options ();
        $options->show_as_summary = true;
        $options->show_users = false;
        $renderer->display ($folder);
      }

      $panel_manager->display ();

      if (! empty ($folders))
      {
        $folder_type_info = $App->type_info_for ('FOLDER');
        echo '<h4>' . $folder_type_info->plural_title . '</h4>';
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
      $form->display ();
    ?>
    </div>
  </div>
  <br>
  <div class="side-bar">
    <div class="side-bar-title">
      Subscriptions
    </div>
    <div class="side-bar-body">
    <?php
      include_once ('webcore/forms/check_subscriptions_form.php');
      $form = new CHECK_SUBSCRIPTIONS_FORM ($App);
      $form->CSS_class = 'search';
      $form->allow_focus = false;
      $form->display ();
    ?>
    </div>
  </div>
<?php
  $box->new_column ('width: 75%');
?>
  <div class="box">
    <?php
      if (isset ($folder))
      {
        $renderer = $folder->handler_for (Handler_menu);
        $renderer->display_as_toolbar ($folder->handler_for (Handler_commands));
      }
    ?>
    <div class="box-title">
      <?php echo $panel->raw_title (); ?>
    </div>
    <?php
      $panel_commands = $panel->commands ();
      if (isset ($panel_commands))
      {
        $renderer = $App->make_menu_renderer ();
        $renderer->set_size (Menu_size_full);
        $renderer->content_mode = Menu_show_as_buttons | Menu_show_icon;
        $renderer->display_as_toolbar ($panel_commands);
      }

      if ($panel->uses_time_selector)
      {
    ?>
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
?>