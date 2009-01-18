<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage util
 * @version 3.0.0
 * @since 2.2.1
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

/***/
require_once ('webcore/sys/system.php');

/**
 * Maintains a time for a channel in the {@link PROFILER}.
 * @package webcore
 * @subpackage util
 * @version 3.0.0
 * @since 2.2.1
 * @access private
 */
class PROFILER_TIMER extends RAISABLE
{
  /**
   * Is this timer enabled?
    * @return boolean
    */
  function running ()
  {
    return isset ($this->_start_time);
  }

  /**
   * Return how much time has elapsed in this timer.
   * @param string $rounded_to Round to this many digits.
   */
  function elapsed ($rounded_to)
  {
    $Result = $this->_elapsed;
    if (isset ($this->_start_time))
      $Result += ($this->_current_time () - $this->_start_time);

    return round ($Result, $rounded_to);
  }

  /**
   * Reset elapsed time and start timing again.
   */
  function restart ()
  {
    $this->_elapsed = 0;
    $this->_num_starts = 0;
    $this->start ();
  }

  /**
   * Start tracking time.
   * Will be added to current elapsed time.
   */
  function start ()
  {
    if (! $this->_num_starts)
      $this->_start_time = $this->_current_time ();
    $this->_num_starts++;
  }

  /**
   * Stop tracking time.
   */
  function stop ()
  {
    $this->_num_starts--;
    if (! $this->_num_starts)
    {
      $this->_elapsed += $this->_current_time () - $this->_start_time;
      $this->_start_time = null;
    }
  }

  /**
   * Makes a single value out of the PHP 'microtime' function.
    * @return real
    * @access private
    */
  function _current_time ()
  {
    list ($usec, $sec) = explode (' ', microtime ());
    return ((float) $usec + (float) $sec);
  }

  /**
   * When was this profiler started?
   * @var real
   * @access private
   */
  var $_start_time;
  /**
   * How much time has elapsed in this timer?
   * @var real
   * @access private
   */
  var $_elapsed = 0;
  /**
   * How many times has {@link start()} been called without {@link stop()}?
   * @var integer
   * @access private
   */
  var $_num_starts = 0;
}

/**
 * Keeps track of multiple elapsed times, keyed by id.
 * For example, call start ('page'), then before a call to a db, call
 * start ('db'), then retrieve elapsed ('db'), then elapsed ('page') at
 * the end of page processing. You will have the time for the db call
 * and the time for processing the entire page.
 * @package webcore
 * @subpackage util
 * @version 3.0.0
 * @since 2.2.1
 * @see PROFILER_TIMER
 * @access private
 */
class PROFILER extends RAISABLE
{
  /**
   * Has a timer with this name been started?
   * @param string $id
   */
  function exists ($id)
  {
    return isset ($this->_timers [$id]);
  }

  /**
   * Returns the elasped time, in seconds.
    * If timer is running, it is not stopped.
    * @param string $id
    * @param string $rounded_to Round to this many digits.
    * @return float
    */
  function elapsed ($id, $rounded_to = 3)
  {
    $timer =& $this->_existing_timer ($id);
    if (isset ($timer))
      return $timer->elapsed ($rounded_to);
    else
      return 0;
  }

  /**
   * Is this timer enabled?
    * @param string $id
    * @return boolean
    */
  function running ($id)
  {
    $timer =& $this->_existing_timer ($id);
    if (isset ($timer))
      return $timer->running ();
    else
      return FALSE;
  }

  /**
   * Continues this timer, appending to the existing count (if any).
    * @param string $id
    */
  function start ($id)
  {
    if (! isset ($this->_timers [$id]))
      $this->_timers [$id] = new PROFILER_TIMER ();
    $this->_timers [$id]->start ();
  }

  /**
   * Starts this timer, resetting to 0 first.
    * @param string $id
    */
  function restart ($id)
  {
    if (! isset ($this->_timers [$id]))
      $this->_timers [$id] = new PROFILER_TIMER ();
    $this->_timers [$id]->restart ();
  }

  /**
   * Stop this timer.
    * Raises exception if this timer was not started.
    * @param string $id
    */
  function stop ($id)
  {
    $timer =& $this->_existing_timer ($id);
    if (isset ($timer))
    {
      $this->assert ($timer->running (), "Timer [$id] was never started.", 'stop', 'PROFILER');
      $timer->stop ();
    }
  }

  /**
   * Return a timer for 'id'.
   * If the timer does not exist, an exception is thrown.
   * @var strind id
   * @access private
   */
  function &_existing_timer ($id)
  {
    if (! isset ($this->_timers [$id]))
      log_message ("Timer [$id] does not exist", Msg_type_warning, Msg_channel_system);

    return $this->_timers [$id];
  }

  /**
   * @var array[object]
   */
  var $_timers = array ();
}

?>