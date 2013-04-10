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

  $id = read_var ('id');
  $type = read_var ('type');

  $folder_query = $App->login->folder_query ();

  switch ($type)
  {
  case History_item_entry:
    $folder = $folder_query->folder_for_entry_at_id ($id);
    break;
  case History_item_comment:
    $folder = $folder_query->folder_for_comment_at_id ($id);
    break;
  case History_item_folder:
    $folder = $folder_query->object_at_id ($id);
    break;
  }

  if (isset ($folder) && 
    $App->login->is_allowed (Privilege_set_attachment, Privilege_create, $folder) && 
    $App->login->is_allowed (Privilege_set_attachment, Privilege_upload, $folder))
  {
    $Page->title->add_object ($folder);
    $Page->location->add_folder_link ($folder);

    if ($type == History_item_folder)
    {
      $host = $folder;
    }
    else
    {
      $entry_query = $folder->entry_query ();

      switch ($type)
      {
      case History_item_entry:
        $entry = $entry_query->object_at_id ($id);
        break;
      case History_item_comment:
        $entry = $entry_query->object_for_comment_at_id ($id);
        break;
      }

      if (isset ($entry))
      {
        $Page->title->add_object ($entry);
        $Page->location->add_object_link ($entry);

        if ($type == History_item_entry)
        {
          $host = $entry;
        }
        else
        {
          $com_query = $entry->comment_query ();
          $comment = $com_query->object_at_id ($id);

          if (isset ($comment))
          {
            $Page->title->add_object ($comment);
            $Page->location->add_object_link ($comment, '', '{icons}buttons/reply');

            $host = $comment;
          }
        }
      }
    }

    if (isset ($host))
    {
      $attachment = $host->new_attachment ();
    }
  }

  if (isset ($attachment))
  {
    $class_name = $App->final_class_name ('ATTACHMENT_FORM', 'webcore/forms/attachment_form.php');
    $form = new $class_name ($host);

    $form->process_new ($attachment);
    if ($form->committed ())
    {
      $App->return_to_referer ($host->home_page ());
    }

    $Page->title->subject = 'Create Attachment';
    $Page->location->append ($Page->title->subject, '', '{icons}buttons/attach');

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
    $Page->raise_security_violation ('You are not allowed to create attachments here.', $folder);
  }
?>