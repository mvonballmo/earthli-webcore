<?php

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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

  if (isset ($folder) && $App->login->is_allowed (Privilege_set_folder, Privilege_secure, $folder))
  {
    $user_query = $App->user_query ();

    $class_name = $App->final_class_name ('FOLDER_USER_PERMISSIONS_CREATE_FORM', 'webcore/forms/folder_user_permissions_create_form.php');
    /** @var $form FOLDER_USER_PERMISSIONS_CREATE_FORM */
    $form = new $class_name ($folder, $user_query);

    $security = $folder->security_definition ();
    $obj = $security->new_permissions (Privilege_kind_user);

    $form->process_new ($obj);
    if ($form->committed ())
    {
      $Env->redirect_local ($folder->permissions_home_page ());
    }

    $Page->title->add_object ($folder);
    $Page->title->subject = 'Add permissions for user';

    $Page->location->add_folder_link ($folder);
    $Page->location->append ('Permissions', $folder->permissions_home_page (), '{icons}buttons/security');
    $Page->location->append ($Page->title->subject, '', '{icons}buttons/create');

    $Page->start_display ();

    if ($App->login->is_allowed (Privilege_set_user, Privilege_create))
    {
      ?>
      <div class="top-box button-content">
        <?php
        $menu = $App->make_menu ();
        $menu->append ('Create User', 'create_user.php', '{icons}buttons/create');
        $menu->renderer = $App->make_menu_renderer ();
        $menu->display ();
        ?>
      </div>
    <?php
    }
  ?>
  <div class="box">
    <div class="box-body form-content">
    <?php
      $form->display ();
    ?>
    </div>
  </div>
  <?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to edit the permissions for this folder.', $folder);
  }
?>