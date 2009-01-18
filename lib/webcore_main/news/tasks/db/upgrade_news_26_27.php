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
require_once ('webcore/tasks/db/upgrade_per_app_25_26.php');

class NEWS_26_27_MIGRATOR_TASK extends UPGRADE_PER_APP_25_26_TASK
{
  var $application_name = 'earthli News';
  var $version_from = '2.6.0';
  var $version_to = '2.7.0';

  function _execute ()
  {
    log_open_block ("Adding owners to objects");
      $this->add_owner_to_table ('news_attachments');
      $this->add_owner_to_table ('news_comments');
      $this->add_owner_to_table ('news_articles');
      $this->add_owner_to_table ('news_folders');
    log_close_block ();

    $this->add_organizational ('news_folders');
    $this->add_version_info ('news_application', 'earthli News', '2.7.0');

    log_open_block ('Replacing obsolete tags');
      $this->_query ("UPDATE news_articles SET description=REPLACE(description, '{attachment}', '{att_link}') WHERE LOCATE('{attachment}', description) > 0;");
      $this->_query ("UPDATE news_articles SET description=REPLACE(description, '{thumbnail}', '{att_thumb}') WHERE LOCATE('{thumbnail}', description) > 0;");
      $this->_query ("UPDATE news_articles SET description=REPLACE(description, '<code>', '<c>') WHERE LOCATE('<code>', description) > 0;");
      $this->_query ("UPDATE news_articles SET description=REPLACE(description, '</code>', '</c>') WHERE LOCATE('</code>', description) > 0;");
    log_close_block ();
  }
}

?>