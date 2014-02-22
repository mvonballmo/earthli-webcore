<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage security
 * @version 3.4.0
 * @since 2.5.0
 * @access private
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

/** */
require_once ('webcore/obj/folder_inheritable_settings.php');
require_once ('webcore/obj/named_object.php');

/**
 * Create no default permissions when stored.
 */
define ("Security_copy_none", 0);
/**
 * Copy current permissions when stored.
 */
define ("Security_copy_current", 1);
/**
 * Create admin permissions for the given user.
 */
define ("Security_create_admin", 2);

/**
 * Used by {@link FOLDER}s to determine a {@link USER}'s rights.
 * @package webcore
 * @subpackage security
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class FOLDER_PERMISSIONS extends NAMED_OBJECT
{
  /**
   * Permissions are defined for this {@link FOLDER}.
   *  @var integer
   */
  public $folder_id;

  /**
   * Unique id for the associated object.
   * Used only if these are {@link GROUP} or {@link USER} permissions.
   * @var integer
   */
  public $ref_id;

  /**
   * To which users are these permissions applied?
   * Can be {@link Privilege_kind_anonymous}, {@link Privilege_kind_registered},
   * {@link Privilege_kind_group} or {@link Privilege_kind_user}.
   * @var string
   */
  public $kind;

  /**
   * Tie-breaker for multiple matches on permissions.
   * Used only with group permissions.
   * @var integer
   */
  public $importance;

  /**
   * List of privileges for this {@link FOLDER}.
   * @var CONTENT_PRIVILEGES
   */
  public $privileges;

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);
    $this->privileges = $app->make_privileges ();
  }

  /**
   * @return boolean
   */
  public function exists ()
  {
    return $this->_exists;
  }

  /**
   * Change the folder for this permission.
   * {@link exists()} will return <code>False</code> until {@link store()} is
   * called.
   * @param FOLDER $folder */
  public function set_folder ($folder)
  {
    $this->folder_id = $folder->id;
    $this->_exists = $this->exists_in_database ();
  }

  /**
   * Returns true if no privileges in any set are enabled.
   * @return boolean
   */
  public function is_empty ()
  {
    return $this->privileges->is_empty ();
  }

  /**
   * Check whether one or more privileges are enabled.
   * @param integer $set_name Check this set of privileges.
   * @param integer $type Check this privilege (or privileges).
   * @return boolean
   */
  public function is_allowed ($set_name, $type)
  {
    return $this->privileges->enabled ($set_name, $type);
  }

  /**
   * Set the given privileges to 'value'.
   * @param integer $set_name Apply to this set of privileges.
   * @param integer $type Set this privilege (or privileges).
   * @param boolean $value Set privileges to this value (true or false).
   */
  public function set ($set_name, $type, $value)
  {
    $this->privileges->set ($set_name, $type, $value);
  }

  /**
   * Grant every content permission.
   * Used for creating 'root' users.
   * @param bool $enabled Set to False to remove all rights.
   */
  public function set_all ($enabled = true)
  {
    $this->privileges->set_all ($enabled);
  }

  /**
   * The user for which these privileges apply.
   * Only works for user privileges. Otherwise, returns null.
   * @return USER
   */
  public function user ()
  {
    if ($this->kind == Privilege_kind_user)
    {
      return $this->app->user_at_id ($this->ref_id);
    }
    
    return null;
  }

  /**
   * The group for which these privileges apply.
   * Only works for group privileges. Otherwise, returns null.
   * @return GROUP
   */
  public function group ()
  {
    if ($this->kind == Privilege_kind_group)
    {
      $group_query = $this->app->group_query ();
      $Result = $group_query->object_at_id ($this->ref_id);
      return $Result;
    }
    
    return null;
  }

  /**
   * @param DATABASE $db Database from which to load values.
   */
  public function load ($db)
  {
    $this->_exists = true;

    $this->kind = $db->f ('kind');
    $this->folder_id = $db->f ('folder_id');
    $this->ref_id = $db->f ('ref_id');
    $this->importance = $db->f ('importance');
    $this->privileges->load ($db);
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    $tname = $this->app->table_names->folder_permissions;

    $storage->restrict ($tname, 'kind');
    $storage->restrict ($tname, 'folder_id');
    $storage->restrict ($tname, 'ref_id');

    $storage->add ($tname, 'kind', Field_type_string, $this->kind, Storage_action_create);
    $storage->add ($tname, 'folder_id', Field_type_integer, $this->folder_id, Storage_action_create);
    $storage->add ($tname, 'ref_id', Field_type_integer, $this->ref_id, Storage_action_create);
    $storage->add ($tname, 'importance', Field_type_integer, $this->importance);

    $this->privileges->store_to ($storage, $tname);

    $this->_exists = true;
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->folder_permissions_home;
  }

  /**
   * Arguments to the home page url for this object.
   * @return string
   */
  public function page_arguments ()
  {
    return 'id=' . $this->folder_id;
  }

  /**
   * @access private
   */
  protected function _create ()
  {
    if ($this->kind == Privilege_kind_group)
    {
      $this->db->logged_query ("SELECT MAX(importance) FROM {$this->app->table_names->folder_permissions}" .
                               " WHERE folder_id = $this->folder_id AND kind = '" . Privilege_kind_group . "'");
      if ($this->db->next_record ())
      {
        $this->importance = $this->db->f (0) + 1;
      }
      else
      {
        $this->importance = 1;
      }
    }

    parent::_create ();
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    $this->db->logged_query ("DELETE LOW_PRIORITY FROM {$this->app->table_names->folder_permissions} WHERE (folder_id = $this->folder_id) " .
                             "AND (ref_id = $this->ref_id) AND (kind = '$this->kind')");
  }

  /**
   * @throws UNKNOWN_VALUE_EXCEPTION
   * @return string
   */
  public function raw_title ()
  {
    switch ($this->kind)
    {
    case Privilege_kind_anonymous:
      return 'Permissions for anonymous users';
    case Privilege_kind_registered:
      return 'Permissions for registered users';
    case Privilege_kind_user:
      $user = $this->user ();
      return 'Permissions for ' . $user->title_as_plain_text ();
    case Privilege_kind_group:
      $group = $this->group ();
      return 'Permissions for ' . $group->title_as_plain_text ();
    default:
      throw new UNKNOWN_VALUE_EXCEPTION($this->kind);
    }
  }

  /**
   * @var boolean
   *  @access private
   */
  protected $_exists = false;
}

/**
 * Inheritable security definition for {@link FOLDER}s.
 * @see FOLDER::security()
 * @package webcore
 * @subpackage security
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class FOLDER_SECURITY extends FOLDER_INHERITABLE_SETTINGS
{
  /**
   * Retrieve and organize all permissions in this folder.
   * All permissions are preloaded using one query.
   */
  public function load_all_permissions ()
  {
    if (! isset ($this->_user_table))
    {
      $this->_user_table = array ();
      $this->_user_permissions = array ();
      $this->_group_table = array ();
      $this->_group_permissions = array ();

      $perm_query = $this->permissions_query ();
      $permissions = $perm_query->objects ();
      foreach ($permissions as $permission)
      {
        switch ($permission->kind)
        {
        case Privilege_kind_anonymous:
          $this->_anonymous_permissions = $permission;
          break;
        case Privilege_kind_registered:
          $this->_registered_permissions = $permission;
          break;
        case Privilege_kind_group:
          $this->_group_permissions [] = $permission;
          $this->_group_table [$permission->ref_id] = $permission;
          break;
        case Privilege_kind_user:
          $this->_user_permissions [] = $permission;
          $this->_user_table [$permission->ref_id] = $permission;
          break;
        }
      }

      $this->_ensure_default_permissions_are_stored();
    }
  }

  /**
   * A query that finds all permissions.
   * @return FOLDER_PERMISSIONS_QUERY
   */
  public function permissions_query ()
  {
    $class_name = $this->app->final_class_name ('FOLDER_PERMISSIONS_QUERY', 'webcore/db/permissions_query.php');
    return new $class_name ($this->_folder);
  }

  /**
   * Permissions for anonymous users.
   * Used only when no group or user permissions apply to a user.
   * @return FOLDER_PERMISSIONS
   */
  public function anonymous_permissions ()
  {
    if (! isset ($this->_anonymous_permissions))
    {
      $query = $this->permissions_query ();
      $query->set_kind (Privilege_kind_anonymous);
      $this->_anonymous_permissions = $query->first_object ();
    }
    return $this->_anonymous_permissions;
  }

  /**
   * Permissions for registered users.
   * Used only when no group or user permissions apply to a user.
   * @return FOLDER_PERMISSIONS
   */
  public function registered_permissions ()
  {
    if (! isset ($this->_registered_permissions))
    {
      $query = $this->permissions_query ();
      $query->set_kind (Privilege_kind_registered);
      $this->_registered_permissions = $query->first_object ();
    }

    return $this->_registered_permissions;
  }

  /**
   * Permissions for {@link GROUP}s.
   * @see FOLDER_PERMISSIONS
   * @return array[FOLDER_PERMISSIONS]
   */
  public function group_permissions ()
  {
    if (! isset ($this->_group_permissions))
    {
      $query = $this->group_permissions_query ();
      $this->_group_permissions = $query->objects ();
    }

    return $this->_group_permissions;
  }

  /**
   * Permissions for {@link USER}s.
   * @see FOLDER_PERMISSIONS
   * @return array[FOLDER_PERMISSIONS]
   */
  public function user_permissions ()
  {
    if (! isset ($this->_user_permissions))
    {
      $query = $this->user_permissions_query ();
      $this->_user_permissions = $query->objects ();
    }

    return $this->_user_permissions;
  }

  /**
   * Permissions for a particular group.
   * @param $id
   * @return FOLDER_PERMISSIONS
   */
  public function group_permissions_at_id ($id)
  {
    if (isset ($this->_group_table))
    {
      return $this->_group_table [$id];
    }
    else
    {
      $query = $this->group_permissions_query ();
      return $query->object_at_id ($id);
    }
  }

  /**
   * Permisions for this user in this folder.
   * @param $id
   * @return FOLDER_PERMISSIONS
   */
  public function user_permissions_at_id ($id)
  {
    if (isset ($this->_user_table))
    {
      return $this->_user_table [$id];
    }
    else
    {
      $query = $this->user_permissions_query ();
      return $query->object_at_id ($id);
    }
  }

  /**
   * A query that finds all group permissions.
   * @return FOLDER_PERMISSIONS_QUERY
   */
  public function group_permissions_query ()
  {
    $Result = $this->permissions_query ();
    $Result->set_kind (Privilege_kind_group);
    return $Result;
  }

  /**
   * A query that finds all user permissions.
   * @return FOLDER_PERMISSIONS_QUERY
   */
  public function user_permissions_query ()
  {
    $Result = $this->permissions_query ();
    $Result->set_kind (Privilege_kind_user);
    return $Result;
  }

  /**
   * A new set of permissions for this folder.
   * Not stored in the database.
   * @param string $kind Can be {@link Privilege_kind_group} or {@link
   * Privilege_kind_user}.
   * @return FOLDER_PERMISSIONS
   */
  public function new_permissions ($kind)
  {
    $Result = new FOLDER_PERMISSIONS ($this->app);
    $Result->folder_id = $this->_definer_id;
    $Result->kind = $kind;
    $Result->ref_id = 0;
    return $Result;
  }

  /**
   * Store this security with the given permissions.
   * The flag determines which permissions are copied to the security object
   * when stored. Changes are applied to the database immediately. Any existing
   * permissions are removed first (to avoid primary key conflicts).
   * @param integer $copy_mode Can be {@link Security_copy_none}, {@link
   * Security_copy_current} or {@link Security_create_admin}.
   * @param USER $user If <code>copy_mode</code> is
   * <code>Security_create_admin</code>, this user is granted administrator
   * rights. Uses the logged-in user if not assigned.
   */
  public function copy_and_store ($copy_mode, $user = null)
  {
    $folder = $this->folder ();

    if ($copy_mode == Security_copy_current)
    {
      $query = $this->permissions_query ();
      /** @var FOLDER_PERMISSIONS[] $permissions */
      $permissions = $query->objects ();

      foreach ($permissions as $perm)
      {
        $perm->set_folder ($folder);
        $perm->store ();
      }
    }
    else
    {
      if ($copy_mode == Security_create_admin)
      {
        if (! isset ($user))
        {
          $user = $this->login;
        }
        $perm = $this->new_permissions (Privilege_kind_user);
        $perm->ref_id = $user->id;
        $perm->set_folder ($folder);
        $perm->set_all ();
        $perm->store ();
      }
    }

    $this->set_inherited (false);
  }

  /**
   * @access private
   */
  protected function _create ()
  {
    parent::_create ();

    log_message("Creating folder.");

    $this->_ensure_default_permissions_are_stored();
  }

  protected function _ensure_default_permissions_are_stored()
  {
  	$this->anonymous_permissions (); // Try loading the existing permissions
    if (! isset ($this->_anonymous_permissions))
    {
      $this->_anonymous_permissions = $this->_create_and_store_permissions (Privilege_kind_anonymous);
    }
    
    $this->registered_permissions (); // Try loading the existing permissions
    if (! isset ($this->_registered_permissions))
    {
      $this->_registered_permissions = $this->_create_and_store_permissions (Privilege_kind_registered);
    }
  }

  /**
   * A new set of permissions for this folder.
   * Stored to the database.
   * @param string $kind
   * @return FOLDER_PERMISSIONS
   */
  protected function _create_and_store_permissions ($kind)
  {
    $Result = $this->new_permissions ($kind);
    $Result->store ();
    return $Result;
  }

  /**
   * Title for a folder's history item for inheriting this option.
   * @param boolean $adding Is the option being added?
   * @return string
   * @access private
   * @abstract
   */
  protected function _history_item_title ($adding)
  {
    return 'Security inheritance changed';
  }

  /**
   * Description for a folder's history item for inheriting this option.
   * @param boolean $adding Is the option being added?
   * @return string
   * @access private
   * @abstract
   */
  protected function _history_item_description ($adding, $folder)
  {
    return 'Security is now inherited from ' . $folder->title_as_plain_text () . '.';
  }

  /**
   * Name of the table in which settings are stored.
   * @return string
   * @access private
   * @abstract
   */
  protected function _settings_table_name ()
  {
    return $this->app->table_names->folder_permissions;
  }

  /**
   * If <code>True</code>, stores to {@link _settings_table_name()}.
   * @var boolean
   * @access private
   */
  protected $_stores_data = false;

  /**
   * Name of the object field in the folder and database.
   * @var string
   * @access private
   */
  protected $_field_name = 'permissions_id';

  /**
   * @access private
   * @var FOLDER_PERMISSIONS
   */
  protected $_anonymous_permissions;

  /**
   * @access private
   * @var FOLDER_PERMISSIONS
   */
  protected $_registered_permissions;

  /**
   * @access private
   * @see FOLDER_PERMISSIONS
   * @var array[FOLDER_PERMISSIONS]
   */
  protected $_group_permissions;

  /**
   * @access private
   * @see FOLDER_PERMISSIONS
   * @var array[FOLDER_PERMISSIONS]
   */
  protected $_user_permissions;

  /**
   * @access private
   * @see FOLDER_PERMISSIONS
   * @var array[integer,FOLDER_PERMISSIONS]
   */
  protected $_group_table;

  /**
   * @access private
   * @see FOLDER_PERMISSIONS
   * @var array[integer,FOLDER_PERMISSIONS]
   */
  protected $_user_table;
}

/**
 * Customizes display of {@link PRIVILEGES}.
 * This class takes care of transforming the actual available privileges into a displayable format.
 * The application can customize which privileges are displayed and which privileges are grouped. If
 * privileges are grouped, then only one control is displayed for it, but it controls all the privileges
 * at once. Simpler applications may want to expose coarser permissions controls to make it easier to
 * administer.
 * @package webcore
 * @subpackage security
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class PERMISSIONS_FORMATTER extends WEBCORE_OBJECT
{
  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $this->_register_formatter (Privilege_range_object, Privilege_view, '{icons}buttons/view', 'View');
    $this->_register_formatter (Privilege_range_object, Privilege_view_history, '{icons}buttons/history', 'View history');
    $this->_register_formatter (Privilege_range_object, Privilege_view_hidden, '{icons}buttons/invisible', 'View hidden');
    $this->_register_formatter (Privilege_range_object, Privilege_create, '{icons}buttons/create', 'Create');
    $this->_register_formatter (Privilege_range_object, Privilege_modify, '{icons}buttons/edit', 'Modify');
    $this->_register_formatter (Privilege_range_object, Privilege_delete, '{icons}buttons/delete', 'Delete');
    $this->_register_formatter (Privilege_range_object, Privilege_purge, '{icons}buttons/purge', 'Purge');
    $this->_register_formatter (Privilege_range_object, Privilege_secure, '{icons}buttons/security', 'Secure');
    $this->_register_formatter (Privilege_range_object, Privilege_upload, '{icons}buttons/upload', 'Upload');

    $this->_register_formatter (Privilege_range_global, Privilege_configure, '{icons}indicators/working', 'Configure');
    $this->_register_formatter (Privilege_range_global, Privilege_offline, '{icons}indicators/offline', 'Offline access');
    $this->_register_formatter (Privilege_range_global, Privilege_subscribe, '{icons}buttons/subscriptions', 'Modify subscriptions');
    $this->_register_formatter (Privilege_range_global, Privilege_password, '{icons}buttons/password', 'Change passwords');
    $this->_register_formatter (Privilege_range_global, Privilege_resources, '{icons}indicators/themes', 'Manage themes/icons');
    $this->_register_formatter (Privilege_range_global, Privilege_login, '{icons}buttons/login', 'Log in');
  }

  /**
   * Content privileges, grouped and mapped for display.
   * @see PRIVILEGE_GROUP
   * @return array[PRIVILEGE_GROUP]
   */
  public function content_privilege_groups ()
  {
    $group = new PRIVILEGE_GROUP ('General');
    $group->description = 'Settings which apply globally within the folder.';

    $map = $group->new_map (Privilege_set_folder, Privilege_upload);
    $map->add (Privilege_set_entry);
    $map->add (Privilege_set_comment);
    $map->add (Privilege_set_attachment);

    $map = $group->new_map (Privilege_set_folder, Privilege_view_history);
    $map->add (Privilege_set_entry);
    $map->add (Privilege_set_comment);
    $map->add (Privilege_set_attachment);

    $map = $group->new_map (Privilege_set_folder, Privilege_view_hidden);
    $map->add (Privilege_set_entry);
    $map->add (Privilege_set_comment);
    $map->add (Privilege_set_attachment);

    $map = $group->new_map (Privilege_set_folder, Privilege_purge);
    $map->add (Privilege_set_entry);
    $map->add (Privilege_set_comment);
    $map->add (Privilege_set_attachment);

    $map = $group->new_map (Privilege_set_folder, Privilege_secure);
    $map->add (Privilege_set_entry);
    $map->add (Privilege_set_comment);
    $map->add (Privilege_set_attachment);

    $Result [] = $group;

    $group = new PRIVILEGE_GROUP ('Folders');
    $group->description = 'Individual settings for this folder and sub-folders.';
    $map = $group->new_map (Privilege_set_folder, Privilege_view);
    $map = $group->new_map (Privilege_set_folder, Privilege_create);
    $map = $group->new_map (Privilege_set_folder, Privilege_modify);
    $map = $group->new_map (Privilege_set_folder, Privilege_delete);
    $Result [] = $group;

    $group = new PRIVILEGE_GROUP ('Content');
    $group->description = 'Settings for all entries and comments.';
    $map = $group->new_map (Privilege_set_entry, Privilege_view);
    $map->add (Privilege_set_comment);
    $map = $group->new_map (Privilege_set_entry, Privilege_create);
    $map = $group->new_map (Privilege_set_entry, Privilege_modify);
    $map->add (Privilege_set_comment);
    $map->add (Privilege_set_attachment);
    $map = $group->new_map (Privilege_set_entry, Privilege_delete);
    $map->add (Privilege_set_comment);
    $map->add (Privilege_set_attachment);
    $Result [] = $group;

    $group = new PRIVILEGE_GROUP ('Comments');
    $group->description = 'Settings for comments (overrides the create setting in content).';
    $map = $group->new_map (Privilege_set_comment, Privilege_create);
    $Result [] = $group;

    $group = new PRIVILEGE_GROUP ('Attachments');
    $group->description = 'Settings for attachments (overrides the create setting in content).';
    $map = $group->new_map (Privilege_set_attachment, Privilege_view);
    $map = $group->new_map (Privilege_set_attachment, Privilege_create);
    $Result [] = $group;

    return $Result;
  }

  /**
   * Global privileges, grouped and mapped for display.
   * @see PRIVILEGE_GROUP
   * @return array[PRIVILEGE_GROUP]
   */
  public function global_privilege_groups ()
  {
    $group = new PRIVILEGE_GROUP ('Global');

    $group->new_map (Privilege_set_global, Privilege_configure, Privilege_range_global);
    $group->new_map (Privilege_set_global, Privilege_login, Privilege_range_global);
    $group->new_map (Privilege_set_global, Privilege_offline, Privilege_range_global);
    $group->new_map (Privilege_set_global, Privilege_subscribe, Privilege_range_global);
    $group->new_map (Privilege_set_global, Privilege_password, Privilege_range_global);
    $group->new_map (Privilege_set_global, Privilege_resources, Privilege_range_global);

    $Result [] = $group;

    $group = new PRIVILEGE_GROUP ('General');

    $map = $group->new_map (Privilege_set_user, Privilege_view_history);
    $map->add (Privilege_set_group);
    $map = $group->new_map (Privilege_set_user, Privilege_view_hidden);
    $map->add (Privilege_set_group);
    $map = $group->new_map (Privilege_set_user, Privilege_purge);
    $map->add (Privilege_set_group);
    $map = $group->new_map (Privilege_set_user, Privilege_secure);
    $map->add (Privilege_set_group);

    $Result [] = $group;

    $group = new PRIVILEGE_GROUP ('Users');
    $map = $group->new_map (Privilege_set_user, Privilege_view);
    $map = $group->new_map (Privilege_set_user, Privilege_create);
    $map = $group->new_map (Privilege_set_user, Privilege_modify);
    $map = $group->new_map (Privilege_set_user, Privilege_delete);
    $Result [] = $group;

    $group = new PRIVILEGE_GROUP ('Groups');
    $map = $group->new_map (Privilege_set_group, Privilege_view);
    $map = $group->new_map (Privilege_set_group, Privilege_create);
    $map = $group->new_map (Privilege_set_group, Privilege_modify);
    $map = $group->new_map (Privilege_set_group, Privilege_delete);
    $Result [] = $group;

    return $Result;
  }

  /**
   * Return the title registered for this privilege.
   * @param PRIVILEGE_MAP $map Information about the privilege.
   */
  public function title_for ($map)
  {
    return $this->_formatters [$map->range][$map->type]->title;
  }

  /**
   * Return the icon registered for this privilege.
   * @param PRIVILEGE_MAP $map Information about the privilege.
   */
  public function icon_for ($map, $size = '16px')
  {
    $formatter = $this->_formatters [$map->range][$map->type];
    return $this->app->resolve_icon_as_html ($formatter->image, $formatter->title, $size);
  }

  /**
   * Register a privilege with a title and image.
   * @param integer $privilege
   * @param string $image
   * @param string $title
   * @access private
   */
  protected function _register_formatter ($range, $type, $image, $title)
  {
    $formatter = new stdClass();
    $formatter->type = $type;
    $formatter->image = $image;
    $formatter->title = $title;
    $this->_formatters [$range][$type] = $formatter;
  }

  /**
   * Formatters indexed by range and privilege.
   * @var array[string][integer]
   * @access private
   */
  protected $_formatters;
}

/**
 * Describes a group of displayed privileges.
 * @package webcore
 * @subpackage security
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class PRIVILEGE_GROUP
{
  /**
   * @var string
   */
  public $title;

  /**
   * @var array[PRIVILEGE_MAP]
   * @see PRIVILEGE_MAP
   */
  public $maps;

  /**
   * @param string $t Title of the group.
   */
  public function __construct ($t)
  {
    $this->title = $t;
  }

  /**
   * @param string $set_name Can be any of the {@link Privilege_set_constants}.
   * @param integer $type Specific privilege to set; must correspond to the
   * 'range'.
   * @param string $range Can be {@link Privilege_range_object} or {@link
   * Privilege_range_global}.
   */
  public function new_map ($set_name, $type, $range = Privilege_range_object)
  {
    $Result = new PRIVILEGE_MAP ($set_name, $type, $range);
    $this->maps [] = $Result;
    return $Result;
  }
}

/**
 * Maps specific privileges to a displayed one.
 * Contains a list of privilege set/type pairs to which this privilege maps.
 * @package webcore
 * @subpackage security
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class PRIVILEGE_MAP
{
  /**
   * @var string
   */
  public $set_name;

  /**
   * @var integer
   */
  public $type;

  /**
   * @var string
   */
  public $range;

  /**
   * Sets the initial set to map.
   * A map can channel the same value to multiple sets. Use {@link add()} to add
   * more sets.
   * @param string $set_name Can be any of the {@link Privilege_set_constants}.
   * @param integer $type Specific privilege to set; must correspond to the
   * 'range'.
   * @param string $range Can be {@link Privilege_range_object} or {@link
   * Privilege_range_global}.
   */
  public function __construct ($set_name, $type, $range)
  {
    $this->set_name = $set_name;
    $this->type = $type;
    $this->range = $range;
    $this->add ($set_name);
  }

  /**
   * Add another set to map to this value.
   * @param string $set_name Can be any of the {@link Privilege_set_constants}.
   * @param integer $type Specific privilege to set; must correspond to the
   * 'range'.
   */
  public function add ($set_name, $type = null)
  {
    $set = new stdClass();
    $set->name = $set_name;
    if (isset ($type))
    {
      $set->type = $type;
    }
    else
    {
      $set->type = $this->type;
    }

    $this->_sets [] = $set;
  }

  /**
   * Return a unique id for a {@link FORM} {@link FIELD}.
   * @return string
   */
  public function id ()
  {
    return $this->set_name . '_' . $this->type;
  }

  /**
   * Replaces the value for all permissions in this map.
   * @param FOLDER_PERMISSIONS $permissions
   * @param boolean @value
   */
  public function store_to_object ($permissions, $value)
  {
    foreach ($this->_sets as $set)
    {
      $permissions->set ($set->name, $set->type, $value);
    }
  }

  /**
   * @var array
   * @access private
   */
  protected $_sets;
}

?>
