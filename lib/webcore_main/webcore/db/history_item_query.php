<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.4.0
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
require_once ('webcore/db/query.php');

/**
 * Return {@link HISTORY_ITEM}s for an object.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.4.0
 */
class HISTORY_ITEM_QUERY extends QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  var $alias = 'act';

  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    $this->set_select ('act.*');
    $this->set_order ('time_created DESC');
    $this->set_table ($this->app->table_names->history_items . ' act');
  }

  /**
   * @return HISTORY_ITEM
   * @access private
   */
  function _make_object ()
  {
    $class_name = $this->app->final_class_name ('HISTORY_ITEM', 'webcore/obj/history_item.php');
    return new $class_name ($this->app);
  }
}

/**
 * Return {@link HISTORY_ITEM}s for an object.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.4.0
 */
class OBJECT_IN_FOLDER_HISTORY_ITEM_QUERY extends HISTORY_ITEM_QUERY
{
  /**
   * @return OBJECT_IN_FOLDER_HISTORY_ITEM
   * @access private
   */
  function _make_object ()
  {
    $class_name = $this->app->final_class_name ('OBJECT_IN_FOLDER_HISTORY_ITEM', 'webcore/obj/webcore_history_items.php');
    return new $class_name ($this->app);
  }
}

/**
 * Return {@link HISTORY_ITEM}s for an object.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.4.0
 */
class ENTRY_HISTORY_ITEM_QUERY extends HISTORY_ITEM_QUERY
{
  /**
   * @return ENTRY_HISTORY_ITEM
   * @access private
   */
  function _make_object ()
  {
    $class_name = $this->app->final_class_name ('ENTRY_HISTORY_ITEM', 'webcore/obj/webcore_history_items.php');
    return new $class_name ($this->app);
  }
}

?>