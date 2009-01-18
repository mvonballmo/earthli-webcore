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

  $folder_query = $App->login->folder_query ();
  $folder =& $folder_query->object_at_id (read_var ('id'));

  if (isset ($folder) &&
      $App->login->is_allowed (Privilege_set_folder, Privilege_view, $folder) &&
      $App->login->is_allowed (Privilege_set_entry, Privilege_view, $folder))
  {
    include_once ('webcore/obj/object_list_builder.php');
    $object_list = new OBJECT_LIST_BUILDER ($folder);
    $object_list->load_from_request ();
    
    $Page->title->add_object ($folder);

    if ($object_list->has_entries ())
    {
      if (sizeof ($object_list->entries) == 1)
      {
        $Page->title->add_object ($object_list->entries [0]);
        $Page->title->subject = 'Print';
    ?>
    <h1><?php echo $App->title; ?></h1>
    <?php
      }
      else
      {
        $Page->title->subject = $object_list->description ();
    ?>
    <h1><?php echo $App->title . ' &mdash; ' . $object_list->description (); ?></h1>
    <?php
      }
      
      $Page->set_printable ();
      $Page->start_display ();

      $class_name = $App->final_class_name ('PRINT_PREVIEW', 'webcore/gui/print_preview.php');
      $preview = new $class_name ($App);

      $preview->show_users = read_var ('show_users');
      $preview->show_comments = read_var ('show_comments');

      $preview->display ($object_list->entries);
    }
    else
    {
      $Page->location->add_folder_link ($folder);
      $Page->location->append ($Page->title->subject);
      $Page->start_display ();
?>
<div class="error">
  You are have not selected any entries to print.
</div>
<?php
    }

    $Page->finish_display ();
  }
  else
    $Page->raise_security_violation ('You are not allowed to print these entries.', $folder);
?>