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

  if (isset ($folder))
  {
    $user_query = $App->user_query ();
    $user = $user_query->object_at_name (read_var ('name'));

    if (isset ($user))
    {
      $security = $folder->security_definition ();
      $perm = $security->user_permissions_at_id ($user->id);
    }
  }

  if (isset ($perm) && $App->login->is_allowed (Privilege_set_folder, Privilege_secure, $folder))
  {
    $class_name = $App->final_class_name ('FOLDER_USER_PERMISSIONS_DELETE_FORM', 'webcore/forms/folder_user_permissions_delete_form.php');
    /** @var FOLDER_USER_PERMISSIONS_DELETE_FORM $form */
    $form = new $class_name ($user);

    $form->process_existing ($perm);
    if ($form->committed ())
    {
      $Env->redirect_local ($folder->permissions_home_page ());
    }

    $Page->title->add_object ($folder);
    $Page->title->subject  = 'Delete permissions for ' . $user->title_as_plain_text ();

    $Page->location->add_folder_link ($folder);
    $Page->location->append ("Permissions", $folder->permissions_home_page (), '{icons}buttons/security');
    $Page->location->append ($Page->title->subject, '', '{icons}buttons/delete');

    $Page->start_display ();
  ?>
  <div class="main-box">
    <div class="form-content">
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
    $Page->raise_security_violation ('You are not allowed to modify permissions for this user/folder.', $folder);
  }
?>