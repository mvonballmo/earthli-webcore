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

  $user = $App->global_user ();

  if (isset ($user) && ($App->login->is_allowed (Privilege_set_user, Privilege_secure)))
  {
    $class_name = $App->final_class_name ('USER_PERMISSIONS_FORM', 'webcore/forms/user_permissions_form.php');
    $form = new $class_name ($App);

    $form->set_visible ('use_defaults', false);

    $form->process_existing ($user);
    if ($form->committed ())
    {
      $Env->redirect_local ('view_users.php');
    }

    $Page->title->subject ='Edit permissions for registered users';
    $Page->location->append ($App->short_title, './');
    $Page->location->append ('Users', 'view_users.php');
    $Page->location->append ($App->resolve_icon_as_html('{icons}buttons/security', '', '16px') . ' ' . $Page->title->subject);

    $Page->start_display ();
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
    $Page->raise_security_violation ('You are not allowed to see global user permissions.');
  }
?>