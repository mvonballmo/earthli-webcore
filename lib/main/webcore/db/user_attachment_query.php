<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.1.0
 * @since 2.7.0
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
require_once ('webcore/db/user_entry_sub_object_query.php');

/**
 * Return {@link ATTACHMENT}s visible to a {@link USER}.
 * Only supports attachments made for {@link ENTRY} objects.
 * @package webcore
 * @subpackage db
 * @version 3.1.0
 * @since 2.7.0
 */
class USER_ATTACHMENT_QUERY extends USER_ENTRY_SUB_OBJECT_QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  public $alias = 'att';

  /**
   * Apply default restrictions and tables.
   */
  public function apply_defaults () 
  {
    $this->set_select ($this->alias . '.*');
    $this->set_table ($this->app->table_names->attachments . ' ' . $this->alias);
    $this->add_table ($this->app->table_names->entries . ' entry', 'entry.id = ' . $this->alias . '.object_id');
    $this->add_table ($this->app->table_names->folders . ' fldr', 'fldr.id = entry.folder_id');
    $this->set_order ('att.time_created');
    $this->restrict ('att.type = \'' . History_item_entry . '\'');
    parent::apply_defaults ();
  }

  /**
   * Return the table to use for the given privilege set.
   * @param string $set_name Can be {@link Privilege_set_folder}, {@link
   * Privilege_set_entry} or {@link Privilege_set_attachment}.
   * @return string
   * @access private
   */
  public function table_for_set ($set_name)
  {
    switch ($set_name)
    {
      case Privilege_set_attachment:
        return $this->alias;
      default:
        return parent::table_for_set ($set_name);
    }
  }

  /**
   * @return ATTACHMENT
   * @access private
   */
  protected function _make_object ()
  {
    $class_name = $this->app->final_class_name ('ATTACHMENT', 'webcore/obj/attachment.php');
    return new $class_name ($this->app);
  }

  /**
   * Called from {@link _prepare_object()}.
   * @param ATTACHMENT $obj
   * @param ENTRY $entry
   * @access private
   */
  protected function _attach_entry_to_object ($obj, $entry)
  {
    $obj->set_host ($entry);
  }

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_attachment;
}

/**
 * Return {@link ATTACHMENT}s visible to a {@link USER} in an application with {@link MULTI_TYPE_ENTRY}s.
 * Only supports attachments made for {@link ENTRY} objects.
 * @package webcore
 * @subpackage db
 * @version 3.1.0
 * @since 2.7.0
 */
class USER_MULTI_TYPE_ATTACHMENT_QUERY extends USER_ATTACHMENT_QUERY
{
  /**
   * Apply default restrictions and tables.
   */
  public function apply_defaults () 
  {
    parent::apply_defaults ();
    $this->add_select ('entry.type as entry_type');
  }

  /**
   * Make the entry associated with a {@link COMMENT}.
   * @return ENTRY
   * @access private
   */
  protected function _make_entry ()
  {
    return $this->app->make_entry ($this->db->f ('entry_type'));
  }

  /**
   * Set the type for the entry.
   * This allows the query to determine which type of object to create for each
   * row in the result.
   * @param ENTRY $entry The entry whose properties should be set.
   * @access private
   */
  protected function _prepare_entry ($entry)
  {
    parent::_prepare_entry ($entry);
    $entry->type = $this->db->f ('entry_type');
  }
}

?>