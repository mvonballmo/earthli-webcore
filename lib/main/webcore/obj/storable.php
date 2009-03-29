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
require_once ('webcore/obj/webcore_object.php');

/**
 * A WebCore object which can be stored to a database.
 * @package webcore
 * @subpackage obj
 * @version 3.0.0
 * @since 2.2.1
 * @abstract
 */
class STORABLE extends WEBCORE_OBJECT
{
  /**
   * Does this object exist?
   * Returns the local status; use {@link exists_with_database()} to check the
   * database.
   * @return boolean
   * @abstract
   */
  function exists ()
  {
    $this->raise_deferred ('exists', 'STORABLE');
  }

  /**
   * Does this object exist in the database?
   * @return boolean
   */
  function exists_in_database ()
  {
    $storage = $this->_make_storage ();
    return $storage->object_exists ($this);
  }

  /**
   * @param DATABASE $db Database from which to load values.
   */
  function load ($db)
  {
  }

  /**
   * Store an object.
   * Determines automatically whether to update an existing entry or create a
   * new one.
   */
  function store ()
  {
    $this->_update_login ();

    $this->_pre_store ();

    if ($this->exists ())
    {
      $this->_update ();
    }
    else
    {
      $this->_create ();
    }
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   * Called from {@link SQL_STORAGE::_commit()}. Call {@link store()} to
   * properly store the object.
   * @abstract
   */
  function store_to ($storage)
  {
    $this->raise_deferred ('store_to', 'STORABLE');
  }

  /**
   * Remove the object from the database.
   * @param PURGE_OPTIONS $options
   */
  function purge ($options = null)
  {
    if ($this->exists ())
    {
      if (! isset ($options))
      {
        $options = $this->make_purge_options();
      }
      $this->_purge ($options);
    }
  }

  /**
   * Object-specific options for use with {@link purge()}.
   * @return PURGE_OPTIONS
   */
  function make_purge_options ()
  {
    return new PURGE_OPTIONS ();
  }

  /**
   * Store the object for the first time
   * Do not call this directly, call 'store' instead.
   * @access private
   */
  function _create ()
  {
    $storage = $this->_make_storage ();
    $storage->create_object ($this);
  }

  /**
   * Update an existing object
   * Do not call this directly, call 'store' instead.
   * @access private
   */
  function _update ()
  {
    $storage = $this->_make_storage ();
    $storage->update_object ($this);
  }

  /**
   * Called after there is a guaranteed login.
   * @access private
   */
  function _pre_store ()
  {
  }

  /**
   * Make sure a valid 'login' exists.
   * Call this before making changes that will store to the database. Most
   * objects will use a modifier id, so this function should ensure that that
   * modifier exists. If there is no logged-in user, it creates an anonymous
   * user. Objects based in {@link APPLICATION}s get this behavior
   * automatically; other objects will need to implement necessary
   * functionality.
   * @access private
   */
  function _update_login ()
  {
    if (isset ($this->app))
    {
      $this->app->force_login ();
    }
  }

  /**
   * @return SQL_STORAGE
    * @access private
    */
  function _make_storage ()
  {
    $class_name = $this->context->final_class_name ('SQL_STORAGE', 'webcore/db/sql_storage.php');
    return new $class_name ($this->context);
  }

  /**
   * Remove the object from the database.
   * Called from {@link purge()} which already checks that the object is
   * in the database (using {@link exists()}).
   * @param PURGE_OPTIONS $options
   * @access private
   * @abstract
   */
  function _purge ($options)
  {
    $this->raise_deferred ('_purge', 'STORABLE');
  }

  /**
   * Removes all unnassociated entries from a table.
   * @param string $from_table
   * @param string $from_field
   * @param string $to_table
   * @param string $to_field
   * @access private
   */
  function _purge_foreign_key ($from_table, $from_field, $to_table, $to_field, $condition = '(1)')
  {
    $this->db->logged_query ("SELECT to_table.$to_field FROM $to_table to_table LEFT JOIN $from_table from_table ON to_table.$to_field = from_table.$from_field WHERE ISNULL(from_table.$from_field) AND $condition");
    while ($this->db->next_record ())
      $ids [] = $this->db->f (0);

    if (isset ($ids) && sizeof ($ids))
    {
      $ids = array_unique ($ids);
      $ids = implode (',', $ids);
      $this->db->logged_query ("DELETE FROM $to_table WHERE $to_field IN ($ids)");
    }
  }
}

/**
 * Used when purging objects.
 * Options used to maintain database integrity when an object has been purged.
 * @package webcore
 * @subpackage obj
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class PURGE_OPTIONS
{
  /**
   * How should the purge history item be published?
   * The purger can determine whether a single publication history item is issued or
   * whether each affected object sends its own message.
   *
   * If the object has {@link AUDITABLE} sub-objects, the given parameter
   * applies to the {@link HISTORY_ITEM}s generated by changes to those objects caused
   * by the purge of this one. Can be {@link History_item_silent} or {@link
   * History_item_needs_send}.
   * @var string
   */
  public $sub_history_item_publication_state = FALSE;
  /**
   * Should the object remove associated resources?
   * This applies to objects that refer to files, like {@link ATTACHMENT}s or
   * {@link PICTURE}s.
   */
  public $remove_resources = TRUE;
}

?>