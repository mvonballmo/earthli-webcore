<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
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

/** */
require_once ('webcore/db/query.php');

/**
 * Return {@link USER} for an {@link APPLICATION}.
 * @package webcore
 * @subpackage db
 * @version 3.5.0
 * @since 2.2.1
 * @abstract
 */
class USER_QUERY extends QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  public $alias = 'usr';

  /**
   * Are permissions loaded with the user?
   * @see include_permissions()
   * @var boolean
   */
  public $permissions_included = false;

  /**
   * Apply default restrictions and tables.
   */
  public function apply_defaults () 
  {
    $this->set_select ('usr.*');
    $this->set_table ($this->app->table_names->users . ' usr');
    $this->set_order ('title ASC');
  }

  /**
   * Add/remove permissions information when retrieving users.
   * @param boolean $value If true, permissions are included.
   */
  public function include_permissions ($value)
  {
    if ($this->permissions_included != $value)
    {
      $this->permissions_included = $value;

      $this->set_table ("{$this->app->table_names->users} usr");
      $this->set_select ('usr.*');

      if ($value)
      {
        $this->add_table ("{$this->app->table_names->user_permissions} perms", 'perms.user_id = usr.id', 'LEFT');
        $this->add_select ('perms.*, (1) as permissions_included');
        $this->add_select ('NOT ISNULL(perms.allow_general_permissions) as permissions_defined');
      }
    }
  }

  /**
   * Specify which user types to return.
   * Can be {@link Privilege_kind_anonymous} or {@link Privilege_kind_registered}.
   * @param string $kind
   */
  public function set_kind ($kind)
  {
    $this->_invalidate ();
    $this->_kind = $kind;
  }

  /**
   * @param string $name
   * @return USER
   */
  public function object_at_name ($name)
  {
    return $this->object_with_field ('usr.title', $name);
  }

  /**
   * @param $email
   * @return USER
   */
  public function object_at_email ($email)
  {
    return $this->object_with_field ('usr.email', $email);
  }

  /**
   * @access private
   */
  protected function _prepare_restrictions ()
  {
    if (isset ($this->_kind))
    {
      $this->_calculated_restrictions [] = "usr.kind = '$this->_kind'";
    }
  }

  /**
   * Return whether the user can see visible objects.
   * If there is no logged-in user, then assume that this query is retrieving login information
   * and return true.
   * @return boolean
   * @access private
   */
  protected function _visible_objects_available ()
  {
    if (isset ($this->login))
    {
      return $this->login->is_allowed ($this->_privilege_set, Privilege_view);
    }

    return true;

  }

  /**
   * Return whether the user can see invisible objects.
   * If there is no logged-in user, then assume that this query is retrieving login information
   * and return true.
   * @return boolean
   * @access private
   */
  protected function _invisible_objects_available ()
  {
    if (isset ($this->login))
    {
      return $this->login->is_allowed ($this->_privilege_set, Privilege_view_hidden);
    }

    return true;
  }

  /**
   * @return USER
   * @access private
   */
  protected function _make_object ()
  {
    $class_name = $this->app->final_class_name ('USER', 'webcore/obj/user.php');
    return new $class_name ($this->app);
  }

  /**
   * @var string
   * @access private
   */
  protected $_kind;

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_user;
}
?>