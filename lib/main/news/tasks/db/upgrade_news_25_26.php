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
require_once ('webcore/tasks/db/upgrade_per_app_24_25.php');

class NEWS_25_26_MIGRATOR_TASK extends UPGRADE_PER_APP_24_25_TASK
{
  public $application_name = 'earthli News';
  public $version_from = '2.5.0';
  public $version_to = '2.6.0';

  function _execute ()
  {
    $this->add_attachments ('news_attachments', 'news_actions');

    $this->update_actions ('news_actions', 'news_comments', 'news_folders', 'news_articles');
    $this->update_subscriptions ('news_subscribers', 'news_subscriptions');
    $this->update_permissions ('news_folder_permissions', 'news_user_permissions');
    $this->update_drafting ('news_articles');
    $this->update_folders ('news_folders');
    $this->update_users ('news_user_permissions');

    log_open_block ('Replacing obsolete tags');
      $this->_query ("UPDATE news_articles SET description=REPLACE(description, '<h3>', '<h>') WHERE LOCATE('<h3>', description) > 0;");
      $this->_query ("UPDATE news_articles SET description=REPLACE(description, '</h3>', '</h>') WHERE LOCATE('</h3>', description) > 0;");
      $this->_query ("UPDATE news_articles SET description=REPLACE(description, '</h4>', '</h>') WHERE LOCATE('</h4>', description) > 0;");
      $this->_query ("UPDATE news_articles SET description=REPLACE(description, '<h4>', '<h level=\"4\">') WHERE LOCATE('<h4>', description) > 0;");
    log_close_block ();
  }
}

?>