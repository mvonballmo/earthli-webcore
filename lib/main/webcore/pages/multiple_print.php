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
    $class_name = $App->final_class_name ('MULTIPLE_OBJECT_PRINTER_FORM', 'webcore/forms/multiple_object_printer_form.php');
    /** @var MULTIPLE_OBJECT_PRINTER_FORM $form */
    $form = new $class_name ($folder);
    $form->load_from_request ();
  }
  
  if (isset ($form) &&
      (! $form->object_list->has_folders () || $App->login->is_allowed (Privilege_set_folder, Privilege_view, $folder)) &&
      (! $form->object_list->has_entries () || $App->login->is_allowed (Privilege_set_entry, Privilege_view, $folder)))
  {
    $form->load_from_object ($folder);

    $Page->title->add_object ($folder);
    $Page->title->subject = 'Print ' . $form->object_list->description ();

    $Page->location->renderer->context = $App;

    $Page->location->add_folder_link ($folder);
    $Page->location->append ("Explorer", "view_explorer.php?id=$folder->id");
    $Page->location->append ($Page->title->subject, '', '{icons}buttons/print');

    $Page->start_display ();
  ?>
  <div class="main-box">
    <div class="form-content">
    <?php
      $form->action = 'print_preview.php';
      $form->display ();
    ?>
    </div>
  </div>
  <?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to print the selected objects.', $folder);
  }
?>