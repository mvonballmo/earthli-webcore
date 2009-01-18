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
 * Return subobjects of an {@link ENTRY} visible to a {@link USER}.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.7.0
 */
class USER_ENTRY_SUB_OBJECT_QUERY extends OBJECT_IN_FOLDER_QUERY
{
  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    $this->add_select ('fldr.id as folder_id, entry.id as entry_id, entry.title as entry_title, entry.state as entry_state');
  }

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
      $sql = $restriction->as_sql (array (Privilege_set_folder, Privilege_set_entry, $this->_privilege_set));
      if (! $sql)
        $this->_set_returns_no_data ();
      else
        $this->_calculated_restrictions [] = $sql;
    }
  }

  /**
   * Return the table to use for the given privilege set.
   * @param string $set_name Can be {@link Privilege_set_folder}, {@link
   * Privilege_set_entry}, {@link Privilege_set_comment} or {@link
   * Privilege_set_attachment}.
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
        return 'entry';
    }
  }

  /**
   * Make the entry associated with a comment.
   * @access private
   * @return ENTRY
   */
  function _make_entry ()
  {
    $class_name = $this->app->final_class_name ('ENTRY', 'webcore/obj/entry.php');
    return new $class_name ($this->app);
  }

  /**
   * Set properties for the entry associated with a comment.
   * This is usually just the bare minimum of properties needed to display a
   * link to the entry. This avoids retrieving all the entry data when only the
   * link needs to be displayed (since this query shows comments, not full
   * entries).
   * @param ENTRY &$entry The entry whose properties should be set.
   * @access private
   */
  function _prepare_entry (&$entry)
  {
    $db =& $this->db;
    $entry->id = $db->f ('entry_id');
    $entry->title = $db->f ('entry_title');
    $entry->state = $db->f ('entry_state');
    $entry->set_parent_folder ($this->login->folder_at_id ($this->db->f ('folder_id')));
  }

  /**
   * Perform any setup needed on each returned object.
   * Create, prepare and set the entry for each comment.
   * @see _make_entry()
   * @see _prepare_entry()
   * @param OBJECT_IN_FOLDER &$obj
   * @access private
   */
  function _prepare_object (&$obj)
  {
    $entry = $this->_make_entry ();
    $this->_prepare_entry ($entry);
    $this->_attach_entry_to_object ($obj, $entry);
  }
  
  /**
   * Called from {@link _prepare_object()}.
   * @param OBJECT_IN_FOLDER &$obj
   * @param ENTRY &$entry
   * @access private
   * @abstract
   */
  function _attach_entry_to_object (&$obj, &$entry)
  {
    $this->raise_deferred ('_attach_entry_to_object', 'USER_ENTRY_SUB_OBJECT_QUERY');
  }  
}

?>