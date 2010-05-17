<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage config
 * @version 3.3.0
 * @since 2.5.0
 */

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

/**
 * Names of the pages in this deployment.
 * The application uses the fields of this object when building links to
 * these pages. If a deployment's page names are different, update them
 * here to make the links work correctly.
 * @package webcore
 * @subpackage config
 * @version 3.3.0
 * @since 2.2.1
 */
class APPLICATION_PAGE_NAMES
{
  /**
   * @var string
   */
  public $folder_home = 'view_folder.php';

  /**
   * @var string
   */
  public $folder_permissions_home = 'view_folder_permissions.php';

  /**
   * @var string
   */
  public $folder_subscriptions_home = 'view_folder_subscriptions.php';

  /**
   * @var string
   */
  public $comment_home = 'view_comment.php';

  /**
   * @var string
   */
  public $entry_home = 'view_entry.php';

  /**
   * @var string
   */
  public $group_home = 'view_group.php';

  /**
   * @var string
   */
  public $user_home = 'view_user.php';

  /**
   * @var string
   */
  public $history_item_home = 'view_history_item.php';

  /**
   * @var string
   */
  public $search_home = 'view_search.php';

  /**
   * @var string
   */
  public $attachment_home = 'view_attachment.php';

  /**
   * @var string
   */
  public $user_subscriptions_home = 'view_user_subscriptions.php';

  /**
   * @var string
   */
  public $user_create = 'create_user.php';

  /**
   * @var string
   */
  public $log_in = 'log_in.php';

  /**
   * @var string
   */
  public $log_out = 'log_out.php';

  /**
   * @var string
   */
  public $offline = 'offline.php';

  /**
   * @var string
   */
  public $configure = 'configure.php';
}

/**
 * Names of the database tables in this deployment.
 * The application uses this object's fields when building SQL queries
 * against the database. If a deployment's table names are different (for
 * example, if working on a shared server with only one available database),
 * update them here to make the application access the correct data.
 * @package webcore
 * @subpackage config
 * @version 3.3.0
 * @since 2.2.1
 */
class APPLICATION_TABLE_NAMES
{
  /**
   * @var string
   */
  public $folders = 'folders';

  /**
   * @var string
   */
  public $entries = 'entries';

  /**
   * @var string
   */
  public $comments = 'comments';

  /**
   * @var string
   */
  public $user_permissions = 'user_permissions';

  /**
   * @var string
   */
  public $folder_permissions = 'folder_permissions';

  /**
   * @var string
   */
  public $subscriptions = 'subscriptions';

  /**
   * @var string
   */
  public $subscribers = 'subscribers';

  /**
   * @var string
   */
  public $history_items = 'history_items';

  /**
   * @var string
   */
  public $users = 'users';

  /**
   * @var string
   */
  public $groups = 'groups';

  /**
   * @var string
   */
  public $users_to_groups = 'users_to_groups';

  /**
   * @var string
   */
  public $icons = 'icons';

  /**
   * @var string
   */
  public $themes = 'themes';

  /**
   * @var string
   */
  public $searches = 'searches';

  /**
   * @var string
   */
  public $attachments = 'attachments';

  /**
   * @var string
   */
  public $versions = 'versions';
}

/**
 * @package webcore
 * @subpackage config
 * @version 3.3.0
 * @since 2.2.1
 */
class APPLICATION_DISPLAY_OPTIONS extends CONTEXT_DISPLAY_OPTIONS
{
  /**
   * Separator used when showing an object's location.
   * This location is within the WebCore hierarchy of objects.
   * @var string
   */
  public $obj_url_separator = '/';
  
  /**
   * Return the list of {@link COMMENT} icons.
   * @see add_comment_kind()
   * @see PROPERTY_VALUE
   * @return array[PROPERTY_VALUE]
   */
  public function comment_icons ()
  {
    if (! isset ($this->_comment_icons))
    {
      $this->_init_comment_icons ();
    }
    return $this->_comment_icons;
  }
  
  /**
   * Add a value for a {@link COMMENT::$kind}.
   * @param string $title
   * @param string $icon Location of an image.
   * @param integer $value Value stored in the database.
   */
  public function add_comment_icon ($value, $title, $icon)
  {
    include_once ('webcore/sys/property.php');
    $prop = new PROPERTY_VALUE ($this->context);
    $prop->value = $value;
    $prop->title = $title;
    $prop->icon = "{icons}comment/$icon";
    $this->_comment_icons [$value] = $prop;
  }

  /**
   * Initialize the initial list of comment icons.
   * Called from {@link comment_icons()}.
   * @access private
   */
  protected function _init_comment_icons ()
  {
    $this->add_comment_icon (0, 'Information', 'information');
    $this->add_comment_icon (1, 'Question', 'question');
    $this->add_comment_icon (2, 'Emphasis', 'emphasis');
    $this->add_comment_icon (3, 'Thumbs Up', 'thumbs_up');
    $this->add_comment_icon (4, 'Thumbs Down', 'thumbs_down');
    $this->add_comment_icon (5, 'Alert', 'warning');
    $this->add_comment_icon (6, 'Smiley', 'smiley');
    $this->add_comment_icon (7, 'Cool', 'cool');
    $this->add_comment_icon (8, 'Chagrined', 'chagrined');
    $this->add_comment_icon (9, 'Worried', 'worried');
    $this->add_comment_icon (10, 'Stern', 'stern');
    $this->add_comment_icon (11, 'Ecstatic', 'ecstatic');
    $this->add_comment_icon (12, 'Dubious', 'dubious');
    $this->add_comment_icon (13, 'Devil', 'devil');
    $this->add_comment_icon (14, 'Crying', 'crying');
    $this->add_comment_icon (15, 'Angry', 'angry');
    $this->add_comment_icon (16, 'Embarrassed', 'oops');
    $this->add_comment_icon (17, 'Confused', 'confused');
    $this->add_comment_icon (18, 'Idea', 'idea');
    $this->add_comment_icon (19, 'Neutral', 'neutral');
    $this->add_comment_icon (20, 'Sad', 'sad');
    $this->add_comment_icon (21, 'Smug', 'smug');
    $this->add_comment_icon (22, 'Stunned', 'stunned');
    $this->add_comment_icon (23, 'Wink', 'wink');
  }  

  /**
   * @see PROPERTY_VALUE
   * @var array[PROPERTY_VALUE]
   * @access private
   */
  protected $_comment_icons;
}

/**
 * @package webcore
 * @subpackage config
 * @version 3.3.0
 * @since 2.2.1
 */
class APPLICATION_MAIL_OPTIONS extends CONTEXT_MAIL_OPTIONS
{
  /**
   * @var string
   */
  public $publisher_user_name = 'auto-publisher';

  /**
   * Specifies how much of an entry to send by default.
   * Used when sending a single entry or when publishing.
   * @var integer
   */
  public $excerpt_length = 500;

  /**
   * How many emails can an anonymous user send an entry to?
   * Used only when sending entries directly.
   * @var integer
   */
  public $max_anonymous_recipients = 5;

  /**
   * How many emails can a registered user send an entry to?
   * Used only when sending entries directly.
   * @var integer
   */
  public $max_registered_recipients = 20;

  /**
   * Character used separate objects in titles.
   * @var string
   */
  public $object_separator = ' > ';
}

/**
 * @package webcore
 * @subpackage config
 * @version 3.3.0
 * @since 2.2.1
 */
class APPLICATION_USER_OPTIONS
{
  /**
   * @var boolean
   */
  public $users_can_be_deleted = false;

  /**
   * @var boolean
   */
  public $users_can_be_purged = false;

  /**
   * Can users modify their own content (regardless of their rights in this folder?
   * @var boolean
   */
  public $users_can_modify_own_content = true;

  /**
   * Can users delete their own content (regardless of their rights in this folder?
   * @var boolean
   */
  public $users_can_delete_own_content = true;

  /**
   * Can users purge their own content (regardless of their rights in this folder?
   * @var boolean
   */
  public $users_can_purge_own_content = false;

  /**
   * @var boolean
   */
  public $users_can_edit_self = true;

  /**
   * @var boolean
   */
  public $users_can_change_name = false;

  /**
   * @var boolean
   */
  public $passwords_are_case_sensitive = false;

  /**
   * @var integer
   */
  public $default_user_creator_id = 1;

  /**
   * @var integer
   */
  public $minimum_password_length = 5;
}

/**
 * @package webcore
 * @subpackage config
 * @version 3.3.0
 * @since 2.2.1
 */
class APPLICATION_STORAGE_OPTIONS extends CONTEXT_STORAGE_OPTIONS
{
  /**
   * Name of the cookie for storing login info.
   * 
   * @var string
   */
  public $login_user_name = 'login_user';

  /**
   * Name of the cookie for storing last known page.
   * 
   * @var string
   */
  public $return_to_page_name = 'return_to_page';

  /**
   * Number of days to store a login.
   * 
   * @var integer
   */
  public $login_duration = 180;
  
  /**
   * If true, login information is stored globally for all earthli applications.
   * If false, this application tracks its login information separately from
   * other earthli applications.
   * 
   * @var boolean
   */
  public $shared_login = true;
}

/**
 * @package webcore
 * @subpackage config
 */
class APPLICATION_ANON_OPTIONS
{
  /**
   * @var string
   */
  public $name_prefix = 'anon@';

  /**
   * @var string
   */
  public $name_suffix;

  /**
   * Shows the anonymous user's ip address in their name.
   * Only used when creating the user's name.
   * @var boolean
   */
  public $show_ip_address = true;

  /**
   * Reverse name lookup for anonymous user's ip address.
   * Use the raw ip address if 'show_ip_address' is true. Only used when
   * creating the user's name
   * @var boolean
   */
  public $resolve_host = true;
}

?>