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
require_once ('webcore/db/comment_query.php');

/**
 * Return comments for an {@link ENTRY}.
 * @package webcore
 * @subpackage db
 * @version 3.1.0
 * @since 2.2.1
 */
class ENTRY_COMMENT_QUERY extends COMMENT_QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  public $alias = 'com';

  /**
   * @param ENTRY $entry Retrieve comments from this entry.
   */
  public function ENTRY_COMMENT_QUERY ($entry)
  {
    COMMENT_QUERY::COMMENT_QUERY ($entry->parent_folder ());
    $this->_entry = $entry;
  }

  /**
   * Apply default restrictions and tables.
   */
  public function apply_defaults () 
  {
    $this->set_select ('com.*');
    $this->set_order ('com.number');
    $this->set_table ($this->app->table_names->comments . ' com');
    $this->add_table ($this->app->table_names->entries . ' entry', 'com.entry_id = entry.id');
  }

  /**
   * Get the entry for the given attachment.
   * @param integer $id
   * @return ENTRY
   */
  public function object_for_attachment_at_id ($id)
  {
    $id = $this->validate_as_integer ($id);
    if ($id)
    {
      $old_tables = $this->_tables;
      $this->add_table ("{$this->app->table_names->attachments} a", 'a.object_id = com.id');
      $this->_start_system_call ("a.id = $id");
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
   * Make the entry associated with a comment.
   * @return ENTRY
   * @access private
   */
  protected function _make_entry ()
  {
    return $this->_entry;
  }

  /**
   * @return array[string]
   * @access private
   */
  protected function _prepare_restrictions ()
  {
    parent::_prepare_restrictions ();
    $this->_calculated_restrictions [] = 'com.entry_id = ' . $this->_entry->id;
  }

  /**
   * Retrieve only comments from this entry.
   * @var ENTRY
   * @access private
   */
  protected $_entry;
}

?>