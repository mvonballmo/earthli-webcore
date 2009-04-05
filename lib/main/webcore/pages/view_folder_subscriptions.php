<?php

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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

  $folder_query = $App->login->folder_query ();
  $folder = $folder_query->object_at_id (read_var ('id'));

  if (isset ($folder) && $App->login->is_allowed (Privilege_set_folder, Privilege_modify, $folder))
  {
    $class_name = $App->final_class_name ('FOLDER_SUBSCRIBER_FORM', 'webcore/forms/folder_subscriber_form.php');
    $form = new $class_name ($folder);

    $form->process_existing ($folder);
    if ($form->committed ())
    {
      $App->return_to_referer ($folder->home_page ());
    }

    $Page->title->add_object ($folder);
    $Page->title->subject = 'Subscribers';

    $Page->location->add_folder_link ($folder);
    $Page->location->append ($Page->title->subject);

    $Page->start_display ();

    $folders = $folder_query->tree ();
    $subscriber_query = $folder->subscriber_query ();
?>
<div class="box">
  <div class="box-title">
    <?php echo $App->title_bar_icon ('{icons}buttons/subscriptions'); ?> <?php echo $subscriber_query->size (); ?> subscribers for <?php echo $folder->title_as_html (); ?>
  </div>
  <?php
    include_once ('webcore/util/options.php');
    $option = new STORED_OPTION ($App, 'show_security_tree');
    $show_tree = $option->value ();
    $opt_link = $option->setter_url_as_html (! $show_tree);
  ?>
  <div class="menu-bar-top">
  <?php
    if (! $show_tree)
    {
  ?>
    <div style="float: left">
      <a href="<?php echo $opt_link; ?>"><?php echo $App->resolve_icon_as_html ('{icons}buttons/show_list', 'Show list', '16px'); ?></a>
      <a href="<?php echo $opt_link; ?>">Show folder tree</a>
    </div>
  <?php
    }

    $menu = $App->make_menu ();
    $menu->renderer->content_mode = Menu_show_all_as_buttons;
    $menu->renderer->alignment = Menu_align_right;
    $menu->append ('Add subscribers', 'create_folder_subscriptions.php?id=' . $folder->id, '{icons}buttons/add_subscribers');
    $menu->display ();
  ?>
    <div style="clear: both"></div>
  </div>
  <div class="box-body">
      <?php
        if ($show_tree)
        {
          $folders = $folder_query->tree ();
          $folder_type_info = $folder->type_info ();

          $box = $Page->make_box_renderer ();
          $box->start_column_set ();
          $box->new_column ('padding-right: 1em');  
      ?>
    <div class="chart">
      <div class="chart-title">
        <div style="float: right">
          <a href="<?php echo $opt_link; ?>"><?php echo $App->resolve_icon_as_html ('{icons}buttons/close', 'Hide folder tree', '16px'); ?></a>
        </div>
        <?php echo $folder_type_info->plural_title; ?>
      </div>
      <div class="chart-body">
      <?php
          include_once ('webcore/gui/folder_tree_node_info.php');
          $tree_node_info = new SUBSCRIPTION_FOLDER_TREE_NODE_INFO ($App);
          $tree_node_info->page_link = $Env->url ();
          $tree_node_info->set_selected_node ($folder);
          $tree_node_info->set_visible_node ($folder);
  
          /* Make a copy (not a reference). */
          $tree = $App->make_tree_renderer ();
          $tree->node_info = $tree_node_info;
          $tree->display ($folders);
      ?>
      </div>
    </div>
<?php
          $box->new_column ('width: 75%');          
        }

        $form->button = "Update";
        $form->display ();
        if ($show_tree)
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
    $Page->raise_security_violation ('You are not allowed to edit subscriptions in this folder.', $folder);
  }
?>