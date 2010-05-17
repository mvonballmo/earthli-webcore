<?php

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
  $folder = $folder_query->folder_for_attachment_at_id ($id, $type);

  if (isset ($folder))
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
      $entry = $entry_query->object_for_attachment_at_id ($id, $type);

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
          $comment = $com_query->object_for_attachment_at_id ($id);

          if (isset ($comment))
          {
            $Page->title->add_object ($comment);
            $Page->location->add_object_link ($comment);

            $host = $comment;
          }
        }
      }
    }

    if (isset ($host))
    {
      $attachment_query = $host->attachment_query ();
      $attachment = $attachment_query->object_at_id ($id);
    }
  }

  if (isset ($attachment) && $App->login->is_allowed (Privilege_set_attachment, Privilege_view, $attachment))
  {
    $App->set_referer ();

    $Page->title->subject = $attachment->title_as_plain_text ();
    $Page->location->add_object_text ($attachment);

    $Page->start_display ();
?>
    <div class="box">
      <div class="box-title">
        <?php echo $attachment->title_as_html (); ?>
      </div>
      <?php
        $renderer = $attachment->handler_for (Handler_menu);
        $renderer->display_as_toolbar ($attachment->handler_for (Handler_commands));
      ?>
      <div class="box-body">
        <?php
          $renderer = $attachment->handler_for (Handler_html_renderer);
          $renderer->display ($attachment);
        ?>
      </div>
    </div>
<?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to view this attachment.', $folder);
  }
?>