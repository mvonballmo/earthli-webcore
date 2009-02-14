<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage config
 * @version 3.0.0
 * @since 2.5.0
 */

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

/**
 * Names of the pages in this deployment.
 * The application uses the fields of this object when building links to
 * these pages. If a deployment's page names are different, update them
 * here to make the links work correctly.
 * @package webcore
 * @subpackage config
 * @version 3.0.0
 * @since 2.2.1
 */
class APPLICATION_PAGE_NAMES
{
  /**
   * @var string
   */
  var $folder_home = 'view_folder.php';
  /**
   * @var string
   */
  var $folder_permissions_home = 'view_folder_permissions.php';
  /**
   * @var string
   */
  var $folder_subscriptions_home = 'view_folder_subscriptions.php';
  /**
   * @var string
   */
  var $comment_home = 'view_comment.php';
  /**
   * @var string
   */
  var $entry_home = 'view_entry.php';
  /**
   * @var string
   */
  var $group_home = 'view_group.php';
  /**
   * @var string
   */
  var $user_home = 'view_user.php';
  /**
   * @var string
   */
  var $history_item_home = 'view_history_item.php';
  /**
   * @var string
   */
  var $search_home = 'view_search.php';
  /**
   * @var string
   */
  var $attachment_home = 'view_attachment.php';
  /**
   * @var string
   */
  var $user_subscriptions_home = 'view_user_subscriptions.php';
  /**
   * @var string
   */
  var $user_create = 'create_user.php';
  /**
   * @var string
   */
  var $log_in = 'log_in.php';
  /**
   * @var string
   */
  var $log_out = 'log_out.php';
  /**
   * @var string
   */
  var $offline = 'offline.php';
  /**
   * @var string
   */
  var $configure = 'configure.php';
}

/**
 * Names of the database tables in this deployment.
 * The application uses this object's fields when building SQL queries
 * against the database. If a deployment's table names are different (for
 * example, if working on a shared server with only one available database),
 * update them here to make the application access the correct data.
 * @package webcore
 * @subpackage config
 * @version 3.0.0
 * @since 2.2.1
 */
class APPLICATION_TABLE_NAMES
{
  /**
   * @var string
   */
  var $folders = 'folders';
  /**
   * @var string
   */
  var $entries = 'entries';
  /**
   * @var string
   */
  var $comments = 'comments';
  /**
   * @var string
   */
  var $user_permissions = 'user_permissions';
  /**
   * @var string
   */
  var $folder_permissions = 'folder_permissions';
  /**
   * @var string
   */
  var $subscriptions = 'subscriptions';
  /**
   * @var string
   */
  var $subscribers = 'subscribers';
  /**
   * @var string
   */
  var $history_items = 'history_items';
  /**
   * @var string
   */
  var $users = 'users';
  /**
   * @var string
   */
  var $groups = 'groups';
  /**
   * @var string
   */
  var $users_to_groups = 'users_to_groups';
  /**
   * @var string
   */
  var $icons = 'icons';
  /**
   * @var string
   */
  var $themes = 'themes';
  /**
   * @var string
   */
  var $searches = 'searches';
  /**
   * @var string
   */
  var $attachments = 'attachments';
  /**
   * @var string
   */
  var $versions = 'versions';
}

/**
 * @package webcore
 * @subpackage config
 * @version 3.0.0
 * @since 2.2.1
 */
class APPLICATION_DISPLAY_OPTIONS extends CONTEXT_DISPLAY_OPTIONS
{
  /**
   * Separator used when showing an object's location.
   * This location is within the WebCore hierarchy of objects.
   * @var string
   */
  var $obj_url_separator = '/';
  
  /**
   * Return the list of {@link COMMENT} icons.
   * @see add_comment_kind()
   * @see PROPERTY_VALUE
   * @return array[PROPERTY_VALUE]
   */
  function comment_icons ()
  {
    if (! isset ($this->_comment_icons))
      $this->_init_comment_icons ();
    return $this->_comment_icons;
  }
  
  /**
   * Add a value for a {@link COMMENT::$kind}.
   * @param string $title
   * @param string $icon Location of an image.
   * @param integer $value Value stored in the database.
   */
  function add_comment_icon ($value, $title, $icon)
  {
    include_once ('webcore/sys/property.php');
    $prop = new PROPERTY_VALUE ($this->context);
    $prop->value = $value;
    $prop->title = $title;
    $prop->icon = "{icons}comment/$icon";
    $this->_comment_icons [$value] =& $prop;
  }

  /**
   * Initialize the initial list of comment icons.
   * Called from {@link comment_icons()}.
   * @access private
   */
  function _init_comment_icons ()
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
  var $_comment_icons;
}

/**
 * @package webcore
 * @subpackage config
 * @version 3.0.0
 * @since 2.2.1
 */
class APPLICATION_MAIL_OPTIONS extends CONTEXT_MAIL_OPTIONS
{
  /**
   * @var string
   */
  var $publisher_user_name = 'auto-publisher';
  /**
   * Specifies how much of an entry to send by default.
    * Used when sending a single entry or when publishing.
    * @var integer
    */
  var $excerpt_length = 500;
  /**
   * How many emails can an anonymous user send an entry to?
    * Used only when sending entries directly.
    * @var integer
    */
  var $max_anonymous_recipients = 5;
  /**
   * How many emails can a registered user send an entry to?
    * Used only when sending entries directly.
    * @var integer
    */
  var $max_registered_recipients = 20;
  /**
   * Character used separate objects in titles.
   * @var string
   */
  var $object_separator = ' > ';
}

/**
 * @package webcore
 * @subpackage config
 * @version 3.0.0
 * @since 2.2.1
 */
class APPLICATION_USER_OPTIONS
{
  /**
   * @var boolean
   */
  var $users_can_be_deleted = FALSE;
  /**
   * @var boolean
   */
  var $users_can_be_purged = FALSE;
  /**
   * Can users modify their own content (regardless of their rights in this folder?
    * @var boolean
    */
  var $users_can_modify_own_content = TRUE;
  /**
   * Can users delete their own content (regardless of their rights in this folder?
    * @var boolean
    */
  var $users_can_delete_own_content = TRUE;
  /**
   * Can users purge their own content (regardless of their rights in this folder?
    * @var boolean
    */
  var $users_can_purge_own_content = FALSE;
  /**
   * @var boolean
   */
  var $users_can_edit_self = TRUE;
  /**
   * @var boolean
   */
  var $users_can_change_name = FALSE;
  /**
   * @var boolean
   */
  var $passwords_are_case_sensitive = FALSE;
  /**
   * @var integer
   */
  var $default_user_creator_id = 1;
  /**
   * @var integer
   */
  var $minimum_password_length = 5;
}

/**
 * @package webcore
 * @subpackage config
 * @version 3.0.0
 * @since 2.2.1
 */
class APPLICATION_STORAGE_OPTIONS extends CONTEXT_STORAGE_OPTIONS
{
  /**
   * Name of the cookie for storing login info.
   * @var string
   */
  var $login_user_name = 'login_user';
  /**
   * Name of the cookie for storing last known page.
   * @var string
   */
  var $return_to_page_name = 'return_to_page';
  /**
   * Number of days to store a login.
   * @var integer
   */
  var $login_duration = 180;
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
  var $name_prefix = 'anon@';
  /**
   * @var string
   */
  var $name_suffix;
  /**
   * Shows the anonymous user's ip address in their name.
   * Only used when creating the user's name.
   * @var boolean
   */
  var $show_ip_address = TRUE;
  /**
   * Reverse name lookup for anonymous user's ip address.
   * Use the raw ip address if 'show_ip_address' is true. Only used when
   * creating the user's name
   * @var boolean
   */
  var $resolve_host = TRUE;
}

?>