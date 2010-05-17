<?php

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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

class UPGRADE_WEBCORE_260_270_TASK extends MIGRATOR_TASK
{
  public $application_name = 'earthli WebCore';
  public $version_from = '2.6.0';
  public $version_to = '2.7.0';

  protected function _execute ()
  {
    if (! $this->db->table_exists ('versions'))
    {
      log_open_block ("Adding version table");
        $this->_query ("CREATE TABLE `versions` (`title` varchar(100) NOT NULL default '', `version` varchar(50) NOT NULL default '')");
        $this->_query ("ALTER TABLE `versions` ADD PRIMARY KEY ( `title` ); ");
      log_close_block ();
    }
    log_open_block ("Removing unneeded indexes");
      $this->_query ("ALTER TABLE `users` DROP INDEX `id`");
    log_close_block ();
    log_open_block ("Added fulltext indexes");
      $this->_query ("ALTER TABLE `users` ADD FULLTEXT (`title`)");
      $this->_query ("ALTER TABLE `users` ADD FULLTEXT (`description`)");
      $this->_query ("ALTER TABLE `users` ADD FULLTEXT (`real_first_name`)");
      $this->_query ("ALTER TABLE `users` ADD FULLTEXT (`real_last_name`)");
      $this->_query ("ALTER TABLE `users` ADD FULLTEXT (`home_page_url`)");
      $this->_query ("ALTER TABLE `users` ADD FULLTEXT (`email`)");
      $this->_query ("ALTER TABLE `users` ADD FULLTEXT (`picture_url`)");
      $this->_query ("ALTER TABLE `users` ADD FULLTEXT (`signature`)");
    log_close_block ();
  }
}

?>