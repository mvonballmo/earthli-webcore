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
  /** @var FOLDER $folder */
  $folder = $folder_query->object_at_id (read_var ('id'));

  if (isset ($folder) && $App->login->is_allowed (Privilege_set_folder, Privilege_modify, $folder))
  {
    $class_name = $App->final_class_name ('ADD_SUBSCRIBERS_TO_FOLDER_FORM', 'webcore/forms/add_subscribers_to_folder_form.php');
    /** @var ADD_SUBSCRIBERS_TO_FOLDER_FORM $form */
    $form = new $class_name ($folder);

    $form->process ($folder);
    if ($form->committed ())
    {
      $Env->redirect_local ($folder->subscriptions_home_page ());
    }

    $Page->title->add_object ($folder);
    $Page->title->subject = 'Add subscribers';

    $Page->location->add_folder_link ($folder);
    $Page->location->append ($Page->title->subject, '', '{icons}buttons/subscriptions');

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
    $Page->raise_security_violation ('You are not allowed to edit subscriptions in this folder.', $folder);
  }
?>