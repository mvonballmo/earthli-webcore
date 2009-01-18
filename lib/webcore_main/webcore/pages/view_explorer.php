<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

This file is part of earthli Webcore.

earthli Webcore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Webcore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Webcore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Webcore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

  $folder_query = $App->login->folder_query ();
  $folder =& $folder_query->object_at_id (read_var ('id'));

  if (isset ($folder) && $App->login->is_allowed (Privilege_set_folder, Privilege_view, $folder))
  {
    $App->set_referer ();

    $Page->title->subject = 'Explore ' . $folder->title_as_plain_text ();
    $parent =& $folder->parent_folder ();
    $Page->location->add_folder_link ($parent, '', 'view_explorer.php');
    $Page->location->add_object_link ($folder);
    $Page->location->append ('Explorer');

    $Page->add_script_file ('{scripts}webcore_forms.js');

    $Page->start_display ();

    $form_name = 'explorer_form';
  ?>
  <div class="box">
    <div class="box-title">
      <?php echo $App->title_bar_icon ('{icons}buttons/explorer'); ?> Exploring <?php echo $folder->title_as_html (); ?>
    </div>
    <?php
      include_once ('webcore/util/options.php');
      $option = new STORED_OPTION ($App, 'show_explorer_tree');
      $show_tree = $option->value ();
      $opt_link = $option->setter_url_as_html (! $show_tree);
      
      $class_name = $App->final_class_name ('EXPLORER_COMMANDS', 'webcore/cmd/explorer_commands.php');
      $commands = new $class_name ($folder, $form_name);
      $renderer = $folder->handler_for (Handler_menu);

      if (! $show_tree)
      {
    ?>
    <div class="menu-bar-top">
      <div style="float: left">
        <a href="<?php echo $opt_link; ?>"><?php echo $App->resolve_icon_as_html ('{icons}buttons/show_list', 'Show list', '16px'); ?></a>
        <a href="<?php echo $opt_link; ?>">Show folder tree</a>
      </div>
      <?php
        $renderer->display ($commands);
      ?>
      <div style="clear: both"></div>
    </div>
    <?php
      }
      else
        $renderer->display_as_toolbar ($commands);
    ?>
    <div class="box-body">
    <?php
      if ($show_tree)
      {
        $box = $Page->make_box_renderer ();
        $box->start_column_set ();
        $box->new_column ('padding-right: 1em');
    ?>
      <div class="chart">
        <div class="chart-title">
          <div style="float: right">
            <a href="<?php echo $opt_link; ?>"><?php echo $App->resolve_icon_as_html ('{icons}buttons/close', 'Hide folder tree', '16px'); ?></a>
          </div>
          Folders
        </div>
        <div class="chart-body">
        <?php 
          /* Make a copy (not a reference). */
          $tree = $App->make_tree_renderer ();
  
          include_once ('webcore/gui/folder_tree_node_info.php');
          $tree_node_info = new EXPLORER_FOLDER_TREE_NODE_INFO ($App);
          $tree_node_info->page_link = 'view_explorer.php';
          $tree_node_info->set_visible_node ($folder);
          $tree_node_info->set_selected_node ($folder);
          $tree->node_info = $tree_node_info;
  
          $folders = $folder_query->tree ();
          $tree->display ($folders);
        ?>
        </div>
      </div>
    <?php
        $box->new_column ('width: 75%');
      }
    ?>
      <form id="<?php echo $form_name; ?>" method="post" action="">
      <input type="hidden" name="id" value="<?php echo $folder->id; ?>">
      <input type="hidden" name="debug" value="<?php echo read_var ('debug'); ?>">
    <?php
      $folder_query->restrict ("parent_id = $folder->id");
      $num_folders = $folder_query->size ();
      if ($num_folders)
      {
        $folder_info = $App->type_info_for ('FOLDER');
    ?>
    <h2 id="folder_list"><?php echo $App->title_bar_icon ('{icons}buttons/new_folder') . ' ' . $num_folders . ' ' . $folder_info->plural_title; ?></h2>
    <?php
        $class_name = $App->final_class_name ('FOLDER_LIST', 'webcore/gui/folder_list.php');
        $list = new $class_name ($App);
        $list->page_name = $Env->url (Url_part_file_name);
        $list->form_name = $form_name;
        $list->paginator->page_number_var_name = 'folder_page_number';
        $list->paginator->page_anchor = "folder_list";
        $list->set_ranges (20, 1);
        $list->set_query ($folder_query);
        $list->display ();
      }

      $entry_types = $App->entry_type_infos ();
      foreach ($entry_types as $type_info)
      {
        $entry_query = $folder->entry_query ();
        $entry_query->set_type ($type_info->id);
        $num_objs = $entry_query->size ();
        if ($num_objs)
        {
          $class_name = $App->final_class_name ('ENTRY_LIST', 'webcore/gui/entry_list.php', $type_info->id);
          $list = new $class_name ($App);
          $list->control_name = "{$type_info->id}_ids";
          $list->paginator->page_number_var_name = "{$type_info->id}_page_number";
          $list->paginator->page_anchor = "{$type_info->id}_list";
          $list->form_name = $form_name;
          $list->set_ranges (20, 1);
          $list->set_query ($entry_query);
          
          /* make a drop-down selector to choose number of entries per page and
           * which filter to apply to the entries (for state).
           */
          
          if ($list->paginator->num_pages () > 1)
            $items_text = ' Showing ' . $list->paginator->first_item_index () . ' - ' . $list->paginator->last_item_index () . ' of ' . $list->paginator->num_items () . ' ' . $type_info->plural_title;                     
          else
            $items_text = ' ' . $list->paginator->num_items () . ' ' . $type_info->plural_title;
      ?>
      <br>
      <h2 id="<?php echo $type_info->id . '_list'; ?>"><?php echo $App->title_bar_icon ($type_info->icon) . $items_text; ?></h2>
      <?php
          $list->display ();
        }
      }
    ?>
      </form>
<?php
      if (isset ($box))
        $box->finish_column_set ();
?>
    </div>
  </div>
  <?php
    $Page->finish_display ();
  }
  else
    $Page->raise_security_violation ('You are not allowed to explore this folder.', $folder);
?>