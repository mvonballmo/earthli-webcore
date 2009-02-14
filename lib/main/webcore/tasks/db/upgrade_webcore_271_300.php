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

require_once ('webcore/init.php');
require_once ('webcore/db/migrator_task.php');

class UPGRADE_WEBCORE_271_300_TASK extends MIGRATOR_TASK
{
  var $application_name = 'earthli WebCore';
  var $version_from = '2.7.0';
  var $version_to = '3.0.0';

  function _execute ()
  {
    if ($this->db->table_exists ('themes'))
    {
      log_open_block ("Updating default themes");
        $this->_query ("UPDATE `themes` SET main_CSS_file_name=REPLACE(main_CSS_file_name, '{styles}themes', '{themes}');");
      log_close_block ();
    }
  }
}

?>