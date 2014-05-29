<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.5.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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

define ('User_email_hidden', 'hidden');
define ('User_email_scrambled', 'scrambled');
define ('User_email_visible', 'visible');

/** */
require_once ('webcore/obj/content_object.php');

/**
 * A user in a WebCore {@link APPLICATION}.
 * @package webcore
 * @subpackage obj
 * @version 3.5.0
 * @since 2.2.1
 */
class USER extends CONTENT_OBJECT
{
  /**
   * @var string
   */
  public $password = '';

  /**
   * @var string
   */
  public $real_first_name = '';

  /**
   * @var string
   */
  public $real_last_name = '';

  /**
   * @var string
   */
  public $home_page_url = '';

  /**
   * @var string
   */
  public $email = '';

  /**
   * @var string
   */
  public $picture_url = '';

  /**
   * @var string
   */
  public $signature = '';

  /**
   * Location of user's icon.
   * Use {@link icon_as_html()} or {@link expanded_icon_url()} to access this property.
   * @var integer
   */
  public $icon_url = '';

  /**
   * @var string
   * @access private
   */
  public $ip_address;

  /**
   * Determines how email is displayed in the interface.
   * Can be {@link User_email_hidden}, {@link User_email_scrambled} or 
   * {@link User_email_visible}.
   * @var boolean
   */
  public $email_visibility = User_email_scrambled;

  /**
   * Was this user logged in by the system?
   * If the system needs to create a user when there is no login, it must ad-hoc load a special
   * user and use that to create the new user (using it as the creator). Such a user should never
   * be used for further validation, since it may have far too many rights.
   * @var boolean
   */
  public $ad_hoc_login = false;

  /**
   * Icon, rendered as HTML.
   * The requested size can also be given, which is either used to retrieve the image or used in the HTML.
   * @var string $size
   * @return string
   */
  public function icon_as_html ($size = Thirty_two_px)
  {
    return $this->app->resolve_icon_as_html($this->icon_url, ' ', $size);
  }

  /**
   * Signature transformed into HTML.
   * If no specific munger is provided, the one from {@link html_formatter()} is used.
   * @param HTML_MUNGER $munger
   * @return string
   */
  public function signature_as_html ($munger = null)
  {
    return $this->_text_as_html ($this->signature, $munger);
  }

  /**
   * Signature transformed into formatted plain text.
   * If no specific munger is provided, the one from {@link plain_text_formatter()} is used.
   * @param PLAIN_TEXT_MUNGER $munger
   * @return string
   */
  public function signature_as_plain_text ($munger = null)
  {
    return $this->_text_as_plain_text ($this->signature, $munger);
  }
  
  /**
   * Return a description of the email.
   * This varies depending on {@link $email_visibility}.
   * @return string
   */
  public function email_as_text ()
  {
    if (! $this->email)
    {
      $Result = '[none]';
    }
    else
    {
      switch ($this->email_visibility)
      {
      case User_email_hidden:
        $Result = '[hidden]';
        break;
      case User_email_scrambled:
        $Result = scramble_email ($this->email);
        break;
      case User_email_visible:
        $Result = $this->email;
        break;
      default:
      $Result = '[none]';
      }
    }    
    return $Result;
  }
  
  /**
   * Fully-qualified URl for the user picture. 
   * @return string
   */
  public function full_picture_url ()
  {
    return $this->context->resolve_file ($this->picture_url);
  }

  /**
   * Is the requested action allowed for this user?
   * If the permission applies to content, the "obj" cannot be empty. Owner-
   * dependent permissions use the {@link OBJECT_IN_FOLDER::owner()} to
   * determine the full permission. {@link APPLICATION_USER_OPTIONS} defines a
   * few settings that control which permissions are owner-dependent. If the
   * permission set is {@link Privilege_set_user}, the "obj" must be a {@link
   * USER} instead.
   * @param string $set_name Check this set of permissions.
   * @param integer $type Check this permission (or permissions).
   * @param OBJECT_IN_FOLDER|USER $obj
   * @see OBJECT_IN_FOLDER
   * @return boolean
   */
  public function is_allowed ($set_name, $type, $obj = null)
  {
    $this->assert (! $this->ad_hoc_login, 'Cannot use an ad-hoc login.', 'is_allowed', 'USER');

    $user_options = $this->app->user_options;
    $user_permissions = $this->permissions ();

    if ($user_permissions->global_privileges->supports ($set_name))
    {
      $Result = $user_permissions->global_privileges->enabled ($set_name, $type);

      if ($set_name == Privilege_set_user)
      {
        switch ($type)
        {
        case Privilege_view:
          if ($obj)
          {
            $Result = $Result || ($obj->equals ($this));
          }
          break;
        case Privilege_modify:
          $Result = $Result || ($user_options->users_can_edit_self && $obj->equals ($this));
          break;
        }
      }

      if ($set_name == Privilege_set_global)
      {
        switch ($type)
        {
        case Privilege_subscribe:
        case Privilege_password:
          if ($obj)
          {
            $Result = $Result || ($obj->equals ($this));
          }
          break;
        }
      }
    }
    else
    {
      if ($user_permissions->allow_privileges->enabled ($set_name, $type))
      {
        $Result = true;
      }
      else if ($user_permissions->deny_privileges->enabled ($set_name, $type))
      {
       $Result = false;
      }
      else
      {
        /** @var FOLDER $folder */
        $folder = $obj->security_context ();
        $folder_permissions = $folder->permissions ();
        $Result = $folder_permissions->enabled ($set_name, $type);
        if (! $Result)
        {
          /** @var USER $owner */
          $owner = $obj->owner ();
          switch ($type)
          {
          case Privilege_view:
          case Privilege_view_history:
            $Result |= $owner->equals ($this);
            break;
          case Privilege_modify:
            $Result |= ($user_options->users_can_modify_own_content && $owner->equals ($this));
            break;
          case Privilege_delete:
            $Result |= ($user_options->users_can_delete_own_content && $owner->equals ($this));
            break;
          case Privilege_purge:
            $Result |= ($user_options->users_can_purge_own_content && $owner->equals ($this));
            break;
          }
        }
      }
    }

    return $Result;
  }

  /**
   * List of searches available.
   * @return SEARCH_QUERY
   */
  public function search_query ()
  {
    $class_name = $this->app->final_class_name ('SEARCH_QUERY', 'webcore/db/search_query.php');
    /** @var SEARCH_QUERY $Result */
    $Result = new $class_name ($this->app);
    $Result->restrict ("user_id = $this->id");
    return $Result;
  }

  /**
   * List of groups for this user.
   * @return GROUP_QUERY
   */
  public function group_query ()
  {
    $class_name = $this->app->final_class_name ('USER_GROUP_QUERY', 'webcore/db/group_query.php');
    /** @var USER_GROUP_QUERY $Result */
    $Result = new $class_name ($this->app);
    $Result->restrict ("utog.user_id = $this->id");
    return $Result;
  }

  /**
   * Permissions applicable to this user.
   * @return USER_PERMISSIONS
   */
  public function permissions ()
  {
    $this->assert (isset ($this->_permissions), "Permissions not loaded for [$this->title].", 'permissions', 'USER');
    return $this->_permissions;
  }

  /**
   * @param string $p
   * @return bool
   * @access private
   */
  public function password_matches ($p)
  {
    if (! $this->app->user_options->passwords_are_case_sensitive)
    {
      $p = strtolower ($p);
    }
    return strcmp ($this->password, md5 ($p)) == 0;
  }

  /**
   * @return boolean
   */
  public function is_anonymous ()
  {
    return $this->kind != Privilege_kind_registered;
  }

  /**
   * @param USER $u
   * @return bool
   * @access private
   */
  public function equals ($u)
  {
    return $u->id == $this->id;
  }

  /**
   * A string representing a link to this object's url.
   * Uses the user-defined object home page and user-defined maximum title length.
   * @param TITLE_FORMATTER $formatter Optional formatter to use.
   * @return string
   */
  public function title_as_link ($formatter = null)
  {
    if (($this->id != Non_existent_user_id) && $this->login->is_allowed (Privilege_set_user, Privilege_view, $this))
    {
      return parent::title_as_link ($formatter);
    }

    return $this->title_as_html ($formatter);
  }

  /**
   * @param string $p
   * @access private
   */
  public function set_password ($p)
  {
    if (! $this->app->user_options->passwords_are_case_sensitive)
    {
      $p = strtolower ($p);
    }
    $this->password = md5 ($p);
  }

  /**
   * Create a new folder rooted at 'parent'.
   * Does not store to the database.
   * @param FOLDER $parent
   * @return FOLDER
   * @access private
   */
  public function new_folder ($parent)
  {
    $Result = $this->app->new_folder ();
    $Result->creator_id = $this->id;
    $Result->owner_id = $this->id;

    if ($parent)
    {
      $Result->parent_id = $parent->id;
      $Result->root_id = $parent->root_id;
      $Result->state = $parent->state;
      $Result->permissions_id = $parent->permissions_id;
    }
    else
    {
      $Result->parent_id = 0;
      $Result->root_id = 0;
      $Result->state = Visible;
      $Result->permissions_id = 0;
    }

    return $Result;
  }

  /**
   * Build as complete a name as possible from the name fields.
   * @param boolean $user_name_is_default Return the user name if no other names exist. If false, returns 'N/A' if no other names are found.
   * @return string
   */
  public function real_name ($user_name_is_default = false)
  {
    if ($this->is_anonymous ())
    {
      if ($user_name_is_default)
      {
        return $this->title;
      }

      return '(N/A)';
    }
    else
    {
      if ($this->real_last_name)
      {
        if ($this->real_first_name)
        {
          $Result = $this->real_first_name . " " . $this->real_last_name;
        }
        else
        {
          $Result = $this->real_last_name;
        }
      }
      else if ($this->real_first_name)
 {
   $Result = $this->real_first_name;
 }

      if (! isset ($Result))
      {
        if ($user_name_is_default)
        {
          $Result = $this->title;
        }
        else
        {
          $Result = '(withheld)';
        }
      }

      return $Result;
    }
  }

  /**
   * All folders viewable by this user.
   * @return USER_FOLDER_QUERY
   */
  public function folder_query ()
  {
    if (! isset ($this->_folder_query))
    {
      $class_name = $this->app->final_class_name ('USER_FOLDER_QUERY', 'webcore/db/user_folder_query.php');
      return new $class_name ($this);
    }

    return $this->_folder_query;
  }

  /**
   * Get the folder for this id.
   * Looks in a local cache, so it can be more effecient than checking
   * {@link folder_query()} directly. If True, raises an exception if the
   * folder is not found. If False, records a warning.
   * @param integer $id
   * @param boolean $can_fail
   * @return FOLDER
   */
  public function folder_at_id ($id, $can_fail = false)
  {
    if ($id)
    {
      $cache = $this->folder_cache ();
      $Result = $cache->object_at_id ($id);
      
      if (isset ($Result) || ! $Result)
      {
        return $Result; 
      }
    }

    $msg = "Folder for id [$id] not found.";
    if ($can_fail)
    {
      log_message ($msg, Msg_type_debug_info, Msg_channel_system);
    }
    else
    {
      $this->raise ($msg, 'folder_at_id', 'USER');
    }
    
    return null;
  }

  /**
   * Creates the folder cache on demand and returns it.
   * @return QUERY_BASED_CACHE
   * @access private
   */
  public function folder_cache ()
  {
    if (! isset ($this->_folder_cache))
    {
      include_once ('webcore/db/query.php');
      $this->_folder_cache = new QUERY_BASED_CACHE ($this->folder_query ());
    }
    return $this->_folder_cache;
  }

  /**
   * All comments viewable by this user.
   * @return USER_COMMENT_QUERY
   */
  public function all_comment_query ()
  {
    $class_name = $this->app->final_class_name ('USER_COMMENT_QUERY', 'webcore/db/user_comment_query.php');
    return new $class_name ($this);
  }

  /**
   * All comments viewable by this user, created by this user.
   * @return USER_COMMENT_QUERY
   */
  public function comment_query ()
  {
    return $this->user_comment_query ($this->id);
  }

  /**
   * All comments viewable by this user, created by the user at 'id'.
   * @param int $creator_id Restrict the query to return only comments created by the user with this id.
   * @return USER_COMMENT_QUERY
   */
  public function user_comment_query ($creator_id)
  {
    $Result = $this->all_comment_query ();
    $Result->restrict ("com.creator_id = $creator_id");
    return $Result;
  }

  /**
   * All entries viewable by this user.
   * @return USER_ENTRY_QUERY
   */
  public function all_entry_query ()
  {
    $class_name = $this->app->final_class_name ('USER_ENTRY_QUERY', 'webcore/db/user_entry_query.php');
    return new $class_name ($this);
  }

  /**
   * All entries viewable by this user, created by this user.
   * @return USER_ENTRY_QUERY
   */
  public function entry_query ()
  {
    return $this->user_entry_query ($this->id);
  }

  /**
   * All entries viewable by this user, created by the user at 'id'.
   * @param int $creator_id Restrict the query to return only comments created by the user with this id.
   * @return USER_ENTRY_QUERY
   */
  public function user_entry_query ($creator_id)
  {
    $Result = $this->all_entry_query ();
    $Result->restrict ("entry.creator_id = $creator_id");
    return $Result;
  }

  /**
   * All attachments viewable by this user.
   * @return USER_ATTACHMENT_QUERY
   */
  public function all_attachment_query ()
  {
    $class_name = $this->app->final_class_name ('USER_ATTACHMENT_QUERY', 'webcore/db/user_attachment_query.php');
    return new $class_name ($this);
  }

  /**
   * All entries viewable by this user.
   * @return USER_HISTORY_ITEM_QUERY
   */
  public function all_history_item_query ()
  {
    $class_name = $this->app->final_class_name ('USER_HISTORY_ITEM_QUERY', 'webcore/db/user_history_item_query.php');
    return new $class_name ($this);
  }

  /**
   * All entries viewable by this user, created by this user.
   * @return USER_HISTORY_ITEM_QUERY
   */
  public function created_history_item_query ()
  {
    return $this->user_history_item_query ($this->id);
  }

  /**
   * All entries viewable by this user, created by the user at 'id'.
   * @param int $user_id Restrict the query to return only history items created by the user with this id.
   * @return \USER_HISTORY_ITEM_QUERY
   */
  public function user_history_item_query ($user_id)
  {
    $Result = $this->all_history_item_query ();
    $Result->restrict ("act.user_id = $user_id");
    return $Result;
  }

  public function title_formatter ()
  {
    $Result = parent::title_formatter ();
    $Result->title = $this->real_name ();
    return $Result;
  }

  /**
   * The associated subscriber for this user.
   * Will always return an object, but the subscriber does not necessarily exist in the database.
   * @return SUBSCRIBER
   */
  public function subscriber ()
  {
    $class_name = $this->app->final_class_name ('SUBSCRIBER', 'webcore/obj/subscriber.php');
    $Result = new $class_name ($this->app);
    if (isset ($this->email))
    {
      $Result->email = $this->email;
    }
    return $Result;
  }

  /**
   * @param DATABASE $db Database from which to load values.
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->kind = $db->f ('kind');
    $this->ip_address = $db->f ('ip_address');
    $this->password = $db->f ('password');
    $this->email = $db->f ('email');
    $this->real_first_name = $db->f ('real_first_name');
    $this->real_last_name = $db->f ('real_last_name');
    $this->icon_url = $db->f ('icon_url');
    $this->home_page_url = $db->f ('home_page_url');
    $this->picture_url = $db->f ('picture_url');
    $this->signature = $db->f ('signature');
    $this->email_visibility = $db->f ('email_visibility');

    if ($db->f ('permissions_included'))
    {
      if ($db->f ('permissions_defined'))
      {
        include_once ('webcore/sys/permissions.php');
        $this->_permissions = new USER_PERMISSIONS ($this->app);
        $this->_permissions->load ($db);
      }
      else
      {
        $this->use_default_permissions ();
      }
    }
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname =$this->table_name ();

    if ($this->ip_address)
    {
      $ip = ip2long ($this->ip_address);
    }
    else
    {
      $ip = 0;
    }

    $storage->add ($tname, 'ip_address', Field_type_integer, $ip, Storage_action_create);
    $storage->add ($tname, 'kind', Field_type_string, $this->kind, Storage_action_create);
    $storage->add ($tname, 'password', Field_type_string, $this->password);
    $storage->add ($tname, 'real_first_name', Field_type_string, $this->real_first_name);
    $storage->add ($tname, 'real_last_name', Field_type_string, $this->real_last_name);
    $storage->add ($tname, 'email', Field_type_string, $this->email);
    $storage->add ($tname, 'home_page_url', Field_type_string, $this->home_page_url);
    $storage->add ($tname, 'picture_url', Field_type_string, $this->picture_url);
    $storage->add ($tname, 'signature', Field_type_string, $this->signature);
    $storage->add ($tname, 'icon_url', Field_type_string, $this->icon_url);
    $storage->add ($tname, 'email_visibility', Field_type_string, $this->email_visibility);
  }

  /**
   * Use the default permissions for this user.
   * Does not load from the database, but retrieves the permissions from the application.
   */
  public function use_default_permissions ()
  {
    if ($this->is_anonymous ())
    {
      $user = $this->app->anon_user ();
    }
    else
    {
      $user = $this->app->global_user ();
    }

    include_once ('webcore/sys/permissions.php');
    $this->_permissions = new USER_PERMISSIONS ($this->app);
    $this->_permissions->user_id = $this->id;
    $this->_permissions->copy_from ($user->permissions ());
  }

  /**
   * Load the permissions defined for this user.
   * If this user uses default permissions, this will have no effect.
   */
  public function load_permissions ()
  {
    if (! isset ($this->_permissions))
    {
      /* Ensure the query executes from a separate connection in case the main database is
         iterating another query. */

      $db = clone($this->db);
      $db->logged_query ("SELECT * FROM {$this->app->table_names->user_permissions} WHERE user_id = $this->id");
      if ($db->next_record ())
      {
        include_once ('webcore/sys/permissions.php');
        $this->_permissions = new USER_PERMISSIONS ($this->app);
        $this->_permissions->load ($db);
      }
      else
      {
        $this->use_default_permissions ();
      }
    }
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->user_home;
  }
  
  /**
   * Arguments to the home page url for this object.
   * @return string
   */
  public function page_arguments ()
  {
    return 'name=' . urlencode ($this->title);
  }

  /**
   * Force a login for creating a user.
   * This avoids endless recursion trying to find the creator for this user.
   * @see APPLICATION::force_login_for_user_create()
   * @access private
   */
  protected function _update_login ()
  {
    $this->app->force_login_for_user_create ();
  }

  /**
   * @param boolean $update_now Actualize the database?
   */
  public function delete ($update_now = true)
  {
    $this->assert ($this->app->user_options->users_can_be_deleted, 'Users cannot be deleted.', 'delete', 'USER');
  }

  /**
   * Name of this object's database table.
   * @return string
   * @access private
   */
  public function table_name ()
  {
    return $this->app->table_names->users;
  }

  /**
   * Return default handler objects for supported tasks.
   * @param string $handler_type Specific functionality required.
   * @param OBJECT_RENDERER_OPTIONS $options
   * @return object
   * @access private
   */
  protected function _default_handler_for ($handler_type, $options = null)
  {
    switch ($handler_type)
    {
      case Handler_print_renderer:
      case Handler_html_renderer:
      case Handler_text_renderer:
        include_once ('webcore/gui/user_renderer.php');
        return new USER_RENDERER ($this->app, $options);
      case Handler_commands:
        include_once ('webcore/cmd/user_commands.php');
        return new USER_COMMANDS ($this);
      case Handler_history_item:
        include_once ('webcore/obj/webcore_history_items.php');
        return new USER_HISTORY_ITEM ($this->app);
      case Handler_subscriptions:
        include_once ('webcore/gui/subscription_renderer.php');
        return new USER_SUBSCRIPTION_RENDERER ($this->app, $options);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }

  /**
   * Apply class-specific restrictions to this query.
   * @param SUBSCRIPTION_QUERY $query
   * @param HISTORY_ITEM $history_item Action that generated this request. May be empty.
   * @access private
   */
  protected function _prepare_subscription_query ($query, $history_item)
  {
    $query->restrict ('watch_entries > 0');
    $query->restrict_kinds (array (Subscribe_user => $this->id));
  }

  /**
   * Access using {@link folder_cache()}.
   * @access private
   * @var QUERY_BASED_CACHE
   */
  protected $_folder_cache;

  /**
   * Locally-cached query for all visible folders.
   * @var USER_FOLDER_QUERY
   * @access private
   */
  protected $_folder_query;

  /**
   * Applicable permissions for this user.
   * May be empty if the user was generated without retrieving permissions. Generally, only the
   * login user will have permissions loaded.
   * @var USER_PERMISSIONS
   * @access private
   */
  protected $_permissions;

  /**
   * Can be {@link Privilege_kind_anonymous} or {@link Privilege_kind_registered}.
   * @var string
   * @access private
   */
  public $kind;
}