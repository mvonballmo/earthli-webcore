<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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

  /* Set these globals before using this template:

       $entry_type_info: TYPE_INFO
  */

  $id = read_var ('id');
  $folder_query = $App->login->folder_query ();
  $folder = $folder_query->folder_for_entry_at_id ($id);

  if (isset ($folder))
  {
    $entry_query = $folder->entry_query ();
    $entry = $entry_query->object_at_id ($id);
  }

  if (isset ($entry) && $App->login->is_allowed (Privilege_set_entry, Privilege_delete, $entry))
  {
    $class_name = $App->final_class_name ('DELETE_OBJECT_IN_FOLDER_FORM', 'webcore/forms/delete_form.php', $entry_type_info->id);
    $form = new $class_name ($folder, Privilege_set_entry);

    $form->process_existing ($entry);
    if ($form->committed ())
    {
      if ($App->login->is_allowed (Privilege_set_entry, Privilege_hidden, $folder, $creator) && ! $form->value_for ('purge'))
      {
        $App->return_to_referer ($entry->home_page ());
      }
      else
      {
        $Env->redirect_local ($folder->home_page ());
      }
    }

    $Page->title->add_object ($folder);
    $Page->title->add_object ($entry);
    $Page->title->subject = 'Delete ' . $entry_type_info->singular_title;

    $Page->location->add_folder_link ($folder);
    $Page->location->add_object_link ($entry);
    $Page->location->append ($Page->title->subject);

    $Page->start_display ();
  ?>
  <div class="box">
    <div class="box-title">
      <?php echo $App->title_bar_icon ('{icons}buttons/delete'); ?> Confirm delete of <?php echo $entry_type_info->singular_title; ?>
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
    $Page->raise_security_violation ("You are not allowed to delete this {$entry_type_info->singular_title}.", $folder);
  }
?>