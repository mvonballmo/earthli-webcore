<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/tasks/db/upgrade_per_app_270_271.php');

class NEWS_280_281_MIGRATOR_TASK extends UPGRADE_PER_APP_270_271_TASK
{
  public $application_name = 'com.earthli.news';
  public $version_from = '2.8.0';
  public $version_to = '2.8.1';

  function _execute ()
  {
    log_open_block ("Updating fields");
      $this->_update_history_type_for_drafts ('news_history_items');
      $this->_update_draft_state ('news_articles');
    log_close_block ();
  }
}

?>