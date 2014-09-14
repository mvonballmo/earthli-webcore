<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.6.0
 * @since 2.4.0
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
require_once ('webcore/db/history_item_query.php');

/**
 * Returns all the {@link HISTORY_ITEM}s a user can see.
 * @package webcore
 * @subpackage db
 * @version 3.6.0
 * @since 2.4.0
 */
class USER_HISTORY_ITEM_QUERY extends HISTORY_ITEM_QUERY
{
  /**
   * @param USER $user The user for which items are to be retrieved.
   */
	public function __construct ($user)
  {
    parent::__construct ($user->app);
    
    $this->_user = $user;
    $this->add_table ("{$this->app->table_names->folders} fldr", 'act.access_id = fldr.id');
    $this->restrict ('NOT (act.object_type = \'' . History_item_user . '\') AND NOT (act.object_type = \'' . History_item_group . '\')');
  }

  /**
   * @access private
   */
  protected function _prepare_restrictions ()
  {
    parent::_prepare_restrictions ();

    if (! $this->_returns_no_data ())
    {
      include_once ('webcore/db/query_security.php');
      $restriction = new QUERY_SECURITY_RESTRICTION ($this, $this->_user);
      $sql = $restriction->as_sql (array (Privilege_set_folder));
      if (! $sql)
      {
        $this->_set_returns_no_data ();
      }
      else
      {
        $this->_calculated_restrictions [] = $sql;
      }
    }
  }

  /**
   * Return the table to use for the given privilege set.
   * @param string $set_name Can be {@link Privilege_set_folder}, {@link
   * Privilege_set_entry} or {@link Privilege_set_comment}.
   * @return string
   * @access private
   */
  public function table_for_set ($set_name)
  {
    switch ($set_name)
    {
      case Privilege_set_folder:
        return 'fldr';
      default:
        throw new UNKNOWN_VALUE_EXCEPTION($set_name);
    }
  }

  /**
   * Perform any setup needed on each returned object.
   * @param HISTORY_ITEM $obj
   * @access private
   */
  protected function _prepare_object ($obj)
  {
    $obj->set_parent_folder ($this->login->folder_at_id ($obj->access_id));
  }

  /**
   * @return HISTORY_ITEM
   * @access private
   */
  protected function _make_object ()
  {
    include_once ('webcore/obj/webcore_history_items.php');

    switch ($this->db->f ('object_type'))
    {
    case 'entry':
      $class_name = $this->app->final_class_name ('ENTRY_HISTORY_ITEM');
      return new $class_name ($this->app);
    default:
      $class_name = $this->app->final_class_name ('OBJECT_IN_FOLDER_HISTORY_ITEM');
      return new $class_name ($this->app);
    }
  }

  /**
   * The user to use for access control.
   * 
   * @var USER
   */
  protected $_user;
}

?>