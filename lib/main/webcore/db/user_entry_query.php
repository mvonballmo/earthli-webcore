<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.2.1
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
require_once ('webcore/db/object_in_folder_query.php');

/**
 * Return {@link ENTRY} objects visible to a {@link USER}.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.2.1
 * @abstract
 */
class USER_ENTRY_QUERY extends OBJECT_IN_FOLDER_QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  var $alias = 'entry';

  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    $this->set_select ('entry.*');
    $this->set_table ($this->app->table_names->entries . ' entry');
    $this->add_table ($this->app->table_names->folders . ' fldr', 'fldr.id = entry.folder_id');
    $this->set_day_field ('entry.time_created');
    $this->order_by_recent ();
  }

  /**
   * Specify the type of entry to retrieve.
   * Does nothing in this class -- only used by applications with multiple
   * entry-types.
   * @param string $type
   */
  function set_type ($type) {}

  /**
   * Prepare security- and filter-based restrictions.
   * @access private
   */
  function _prepare_restrictions ()
  {
    parent::_prepare_restrictions ();

    if (! $this->_returns_no_data ())
    {
      include_once ('webcore/db/query_security.php');
      $restriction = new QUERY_SECURITY_RESTRICTION ($this);
      $sql = $restriction->as_sql (array (Privilege_set_folder, Privilege_set_entry));
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
  function table_for_set ($set_name)
  {
    switch ($set_name)
    {
      case Privilege_set_folder:
        return 'fldr';
      case Privilege_set_entry:
        return $this->alias;
    }
  }

  /**
   * @return ENTRY
   * @access private
   */
  function _make_object ()
  {
    $class_name = $this->app->final_class_name ('ENTRY', 'webcore/obj/entry.php');
    return new $class_name ($this->app);
  }

  /**
   * Perform any setup needed on each returned object.
   * @param ENTRY &$obj
   * @access private
   */
  function _prepare_object (&$obj)
  {
    $obj->set_parent_folder ($this->login->folder_at_id ($this->db->f ('folder_id')));
  }

  /**
   * @var USER_FOLDER_QUERY
   * @access private
   */
  var $_folder_query;
  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  var $_privilege_set = Privilege_set_entry;
}

/**
 * Return {@link ENTRY}s for a {@link USER} in a multi entry-type application.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.7.0
 */
class USER_MULTI_ENTRY_QUERY extends USER_ENTRY_QUERY
{
  /**
   * @param ALBUM_APPLICATION &$app Main application.
   */
  function USER_MULTI_ENTRY_QUERY (&$app)
  {
    USER_ENTRY_QUERY::USER_ENTRY_QUERY ($app);
    $this->set_type ('');
  }

  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    parent::apply_defaults ();
    $this->add_select ('entry.type as entry_type');
  }

  /**
   * Specify the type of entry to retrieve.
   * @param string $type
   */
  function set_type ($type)
  {
    if ($type)
    {
      $this->restrict ("entry_type = '$type'");
    }
  }

  /**
   * @return ENTRY
   * @access private
   */
  function _make_object ()
  {
    return $this->app->make_entry ($this->db->f ('entry_type'));
  }
}

/**
 * Retrieves {@link DRAFTABLE_ENTRY}s visible to a {@link USER}.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.7.1
 */
class USER_DRAFTABLE_ENTRY_QUERY extends USER_ENTRY_QUERY
{
  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    parent::apply_defaults ();
    $this->set_day_field ('entry.time_published');
    $this->order_by_recent ();
  }

  /**
   * Reset the ordering to show recent items first.
   * @access private
   */
  function _order_by_recent ()
  {
    if ($this->includes (Unpublished) && ! $this->includes (Visible))
    {
      $this->set_order ('entry.state ASC, entry.time_modified DESC');
    }
    else
    {
      $this->set_order ('entry.state ASC, entry.time_published DESC, entry.time_created DESC');
    }
  }
}

?>