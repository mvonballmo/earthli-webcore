<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage util
 * @version 3.0.0
 * @since 2.4.0
 * @access private
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
require_once ('webcore/util/object_cache.php');

/**
 * Generates a {@link UNIQUE_OBJECT}'s 'context' within a list.
 * @package webcore
 * @subpackage util
 * @version 3.0.0
 * @since 2.4.0
 * @access private
 */
class UNIQUE_OBJECT_CACHE extends OBJECT_CACHE
{
  /**
   * @param QUERY &$query List objects from this query.
   */
  function UNIQUE_OBJECT_CACHE (&$query)
  {
    $this->_app =& $query->app;
    $this->_query =& $query;
    $this->window_size = $this->_app->display_options->objects_to_show;
    $this->_app->storage->load_multiple_values ($this->_stored_name (''));
  }

  /**
   * Center the window on this id.
   * If this id is found in the list on which the cache operates, then {@link is_valid()} will return true.
   * @param integer $selected_id
   */
  function set_selected_id ($selected_id)
  {
    $this->_app->storage->start_multiple_value ($this->_stored_name ('')); 
    parent::set_selected_id ($selected_id);
    $this->_app->storage->finish_multiple_value ();
  }

  /**
   * Return a name to use as a key in a storage mechanism.
   * @param string $name
   * @return string
   * @access private
   */
  function _stored_name ($name)
  {
    if (! isset ($this->_hash_value))
      $this->_hash_value = $this->_query->hash ();

    $Result = 'list_' . $this->_hash_value;
    if ($name)
      $Result .= '_' . $name;
      
    return $Result;
  }

  /**
   * Calculate the number of objects in this master list.
   * This is only called if the value is not found in storage.
   * @return integer
   * @access private
   */
  function _num_objects_in_list ()
  {
    return $this->_query->size ();
  }

  /**
   * Load the requested objects.
   * @param string $ids Comma-separated list of ids to load.
   * @return array[object]
   * @access private
   * @abstract
   */
  function _load_objects_at_ids ($ids)
  {
    $this->_query->set_limits (0, 0);
    return $this->_query->objects_at_ids ($ids);
  }

  /**
   * Load the requested objects.
   * @param integer $first Index within master list for first item.
   * @param integer $count Number of items to retrieve from that position.
   * @return array[object]
   * @access private
   * @abstract
   */
  function _load_objects_in_range ($first, $count)
  {
    $this->_query->set_limits ($first, $count);
    return $this->_query->objects ();
  }

  /**
   * Read a value from storage.
   * @param string $name
   * @return string
   * @access private
   */
  function _read_value ($name)
  {
    $Result = $this->_app->storage->value ($this->_stored_name ($name));
    $this->_record ("Read [$name] = [$Result]");
    return $Result;
  }

  /**
   * Write a value to storage.
   * @param string $name
   * @param string $value
   * @access private
   */
  function _write_value ($name, $value)
  {
    $this->_app->storage->set_value ($this->_stored_name ($name), $value);
    $this->_record ("Wrote [$name] = [$value]");
  }

  /**
   * Record a message to a logging mechanism.
   * @param string $msg
   * @param string $type Type of the message; see {@link Msg_type_debug_info}
   * @param string $channel identifier indicating the channel on which the message is sent.
   * @access private
   * @abstract
   */
  function _record ($msg, $type = Msg_type_debug_info, $channel = Msg_channel_system)
  {
    log_message ($msg, $type, $channel);
  }

  /**
   * Start looking for ids in the given range.
   * @param integer $first Index within master list for first item.
   * @param integer $count Number of items to retrieve from that position.
   * @access private
   * @abstract
   */
  function _start_id_search ($first = 0, $count = 0)
  {
    // deliberately make a copy of the query

    $q = $this->_query->make_clone ();
    $q->set_limits ($first, $count);
    $q->set_select ($q->alias . '.' . $q->id);
    $this->_iteration_db = $q->raw_output ();
  }

  /**
   * Are there ids left to iterate?
   * Start the iteration cycle with {@link _start_id_search()}.
   * @return boolean
   * @access private
   * @abstract
   */
  function _ids_exist ()
  {
    return $this->_iteration_db->next_record ();
  }

  /**
   * Go to the next id in an iteration.
   * Start the iteration cycle with {@link _start_id_search()}.
   * @return integer
   * @access private
   * @abstract
   */
  function _load_id ()
  {
    return $this->_iteration_db->f ($this->_query->id);
  }

  /**
   * Application to which this cache is attached.
    * @var APPLICATION
    * @access private
    */
  var $_app;
  /**
   * Objects for this list are pulled from this query.
    * @var QUERY
    * @access private
    */
  var $_query;
  /**
   * Used during iteration
    * @var DATABASE
    * @access private
    */
  var $_iteration_db;
}

?>