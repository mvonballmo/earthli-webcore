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

  $Page->title->subject = 'Browse for user';
  
  if ($App->login->is_allowed (Privilege_set_user, Privilege_view))
  {
    $folder_query = $App->login->folder_query ();
    /** @var FOLDER $folder */
    $folder = $folder_query->object_at_id (read_var ('id'));
    if ($folder)
    {
      $Page->template_options->header_visible = false;
      $Page->template_options->footer_visible = false;
      $Page->add_script_file ('{scripts}webcore_forms.js');
      $Page->start_display ();
    ?>
    <div class="top-box">
      <div class="button-content">
      <?php
      $class_name = $App->final_class_name ('USER_LIST_COMMANDS', 'webcore/cmd/user_management_commands.php');
      $commands = new $class_name ($App);
      $renderer = $App->make_menu_renderer ();
      $renderer->display ($commands);
      ?>
      </div>
    </div>
    <div class="main-box">
      <h2>
        Browse for user
      </h2>
    <?php
      $user_query = $App->user_query ();

      /* get the list of user ids that are already mapped in this folder. */

      $security = $folder->security_definition ();
      $permissions = $security->user_permissions ();

      $ids = array ();
      foreach ($permissions as $permission)
      {
        $ids [] = $permission->ref_id;
      }
      if (sizeof ($ids))
      {
        $user_query->restrict_by_op ('usr.id', $ids, Operator_not_in);
      }

      if (read_var ('show_anon'))
      {
        $user_query->set_kind (Privilege_kind_anonymous);
      }
      else
      {
        $user_query->set_kind (Privilege_kind_registered);
      }

      $class_name = $App->final_class_name ('USER_BROWSER_GRID', 'webcore/gui/user_browser_grid.php');
      /** @var USER_BROWSER_GRID $grid */
      $grid = new $class_name ($App);
      $grid->set_ranges (15, 1);
      $grid->set_query ($user_query);
      $grid->display ();
    ?>
      <p class="info-box-bottom">Users who already have permissions for <?php echo $folder->title_as_link (); ?>
        are <em>not</em> displayed.</p>
    </div>
    <?php
      $Page->finish_display ();
    }
    else
    {
      $Page->raise_security_violation ('You are not allowed to edit this folder\'s permissions.', $folder);
    }
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to view users.', $folder);
  }
?>