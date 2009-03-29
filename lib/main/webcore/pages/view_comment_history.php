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

  $id = read_var ('id');
  $folder_query = $App->login->folder_query ();
  $folder = $folder_query->folder_for_comment_at_id ($id);

  if (isset ($folder))
  {
    $entry_query = $folder->entry_query ();
    $entry = $entry_query->object_for_comment_at_id ($id);

    if (isset ($entry))
    {
      $com_query = $entry->comment_query ();
      $comment = $com_query->object_at_id ($id);
    }
  }


  if (isset ($comment) && $App->login->is_allowed (Privilege_set_comment, Privilege_view_history, $comment))
  {
    $Page->title->add_object ($folder);
    $Page->title->add_object ($entry);
    $Page->title->add_object ($comment);

    $Page->location->add_folder_link ($folder);
    $Page->location->add_object_link ($entry);
    $Page->location->add_object_link ($comment);

    $history_item_query = $comment->history_item_query ();
    $obj = $comment;

    include_once ('webcore/pages/view_history.php');
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to view this comment\'s history.', $folder);
  }
?>