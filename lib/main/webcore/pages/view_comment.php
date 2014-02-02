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

  if (isset ($comment) && isset ($entry) && $App->login->is_allowed (Privilege_set_comment, Privilege_view, $comment))
  {
    $App->set_referer ();
    $App->set_search_text (read_var ('search_text'));

    $Page->title->add_object ($folder);
    $Page->title->add_object ($entry);
    $Page->title->subject = $comment->title_as_plain_text ();

    $Page->location->add_folder_link ($folder);
    $Page->location->add_object_link ($entry);
    $Page->location->add_object_text ($comment);

    $Page->start_display ();

    $class_name = $App->final_class_name ('COMMENT_LIST_RENDERER', 'webcore/gui/comment_renderer.php');
    /** @var $com_renderer COMMENT_LIST_RENDERER */
    $com_renderer = new $class_name ($com_query, $comment);

    $commands = $com_renderer->make_commands();
    if ($commands->num_executable_commands() > 0)
    {
  ?>
<div class="top-box">
  <div class="button-content">
    <span class="field"><?php echo $com_renderer->size(); ?></span> Replies
    <?php
    $menu = $App->make_menu();
    $menu->renderer->content_mode = Menu_show_as_buttons;
    $menu->display ();
    ?>
  </div>
</div>
  <?php
  }
  ?>
  <div class="main-box">
    <div class="grid-content">
      <?php $com_renderer->display (); ?>
    </div>
  </div>
  <?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to view this comment.', $folder);
  }
?>