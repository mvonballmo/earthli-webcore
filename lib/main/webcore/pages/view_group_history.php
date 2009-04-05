<?php

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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

  $group_query = $App->group_query ();
  $group = $group_query->object_at_id (read_var ('id'));

  if (isset ($group))
  {
    $Page->title->add_object ($group);

    $Page->location->add_root_link ();
    $Page->location->append ('Groups', 'view_groups.php');
    $Page->location->add_object_link ($group);

    $history_item_query = $group->history_item_query ();
    $obj = $group;

    include_once ('webcore/pages/view_history.php');
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to view this group\'s history.');
  }
?>