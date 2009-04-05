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
  
  if (isset ($folder))
  {
    $security = $folder->security_definition ();
    $perm = $security->registered_permissions ();
  }

  if (isset ($perm) && $App->login->is_allowed (Privilege_set_folder, Privilege_secure, $folder))
  {
    $class_name = $App->final_class_name ('FOLDER_PERMISSIONS_FORM', 'webcore/forms/folder_permissions_form.php');
    $form = new $class_name ($App);

    $form->process_existing ($perm);
    if ($form->committed ())
    {
      $App->return_to_referer ($folder->permissions_home_page ());
    }

    $Page->title->add_object ($folder);
    $Page->title->subject = 'Edit permissions for registered users';

    $Page->location->add_folder_link ($folder);
    $Page->location->append ("Permissions", $folder->permissions_home_page ());
    $Page->location->append ($Page->title->subject);

    $Page->start_display ();
  ?>
  <div class="box">
    <div class="box-title">
      <?php echo $App->title_bar_icon ('{icons}buttons/security'); ?> <?php echo $Page->title->subject; ?>
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
    $Page->raise_security_violation ('You are not allowed to edit permissions for this folder.', $folder);
  }
?>