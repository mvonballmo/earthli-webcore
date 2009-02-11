<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package news
 * @subpackage sys
 * @version 3.0.0
 * @since 2.4.0
 */

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

/** */
require_once ('webcore/sys/application.php');
require_once ('news/sys/news_type_infos.php');

/**
 * @package news
 * @subpackage sys
 */
class NEWS_APPLICATION_PAGE_NAMES extends APPLICATION_PAGE_NAMES
{
  /**
   * @var string
   */
  var $entry_home = 'view_article.php';
}

/**
 * @package news
 * @subpackage sys
 */
class NEWS_APPLICATION_TABLE_NAMES extends APPLICATION_TABLE_NAMES
{
  /**
   * @var string
   */
  var $folders = 'news_folders';
  /**
   * @var string
   */
  var $comments = 'news_comments';
  /**
   * @var string
   */
  var $entries = 'news_articles';
  /**
   * @var string
   */
  var $user_permissions = 'news_user_permissions';
  /**
   * @var string
   */
  var $folder_permissions = 'news_folder_permissions';
  /**
   * @var string
   */
  var $subscriptions = 'news_subscriptions';
  /**
   * @var string
   */
  var $subscribers = 'news_subscribers';
  /**
   * @var string
   */
  var $history_items = 'news_history_items';
  /**
   * @var string
   */
  var $searches = 'news_searches';
  /**
   * @var string
   */
  var $attachments = 'news_attachments';
}

/**
 * A WebCore application that lets users manage, edit and publish articles.
 * Includes a 'draft' mode for unfinished work.
 * @package news
 * @subpackage sys
 * @version 3.0.0
 * @since 2.4.0
 */
class NEWS_APPLICATION extends DRAFTABLE_APPLICATION
{
  /**
   * @var string
   */
  var $title = 'earthli News';
  /**
   * @var string
   */
  var $short_title = 'News';
  /**
   * @var string
   */
  var $icon = '{app_icons}app/news';
  /**
   * @var string
   */
  var $support_url = 'http://earthli.com/software/webcore/app_news.php';
  /**
   * Unique ID for this framework.
   * @var string
   */
  var $framework_id = 'com.earthli.news';
  /**
   * @var integer
   */
  var $version = '3.0.0';

  /**
   * @param PAGE &$page Page to which this application is attached.
   */
  function NEWS_APPLICATION (&$page)
  {
    APPLICATION::APPLICATION ($page);

    $this->set_path (Folder_name_application, '{' . Folder_name_apps . '}news');
    $this->set_path (Folder_name_attachments, '{' . Folder_name_data . '}news/attachments');

    $this->storage_options->return_to_page_name = 'news_page';
  }

  /**
   * Add classes to the {@link $classes} object factory.
   * @access private
   */
  function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();
    $this->register_class ('DRAFTABLE_ENTRY_TYPE_INFO', 'ARTICLE_TYPE_INFO');
    $this->register_class ('FOLDER_TYPE_INFO', 'SECTION_TYPE_INFO');
    $this->register_class ('ENTRY_GRID', 'ARTICLE_GRID', 'news/gui/article_grid.php');
    $this->register_class ('FOLDER_GRID', 'SECTION_GRID', 'news/gui/section_grid.php');
    $this->register_class ('ENTRY_SUMMARY_GRID', 'ARTICLE_SUMMARY_GRID', 'news/gui/article_grid.php');
    $this->register_class ('ENTRY_FORM', 'ARTICLE_FORM', 'news/forms/article_form.php');
    $this->register_class ('APPLICATION_TABLE_NAMES', 'NEWS_APPLICATION_TABLE_NAMES');
    $this->register_class ('APPLICATION_PAGE_NAMES', 'NEWS_APPLICATION_PAGE_NAMES');

    $this->register_handler ('FOLDER', Handler_commands, 'SECTION_COMMANDS', 'news/cmd/section_commands.php');
    $this->register_handler ('DRAFTABLE_ENTRY', Handler_commands, 'ARTICLE_COMMANDS', 'news/cmd/article_commands.php');

    $this->register_entry_class ('article', 'DRAFTABLE_ENTRY', 'webcore/obj/entry.php');
    $this->register_search ('article', 'ARTICLE', 'ARTICLE_SEARCH', 'news/obj/news_search.php');
  }

  /**
   * Name used for version information.
   * @return string
   */
  function name ()
  {
    return 'earthli News';
  }

  /**
   * The actual file system location of the application source.
   * Copy/paste to descendents to return the correct location.
   * @return string
   * @access private
   */
  function _source_path ()
  {
    return __FILE__;
  }
}

?>
