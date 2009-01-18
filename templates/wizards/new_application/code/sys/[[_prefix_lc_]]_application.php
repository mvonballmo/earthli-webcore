<?php

/**
 * @copyright Copyright (c) 2006 [[_author_name_]]
 * @author [[_author_name_]] <[[_author_email_]]>
 * @filesource
 * @package [[_app_name_]]
 * @subpackage pages
 * @version 1.0.0
 * @since 1.0.0
 */

/****************************************************************************

Copyright (c) 2006 [[_author_name_]]

This file is part of [[_app_title_]].

[[_app_title_]] is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

[[_app_title_]] is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with [[_app_title_]]; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the [[_app_title_]], visit:

[[_app_url_]]

****************************************************************************/

/** */
require_once ('webcore/sys/application.php');
require_once ('[[_app_folder_]]/sys/[[_prefix_lc_]]_type_infos.php');

/** @package [[_app_name_]]
 * @subpackage sys
 * @version 1.0.0
 * @since 1.0.0 */
class [[_prefix_uc_]]_APPLICATION_TABLE_NAMES extends APPLICATION_TABLE_NAMES
{
  /** @var string */
  var $folders = '[[_prefix_lc_]]_folders';
  /** @var string */
  var $comments = '[[_prefix_lc_]]_comments';
  /** @var string */
  var $entries = '[[_prefix_lc_]]_entries';
  /** @var string */
  var $user_permissions = '[[_prefix_lc_]]_user_permissions';
  /** @var string */
  var $folder_permissions = '[[_prefix_lc_]]_folder_permissions';
  /** @var string */
  var $subscriptions = '[[_prefix_lc_]]_subscriptions';
  /** @var string */
  var $subscribers = '[[_prefix_lc_]]_subscribers';
  /** @var string */
  var $history_items = '[[_prefix_lc_]]_history_items';
  /** @var string */
  var $searches = '[[_prefix_lc_]]_searches';
  /** @var string */
  var $attachments = '[[_prefix_lc_]]_attachments';
}

/** A WebCore application that lets users enter {@link [[_entry_name_uc_]]}s.
 * @package [[_app_name_]]
 * @subpackage sys
 * @version 1.0.0
 * @since 1.0.0 */
class [[_prefix_uc_]]_APPLICATION extends APPLICATION
{
  /** @var string */
  var $title = '[[_app_title_]]';
  /** @var string */
  var $short_title = '[[_app_name_]]';
  /** @var string */
  var $icon = '{app_icons}app/[[_app_name_]]';
  /** @var string */
  var $support_url = '[[_app_url_]]';
  /** Unique ID for this framework.
   * @var string */
  var $framework_id = 'com.earthli.[[_app_name_]]';
  /** @var integer */
  var $version = '1.0.0';

  /** @param PAGE &$page Page to which this application is attached.
   * @access private */
  function &RECIPE_APPLICATION (&$page)
  {
    APPLICATION::APPLICATION ($page);

    $this->set_path (Folder_name_application, '{' . Folder_name_pages . '}[[_app_folder_]]');
    $this->storage_options->return_to_page_name = '[[_app_name_]]_page';
  }

  /** Add classes to the {@link $classes} object factory.
   * @access private */
  function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();

    $this->register_entry_class ('[[_entry_name_lc_]]', 'ENTRY', 'webcore/obj/entry.php');

    $this->register_class ('APPLICATION_TABLE_NAMES', '[[_prefix_uc_]]_APPLICATION_TABLE_NAMES');

//    $this->register_class ('ENTRY', '[[_prefix_uc_]]', '[[_app_folder_]]/obj/[[_entry_name_lc_]].php');
//    $this->register_class ('FOLDER_ENTRY_QUERY', 'FOLDER_[[_entry_name_uc_]]_QUERY', '[[_app_folder_]]/db/folder_[[_entry_name_lc_]]_query.php');
//    $this->register_class ('ENTRY_GRID', '[[_entry_name_uc_]]_GRID', '[[_app_folder_]]/gui/[[_entry_name_lc_]]_grid.php');
//    $this->register_class ('ENTRY_FORM', '[[_entry_name_uc_]]_FORM', '[[_app_folder_]]/forms/[[_entry_name_lc_]]_form.php');
//    $this->register_class ('ENTRY_SUMMARY_GRID', '[[_entry_name_uc_]]_SUMMARY_GRID', '[[_app_folder_]]/gui/[[_entry_name_lc_]]_grid.php');

//    $this->register_handler ('FOLDER', Handler_commands, '[[_folder_name_uc_]]_COMMANDS', 'recipes/cmd/[[_folder_name_lc_]]_commands.php');

//    $this->register_search ('[[_entry_name_lc_]]', '[[_entry_name_uc_]]', '[[_entry_name_uc_]]_SEARCH', '[[_app_folder_]]/obj/[[_entry_name_lc_]]_search.php');
  }
  
  /** The actual file system location of the application source.
   * Copy/paste to descendents to return the correct location.
   * @return string
   * @access private */
  function _source_path ()
  {
    return __FILE__;
  }
}

?>
