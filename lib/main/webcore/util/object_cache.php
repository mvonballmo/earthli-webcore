<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.4.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/sys/system.php');

/**
 * Generates an object's 'context' within a list.
 * The context of an object is the list of objects immediately before and after that object in
 * a sorted list. When an object is displayed from a list, this class will quickly generate this
 * context using a cookie-based caching mechanism to minimize the number and size of queries and
 * to minimize the amount of calculation (looping) required to locate the selected object.
 *
 * This object handles arbitrarily long lists of objects by using a cookie-based cache. This cache,
 * in turn, is twice as large as the 'window' of items that are displayed at a given time. In this
 * way, if the user navigates with next and previous to new items, the list of required ids can easily
 * be retrieved from the local cache and the request to the database is limited to the objects that
 * actually need to be displayed. Other techniques limit the amount of work and querying needed to
 * reposition the cache when there is a cache-miss.
 *
 * The first time the cache is hit, the full list must be examined, in order to find the {@link UNIQUE_OBJECT}.
 *
 * This object will calculate and find all of the objects that are 'interesting' when displaying a list,
 * like the {@link $first_object}, {@link $next_object}, {@link $previous_object}, and {@link $last_object}. It
 * also provides the {@link $next_page_object} and {@link $previous_page_object}, which are the objects
 * exactly on {@link $window_size} away from the currently selected one, in either direction.
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.4.0
 * @access private
 * @abstract
 */
abstract class OBJECT_CACHE extends RAISABLE
{
  /**
   * Maximum number of entries to show in the context.
   * The internal cache will is adjusted to work efficiently with this value.
   * @var integer
   */
  public $window_size;

  /**
   * A value indicating whether the cache will load/store to the backing store.
   *
   * If disabled, then the cache will always miss. Disable the cache if it's not
   * helping or if you don't want to store to the backing store anymore.
   *
   * @var bool
   */
  public $storage_enabled;

  /**
   * Total number of objects represented by this cache.
   * The cache is simply a window of objects over a (possibly) larger list. This is the number
   * of objects in the entire (virtual) list. {@link $objects_in_window} will only have {@link window_size}
   * number of objects in it.
   *
   * Use this along with {@link $position_of_selected_id} to show a position indicator (e.g. item 23/25).
   * @var integer
   */
  public $num_objects_in_list;

  /**
   * Index of current item in total number of objects.
   * Use this along with {@link $num_objects_in_list} to show a position indicator (e.g. item 23/25).
   * @var integer
   */
  public $position_of_selected_id;

  /**
   * Context list for the current object.
   * Set the current object with {@link set_selected_id()}.
   * @var object[]
   */
  public $objects_in_window;

  /**
   * First object in the list.
   * Is always set; use {@link is_first()} to determine whether it is the same as the selected object.
   * @var object
   */
  public $first_object;

  /**
   * Object one 'window_size' before the selected one.
   * May be empty; use {@link is_on_first_page()} to determine whether it is set.
   * @var object
   */
  public $previous_page_object;

  /**
   * Object one position before the selected one.
   * May be empty; use {@link is_first()} to determine whether it is set.
   * @var object
   */
  public $previous_object;

  /**
   * Object at the selected position.
   * @var object
   */
  public $selected_object;

  /**
   * Object one position after the selected one.
   * May be empty; use {@link is_last()} to determine whether it is set.
   * @var object
   */
  public $next_object;

  /**
   * Object one 'window_size' after the selected one.
   * May be empty; use {@link is_on_last_page()} to determine whether it is set.
   * @var object
   */
  public $next_page_object;

  /**
   * Last object in the entire list (not the window).
   * Is always set; use {@link is_last()} to determine whether it is the same as the {@link $selected_object}.
   * @var object
   */
  public $last_object;

  /**
   * Was the object requested with {@link set_selected_id()} located?
   * @return boolean
   */
  public function is_valid ()
  {
    return sizeof ($this->objects_in_window) > 0;
  }

  public function is_on_first_page ()
  {
    return ! isset ($this->previous_page_object);
  }

  public function is_first ()
  {
    return ! isset ($this->previous_object);
  }

  public function is_on_last_page ()
  {
    return ! isset ($this->next_page_object);
  }

  public function is_last ()
  {
    return ! isset ($this->next_object);
  }

  /**
   * Center the window on this id.
   * If this id is found in the list on which the cache operates, then {@link is_valid()} will return true.
   * @param integer $selected_id
   */
  public function set_selected_id ($selected_id)
  {
    $stored_num_objects = $this->_read_value ('id_size');
    $actual_num_objects = $this->_num_objects_in_list ();
    $this->num_objects_in_list = $actual_num_objects;

    $rebuild_cache = $stored_num_objects != $actual_num_objects;

    if ($rebuild_cache)
    {
      $this->_record ('Rebuilding cache due to new entry');
    }

    if (! $rebuild_cache)
    {
      $id_list = $this->_read_value ('id_list');
      $first_id = $this->_read_value ('id_first');
      $last_id = $this->_read_value ('id_last');
      $pos_of_local_list = $this->_read_value ('id_list_pos');

      $ids = explode (',', $id_list);
      $cache_size = sizeof ($ids);

      $rebuild_cache = $id_list && ! $first_id || ! $last_id || ! isset ($pos_of_local_list);

      if (! $rebuild_cache)
      {
        $this->_record ('ID list has ' . $cache_size . ' items');
        $index_of_selected_id = array_search ($selected_id, $ids);
        $rebuild_cache = $index_of_selected_id === false;
        if (! $rebuild_cache)
        {
          $this->_record ("[$selected_id] found in list at: $index_of_selected_id");

          /* The cache is valid if there are 'window_size' items to either side of the selected id
             or either one of the cache boundaries is equal to the full list boundary. */

          $rebuild_cache = ! (((($index_of_selected_id + ($this->window_size)) < $cache_size) || ($last_id == $ids [$cache_size - 1])) &&
                              ((($index_of_selected_id - ($this->window_size)) >= 0) || ($first_id == $ids [0])));

          /* Cache needs to be repositioned. */

          if ($rebuild_cache)
          {
            $this->_record ("Repositioning cache");

            $first = $pos_of_local_list + $index_of_selected_id - ($this->window_size * 2);
            $count = ($this->window_size * 4);

            if ($first + $count > $this->num_objects_in_list)
            {
              $first = $this->num_objects_in_list - $count;
            }

            if ($first < 0)
            {
              $first = 0;
            }

            $ids = array ();
            $this->_start_id_search ($first, $count);
            while ($this->_ids_exist ())
            {
              $id = $this->_load_id ();
              if ($id == $selected_id)
              {
                $index_of_selected_id = sizeof ($ids);
              }
              $ids [] = $id;
            }

            $id_list = implode (',', $ids);
            $this->_write_value ('id_list', $id_list);
            $this->_write_value ('id_list_pos', $first);

            $pos_of_local_list = $first;
            $rebuild_cache = false;

            $this->_record ("Repositioned cache to [$first] with id at pos [$index_of_selected_id].");
          }
        }
      }
    }

    if ($rebuild_cache)
    {
      /* We need to extract ids from the database now. Search until we have 'window_size' more
         elements beyond the index of the selected element (enough to center the window around
         the currently selected element). If the id of the last element in the list is not yet
         known, we have extract ALL the ids from the db. Once this id is known, it is stored in
         the cookie separately, so even if we get an id outside of the current window, we still
         won't have to search for the last id again. */

      $this->_record ("Rebuilding list...");

      $i = 0;
      $index_of_selected_id = null;
      $first_id = null;
      $last_id = null;

      $ids = array ();
      $this->_start_id_search ();
      while ($this->_ids_exist ())
      {
        $id = $this->_load_id ();
        if ($id == $selected_id)
        {
          $index_of_selected_id = sizeof ($ids);
        }
        if (! isset ($index_of_selected_id) || ($i < $index_of_selected_id + ($this->window_size * 2)) || ! isset ($last_id))
        {
          $ids [] = $id;
        }
        else
        {
          break;
        }
      }

      if (! isset ($last_id))
      {
        $first_id = $ids [0];
        $last_id = $ids [sizeof ($ids) - 1];
        $this->_write_value ('id_first', $first_id);
        $this->_write_value ('id_last', $last_id);
      }

      /* Figure out the position of the cache window within the whole element list.
         Retrieve those ids from the list and store them back in the cookie as cached. */

      $cache_start = $index_of_selected_id - ($this->window_size * 2);
      $cache_count = ($this->window_size * 4);

      $this->_record ($cache_start);

      if ($cache_start + $cache_count > $this->num_objects_in_list)
      {
        $cache_start = $this->num_objects_in_list - $cache_count;
      }

      if ($cache_start < 0)
      {
        $cache_start = 0;
      }

      if ($cache_start + $cache_count > $this->num_objects_in_list)
      {
        $cache_count = $this->num_objects_in_list - $cache_start;
      }

      $this->_record ("Retrieving cache from [$cache_start] ([$cache_count] items).");

      $ids = array_slice ($ids, $cache_start, $cache_count);

      $this->_record ("Caching [" . sizeof ($ids) . "] ids.");

      $id_list = implode (',', $ids);
      $this->_write_value ('id_list', $id_list);
      $this->_write_value ('id_size', $this->num_objects_in_list);
      $this->_write_value ('id_list_pos', $cache_start);

      $pos_of_local_list = $cache_start;
      $index_of_selected_id -= $pos_of_local_list;
    }

    $abs_index_of_selected_id = $pos_of_local_list + $index_of_selected_id;

    $this->position_of_selected_id = $abs_index_of_selected_id + 1;

    $half_win_size = floor ($this->window_size / 2);
    $cache_size = sizeof ($ids);

    if (($abs_index_of_selected_id - $half_win_size) > 0)
    {
      $prev_page_index = max (0, $index_of_selected_id - $this->window_size);
      $prev_page_id = $ids [$prev_page_index];
    }

    if (($abs_index_of_selected_id + $half_win_size) < $this->num_objects_in_list)
    {
      $next_page_index = min ($cache_size - 1, $index_of_selected_id + $this->window_size);
      $next_page_id = $ids [$next_page_index];
    }

    if ($abs_index_of_selected_id > 0)
    {
      $prev_id = $ids [$index_of_selected_id - 1];
    }
    if ($abs_index_of_selected_id < $this->num_objects_in_list - 1)
    {
      $next_id = $ids [$index_of_selected_id + 1];
    }

    $first = $abs_index_of_selected_id - floor ($this->window_size / 2);
    $count = $this->window_size;

    if ($first + $count > $this->num_objects_in_list)
    {
      $first = $this->num_objects_in_list - $count;
    }

    if ($first < 0)
    {
      $first = 0;
    }

    if ($first + $count > $this->num_objects_in_list)
    {
      $count = $this->num_objects_in_list - $first;
    }

    $this->objects_in_window = $this->_load_objects_in_range ($first, $count);
    $this->selected_object = $this->objects_in_window [$abs_index_of_selected_id - $first];

    $count = sizeof ($this->objects_in_window);
    $this->_record ("Got [$count] objects from [$first]");

    if (isset ($index_of_selected_id))
    {
      $special_ids = array ();

      if (isset ($prev_id))
      {
        $special_ids [] = $prev_id;
      }
      if (isset ($next_id))
      {
        $special_ids [] = $next_id;
      }

      if ($first_id != $selected_id)
      {
        $special_ids [] = $first_id;
      }
      if ($last_id != $selected_id)
      {
        $special_ids [] = $last_id;
      }

      if (isset ($prev_page_id))
      {
        $special_ids [] = $prev_page_id;
      }
      if (isset ($next_page_id))
      {
        $special_ids [] = $next_page_id;
      }

      if (sizeof ($special_ids))
      {
        $special_ids = implode (',', $special_ids);
        $this->_record ("Loading special ids [$special_ids]");
        $special_objs = $this->_load_objects_at_ids ($special_ids);

        foreach ($special_objs as $obj)
        {
          $indexed_objs [$obj->id] = $obj;
        }

        if (isset ($prev_id))
        {
          $this->previous_object = $indexed_objs [$prev_id];
        }
        if (isset ($next_id))
        {
          $this->next_object = $indexed_objs [$next_id];
        }

        if ($first_id != $selected_id)
        {
          $this->first_object = $indexed_objs [$first_id];
        }
        if ($last_id != $selected_id)
        {
          $this->last_object = $indexed_objs [$last_id];
        }

        if (isset ($prev_page_id))
        {
          $this->previous_page_object = $indexed_objs [$prev_page_id];
        }
        if (isset ($next_page_id))
        {
          $this->next_page_object = $indexed_objs [$next_page_id];
        }
      }
    }
  }

  /**
   * Return a name to use as a key in a storage mechanism.
   * @param string $name
   * @return string
   * @access private
   */
  protected function _stored_name ($name)
  {
    return $name;
  }

  /**
   * Calculate the number of objects in this master list.
   * This is only called if the value is not found in storage.
   * @return integer
   * @access private
   * @abstract
   */
  protected abstract function _num_objects_in_list ();

  /**
   * Load the requested objects.
   * @param string $ids Comma-separated list of ids to load.
   * @return object[]
   * @access private
   * @abstract
   */
  protected abstract function _load_objects_at_ids ($ids);

  /**
   * Load the requested objects.
   * @param integer $first Index within master list for first item.
   * @param integer $count Number of items to retrieve from that position.
   * @return object[]
   * @access private
   * @abstract
   */
  protected abstract function _load_objects_in_range ($first, $count);

  /**
   * Read a value from storage.
   * @param string $name
   * @return string
   * @access private
   * @abstract
   */
  protected abstract function _read_value ($name);

  /**
   * Write a value to storage.
   * @param string $name
   * @param string $value
   * @access private
   * @abstract
   */
  protected abstract function _write_value ($name, $value);

  /**
   * Record a message to a logging mechanism.
   * @param string $msg
   * @param string $type Type of the message; see {@link Msg_type_debug_info}
   * @param string $channel identifier indicating the channel on which the message is sent.
   * @access private
   * @abstract
   */
  protected abstract function _record ($msg, $type = Msg_type_debug_info, $channel = Msg_channel_system);

  /**
   * Start looking for ids in the given range.
   * @param integer $first Index within master list for first item.
   * @param integer $count Number of items to retrieve from that position.
   * @access private
   * @abstract
   */
  protected abstract function _start_id_search ($first = 0, $count = 0);

  /**
   * Are there ids left to iterate?
   * Start the iteration cycle with {@link _start_id_search()}.
   * @return boolean
   * @access private
   * @abstract
   */
  protected abstract function _ids_exist ();

  /**
   * Go to the next id in an iteration.
   * Start the iteration cycle with {@link _start_id_search()}.
   * @return integer
   * @access private
   * @abstract
   */
  protected abstract function _load_id ();
}

?>