<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
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
require_once ('webcore/obj/entry.php');

/**
 * An entry in an application with multiple types.
 * Entries must shared some information (at least the id) so that the application can manage all
 * types at once. This class handles all the details of updating the shared table and configuring
 * the extra-info table.
 * @package webcore
 * @subpackage obj
 * @version 3.0.0
 * @since 2.2.1
 * @abstract
 */
class MULTI_TYPE_ENTRY extends ENTRY
{
  /**
   * Name of the type of entry.
    * @var string
    * @access private
    */
  public $type;
  /**
   * @var integer
    * @access private
    */
  public $entry_id;

  /**
   * @param DATABASE $db Database from which to load values.
   */
  function load ($db)
  {
    parent::load ($db);
    $this->type = $db->f ("type");
    $this->entry_id = $this->id;
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  function store_to ($storage)
  {
    parent::store_to ($storage);
    $storage->add ($this->_table_name (), 'type', Field_type_string, $this->type, Storage_action_create);
    $tname = $this->_secondary_table_name ();
    $storage->add ($tname, 'entry_id', Field_type_integer, $this->entry_id, Storage_action_create);
    $storage->restrict ($tname, 'entry_id');
  }

  /**
   * The name of the 'extra-info' table for this type.
    * @return string
    * @access private
    * @abstract
    */
  function _secondary_table_name () { $this->raise_deferred ('_secondary_table_name', 'MULTI_TYPE_ENTRY'); }

  /**
   * @return SQL_MULTI_TYPE_ENTRY_STORAGE
    * @access private
    */
  function _make_storage ()
  {
    $class_name = $this->app->final_class_name ('SQL_MULTI_TYPE_ENTRY_STORAGE', 'webcore/db/sql_entry_storage.php');
    return new $class_name ($this->app);
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  function _purge ($options)
  {
    $tname = $this->_secondary_table_name ();
    $this->db->logged_query ("DELETE LOW_PRIORITY FROM {$tname} WHERE entry_id = $this->id");
    parent::_purge ($options);
  }
}

?>