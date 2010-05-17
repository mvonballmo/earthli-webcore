<?php

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
require_once ('webcore/tasks/db/upgrade_per_app_260_270.php');

class NEWS_270_280_MIGRATOR_TASK extends UPGRADE_PER_APP_260_270_TASK
{
  public $application_name = 'earthli News';
  public $version_from = '2.7.0';
  public $version_to = '2.8.0';

  protected function _execute ()
  {
    log_open_block ("Cleaning up indexes");
      $this->clean_up_folder_permissions ('news_folder_permissions');
      $this->clean_up_user_permissions ('news_user_permissions');
      $this->clean_up_id_indexes ('news_articles');
      $this->clean_up_id_indexes ('news_folders');
      $this->clean_up_id_indexes ('news_comments');
      $this->clean_up_subscriptions ('news_subscriptions');
      $this->add_full_text_to_comments ('news_comments');
      $this->add_folder_id_index ('news_articles');
    log_close_block ();
    log_open_block ("Renaming subscriber fields");
      $this->rename_subscriber_fields ('news_subscribers');
      $this->rename_action_table ('news_actions', 'news_history_items');
    log_close_block ();    
  }
}

?>