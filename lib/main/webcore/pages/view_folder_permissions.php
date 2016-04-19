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

  /** @var FOLDER $folder */
  $folder = $folder_query->object_at_id (read_var ('id'));

  if (isset ($folder) && $App->login->is_allowed (Privilege_set_folder, Privilege_secure, $folder))
  {
    $App->set_referer ();

    $security = $folder->security_definition ();
    $security->load_all_permissions ();

    $Page->title->add_object ($folder);
    $Page->title->subject = 'Permissions';

    $Page->location->add_folder_link ($folder);
    $Page->location->append ("Permissions", '', '{icons}buttons/security');

    $Page->start_display ();

    $defined = $folder->defines_security ();
    $parent = $folder->parent_folder ();

    $formatter = new PERMISSIONS_FORMATTER ($App);
    $privilege_groups = $formatter->content_privilege_groups ();

    include_once ('webcore/util/options.php');
    $tree_option = new STORED_OPTION ($App, 'show_security_tree');
    $show_tree = $tree_option->value ();
    $tree_opt_link = $tree_option->setter_url_as_html (! $show_tree);

    $details_option = new STORED_OPTION ($App, 'show_full_permissions');
    $show_details = $details_option->value ();
    $details_opt_link = $details_option->setter_url_as_html (! $show_details);

    $menu = $App->make_menu();

    if ($defined)
    {
      if ($App->login->is_allowed (Privilege_set_user, Privilege_view))
      {
        $menu->append('Add user...', 'create_folder_user_permissions.php?id=' . $folder->id, '{icons}buttons/add');
      }
      if ($App->login->is_allowed (Privilege_set_group, Privilege_view))
      {
        $menu->append('Add group...', 'create_folder_group_permissions.php?id=' . $folder->id, '{icons}buttons/add');
      }
    }

    if ($folder->defines_security ())
    {
      $menu->append('Delete...', 'create_folder_permissions.php?id=' . $folder->id, '{icons}buttons/delete');
    }
    else
    {
      $menu->append('Define permissions...', 'create_folder_permissions.php?id=' . $folder->id, '{icons}buttons/create');
    }

    if (! $show_tree)
    {
      $menu->append('Show folders', $tree_opt_link, '{icons}buttons/show_list');
    }
    else
    {
      $menu->append('Hide folders', $tree_opt_link, '{icons}buttons/close');
    }

    if (! $show_details)
    {
      $menu->append('Detail view', $details_opt_link, '{icons}buttons/view');
    }
    else
    {
      $menu->append('Compact view', $details_opt_link, '{icons}indicators/invisible');
    }

    function draw_headers ()
    {
      global $privilege_groups;
      ?>
      <tr>
        <td></td>
        <?php
        $index = 1;
        foreach ($privilege_groups as $group)
        {
          ?>
          <th><?php echo $group->title; ?></th>
          <?php
          $index += 1;
        }
        ?>
        <td></td>
      </tr>
    <?php
    }

    function draw_privilege_set ($title, $perm)
    {
      global $formatter;
      global $privilege_groups;
      global $App;
      global $show_details;
?>
      <th>
        <?php echo $title; ?>
      </th>
<?php
      $index = 1;
      foreach ($privilege_groups as $group)
      {
        ?>
        <td>
          <?php
          foreach ($group->maps as $map)
          {
            $class_name = $perm->privileges->enabled ($map->set_name, $map->type) ? '' : 'unavailable';

            if ($show_details)
            {
              echo '<div class="' . $class_name . '">' . $App->get_icon_with_text($formatter->icon_url_for($map), Sixteen_px, $formatter->title_for($map)) . '</div>';
            }
            else
            {
              echo '<span class="' . $class_name . '">' . $App->get_icon_with_text($formatter->icon_url_for($map), Sixteen_px, '') . '</span>';
            }
          }
          ?>
        </td>
        <?php
        $index += 1;
      }
    }

    ?>
  <div class="top-box">
    <div class="button-content">
    <?php
    $menu->renderer->set_size(Menu_size_full);
    $menu->renderer->content_mode = Menu_show_all_as_buttons;
    $menu->display();
  ?>
    </div>
  </div>
  <div class="main-box">
    <?php
      if ($show_tree)
      {
        /** @var FOLDER[] $folders */
        $folders = $folder_query->tree ();
    ?>
    <div class="columns">
      <div class="left-sidebar">
        <?php
          include_once ('webcore/gui/folder_tree_node_info.php');
          $tree_node_info = new SECURITY_FOLDER_TREE_NODE_INFO ($App);
          $tree_node_info->page_link = $Env->url ();
          $tree_node_info->set_visible_node ($folder);
          $tree_node_info->set_selected_node ($folder);
          $tree_node_info->set_defined_nodes_visible ($folders);

          $tree = $App->make_tree_renderer ();
          $tree->node_info = $tree_node_info;
          $tree->display ($folders);
        ?>
      </div>
      <div>
        <?php
          }
          else
          {
        ?>
        <div class="text-flow">
          <p>
          <?php
          if ($folder->defines_security ())
          {
            echo 'Permissions for this folder are defined below.';
          }
          else
          {
            /** @var FOLDER $permissions_folder */
            $permissions_folder = null;
            $folder_query = $App->login->folder_query ();
            $parent = $folder->parent_folder ();
            $permissions_folder = $folder_query->object_at_id ($parent->permissions_id);

            if ($App->login->is_allowed (Privilege_set_folder, Privilege_secure, $permissions_folder))
            {
              $t = $permissions_folder->title_formatter ();
              $t->set_name ($Env->url (Url_part_file_name));
              $title = $permissions_folder->title_as_link ($t);
            }
            else
            {
              $title = $permissions_folder->title_as_html ();
            }

            echo 'Permissions are inherited from ' . $title . '.';
          }
          ?>
          </p>
        <?php
        }
        ?>
        <table class="basic top columns left-labels">
        <?php
          $cols = sizeof ($privilege_groups) + 2;
        ?>
          <tr>
            <td colspan="<?php echo $cols; ?>">
              <h2>General</h2>
            </td>
          </tr>
          <?php
          draw_headers ();
          ?>
          <tr>
            <?php
            draw_privilege_set ('Registered', $security->registered_permissions ());
            ?>
            <td>
              <?php
              if ($defined)
              {
                $args = 'id=' . $folder->id;
                $menu = $App->make_menu();
                $menu->append('Edit...', 'edit_folder_all_users_permissions.php?' . $args, '{icons}buttons/edit');
                $menu->renderer->set_size(Menu_size_minimal);
                $menu->renderer->content_mode = Menu_show_all_as_buttons;
                $menu->display();
              }
              ?>
            </td>
          </tr>
          <tr>
            <?php
            draw_privilege_set ('Anonymous', $security->anonymous_permissions ());
            ?>
            <td>
              <?php
              if ($defined)
              {
                $args = 'id=' . $folder->id;
                $menu = $App->make_menu();
                $menu->append('Edit...', 'edit_folder_anon_user_permissions.php?' . $args, '{icons}buttons/edit');
                $menu->renderer->set_size(Menu_size_minimal);
                $menu->renderer->content_mode = Menu_show_all_as_buttons;
                $menu->display();
              }
              ?>
            </td>
          </tr>
      <?php


          $users = $security->user_permissions ();

          if (sizeof ($users) || $defined)
          {
            ?>
            <tr>
              <td colspan="<?php echo $cols; ?>">
                <h2><?php echo sizeof ($users); ?> Users</h2>
              </td>
            </tr>

            <?php
            if (sizeof ($users))
            {
              draw_headers ();
              foreach ($users as $perms)
              {
                $user = $perms->user ();
                ?>
                <tr>
                  <?php
                  draw_privilege_set ($user->title_as_link (), $perms);
                  ?>
                  <td>
                    <?php
                    if ($defined)
                    {
                      $args = 'id=' . $folder->id . '&name=' . urlencode ($user->title);
                      $menu = $App->make_menu();
                      $menu->append('Edit...', 'edit_folder_user_permissions.php?' . $args, '{icons}buttons/edit');
                      $menu->append('Delete...', 'delete_folder_user_permissions.php?' . $args, '{icons}buttons/delete');
                      $menu->renderer->set_size(Menu_size_minimal);
                      $menu->renderer->content_mode = Menu_show_all_as_buttons;
                      $menu->display();
                    }
                    ?>
                  </td>
                </tr>
              <?php
              }
            }
          }

            $groups = $security->group_permissions ();

            if (sizeof ($groups) || $defined)
            {
          ?>
          <tr>
            <td colspan="<?php echo $cols; ?>">
              <h2><?php echo sizeof ($groups); ?> Groups</h2>
            </td>
          </tr>
          <?php
              if (sizeof ($groups))
              {
                draw_headers ();
                foreach ($groups as $perms)
                {
                  $group = $perms->group ();
          ?>
          <tr>
          <?php
                  draw_privilege_set ($group->title_as_link (), $perms);
          ?>
            <td>
              <?php
              if ($defined)
              {
                $args = 'id=' . $folder->id . '&group_id=' . $perms->ref_id;
                $menu = $App->make_menu();
                $menu->append('Edit...', 'edit_folder_group_permissions.php?' . $args, '{icons}buttons/edit');
                $menu->append('Delete...', 'delete_folder_group_permissions.php?' . $args, '{icons}buttons/delete');
                $menu->renderer->set_size(Menu_size_minimal);
                $menu->renderer->content_mode = Menu_show_all_as_buttons;
                $menu->display();
              }
              ?>
            </td>
          </tr>
          <?php
                }
              }
            }
      ?>
        </table>
      </div>
    </div>
  </div>
  <?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to see the permissions for this folder.', $folder);
  }
?>