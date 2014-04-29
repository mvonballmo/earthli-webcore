<?php

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
  /** @var $folder FOLDER */
  $folder = $folder_query->object_at_id (read_var ('id'));

  if (isset ($folder) && $App->login->is_allowed (Privilege_set_folder, Privilege_view, $folder))
  {
    $App->set_referer ();

    $Page->title->subject = 'Explore ' . $folder->title_as_plain_text ();
    $parent = $folder->parent_folder ();
    $Page->location->add_folder_link ($folder, '', '', false);
    if (isset ($parent))
    {
      $Page->location->add_folder_link ($parent, '', 'view_explorer.php');
    }
    $Page->location->append ('Explorer', '', '{icons}buttons/explorer');

    $Page->add_script_file ('{scripts}webcore_forms.js');

    $Page->start_display ();

    $form_name = 'explorer_form';
  ?>
  <div class="top-box">
    <div class="button-content">
  <?php
    include_once ('webcore/util/options.php');
    $option = new STORED_OPTION ($App, 'show_explorer_tree');
    $show_tree = $option->value ();
    $opt_link = $option->setter_url_as_html (! $show_tree);

    if (! $show_tree)
    {
      $icon = '{icons}buttons/show_list';
      $caption = 'Show folders';
    }
    else
    {
      $icon = '{icons}buttons/close';
      $caption = 'Hide folders';
    }

    $icon = $App->get_icon_url ($icon, Sixteen_px);
    ?><a href="<?php echo $opt_link; ?>" class="button"><span class="icon sixteen" style="background-image: url(<?php echo $icon; ?>)"><?php echo $caption; ?></span></a><?php

    $class_name = $App->final_class_name ('EXPLORER_COMMANDS', 'webcore/cmd/explorer_commands.php');
    /** @var $commands EXPLORER_COMMANDS */
    $commands = new $class_name ($folder, $form_name);
    /** @var $renderer MENU_RENDERER */
    $renderer = $folder->handler_for (Handler_menu);
    $renderer->set_size (Menu_size_standard);
    $renderer->display ($commands);
    ?>
    </div>
  </div>
  <div class="main-box">
    <?php
      if ($show_tree)
      {
        $box = $Page->make_box_renderer ();
        $box->start_column_set ();
        $box->new_column_of_type ('left-column');
    ?>
      <div class="left-sidebar" style="white-space: nowrap">
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
    <?php
        $box->new_column_of_type ('right-column');
      }
    ?>
      <form id="<?php echo $form_name; ?>" method="post" action="">
        <div>
          <input type="hidden" name="id" value="<?php echo $folder->id; ?>">
          <input type="hidden" name="debug" value="<?php echo read_var ('debug'); ?>">
    <?php
      $folder_query->restrict ("parent_id = $folder->id");
      $num_folders = $folder_query->size ();
      if ($num_folders)
      {
        $folder_info = $App->type_info_for ('FOLDER');
    ?>
    <h2 id="folder_list"><?php echo $num_folders . ' ' . $folder_info->plural_title; ?></h2>
    <?php
        $class_name = $App->final_class_name ('FOLDER_LIST', 'webcore/gui/folder_list.php');
        /** @var $list FOLDER_LIST */
        $list = new $class_name ($App);
        $list->page_name = $Env->url (Url_part_file_name);
        $list->form_name = $form_name;
        $list->pager->page_number_var_name = 'folder_page_number';
        $list->pager->page_anchor = "folder_list";
        $list->set_ranges (20, 1);
        $list->set_query ($folder_query);
        $list->display ();
      }

      $entry_types = $App->entry_type_infos ();
      foreach ($entry_types as $type_info)
      {
        /** @var $entry_query FOLDER_DRAFTABLE_ENTRY_QUERY */
        $entry_query = $folder->entry_query ();
        $entry_query->set_type ($type_info->id);
        $num_objs = $entry_query->size ();
        if ($num_objs)
        {
          $class_name = $App->final_class_name ('ENTRY_LIST', 'webcore/gui/entry_list.php', $type_info->id);
          /** @var $list ENTRY_LIST */
          $list = new $class_name ($App);
          $list->control_name = "{$type_info->id}_ids";
          $list->pager->page_number_var_name = "{$type_info->id}_page_number";
          $list->pager->page_anchor = "{$type_info->id}_list";
          $list->form_name = $form_name;
          $list->set_ranges (20, 1);
          $list->set_query ($entry_query);
          
          /* make a drop-down selector to choose number of entries per page and
           * which filter to apply to the entries (for state).
           */
          
          if ($list->pager->num_pages () > 1)
          {
            $items_text = ' Showing ' . $list->pager->first_item_index () . ' - ' . $list->pager->last_item_index () . ' of ' . $list->pager->num_items () . ' ' . $type_info->plural_title;
          }
          else
          {
            $items_text = ' ' . $list->pager->num_items () . ' ' . $type_info->plural_title;
          }
      ?>
      <h2 id="<?php echo $type_info->id . '_list'; ?>"><?php echo $items_text; ?></h2>
      <?php
          $list->display ();
        }
      }
    ?>
        </div>
      </form>
<?php
      if (isset ($box))
      {
        $box->finish_column_set ();
      }
?>
    </div>
  </div>
  <?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to explore this folder.', $folder);
  }
?>