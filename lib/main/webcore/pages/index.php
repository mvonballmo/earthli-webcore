<?php

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
    /** @var $folder FOLDER */
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
  /** @var $panel_manager INDEX_PANEL_MANAGER */
  $panel_manager = new $class_name ($App, $folders);
  $panel = $panel_manager->selected_panel ();

  $Page->title->subject = $panel->num_objects () . ' ' . $panel->raw_title();

  $Page->location->add_root_link (false);

  if (isset ($folder))
  {
    $Page->location->append ($folder->title_as_html ());
  }
  else
  {
    $Page->location->append ($App->short_title);
  }

  $Page->location->append($Page->title->subject);

  $Page->add_script_file ('{scripts}webcore_forms.js');

  $Page->newsfeed_options->title->subject = $App->title;
  $Page->newsfeed_options->file_name = '{app}/index_rss.php';

  $Page->start_display ();
?>
  <div class="top-box">
    <div class="columns text-flow">
    <?php
    if (isset ($folder))
    {
      /** @var $renderer OBJECT_RENDERER */
      $renderer = $folder->handler_for (Handler_html_renderer);
      $options = $renderer->options ();
      $options->show_as_summary = true;
      $options->show_users = false;

      $text = $renderer->display_to_string ($folder);

      if ($text)
      {
        ?>
        <div class="left-sidebar">
          <?php echo $text; ?>
        </div>
        <?php
      }
    }

    ?>
    <div>
      <h4>Contents</h4>
      <div class="panels">
        <?php $panel_manager->display (); ?>
      </div>
    </div>
    <?php
    if (! empty ($folders))
    {
      $folder_type_info = $App->type_info_for ('FOLDER');
      ?>
      <div class="tree-content">
        <h4><?php echo $folder_type_info->plural_title; ?></h4>
        <?php
        $tree = $App->make_tree_renderer ();
        include_once ('webcore/gui/folder_tree_node_info.php');
        $tree->node_info = new FOLDER_TREE_NODE_INFO ($App);
        $tree->node_info->page_args = read_vars (array ('panel', 'time_frame'));
        $tree->display ($folders);
        ?>
      </div>
      <?php
    }
    ?>
      <div>
        <h4>Search</h4>
        <div class="form-content">
          <?php
          $class_name = $App->final_class_name ('EXECUTE_SEARCH_FORM', 'webcore/forms/execute_search_form.php');
          $search = null;
          $selected_panel = $panel_manager->selected_panel ();
          /** @var $form EXECUTE_SEARCH_FORM */
          $form = new $class_name ($App, $search);
          $form->load_with_defaults ();
          $form->set_value ('state', $selected_panel->state);
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
?>