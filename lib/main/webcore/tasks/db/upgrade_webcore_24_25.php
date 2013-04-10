<?php

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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

class UPGRADE_WEBCORE_24_25_TASK extends MIGRATOR_TASK
{
  public $application_name = 'earthli WebCore';
  public $version_from = '2.4.0';
  public $version_to = '2.5.0';

  protected function _execute ()
  {
    log_open_block ("Updating users table");
      $this->_query ("ALTER TABLE `users` ADD `kind` ENUM( 'anonymous', 'registered' ) DEFAULT 'anonymous' NOT NULL");
      $this->_query ("UPDATE `users` SET kind = 'registered' WHERE type = 4;");
      $this->_query ("ALTER TABLE `users` DROP `type`, DROP `icon_id`");
      $this->_query ('DROP TABLE `user_icons`');
    log_close_block ();
  }
}

?>