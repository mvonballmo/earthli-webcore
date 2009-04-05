<?php

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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

  include ('projects/init.php');

  $Env->set_buffered (false);
  set_time_limit (600);
  $Page->start_display ();

  echo "<ul>";
  echo "<li>Adding publication state to releases table</li>";
  $Page->database->query ("ALTER TABLE project_releases ADD publication_state TINYINT UNSIGNED DEFAULT '0' NOT NULL");
  echo "</ul>";
?>