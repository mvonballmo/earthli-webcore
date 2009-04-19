<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
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
require_once ('webcore/db/object_in_folder_query.php');

/**
 * Return {@link ENTRY}s for a {@link FOLDER}.
 * @package webcore
 * @subpackage db
 * @version 3.1.0
 * @since 2.2.1
 * @abstract
 */
class FOLDER_ENTRY_QUERY extends OBJECT_IN_SINGLE_FOLDER_QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  public $alias = 'entry';

  /**
   * Apply default restrictions and tables.
   */
  public function apply_defaults () 
  {
    $this->set_select ('entry.*');
    $this->set_table ($this->app->table_names->entries . ' entry');
    $this->add_table ($this->app->table_names->folders . ' fldr', 'entry.folder_id = fldr.id');
    $this->set_day_field ('entry.time_created');
    $this->order_by_recent ();
  }

  /**
   * Specify the type of entry to retrieve.
   * Does nothing in this class -- only used by applications with multiple
   * entry-types.
   * @param string $type
   */
  public function set_type ($type) {}

  /**
   * Get the entry for the given comment id.
   * @param integer $id
   * @return ENTRY
   */
  public function object_for_comment_at_id ($id)
  {
    $id = $this->validate_as_integer ($id);
    if ($id)
    {
      $this->db->logged_query ("SELECT entry.id FROM {$this->app->table_names->entries} entry" .
                               " INNER JOIN {$this->app->table_names->comments} com on com.entry_id = entry.id" .
                               " WHERE com.id = $id");

      if ($this->db->next_record ())
      {
        return $this->object_at_id ($this->db->f ("id"));
      }
    }
    
    return null;
  }

  /**
   * Get the entry for the given attachment.
   * @param integer $id
   * @param string $type
   * @return ENTRY
   */
  public function object_for_attachment_at_id ($id, $type)
  {
    $id = $this->validate_as_integer ($id);
    if ($id)
    {
      $old_tables = $this->_tables;

      if ($type == History_item_comment)
      {
        $this->add_table ("{$this->app->table_names->comments} com", 'com.entry_id = entry.id');
        $this->add_table ("{$this->app->table_names->attachments} att", 'att.object_id = com.id');
      }
      else
      {
        $this->add_table ("{$this->app->table_names->attachments} att", 'att.object_id = entry.id');
      }

      $this->_start_system_call ("att.id = $id");
      $objs = $this->objects ();
      if (sizeof ($objs))
      {
        $Result = $objs [0];
      }
      $this->_end_system_call ();
      $this->_tables = $old_tables;
      
      return $Result;
    }
    
    return null;
  }

  /**
   * @return ENTRY
   * @access private
   */
  protected function _make_object ()
  {
    $class_name = $this->app->final_class_name ('ENTRY', 'webcore/obj/entry.php');
    return new $class_name ($this->app);
  }

  /**
   * Prepare security- and filter-based restrictions.
   * @access private
   */
  protected function _prepare_restrictions ()
  {
    parent::_prepare_restrictions ();
    $this->_calculated_restrictions [] = $this->_sql_folder_id () . " = " . $this->_folder->id;
  }

  /**
   * @return string
   * @param string $calculated_filter
   * @access private
   */
  protected function _filter_restriction ($calculated_filter)
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
      $tbl = $this->alias;
      $Result = "($Result) OR (($tbl.state & {$unpublished_filter} = " . $expected_state . ") AND ($tbl.owner_id = {$this->login->id}))";
    }

    return $Result;
  }

  /**
   * Return the SQL that retrieves the folder id from the query.
   * Most objects which need the folder id will store it directly in the same table
   * as the object itself, under the field 'folder_id'. That is the default return value
   * here. However, if a query joins the main object table to another in order to retrieve
   * the folder id, it can override here to return the new path to the field.
   * @return string
   */
  protected function _sql_folder_id ()
  {
    return $this->alias . '.folder_id';
  }

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_entry;
}

/**
 * Return {@link ENTRY}s for a {@link FOLDER} in a multi entry-type application.
 * @package webcore
 * @subpackage db
 * @version 3.1.0
 * @since 2.2.1
 */
class FOLDER_MULTI_ENTRY_QUERY extends FOLDER_ENTRY_QUERY
{
  /**
   * @param FOLDER $folder Retrieve entries from this folder.
   */
  public function FOLDER_MULTI_ENTRY_QUERY ($folder)
  {
    FOLDER_ENTRY_QUERY::FOLDER_ENTRY_QUERY ($folder);
    $this->set_type ('');
  }
  
  /**
   * Apply default restrictions and tables.
   */
  public function apply_defaults () 
  {
    parent::apply_defaults ();
    $this->add_select ('entry.type as entry_type');
  }

  /**
   * Specify the type of entry to retrieve.
   * @param string $type
   */
  public function set_type ($type)
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
  protected function _make_object ()
  {
    return $this->app->make_entry ($this->db->f ('entry_type'));
  }
}

/**
 * Retrieves {@link DRAFTABLE_ENTRY}s related to a particular {@link FOLDER}.
 * @package webcore
 * @subpackage db
 * @version 3.1.0
 * @since 2.7.1
 */
class FOLDER_DRAFTABLE_ENTRY_QUERY extends FOLDER_ENTRY_QUERY
{
  /**
   * Apply default restrictions and tables.
   */
  public function apply_defaults () 
  {
    parent::apply_defaults ();
    $this->set_day_field ('entry.time_published');
    $this->order_by_recent ();
  }
  
  /**
   * Reset the ordering to show recent items first.
   * @access private
   */
  protected function _order_by_recent ()
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