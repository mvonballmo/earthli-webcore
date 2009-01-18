<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage security
 * @version 3.0.0
 * @since 2.5.0
 * @access private
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

/** */
require_once ('webcore/sys/bits.php');
require_once ('webcore/obj/named_object.php');

/**
 * Defines a set of generic privileges.
 * These just define the set of available privileges for an application and do not describe
 * to which object they are attached or where they are stored. {@link FOLDER_PERMISSIONS}
 * and {@link USER_PERMISSIONS} use these to guarantee that they are storing and reading the
 * the same sets of privileges. {@link APPLICATION::make_privileges()} creates this application-
 * specific set of privileges.
 * @package webcore
 * @subpackage security
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class PRIVILEGES extends RAISABLE
{
  /**
   * Is this set supported by these privileges?
   * @param string $set_name
   * @return boolean
   */
  function supports ($set_name)
  {
    return isset ($this->_sets [$set_name]);
  }

  /**
   * Returns true if no privileges in the set are enabled.
   * If no set is specified, all sets are checked.
   * @param string $set_name
   * @return boolean
   */
  function is_empty ($set_name = '')
  {
    if ($set_name)
    {
      $this->_raise_if_not_supported ($set_name, 'is_empty');
      return $this->_sets [$set_name]->is_empty ();
    }
    else
    {
      $Result = TRUE;
      foreach ($this->_sets as $set_name => $set)
        $Result = $Result && $set->is_empty ();
      return $Result;
    }
  }

  /**
   * Check whether one or more privileges are enabled.
   * @param integer $set_name Check this set of privileges.
   * @param integer $type Check this privilege (or privileges).
   * @return boolean
   */
  function enabled ($set_name, $type)
  {
    $this->_raise_if_not_supported ($set_name, 'enabled');
    return $this->_sets [$set_name]->enabled ($type);
  }

  /**
   * Set the given privileges to 'value'.
   * @param integer $set_name Apply to this set of privileges.
   * @param integer $type Set this privilege (or privileges).
   * @param boolean $value Set privileges to this value (true or false).
   */
  function set ($set_name, $type, $value)
  {
    $this->_raise_if_not_supported ($set_name, 'set');
    $this->_sets [$set_name]->set_enabled ($type, $value);
  }

  /**
   * @param DATABASE &$db Database from which to load values.
   */
  function load (&$db, $prefix = '')
  {
    foreach ($this->_set_names as $set_name)
      $this->_sets [$set_name]->load ($db->f ($prefix . $set_name));
  }

  /**
   * Grant every content permission.
   * Used for creating 'root' users.
   * @param $enabled Set to False to remove all rights.
   */
  function set_all ($enabled = TRUE)
  {
    if ($enabled)
      $flags = Privilege_range_object_all;
    else
      $flags = 0;

    foreach ($this->_set_names as $set_name)
      $this->_sets [$set_name]->load ($flags);
  }

  /**
   * @param SQL_STORAGE &$storage Store values to this object.
   * @param string $table_name Store values to this table.
   * @access private
   */
  function store_to (&$storage, $table_name, $prefix = '')
  {
    foreach ($this->_set_names as $set_name)
      $storage->add ($table_name, $prefix . $set_name, Field_type_integer, $this->_sets [$set_name]->bits);
  }

  /**
   * Adds a new set of privileges.
   * Called from the constructor.
   * @param string $name Name of the set. Used when loading and storing values.
   * @param BITS &$bits Reference to the storage for these privileges.
   * @access private
   */
  function _register_privileges ($name, &$bits)
  {
    $this->_sets [$name] =& $bits;
    $this->_set_names [] = $name;
  }

  /**
   * Raise an exception if the set doesn't exist.
   * @param string $set_name
   * @access private
   */
  function _raise_if_not_supported ($set_name, $func_name)
  {
    $this->assert ($this->supports ($set_name), "[$set_name] is not supported", $func_name, 'PRIVILEGES');
  }

  /**
   * @var array[string]
   * @access private
   */
  var $_set_names;
  /**
   * @var array[string,BITS]
   * @access private
   */
  var $_sets;
}

/**
 * Required privileges for {@link APPLICATION}s.
 * Defines privileges for general use, folders and comments. These do not specify where or
 * how privileges are stored. {@link FOLDER_PERMISSIONS} and {@link USER_PERMISSIONS} use
 * application-defined descendents of this class (created by {@link APPLICATION::make_privileges()})
 * to ensure that they are both reading and storing the same privileges.
 * @package webcore
 * @subpackage security
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class CONTENT_PRIVILEGES extends PRIVILEGES
{
  /**
   * Privileges for miscellaneous use.
   * @see is_allowed()
   * @var BITS
   * @access private
   */
  var $general_privileges;
  /**
   * Privileges for {@link FOLDER}s.
   * @see is_allowed()
   * @var BITS
   * @access private
   */
  var $folder_privileges;
  /**
   * Privileges for {@link COMMENT}s.
   * @see is_allowed()
   * @var BITS
   * @access private
   */
  var $comment_privileges;

  function CONTENT_PRIVILEGES ()
  {
    $this->general_privileges = new BITS ();
    $this->_register_privileges (Privilege_set_general, $this->general_privileges);

    $this->folder_privileges = new BITS ();
    $this->_register_privileges (Privilege_set_folder, $this->folder_privileges);

    $this->comment_privileges = new BITS ();
    $this->_register_privileges (Privilege_set_comment, $this->comment_privileges);

    $this->attachment_privileges = new BITS ();
    $this->_register_privileges (Privilege_set_attachment, $this->attachment_privileges);
  }
}

/**
 * Adds privileges for entries.
 * Applications with either only one type of entry or the same privileges for all entry
 * types use this set of privileges.
 * @package webcore
 * @subpackage security
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class SINGLE_ENTRY_PRIVILEGES extends CONTENT_PRIVILEGES
{
  /**
   * Privileges flags for {@link ENTRY} objects.
   * @see is_allowed()
   * @var BITS
   * @access private
   */
  var $entry_privileges;

  function SINGLE_ENTRY_PRIVILEGES ()
  {
    CONTENT_PRIVILEGES::CONTENT_PRIVILEGES ();

    include_once ('webcore/sys/bits.php');
    $this->entry_privileges = new BITS ();
    $this->_register_privileges (Privilege_set_entry, $this->entry_privileges);
  }
}

/**
 * Privileges which apply globally in an {@link APPLICATION}.
 * @package webcore
 * @subpackage security
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class GLOBAL_PRIVILEGES extends PRIVILEGES
{
  /**
   * Privileges applied for {@link USER}s.
   * @see is_allowed()
   * @var BITS
   * @access private
   */
  var $user_privileges;
  /**
   * Privileges applied for {@link GROUP}s.
   * @see is_allowed()
   * @var BITS
   * @access private
   */
  var $group_privileges;

  function GLOBAL_PRIVILEGES ()
  {
    $this->general_privileges = new BITS ();
    $this->_register_privileges (Privilege_set_global, $this->general_privileges);

    $this->user_privileges = new BITS ();
    $this->_register_privileges (Privilege_set_user, $this->user_privileges);

    $this->group_privileges = new BITS ();
    $this->_register_privileges (Privilege_set_group, $this->group_privileges);

  }
}

/**
 * Permissions defined for a {@link USER}.
 * Each user has a list of privileges which are always allowed and a list of privileges which
 * always denied. If the privilege corresponds to a content privilege and neither allow nor deny
 * is specified, the content privileges defined for the {@link FOLDER} are used.
 * @package webcore
 * @subpackage security
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class USER_PERMISSIONS extends STORABLE
{
  /**
   * Permissions are defined for this {@link USER}.
   * @var integer
   */
  var $user_id;
  /**
   * Privileges which are always allowed for this {@link USER}.
   * @var CONTENT_PRIVILEGES
   */
  var $allow_privileges;
  /**
   * Privileges which are never allowed for this {@link USER}.
   * @var CONTENT_PRIVILEGES
   */
  var $deny_privileges;
  /**
   * Global privileges for this {@link USER}.
   * @var GLOBAL_PRIVILEGES
   */
  var $global_privileges;

  /**
   * @param APPLICATION &$app Main application.
   */
  function USER_PERMISSIONS (&$app)
  {
    STORABLE::STORABLE ($app);

    $this->global_privileges = new GLOBAL_PRIVILEGES ();
    $this->allow_privileges = $app->make_privileges ();
    $this->deny_privileges = $app->make_privileges ();
  }

  /**
   * Returns whether the privilege is granted or denied.
   * Can return {@link Privilege_always_granted}, {@link Permissions_always_denied} or
   * {@link Privilege_controlled_by_content}.
   * @param string $set_name Check this set of privileges.
   * @param integer $type Check this privilege (or privileges).
   */
  function value_for ($set_name, $type)
  {
    if ($this->global_privileges->supports ($set_name))
    {
      if ($this->global_privileges->enabled ($set_name, $type))
        return Privilege_always_granted;
      else
        return Privilege_always_denied;
    }
    else
    {
      if ($this->allow_privileges->enabled ($set_name, $type))
        return Privilege_always_granted;
      else if ($this->deny_privileges->enabled ($set_name, $type))
        return Privilege_always_denied;
      else
        return Privilege_controlled_by_content;
    }
  }

  /**
   * Set the requested privilege for this {@link USER}.
   * @param string $set_name Check this set of privileges.
   * @param integer $type Check this privilege (or privileges).
   * @param integer $value Can be one of {@link Privilege_always_granted}, {@link Permissions_always_denied} or {@link Privilege_controlled_by_content}.
    * @access private
    */
  function set ($set_name, $type, $value)
  {
    if ($this->global_privileges->supports ($set_name))
    {
      switch ($value)
      {
      case Privilege_always_denied:
        $this->global_privileges->set ($set_name, $type, FALSE);
        break;
      case Privilege_always_granted:
        $this->global_privileges->set ($set_name, $type, TRUE);
        break;
      case Privilege_controlled_by_content:
        $this->raise ('Global privileges cannot be controlled by content.', 'set_privilege_at', 'USER_PERMISSIONS');
      }
    }
    else
    {
      switch ($value)
      {
      case Privilege_always_denied:
        $this->allow_privileges->set ($set_name, $type, FALSE);
        $this->deny_privileges->set ($set_name, $type, TRUE);
        break;
      case Privilege_always_granted:
        $this->allow_privileges->set ($set_name, $type, TRUE);
        $this->deny_privileges->set ($set_name, $type, FALSE);
        break;
      case Privilege_controlled_by_content:
        $this->allow_privileges->set ($set_name, $type, FALSE);
        $this->deny_privileges->set ($set_name, $type, FALSE);
        break;
      }
    }
  }

  /**
   * @return boolean
   */
  function exists ()
  {
    return $this->_exists;
  }

  /**
   * Copy permissions from another user.
   * @param USER_PERMISSIONS &$other
   */
  function copy_from (&$other)
  {
    $this->global_privileges = $other->global_privileges;
    $this->allow_privileges = $other->allow_privileges;
    $this->deny_privileges = $other->deny_privileges;
  }

  /**
   * @param DATABASE &$db Database from which to load values.
   */
  function load (&$db)
  {
    $this->_exists = TRUE;
    $this->user_id = $db->f ('user_id');
    $this->global_privileges->load ($db);
    $this->allow_privileges->load ($db, 'allow_');
    $this->deny_privileges->load ($db, 'deny_');
  }

  /**
   * @param SQL_STORAGE &$storage Store values to this object.
    * @access private
    */
  function store_to (&$storage)
  {
    $tname = $this->app->table_names->user_permissions;

    $storage->restrict ($tname, 'user_id');
    $storage->add ($tname, 'user_id', Field_type_integer, $this->user_id, Storage_action_create);

    $this->global_privileges->store_to ($storage, $tname);
    $this->allow_privileges->store_to ($storage, $tname, 'allow_');
    $this->deny_privileges->store_to ($storage, $tname, 'deny_');

    $this->_exists = TRUE;
  }

  function delete ()
  {
    $this->db->logged_query ("DELETE LOW_PRIORITY FROM {$this->app->table_names->user_permissions} WHERE (user_id = $this->user_id)");
  }

  /**
   * @var boolean
   *  @access private
   */
  var $_exists = FALSE;
}

?>