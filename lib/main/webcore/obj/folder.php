<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.1.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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

/** */
require_once ('webcore/obj/attachment_host.php');

/**
 * Container in a WebCore {@link APPLICATION}.
 * Maintains security rights and lists of {@link ENTRY}s.
 * @package webcore
 * @subpackage obj
 * @version 3.1.0
 * @since 2.2.1
 */
class FOLDER extends ATTACHMENT_HOST
{
  /**
   * The icon associated with this folder.
   * Can be an absolute or relative URL. Can also be based on a folder obtained with
   * {@link APPLICATION::link_to()}.
   * @var string
   */
  public $icon_url;

  /**
   * Short description of the contents of the folder.
   * @var string
   */
  public $summary;

  /**
   * Denotes a folder that does not hold content.
   * Content controls will not be displayed for this folder.
   * @var boolean
   */
  public $organizational;

  /**
   * Foreign key reference to the id of this folder's parent.
   * Empty if this is a root folder.
   * @var integer
   * @access private
   */
  public $parent_id;

  /**
   * Foreign key reference to the id of the root folder for this subtree.
   * Zero if this is a root folder
   * @var integer
   * @access private
   */
  public $root_id;

  /**
   * Foreign key reference to the id of the id this folder uses to obtain permissions.
   * @var integer
   * @access private
   */
  public $permissions_id;

  /**
   * Is this the root folder in this {@link APPLICATION}?
   * There is only one root folder, identified by {@link APPLICATION::$root_folder_id}. An application's
   * entire content tree is based on this folder.
   * @return boolean
   */
  public function is_root ()
  {
    return $this->id == $this->app->root_folder_id;
  }

  /**
   * Can this folder contain content?
   * Folders marked as organizational cannot contain content.
   * @return boolean
   */
  public function is_organizational ()
  {
    return $this->is_root () || $this->organizational;
  }

  /**
   * Summary, rendered as valid HTML.
   * @param HTML_MUNGER $munger
   * @return string
   */
  public function summary_as_html ($munger = null)
  {
    return $this->_text_as_html ($this->summary, $munger);
  }

  /**
   * Icon, renderered as HTML.
   * The requested size can also be given, which is either used to retrieve the image or used in the HTML.
   * @var string $size
   * @return string
   */
  public function icon_as_html ($size = '32px')
  {
    return $this->app->image_as_html ($this->expanded_icon_url ($size), ' ');
  }

  /**
   * Fully resolved path to the icon for this folder.
   * @param string $size
   * @return string
   */
  public function expanded_icon_url ($size = '32px')
  {
    if ($this->icon_url)
    {
      return $this->app->sized_icon ($this->icon_url, $size);
    }
    
    return '';
  }

  /**
   * Last update time of any content in the folder.
   * If a comment exists, return the time of the comment, else return
   * the time of the most recent entry, else return the create time
   * of the folder itself. Useful for displaying activity in folders.
   * @return DATE_TIME
   */
  public function latest_object_create_time ()
  {
    $this->_cache_latest_object_info ();
    return $this->_latest_object_create_time;
  }

  /**
   * Last updater of any content in the folder.
   * If a comment exists, return the creator of the comment, else return
   * the creator of the most recent entry, else return the creator
   * of the folder itself. Useful for displaying activity in folders.
   * @return USER
   */
  public function latest_object_creator ()
  {
    $this->_cache_latest_object_info ();
    return $this->_latest_object_creator;
  }

  /**
   * Permissions for the {@link $login} user.
   * Whenever a user requests a folder, it is created along with a set of permissions
   * most applicable to that user.
   * @return CONTENT_PRIVILEGES
   */
  public function permissions ()
  {
    $this->assert (isset ($this->_privileges), "Permissions not loaded for [$this->title].", 'permissions', 'FOLDER');
    return $this->_privileges;
  }

  /**
   * Parent folder for this one; may be empty.
   * @return FOLDER
   */
  public function permissions_folder ()
  {
    if (! isset ($this->_permissions_folder))
    {
      $folder_query = $this->login->folder_query ();
      $this->_permissions_folder = $folder_query->object_at_id ($this->permissions_id);
    }

    return $this->_permissions_folder;
  }

  /**
   * Contains permissions for this object.
   * @return FOLDER
   */
  public function security_context ()
  {
    return $this;
  }

  /**
   * Does this folder define its own security definition?
   * @return boolean
   * @access private
   */
  public function defines_security ()
  {
    return $this->permissions_id == $this->id;
  }

  /**
   * The security definition for this folder.
   * This will be either inherited or defined in this folder.
   * @return FOLDER_SECURITY
   */
  public function security_definition ()
  {
    if ($this->login->is_allowed (Privilege_set_folder, Privilege_secure, $this))
    {
      if (! isset ($this->_security))
      {
        $this->_security = $this->_make_security_definition ();
      }

      return $this->_security;
    }
    
    return null;
  }


  /**
   * Make a new entry object of the requested type.
   * Does not store to database; just creates the PHP object
   * @param string $type
   * @return ENTRY
   */
  public function new_object ($type = '')
  {
    if ($type)
    {
      $method_name = "_make_$type";
      if (! method_exists ($this, $method_name))
      {
        $method_name = '_make_entry';
      }
    }
    else
    {
      $method_name = '_make_entry';
    }

    $Result = $this->$method_name ();
    $Result->set_parent_folder ($this);

    return $Result;
  }

  /**
   * Create a sub folder of this one.
   * Does not store to the database.
   * @return FOLDER
   */
  public function new_folder ()
  {
    $Result = $this->app->new_folder ();
    $Result->set_parent_folder ($this);
    $Result->state = $this->state;
    $Result->permissions_id = $this->permissions_id;
    $Result->root_id = $this->root_id;
    return $Result;
  }

  /**
   * Indicate that the sub-folders are cached.
   * This is necessary for folders with no sub-folders, to show that the
   * system has already determined that the current sub-folder list is all there is.
   * @access private
   */
  public function set_sub_folders_cached ()
  {
    $this->_sub_folders_cached = true;
  }

  /**
   * Are the sub-folders cached?
   * @return bool
   * @access private
   */
  public function sub_folders_cached ()
  {
    return $this->_sub_folders_cached;
  }

  /**
   * Return a list of sub-folders visible to the logged-in user.
   * @return array[FOLDER]
   */
  public function sub_folders ()
  {
    if (! $this->_sub_folders_cached)
    {
      $folder_query = $this->login->folder_query ();
      $this->_sub_folders = $folder_query->tree ($this->id, $this->root_id);
      $this->_sub_folders_cached = true;
    }

    return $this->_sub_folders;
  }

  /**
   * Expand links to entry aliases.
   * If a user embeds {entry} in a URL, it is automatically expanded to the URL for the
   * {$link ENTRY} object.
   * @param string $url
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to
   * {@link Force_root_on}.
   * @return string
   */
  public function resolve_url ($url, $root_override = null)
  {
    $entry_key = '{entry}';

    if (strpos ($url, $entry_key) !== false)
    {
      $id = substr ($url, strlen ($entry_key));
      if ($id && ($id [0] == '/'))
      {
        $id = substr ($id, 1);
      }

      if (is_numeric ($id))
      {
        $entry_query = $this->entry_query ();
        $entry = $entry_query->object_at_id ($id);
        $url = $entry->home_page ();
      }
      else
      {
        $url = "[ERROR]:entry for $id not found";
      }
    }

    return parent::resolve_url ($url, $root_override);
  }

  /**
   * Retrieves the parent folder object.
   * Goes to the user folder query to retrieve a (possibly cached) folder object
   * for the parent. Since folders can be loaded singly, it's possible that a
   * parent folder is not already set (in contrast to other objects, which, being
   * loaded in a folder context, should always have their parent folder object set).
   * @return FOLDER
   * @access private
   */
  protected function _load_parent_folder ()
  {
    return $this->login->folder_at_id ($this->parent_id, true);
  }

  /**
   * Set the containing folder for the object.
   * @access private
   */
  public function set_parent_folder ($fldr)
  {
    parent::set_parent_folder ($fldr);
    $this->parent_id = $fldr->id;
    $this->root_id = $fldr->root_id;
  }

  /**
   * Attach this folder as a sub-folder here.
   * @param FOLDER $folder
   * @access private
   */
  public function add_sub_folder ($folder)
  {
    $this->_sub_folders [] = $folder;
    $folder->set_parent_folder ($this);
  }

  /**
   * A query that finds all comments in this folder.
   * @return FOLDER_COMMENT_QUERY
   */
  public function comment_query ()
  {
    $class_name = $this->app->final_class_name ('FOLDER_COMMENT_QUERY', 'webcore/db/folder_comment_query.php');
    return new $class_name ($this);
  }

  /**
   * A query that finds all entries in this folder.
   * @return FOLDER_ENTRY_QUERY
   */
  public function entry_query ()
  {
    $class_name = $this->app->final_class_name ('FOLDER_ENTRY_QUERY', 'webcore/db/folder_entry_query.php');
    return new $class_name ($this);
  }

  /**
   * @return string
   */
  public function permissions_home_page ()
  {
    return "{$this->app->page_names->folder_permissions_home}?id=$this->id";
  }

  /**
   * @return string
   */
  public function subscriptions_home_page ()
  {
    return "{$this->app->page_names->folder_subscriptions_home}?id=$this->id";
  }

  /**
   * @param DATABASE $db Database from which to load values.
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->summary = $db->f ('summary');
    $this->icon_url = $db->f ('icon_url');
    $this->organizational = $db->f ('organizational');

    $this->parent_id = $db->f ('parent_id');
    $this->root_id = $db->f ('root_id');
    $this->permissions_id = $db->f ('permissions_id');

    $this->_privileges = $this->app->make_privileges ();
    $this->_privileges->load ($db);

    $folder_cache = $this->login->folder_cache ();
    $folder_cache->add_object ($this);
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname =$this->table_name ();
    $storage->add ($tname, 'parent_id', Field_type_integer, $this->parent_id);
    $storage->add ($tname, 'root_id', Field_type_integer, $this->root_id);
    $storage->add ($tname, 'permissions_id', Field_type_integer, $this->permissions_id);
    $storage->add ($tname, 'summary', Field_type_string, $this->summary);
    $storage->add ($tname, 'icon_url', Field_type_string, $this->icon_url);
    $storage->add ($tname, 'organizational', Field_type_boolean, $this->organizational);
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->folder_home;
  }

  /**
   * Handle special case for root folders.
   * If the folder has no parent (it's a root folder), then update its
   * permissions to point to itself. Does not create any permissions records by
   * default. If the folder has a parent, apply any subscriptions from the
   * parent to this new folder.
   * @access private
   */
  protected function _create ()
  {
    parent::_create ();

    $parent = $this->parent_folder ();

    if (! isset ($parent))
    {
      $sec = $this->security_definition ();
      $sec->set_inherited (false);
    }
    else
    {
      $subscriber_query = $parent->subscriber_query ();
      $objs = $subscriber_query->objects ();
      foreach ($objs as $obj)
      {
        $obj->subscribe ($this->id, Subscribe_folder);
      }
    }
  }

  /**
   * Update database for changed state.
   * When a state-change occurs, the state is applied to the comment and all sub-comments.
   * @access private
   */
  protected function _state_changed ()
  {
    if ($this->exists ())
    {
      $sub_folders = $this->sub_folders ();
      foreach ($sub_folders as &$folder)
      {
        $folder->set_state ($this->state, true);
      }
    }

    parent::_state_changed ();
  }

  /**
   * Move the object to the specified folder.
   * @param FOLDER $fldr
   * @param FOLDER_OPERATION_OPTIONS $options
   */
  protected function _move_to ($fldr, $options)
  {
    if ($options->update_now && $options->maintain_permissions)
    {
      if ($this->permissions_id != $fldr->permissions_id)
      {
        if (! $this->defines_security ())
        {
          $security = $this->security_definition ();
          $security->copy_and_store (Security_copy_current);
          $this->permissions_id = $this->id;
        }
        else
        {
          $this->permissions_id = $fldr->permissions_id;
        }
      }
    }
    else
    {
      $this->permissions_id = $fldr->permissions_id;
    }
    parent::_move_to ($fldr, $options);
  }

  /**
   * Copy the object to the specified folder.
   * @param FOLDER $fldr
   * @param FOLDER_OPERATION_OPTIONS $options
   */
  protected function _copy_to ($fldr, $options)
  {
    if ($options->update_now && $options->maintain_permissions)
    {
      if ($this->permissions_id != $fldr->permissions_id)
      {
        $security = $this->security_definition ();
        $security->copy_and_store (Security_copy_current);
        $this->permissions_id = $this->id;
      }
    }
    else
    {
      $this->permissions_id = $fldr->permissions_id;
    }
    parent::_copy_to ($fldr, $options);
  }

  /**
   * Name of the {@link FOLDER_PERMISSIONS} to use for this object.
   * @access private
   */
  protected function _privilege_set ()
  {
    return Privilege_set_folder;
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    $sub_folders = $this->sub_folders ();
    if (sizeof ($sub_folders))
    {
      foreach ($sub_folders as $folder)
      {
        /* If the data contains a cycle (where the parent = child), then
           ignore it. The folder will be removed anyway. */
        if ($folder->id != $this->id)
        {
          $folder->purge ($options);
        }
      }
    }

    // Remove permissions

    if ($this->defines_security ())
    {
      $security = $this->security_definition ();
      $security->purge ();
    }

    $tables = $this->app->table_names;

    /* Remove comments */
    $this->_purge_foreign_key ($tables->entries, 'id', $tables->comments, 'entry_id');
    /* Remove entries */
    $this->db->logged_query ("DELETE LOW_PRIORITY FROM {$tables->entries} WHERE folder_id = $this->id");
    /* Remove history items */
    $this->db->logged_query ("DELETE LOW_PRIORITY FROM {$tables->history_items} WHERE access_id = $this->id AND (object_type IN ('folder', 'entry', 'comment'))");
    /* Remove subscriptions */
    $this->db->logged_query ("DELETE LOW_PRIORITY FROM {$tables->subscriptions} WHERE ref_id = $this->id");

    $this->permissions_id = 0;
    $this->parent_id = 0;
    $this->root_id = 0;
    
    parent::_purge ($options);
  }

  /**
   * @return FOLDER_SECURITY
   * @access private
   */
  protected function _make_security_definition ()
  {
    $class_name = $this->app->final_class_name ('FOLDER_SECURITY', 'webcore/sys/security.php');
    return new $class_name ($this);
  }

  /**
   * @access private
   */
  protected function _cache_latest_object_info ()
  {
    if (! isset ($this->_latest_object_time))
    {
      $qs = "SELECT usr.id, entry.time_created FROM {$this->app->table_names->users} usr" .
            " INNER JOIN {$this->app->table_names->entries} entry on entry.creator_id = usr.id" .
            " WHERE ORDER BY entry.time_created DESC LIMIT 1";

      $this->db->logged_query ($qs);

      if ($this->db->next_record ())
      {
        $this->_latest_object_create_time = $this->app->make_date_time ();
        $this->_latest_object_create_time->set_from_iso ($this->db->f ("time_created"));
        $this->_latest_object_creator = $this->app->user_at_id ($this->db->f ("id"));
      }
      else
      {
        $this->_latest_object_create_time = $this->time_modified;
        $this->_latest_object_creator = $this->modifier ();
      }
    }
  }

  /**
   * @return ENTRY
   * @access private
   */
  protected function _make_entry ()
  {
    $class_name = $this->app->final_class_name ('ENTRY', 'webcore/obj/entry.php');
    return new $class_name ($this->app);
  }

  /**
   * Name of this object's database table.
   * @return string
   * @access private
   */
  public function table_name ()
  {
    return $this->app->table_names->folders;
  }

  /**
   * Return default handler objects for supported tasks.
   * @param string $handler_type Specific functionality required.
   * @param object $options
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
        include_once ('webcore/gui/folder_renderer.php');
        return new FOLDER_RENDERER ($this->app, $options);
      case Handler_commands:
        include_once ('webcore/cmd/folder_commands.php');
        return new FOLDER_COMMANDS ($this);
      case Handler_history_item:
        include_once ('webcore/obj/webcore_history_items.php');
        return new FOLDER_HISTORY_ITEM ($this->app);
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
    $query->restrict_kinds (array (Subscribe_folder => $this->id
                                   , Subscribe_user => $this->creator_id));
  }

  /**
   * @var FOLDER
   * @access private
   */
  protected $_parent;

  /**
   * @var FOLDER
   * @access private
   */
  protected $_permissions_folder;

  /**
   * @var DATE_TIME
   * @access private
   */
  protected $_latest_object_create_time;

  /**
   * @var USER
   * @access private
   */
  protected $_latest_object_creator;

  /**
   * @var boolean
   * @access private
   */
  protected $_sub_folders_cached = false;

  /**
   * @var array[FOLDER]
   * @access private
   */
  protected $_sub_folders;

  /**
   * @var bool
   * @access private
   */
  protected $_use_cached_sub_folders;

  /**
   * Permissions for the {@link $login} user.
   * @var CONTENT_PRIVILEGES
   * @access private
   */
  protected $_privileges;
}

?>