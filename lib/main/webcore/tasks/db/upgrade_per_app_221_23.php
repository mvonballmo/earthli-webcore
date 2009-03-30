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

require_once ('webcore/db/migrator_task.php');

class UPGRADE_PER_APP_221_23_TASK extends MIGRATOR_TASK
{
  public function build_folder_roots ($table_name)
  {
    global $Page;

    $obj = null;
    $folders = array ();
    while ($Page->database->next_record ())
    {
      $obj->id = $Page->database->f ('id');
      $obj->parent_id = $Page->database->f ('parent_id');
      $folders [] = $obj;
      $folder_map [$obj->id] = $obj;
      unset($obj);
    }

    $i = 0;
    $c = sizeof ($folders);

    while ($i < $c)
    {
      $folder = $folders [$i];
      if ($folder->parent_id)
      {
        $parent = $folder_map [$folder->parent_id];
        $parent->children [] = $folder;
        $folder->parent = $parent;
      }
      else
      {
        $roots [] = $folder;
      }

      $i++;
    }

    build_folder_tree ($roots, $table_name);
  }

  public function build_folder_tree ($folders, $table_name)
  {
    if (sizeof ($folders))
    {
      foreach ($folders as $folder)
      {
        if (sizeof ($folder->children))
        {
          foreach ($folder->children as $child)
          {
            $parent = $folder;
            while ($child && $parent->id)
            {
              _query ("INSERT INTO $table_name (parent_id, child_id) VALUES ($parent->id, $child->id)");
              _query ("INSERT INTO $table_name (parent_id, child_id) VALUES ($parent->id, $child->id)");
              $parent = $parent->parent;
            }
          }
          build_folder_tree ($folder->children, $table_name);
        }
      }
    }
  }
}

?>