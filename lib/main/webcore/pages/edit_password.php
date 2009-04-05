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

  $user_query = $App->user_query ();
  $user = $user_query->object_at_name (read_var ('name'));

  if (isset ($user) && $App->login->is_allowed (Privilege_set_global, Privilege_password, $user))
  {
    $class_name = $App->final_class_name ('PASSWORD_FORM', 'webcore/forms/password_form.php');
    $form = new $class_name ($App);

    $form->process ($user);
    if ($form->committed ())
    {
      $App->return_to_referer ($user->home_page ());
    }

    $Page->title->add_object ($user);
    $Page->title->subject = 'Change password';

    $Page->location->add_root_link ();
    $Page->location->append ("Users", "view_users.php");
    $Page->location->add_object_link ($user);
    $Page->location->append ($Page->title->subject);

    $Page->start_display ();
  ?>
  <div class="box">
    <div class="box-title">
      <?php echo $App->title_bar_icon ('{icons}buttons/password'); ?> Change Password for <?php echo $user->title; ?>
    </div>
    <div class="box-body">
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
    $Page->raise_security_violation ('You are not allowed to change this user\'s password.', $user);
  }
?>