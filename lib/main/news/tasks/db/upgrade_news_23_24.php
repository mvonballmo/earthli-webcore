<?php

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

This file is part of earthli News.

earthli News is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli News is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli News; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli News, visit:

http://earthli.com/software/webcore/app_news.php

****************************************************************************/

require_once ('webcore/init.php');
require_once ('webcore/tasks/db/upgrade_per_app_221_23.php');

class NEWS_23_24_MIGRATOR_TASK extends UPGRADE_PER_APP_221_23_TASK
{
  public $application_name = 'earthli News';
  public $version_from = '2.3.0';
  public $version_to = '2.4.0';

  protected function _execute ()
  {
    log_open_block ("Updating names [object => entry]...");
      $this->_query ("ALTER TABLE `news_subscriptions` CHANGE `watch_objects` `watch_entries` TINYINT( 4 ) UNSIGNED DEFAULT '0' NOT NULL");
      $this->_query ("ALTER TABLE `news_comments` CHANGE `object_id` `entry_id` INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL");
    log_close_block ();

    log_open_block ("Building news tree...");

      $this->_query ("CREATE TABLE `news_tree` (`parent_id` INT UNSIGNED NOT NULL ,`child_id` INT UNSIGNED NOT NULL)");

      $this->_query ("SELECT * from news_folders");
      $this->build_folder_roots ('news_tree');

    log_close_block ();
  }
}

?>