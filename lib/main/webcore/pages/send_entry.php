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

  if (isset ($entry) && $App->login->is_allowed (Privilege_set_entry, Privilege_view, $entry))
  {
    $class_name = $App->final_class_name ('SEND_OBJECT_IN_FOLDER_FORM', 'webcore/forms/send_object_in_folder_form.php', $entry_type_info->id);
    /** @var SEND_OBJECT_IN_FOLDER_FORM $form */
    $form = new $class_name ($App);

    $form->process_existing ($entry);
    if ($form->committed ())
    {
      $App->return_to_referer ($entry->home_page ());
    }

    $Page->title->add_object ($folder);
    $Page->title->add_object ($entry);
    $Page->title->subject = 'Send ' . $entry_type_info->singular_title;

    $Page->location->add_folder_link ($folder);
    $Page->location->add_object_link ($entry);
    $Page->location->append ($Page->title->subject, '', '{icons}buttons/send');

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
    $Page->raise_security_violation ("You are not allowed to view this {$entry_type_info->singular_title}.", $folder);
  }
?>