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

  /** @var $parent FOLDER */
  $parent = $folder_query->object_at_id (read_var ('id'));

  if (isset ($parent) && $App->login->is_allowed (Privilege_set_folder, Privilege_create, $parent))
  {
    $class_name = $App->final_class_name ('FOLDER_FORM', 'webcore/forms/folder_form.php');
    /** @var $form FOLDER_FORM */
    $form = new $class_name ($parent);

    $folder = $parent->new_folder ();
    $type_info = $folder->type_info ();

    $form->process_new ($folder);
    if ($form->committed ())
    {
      $Env->redirect_local ($folder->home_page ());
    }

    if ($type_info->icon)
    {
      $icon = $App->resolve_file($type_info->icon);
    }
    else
    {
      $icon = '{icons}buttons/create';
    }

    if ($parent)
    {
      $Page->title->add_object ($parent);
      $Page->location->add_folder_link ($parent);
    }

    $Page->title->subject = 'Create ' . $type_info->singular_title;
    $Page->location->append ($Page->title->subject, '', $icon);

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
    $Page->raise_security_violation ('You are not allowed to create folders.', $parent);
  }
?>