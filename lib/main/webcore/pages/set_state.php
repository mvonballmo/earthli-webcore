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

  /* Set these globals before using this template:

       $state: integer
  */

  $last_page = read_array_index ($_GET, 'last_page');
  $id = read_array_index ($_GET, 'id');

  if ($last_page && $id)
  {
    $entry_query = $App->login->all_entry_query ();
    $entry = $entry_query->object_at_id ($id);
  }
  
  if (isset ($entry))
  {
    $entry->set_state ($state, TRUE);
    $Env->redirect_root ($last_page);
  }
  else
  {
    $Page->start_display ();
    echo "<div class=\"error\">Could not set state.</div>";
    $Page->finish_display ();
  }
?>