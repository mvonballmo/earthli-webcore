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

  if (isset ($folder) && $App->login->is_allowed (Privilege_set_folder, Privilege_purge, $folder))
  {
    $class_name = $App->final_class_name ('PURGE_OBJECT_FORM', 'webcore/forms/purge_form.php', 'folder');
    $form = new $class_name ($App);

    $form->process_existing ($folder);
    if ($form->committed ())
    {
      $parent = $folder->parent_folder ();

      if ($parent)
      {
        $Env->redirect_local ($parent->home_page ());
      }
      else
      {
        $Env->redirect_local ("index.php");
      }
    }

    $Page->title->add_object ($folder);
    $Page->title->subject = 'Purge Folder';

    $Page->location->add_folder_link ($folder);
    $Page->location->append ($Page->title->subject);

    $Page->start_display ();
  ?>
  <div class="box">
    <div class="box-title">
      <?php echo $App->title_bar_icon ('{icons}buttons/purge'); ?> Purge <?php echo $folder->title_as_html (); ?>?
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
    $Page->raise_security_violation ('You are not allowed to purge this folder.', $folder);
  }
?>