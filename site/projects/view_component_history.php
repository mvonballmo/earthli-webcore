<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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

  require_once ('projects/start.php');

  $id = read_var ('id');
  $folder_query = $App->login->folder_query ();  
  $folder =& $folder_query->folder_for_component_at_id ($id);

  if (isset ($folder))
  {
    $comp_query = $folder->component_query ();
    $comp =& $comp_query->object_at_id ($id);
  }
  
  if (isset ($comp))
  {
    $Page->title->add_object ($folder);
    $Page->title->add_object ($comp);
    
    $Page->location->add_folder_link ($folder, "panel=components");
    $Page->location->add_object_link ($comp);
          
    $history_item_query = $comp->history_item_query ();
    $obj =& $comp;

    include_once ('webcore/pages/view_history.php');
  }
  else
    $Page->raise_security_violation ('You are not allowed to view this component\'s history.', $folder);
?>