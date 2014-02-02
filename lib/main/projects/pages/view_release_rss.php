<?php

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

  $id = read_var ('id');
  $folder_query = $App->login->folder_query ();
  $folder = $folder_query->folder_for_release_at_id ($id);

  if (isset ($folder))
  {
    $rel_query = $folder->release_query ();
    $release = $rel_query->object_at_id ($id);
  }

  if (isset ($release) && $App->login->is_allowed (Privilege_set_release, Privilege_view, $release))
  {
    $branch = $release->branch ();

    $class_name = $App->final_class_name ('NEWSFEED_ENGINE', 'webcore/config/newsfeed_engine.php', 'index');
    $newsfeed_engine = new $class_name ($App);

    $newsfeed = $newsfeed_engine->make_renderer ($folder);
    $newsfeed->title->add_object ($folder);
    $newsfeed->title->add_object ($branch);
    $newsfeed->title->add_object ($release);
    $newsfeed->display ($release->entry_query ());
  }
?>