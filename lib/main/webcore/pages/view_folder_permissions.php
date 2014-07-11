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

    $renderer = $App->make_controls_renderer ();

    echo $renderer->button_as_html($caption, $opt_link, $icon);

    if ($folder->defines_security ())
    {
      echo $renderer->button_as_html('Revert to inherited...', 'create_folder_permissions.php?id=' . $folder->id, '{icons}buttons/restore');
    }
    else
    {
      echo $renderer->button_as_html('Define permissions...', 'create_folder_permissions.php?id=' . $folder->id, '{icons}buttons/create');
    }

    if ($defined)
    {
      echo $renderer->button_as_html ('Add user permissions...', 'create_folder_user_permissions.php?id=' . $folder->id, '{icons}buttons/add');
      echo $renderer->button_as_html ('Add group permissions...', 'create_folder_group_permissions.php?id=' . $folder->id, '{icons}buttons/add');
    }
    ?>
    </div>
  </div>
  <div class="main-box">
    <?php
      if ($show_tree)
      {
        /** @var FOLDER[] $folders */
        $folders = $folder_query->tree ();

        $box = $Page->make_box_renderer ();
        $box->start_column_set ();
        $box->new_column_of_type ('left-sidebar-column');
    ?>
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
    <?php
        $box->new_column_of_type('content-column text-flow');
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
  <table class="basic top columns">
  <?php

    $cols = sizeof ($privilege_groups) + 2;

    function draw_headers ()
    {
      global $privilege_groups;
      global $cols;
  ?>
  <tr>
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
  </tr>
  <?php
    }

    function draw_privilege_set ($title, $perm)
    {
      global $formatter;
      global $privilege_groups;
      global $App;

        $index = 1;
        foreach ($privilege_groups as $group)
        {
    ?>
    <td>
    <?php
        foreach ($group->maps as $map)
        {
          $class_name = $perm->privileges->enabled ($map->set_name, $map->type) ? '' : 'unavailable';

//          echo '<div class="' . $class_name . '">' . $App->get_icon_with_text($formatter->icon_url_for($map), Sixteen_px, $formatter->title_for($map)) . '</div>';
          echo '<span class="' . $class_name . '">' . $App->get_icon_with_text($formatter->icon_url_for($map), Sixteen_px, '') . '</span>';
        }
    ?>
    </td>
    <?php
        $index += 1;
      }
    }

    if ($defined)
    {
    }

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
        foreach ($users as $perms)
        {
          $user = $perms->user ();
          ?>
          <tr>
            <td colspan="<?php echo $cols; ?>">
              <h3>
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
                <?php echo $user->title_as_link (); ?>
              </h3>
            </td>
          </tr>
          <?php
          draw_headers ();
          ?>
          <tr>
            <?php
            draw_privilege_set ($user->title_as_link (), $perms);
            ?>
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
          foreach ($groups as $perms)
          {
            $group = $perms->group ();
    ?>
    <tr>
      <td colspan="<?php echo $cols; ?>">
        <h3>
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
          <?php echo $group->title_as_link (); ?>
        </h3>
      </td>
    </tr>
    <?php
    draw_headers ();
    ?>
    <tr>
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
    <td colspan="<?php echo $cols; ?>">
      <h2>General</h2>
    </td>
  </tr>
    <tr>
      <td colspan="<?php echo $cols; ?>">
        <h3>
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
          Registered
        </h3>
      </td>
    </tr>
    <tr>
      <?php
      draw_privilege_set ('Registered', $security->registered_permissions ());
      ?>
    </tr>
    <tr>
      <td colspan="<?php echo $cols; ?>">
        <h3>
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
          Anonymous
        </h3>
      </td>
    </tr>
    <?php draw_headers (); ?>
    <tr>
      <?php
      draw_privilege_set ('Anonymous', $security->anonymous_permissions ());
      ?>
    </tr>
  </table>
<?php
      if (isset ($box))
      {
        $box->finish_column_set ();
      }
    else
    {
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
    $Page->raise_security_violation ('You are not allowed to see the permissions for this folder.', $folder);
  }
?>