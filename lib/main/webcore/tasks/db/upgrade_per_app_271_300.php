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

class UPGRADE_PER_APP_271_300_TASK extends MIGRATOR_TASK
{
  protected function _update_history_type_for_drafts ($table_name)
  {
    $this->_query ("ALTER TABLE `news_history_items` CHANGE `kind` `kind` ENUM( 'Created', 'Updated', 'Deleted', 'Restored', 'Hidden', 'Hidden update', 'Locked', 'Published', 'Queued', 'Abandoned', 'Unpublished' ) NOT NULL");
  }

  protected function _update_draft_state ($table_name)
  {
    $this->_query ("UPDATE `$table_name` SET state = 50 WHERE state = 18");
    $this->_query ("UPDATE `$table_name` SET time_published='0000-00-00 00:00:00', publisher_id = 0 WHERE state = " . Draft);
    $this->_query ("UPDATE `$table_name` SET time_published='0000-00-00 00:00:00', publisher_id = 0 WHERE state = " . Abandoned);
    $this->_query ("UPDATE `$table_name` SET time_published='0000-00-00 00:00:00', publisher_id = 0 WHERE state = " . Queued);
  }

  protected function _update_folders ($table_name)
  {
    $this->_query ("ALTER TABLE `$table_name` CHANGE `summary` `summary` TEXT NULL DEFAULT NULL");
  }
}

?>