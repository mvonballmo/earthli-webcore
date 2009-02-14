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

  $folder_query = $App->login->folder_query ();
  $folder =& $folder_query->object_at_id (read_var ('id'));

  if (isset ($folder) && $App->login->is_allowed (Privilege_set_folder, Privilege_secure, $folder))
  {
    $App->set_referer ();

    $security = $folder->security_definition ();
    $security->load_all_permissions ();

    $Page->title->add_object ($folder);
    $Page->title->subject = 'Permissions';

    $Page->location->add_folder_link ($folder);
    $Page->location->append ("Permissions");

    $Page->start_display ();

    $defined = $folder->defines_security ();
    $parent =& $folder->parent_folder ();

    $formatter = new PERMISSIONS_FORMATTER ($App);
    $privilege_groups = $formatter->content_privilege_groups ();
  ?>
  <div class="box">
    <div class="box-title">
      <?php echo $App->title_bar_icon ('{icons}buttons/security'); ?> Permissions for <?php echo $folder->title_as_html (); ?>
    </div>
    <?php
      include_once ('webcore/util/options.php');
      $option = new STORED_OPTION ($App, 'show_security_tree');
      $show_tree = $option->value ();
      $opt_link = $option->setter_url_as_html (! $show_tree);

      if (! $show_tree)
      {
    ?>
    <div class="menu-bar-top">
      <div style="float: left">
        <a href="<?php echo $opt_link; ?>"><?php echo $App->resolve_icon_as_html ('{icons}buttons/show_list', 'Show list', '16px'); ?></a>
        <a href="<?php echo $opt_link; ?>">Show folder tree</a>
      </div>
      <div style="clear: both"></div>
    </div>
    <?php
      }
    ?>
    <div class="box-body">
      <?php
        if ($show_tree)
        {
          $folders = $folder_query->tree ();

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
          include_once ('webcore/gui/folder_tree_node_info.php');
          $tree_node_info = new SECURITY_FOLDER_TREE_NODE_INFO ($App);
          $tree_node_info->page_link = $Env->url ();
          $tree_node_info->set_visible_node ($folder);
          $tree_node_info->set_selected_node ($folder);
          $tree_node_info->set_defined_nodes_visible ($folders);

          /* Make a copy (not a reference). */
          $tree = $App->make_tree_renderer ();
          $tree->node_info = & $tree_node_info;
          $tree->display ($folders);
      ?>
      </div>
    </div>
<?php
          $box->new_column ('width: 75%');
        }
?>
    <p class="notes">The permissions for this folder are shown below. Permissions are,
      by default, inherited from the parent folder. Inherited permissions are displayed
      as read-only. Use the button below to define or revert permissions.</p>
    <table cellspacing="0" cellpadding="3" style="margin: auto">
    <?php

      $cols = sizeof ($privilege_groups) + 2;

      if ($parent)
      {
    ?>
      <tr>
        <td colspan="<?php echo $cols; ?>">
        <?php
          $class_name = $App->final_class_name ('PERMISSIONS_INHERITANCE_FORM', 'webcore/forms/permissions_inheritance_form.php');
          $form = new $class_name ($App);
          $form->process_existing ($folder);
          $form->display ();
        ?>
        </td>
      </tr>
    <?php
      }

      function draw_headers ()
      {
        global $App;
        global $privilege_groups;
    ?>
    <tr>
      <td></td>
      <td></td>
    <?php
        $idx = 1;
        foreach ($privilege_groups as $group)
        {
          if ($idx % 2)
            $bg_style = '; background-image: url(' . $App->resolve_file ('{icons}shades/5_percent_black') . ')';
          else
            $bg_style = '';
    ?>
    <td class="detail" style="padding-right: 1em<?php echo $bg_style; ?>"><?php echo $group->title; ?></td>
    <?php
          $idx++;
        }
    ?>
    </tr>
    <?php
      }

      function draw_privilege_set ($title, $perm)
      {
        global $formatter;
        global $privilege_groups;
        global $App;
    ?>
      <td class="label"><?php echo $title; ?></td>
      <?php
          $idx = 1;
          foreach ($privilege_groups as $group)
          {
            if ($idx % 2)
              $bg_style = '; background-image: url(' . $App->resolve_file ('{icons}shades/5_percent_black') . ')';
            else
              $bg_style = '';
      ?>
      <td class="detail" style="padding-right: 1em<?php echo $bg_style; ?>; white-space: nowrap">
      <?php
          foreach ($group->maps as $map)
          {
            if ($perm->privileges->enabled ($map->set_name, $map->type))
              echo $formatter->icon_for ($map);
            else
              echo $App->resolve_icon_as_html ('{icons}buttons/blank', ' ', '16px');
          }
      ?>
      </td>
      <?php
          $idx++;
        }
      }

      if ($defined)
        $renderer = $App->make_controls_renderer ();
    ?>
      <tr>
        <td></td>
        <td colspan="<?php echo $cols - 1; ?>">
          <h2>General</h2>
        </td>
      </tr>
      <?php draw_headers (); ?>
      <tr>
        <td style="vertical-align: top; text-align: right">
        <?php
          if ($defined)
            echo $renderer->button_as_html ('', 'edit_folder_anon_user_permissions.php?id=' . $folder->id, '{icons}buttons/edit');
        ?>
        </td>
        <?php
          draw_privilege_set ('Anonymous', $security->anonymous_permissions ());
        ?>
      </tr>
      <tr>
        <td style="vertical-align: top; text-align: right">
        <?php
          if ($defined)
            echo $renderer->button_as_html ('', 'edit_folder_all_users_permissions.php?id=' . $folder->id, '{icons}buttons/edit');
        ?>
        </td>
        <?php
          draw_privilege_set ('Registered', $security->registered_permissions ());
        ?>
      </tr>
      <tr>
        <td colspan="<?php echo $cols; ?>">&nbsp;</td>
      </tr>
      <?php
        $groups =& $security->group_permissions ();

        if (sizeof ($groups) || $defined)
        {
      ?>
      <tr>
        <td style="text-align: right">
        <?php
          if ($defined)
            echo $renderer->button_as_html ('', 'create_folder_group_permissions.php?id=' . $folder->id, '{icons}buttons/add');
        ?>
        </td>
        <td colspan="<?php echo $cols - 1; ?>">
          <h2><?php echo sizeof ($groups); ?> Groups</h2>
        </td>
      </tr>
      <?php
          draw_headers ();

          if (sizeof ($groups))
          {
            foreach ($groups as $perms)
            {
              $group = $perms->group ();
      ?>
      <tr>
        <td style="vertical-align: top; text-align: right; white-space: nowrap">
        <?php
              if ($defined)
              {
                $buttons = array ();
                $buttons [] = $renderer->button_as_html ('', 'edit_folder_group_permissions.php?id=' . $folder->id . '&group_id=' . $perms->ref_id, '{icons}buttons/edit');
                $buttons [] = $renderer->button_as_html ('', 'delete_folder_group_permissions.php?id=' . $folder->id . '&group_id=' . $perms->ref_id, '{icons}buttons/delete');
                $renderer->draw_buttons ($buttons);
              }
        ?>
        </td>
      <?php
              draw_privilege_set ($group->title_as_link (), $perms);
      ?>
      </tr>
      <?php
            }
          }
        }
      ?>
      <tr>
        <td colspan="<?php echo $cols; ?>">&nbsp;</td>
      </tr>
      <?php
        $users =& $security->user_permissions ();

        if (sizeof ($users) || $defined)
        {
      ?>
      <tr>
        <td style="text-align: right">
        <?php
          if ($defined)
            echo $renderer->button_as_html ('', 'create_folder_user_permissions.php?id=' . $folder->id, '{icons}buttons/add');
        ?>
        </td>
        <td colspan="<?php echo $cols - 1; ?>">
          <h2><?php echo sizeof ($users); ?> Users</h2>
        </td>
      </tr>

      <?php
          draw_headers ();

          if (sizeof ($users))
          {
            foreach ($users as $perms)
            {
              $user = $perms->user ();
      ?>
      <tr>
        <td style="vertical-align: top; text-align: right; white-space: nowrap">
        <?php
              if ($defined)
              {
                $args = 'id=' . $folder->id . '&name=' . urlencode ($user->title);
                $buttons = array ();
                $buttons [] = $renderer->button_as_html ('', 'edit_folder_user_permissions.php?' . $args, '{icons}buttons/edit');
                $buttons [] = $renderer->button_as_html ('', 'delete_folder_user_permissions.php?' . $args, '{icons}buttons/delete');
                $renderer->draw_buttons ($buttons);
              }
        ?>
        </td>
      <?php
              draw_privilege_set ($user->title_as_link (), $perms);
      ?>
      </tr>
      <?php
            }
          }
        }
      ?>
    </table>
<?php
      if ($show_tree)
        $box->finish_column_set ();
?>
    </div>
  </div>
  <?php
    $Page->finish_display ();
  }
  else
    $Page->raise_security_violation ('You are not allowed to see the permissions for this folder.', $folder);
?>