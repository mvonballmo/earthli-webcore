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
  $parent_id = read_var ('parent_id');

  $folder_query = $App->login->folder_query ();
  $folder = $folder_query->folder_for_entry_at_id ($id);

  if (isset ($folder) && $App->login->is_allowed (Privilege_set_comment, Privilege_create, $folder))
  {
    $entry_query = $folder->entry_query ();
    $entry = $entry_query->object_at_id ($id);

    if (isset ($entry))
    {
      $com_query = $entry->comment_query ();
      if ($parent_id)
      {
        $parent = $com_query->object_at_id ($parent_id);
      }

      if (! $parent_id || isset ($parent))
      {
        $comment = $entry->new_comment ($parent_id);
      }
    }
  }

  if (isset ($comment))
  {
    $class_name = $App->final_class_name ('COMMENT_FORM', 'webcore/forms/comment_form.php');
    /** @var COMMENT_FORM $form */
    $form = new $class_name ($folder);

    $form->process_new ($comment);
    if ($form->committed ())
    {
      $App->return_to_referer ($comment->home_page ());
    }
    else
    {
      if (isset ($parent))
      {
        $prev = $parent;
      }
      else
      {
        $prev = $entry;
      }

      $form->add_preview ($prev, 'In reply to: ' . $prev->title_as_html (), ! $form->previewing ());
    }

    $Page->title->add_object ($folder);
    $Page->title->add_object ($entry);
    $Page->title->subject = 'Create comment';

    $Page->location->add_folder_link ($folder);
    $Page->location->add_object_link ($entry);
    if (isset ($parent))
    {
      $Page->location->add_object_link ($parent);
    }
    $Page->location->append ($Page->title->subject, '', '{icons}buttons/reply');

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
    $Page->raise_security_violation ('You are not allowed to create comments here.', $folder);
  }
?>