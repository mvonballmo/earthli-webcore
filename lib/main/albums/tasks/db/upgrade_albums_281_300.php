<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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

require_once ('webcore/init.php');
require_once ('webcore/tasks/db/upgrade_per_app_271_300.php');

class ALBUMS_280_290_MIGRATOR_TASK extends UPGRADE_PER_APP_271_300_TASK
{
  public $application_name = 'earthli Albums';
  public $version_from = '2.9.0';
  public $version_to = '3.0.0';

  protected function _execute ()
  {
    log_open_block ("Updating summary in folders");
      $this->_update_folders ('album_folders');
    log_close_block ();
  }
}

?>