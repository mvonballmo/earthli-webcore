<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage sys
 * @version 3.4.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

This file is part of earthli Albums.

earthli Albums is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Albums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Albums; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Albums, visit:

http://www.earthli.com/software/webcore/albums

****************************************************************************/

/** */
require_once ('webcore/sys/application.php');
require_once ('albums/sys/album_type_infos.php');

/**
 * @package albums
 * @subpackage sys
 */
class ALBUM_APPLICATION_PAGE_NAMES extends APPLICATION_PAGE_NAMES
{
  /**
   * @var string
   */
  public $picture_home = 'view_picture.php';

  /**
   * @var string
   */
  public $journal_home = 'view_journal.php';
}

/**
 * @package albums
 * @subpackage sys
 */
class ALBUM_APPLICATION_TABLE_NAMES extends APPLICATION_TABLE_NAMES
{
  /**
   * @var string
   */
  public $folders = 'album_folders';

  /**
   * @var string
   */
  public $comments = 'album_comments';

  /**
   * @var string
   */
  public $entries = 'album_entries';

  /**
   * @var string
   */
  public $journals = 'album_journal';

  /**
   * @var string
   */
  public $pictures = 'album_pictures';

  /**
   * @var string
   */
  public $user_permissions = 'album_user_permissions';

  /**
   * @var string
   */
  public $folder_permissions = 'album_folder_permissions';

  /**
   * @var string
   */
  public $subscriptions = 'album_subscriptions';

  /**
   * @var string
   */
  public $subscribers = 'album_subscribers';

  /**
   * @var string
   */
  public $history_items = 'album_history_items';

  /**
   * @var string
   */
  public $searches = 'album_searches';

  /**
   * @var string
   */
  public $attachments = 'album_attachments';
}

/**
 * @package albums
 * @subpackage sys
 */
class ALBUM_APPLICATION_PICTURE_OPTIONS
{
  /**
   * @var integer
   */
  public $default_max_picture_height = 480;

  /**
   * @var integer
   */
  public $default_max_picture_width = 640;
}

/**
 * A WebCore application that lets users build albums of journals and pictures.
 * Automatically generates calendars and printouts.
 * @package albums
 * @subpackage sys
 * @version 3.4.0
 * @since 2.5.0
 */
class ALBUM_APPLICATION extends APPLICATION
{
  /**
   * @var string
   */
  public $title = 'earthli Albums';

  /**
   * @var string
   */
  public $short_title = 'Albums';

  /**
   * @var string
   */
  public $icon = '{app_icons}app/albums';

  /**
   * @var string
   */
  public $support_url = 'http://earthli.com/software/webcore/app_albums.php';

  /**
   * Unique ID for this framework.
   * @var string
   */
  public $framework_id = 'com.earthli.albums';

  /**
   * @var integer
   */
  public $version = '3.4.0';

  /**
   * @param PAGE $page Page to which this application is attached.
   */
  public function __construct ($page)
  {
    parent::__construct ($page);

    $this->set_path (Folder_name_application, '{' . Folder_name_apps . '}albums');
    $this->set_path (Folder_name_attachments, '{' . Folder_name_data . '}albums/attachments');

    $class_name = $this->final_class_name ('ALBUM_APPLICATION_PICTURE_OPTIONS');
    $this->picture_options = new $class_name ();
  }

  /**
   * Add classes to the {@link $classes} object factory.
   * @access private
   */
  protected function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();

    $this->register_class ('FOLDER', 'ALBUM', 'albums/obj/album.php');
    $this->register_class ('FOLDER_ENTRY_QUERY', 'ALBUM_ENTRY_QUERY', 'albums/db/album_entry_query.php');
    $this->register_class ('USER_ENTRY_QUERY', 'ALBUM_USER_ENTRY_QUERY', 'albums/db/album_user_entry_query.php');
    $this->register_class ('USER_FOLDER_QUERY', 'USER_ALBUM_QUERY', 'albums/db/user_album_query.php');
    $this->register_class ('USER_COMMENT_QUERY', 'USER_MULTI_TYPE_COMMENT_QUERY');
    $this->register_class ('FOLDER_COMMENT_QUERY', 'FOLDER_MULTI_TYPE_COMMENT_QUERY');
    $this->register_class ('FOLDER_GRID', 'ALBUM_GRID', 'albums/gui/album_grid.php');
    $this->register_class ('ENTRY_GRID', 'PICTURE_GRID', 'albums/gui/picture_grid.php', 'picture');
    $this->register_class ('ENTRY_GRID', 'JOURNAL_GRID', 'albums/gui/journal_grid.php', 'journal');
    $this->register_class ('ENTRY_FORM', 'PICTURE_FORM', 'albums/forms/picture_form.php', 'picture');
    $this->register_class ('FOLDER_FORM', 'ALBUM_FORM', 'albums/forms/album_form.php');
    $this->register_class ('ENTRY_FORM', 'JOURNAL_FORM', 'albums/forms/journal_form.php', 'journal');
    $this->register_class ('PURGE_OBJECT_FORM', 'PURGE_PICTURE_FORM', 'albums/forms/purge_picture_form.php', 'picture');
    $this->register_class ('MULTIPLE_OBJECT_MOVER_FORM', 'ALBUM_MULTIPLE_OBJECT_MOVER_FORM', 'albums/forms/album_multiple_object_mover_form.php');
    $this->register_class ('MULTIPLE_OBJECT_PRINTER_FORM', 'ALBUM_MULTIPLE_OBJECT_PRINTER_FORM', 'albums/forms/album_multiple_object_printer_form.php');
    $this->register_class ('MULTIPLE_OBJECT_PURGER_FORM', 'ALBUM_MULTIPLE_OBJECT_PURGER_FORM', 'albums/forms/album_multiple_object_purger_form.php');
    $this->register_class ('PRINT_PREVIEW', 'ALBUM_PRINT_PREVIEW', 'albums/gui/album_print_preview.php');
    $this->register_class ('ENTRY_SUMMARY_GRID', 'PICTURE_SUMMARY_GRID', 'albums/gui/picture_grid.php', 'picture');
    $this->register_class ('ENTRY_SUMMARY_GRID', 'JOURNAL_SUMMARY_GRID', 'albums/gui/journal_grid.php', 'journal');
    $this->register_class ('ENTRY_LIST', 'ALBUM_ENTRY_LIST', 'albums/gui/album_entry_list.php');
    $this->register_class ('APPLICATION_TABLE_NAMES', 'ALBUM_APPLICATION_TABLE_NAMES');
    $this->register_class ('APPLICATION_PAGE_NAMES', 'ALBUM_APPLICATION_PAGE_NAMES');
    $this->register_class ('CONTEXT_DISPLAY_OPTIONS', 'ALBUM_APPLICATION_DISPLAY_OPTIONS', 'albums/config/album_application_config.php');
    $this->register_class ('INDEX_PANEL_MANAGER', 'ALBUM_INDEX_PANEL_MANAGER', 'albums/gui/album_panel.php');
    $this->register_class ('FOLDER_PANEL_MANAGER', 'ALBUM_FOLDER_PANEL_MANAGER', 'albums/gui/album_panel.php');
    $this->register_class ('USER_PANEL_MANAGER', 'ALBUM_USER_PANEL_MANAGER', 'albums/gui/album_panel.php');

    $this->register_entry_class ('picture', 'PICTURE', 'albums/obj/picture.php');
    $this->register_entry_class ('journal', 'JOURNAL', 'albums/obj/journal.php');

    $this->register_search ('picture', 'PICTURE', 'PICTURE_SEARCH', 'albums/obj/album_search.php');
    $this->register_search ('journal', 'JOURNAL', 'JOURNAL_SEARCH', 'albums/obj/album_search.php');
  }

  /**
   * Name used for version information.
   * @return string
   */
  public function name ()
  {
    return 'earthli Albums';
  }

  /**
   * The actual file system location of the application source.
   * Copy/paste to descendents to return the correct location.
   * @return string
   * @access private
   */
  protected function _source_path ()
  {
    return __FILE__;
  }
}

?>
