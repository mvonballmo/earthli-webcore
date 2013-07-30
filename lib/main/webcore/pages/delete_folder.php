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

  if (isset ($folder) && $App->login->is_allowed (Privilege_set_folder, Privilege_delete, $folder))
  {
    $class_name = $App->final_class_name ('DELETE_OBJECT_IN_FOLDER_FORM', 'webcore/forms/delete_form.php', 'folder');
    /** @var DELETE_OBJECT_IN_FOLDER_FORM $form */
    $form = new $class_name ($folder, Privilege_set_folder);

    $form->process_existing ($folder);
    if ($form->committed ())
    {
      $parent = $folder->parent_folder ();

      if ($App->login->is_allowed (Privilege_set_folder, Privilege_view_hidden, $folder) && ! $form->value_for ('purge'))
      {
        $App->return_to_referer ($folder->home_page ());
      }
      else
      {
        if (isset ($parent))
        {
          $Env->redirect_local ($parent->home_page ());
        }
        else
        {
          $Env->redirect_local ('index.php');
        }
      }
    }

    $Page->title->add_object ($folder);
    $Page->title->subject = 'Delete folder';

    $Page->location->add_folder_link ($folder);
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
    $Page->raise_security_violation ('You are not allowed to delete this folder.', $folder);
  }
?>