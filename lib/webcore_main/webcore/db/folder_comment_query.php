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
require_once ('webcore/db/comment_query.php');

/**
 * Return {@link COMMENT}s for a {@link FOLDER}.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.2.1
 */
class FOLDER_COMMENT_QUERY extends COMMENT_QUERY
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
    $table_names = $this->app->table_names;
    $this->set_select ('com.*, entry.state as entry_state, entry.title as entry_title');
    $this->set_table ($table_names->comments . ' com');
    $this->add_table ($table_names->entries . ' entry', 'entry.id = com.entry_id');
    $this->set_day_field ($this->alias . '.time_created');
    $this->order_by_recent ();
  }

  /**
   * Prepare security- and filter-based restrictions.
   * @access private
   */
  function _prepare_restrictions ()
  {
    parent::_prepare_restrictions ();
    $this->_calculated_restrictions [] = 'entry.folder_id = ' . $this->_folder->id;
  }

  /**
   * @return string
   * @param string $calculated_filter
   * @access private
   */
  function _filter_restriction ($calculated_filter)
  {
    $Result = parent::_filter_restriction ($calculated_filter);

    /* If the user can't see invisible objects, expand the default
     * query to include not only all objects that pass the filter, but also
     * all unpublished objects that the user owns. 
     */

    if ((($calculated_filter & Invisible) == 0) && (($this->_filter & Unpublished) == Unpublished))
    {
      if ($this->_filter == All)
      {
        $unpublished_filter = $this->_filter & ~Deleted | Invisible;
        $expected_state = Unpublished;
      }
      else
      {
        $unpublished_filter = $this->_filter;
        $expected_state = $this->_filter;
      }
      $Result = "($Result) OR ((entry.state & {$unpublished_filter} = " . $expected_state . ") AND (entry.owner_id = {$this->login->id}))";
    }

    return $Result;
  }

  /**
   * Set properties for the entry associated with a comment.
   * @param ENTRY &$entry The entry whose properties should be set.
   * @access private
   */
  function _prepare_entry (&$entry)
  {
    parent::_prepare_entry ($entry);
    $entry->title = $this->db->f ('entry_title');
    $entry->state = $this->db->f ('entry_state');
  }
}

/**
 * Return {@link COMMENT}s for a {@link FOLDER} in a multi-type entry application.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.2.1
 */
class FOLDER_MULTI_TYPE_COMMENT_QUERY extends FOLDER_COMMENT_QUERY
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