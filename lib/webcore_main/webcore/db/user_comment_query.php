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
require_once ('webcore/db/user_entry_sub_object_query.php');

/**
 * Return {@link COMMENT}s visible to a {@link USER}.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.2.1
 */
class USER_COMMENT_QUERY extends USER_ENTRY_SUB_OBJECT_QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  var $alias = 'com';

  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    $this->set_select ($this->alias . '.*');
    $this->set_table ($this->app->table_names->comments . ' ' . $this->alias);
    $this->add_table ($this->app->table_names->entries . ' entry', 'entry.id = ' . $this->alias . '.entry_id');
    $this->add_table ($this->app->table_names->folders . ' fldr', 'fldr.id = entry.folder_id');
    $this->set_day_field ($this->alias . '.time_created');
    $this->order_by_recent ();
    parent::apply_defaults ();
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
      case Privilege_set_comment:
        return $this->alias;
      default:
        return parent::table_for_set ($set_name);
    }
  }

  /**
   * @return COMMENT
   * @access private
   */
  function _make_object ()
  {
    $class_name = $this->app->final_class_name ('COMMENT', 'webcore/obj/comment.php');
    return new $class_name ($this->app);
  }

  /**
   * Called from {@link _prepare_object()}.
   * @param COMMENT &$obj
   * @param ENTRY &$entry
   * @access private
   */
  function _attach_entry_to_object (&$obj, &$entry)
  {
    $obj->set_entry ($entry);
  }
  
  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  var $_privilege_set = Privilege_set_comment;
}

/**
 * Return {@link COMMENT}s visible to a {@link USER} in an application with {@link MULTI_TYPE_ENTRY}s.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.2.1
 */
class USER_MULTI_TYPE_COMMENT_QUERY extends USER_COMMENT_QUERY
{
  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    parent::apply_defaults ();
    $this->add_select ('entry.type as entry_type');
  }

  /**
   * Make the entry associated with a {@link COMMENT}.
   * @return ENTRY
   * @access private
   */
  function _make_entry ()
  {
    return $this->app->make_entry ($this->db->f ('entry_type'));
  }

  /**
   * Set the type for the entry.
   * This allows the query to determine which type of object to create for each
   * row in the result.
   * @param ENTRY &$entry The entry whose properties should be set.
   * @access private
   */
  function _prepare_entry (&$entry)
  {
    parent::_prepare_entry ($entry);
    $entry->type = $this->db->f ('entry_type');
  }
}

?>