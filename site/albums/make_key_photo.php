<?php

/****************************************************************************

Copyright (c) 2014 Marco Von Ballmoos

This file is part of earthli Albums.

earthli Albums is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Albums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Albums; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Albums, visit:

http://www.earthli.com/software/webcore/albums

 ****************************************************************************/

require_once ('albums/start.php');

$id = read_var ('id');
$last_page = read_array_index($_GET, 'last_page');

if ($id)
{
  $query = $App->login->all_entry_query ();
  /** @var $obj ENTRY */
  $obj = $query->object_at_id ($id);
}

if ($id && $last_page && $obj)
{
  /** @var ALBUM $folder */
  $folder = $obj->parent_folder();
  $Page->location->add_folder_link($folder);
  $Page->location->add_object_link($obj);

  $history_item = $folder->new_history_item ();
  $folder->main_picture_id = $id;
  $folder->store_if_different ($history_item);

  $Env->redirect_root($last_page);
}
else
{
  $Page->start_display();
  echo "<div class=\"error\">Could not set key photo to photo with id = [$id].</div>";
  $Page->finish_display();
}