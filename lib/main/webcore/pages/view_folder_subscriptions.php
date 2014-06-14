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

  $folder_query = $App->login->folder_query ();
  /** @var $folder FOLDER */
  $folder = $folder_query->object_at_id (read_var ('id'));

  if (isset ($folder) && $App->login->is_allowed (Privilege_set_folder, Privilege_modify, $folder))
  {
    $subscriber_query = $folder->subscriber_query ();

    $class_name = $App->final_class_name ('FOLDER_SUBSCRIBER_FORM', 'webcore/forms/folder_subscriber_form.php');
    /** @var $form FOLDER_SUBSCRIBER_FORM */
    $form = new $class_name ($folder);

    $form->process_existing ($folder);
    if ($form->committed ())
    {
      $App->return_to_referer ($folder->home_page ());
    }

    $Page->title->add_object ($folder);
    $Page->title->subject = $subscriber_query->size () . ' subscribers';

    $Page->location->add_folder_link ($folder);
    $Page->location->append ($Page->title->subject, '', '{icons}buttons/subscriptions');

    $Page->start_display ();

    $folders = $folder_query->tree ();
?>
<div class="top-box">
  <div class="button-content">
    <?php
    include_once ('webcore/util/options.php');
    $option = new STORED_OPTION ($App, 'show_security_tree');
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

    ?><a href="<?php echo $opt_link; ?>" class="button"><?php echo $Page->get_icon_with_text($icon, Sixteen_px, $caption); ?></a><?php

    $menu = $App->make_menu ();
    $menu->renderer->content_mode = Menu_show_all_as_buttons;
    $menu->append ('Add subscribers', 'create_folder_subscriptions.php?id=' . $folder->id, '{icons}buttons/add_subscribers');
    $menu->display ();
    ?>
  </div>
</div>
<div class="main-box">
  <?php
    if ($show_tree)
    {
      $folders = $folder_query->tree ();
      $folder_type_info = $folder->type_info ();

      $box = $Page->make_box_renderer ();
      $box->start_column_set ();
      $box->new_column_of_type ('left-sidebar-column');
  ?>
  <div class="left-sidebar">
  <?php
      include_once ('webcore/gui/folder_tree_node_info.php');
      $tree_node_info = new SUBSCRIPTION_FOLDER_TREE_NODE_INFO ($App);
      $tree_node_info->page_link = $Env->url ();
      $tree_node_info->set_selected_node ($folder);
      $tree_node_info->set_visible_node ($folder);

      $tree = $App->make_tree_renderer ();
      $tree->node_info = $tree_node_info;
      $tree->display ($folders);
  ?>
  </div>
  <?php
      $box->new_column_of_type('content-column text-flow');
    }

    $form->button = "Update";
    $form->display ();

    if (isset ($box))
    {
      $box->finish_column_set ();
    }
  ?>
  </div>
<?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to edit subscriptions in this folder.', $folder);
  }
?>