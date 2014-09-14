<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tests
 * @version 3.6.0
 * @since 2.6.0
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
require_once ('webcore/sys/task.php');

/**
 * Channel used to log all migration-related messages.
 */
define ('Msg_channel_test', 'Test');

/**
 * Facilitates migrating databases for WebCore {@link APPLICATION}s.
 * @package webcore
 * @subpackage tests
 * @version 3.6.0
 * @since 2.6.0
 */
abstract class TEST_TASK extends TASK
{
  /**
   * Icon to show in the title bar when executing.
   * @var string
   */
  public $icon = '{icons}buttons/test';

  /**
   * Log all messages in this channel.
   * @var string
   */
  public $log_channel = Msg_channel_test;

  /**
   * Return a formatted title for this task.
   * Used as the {@link PAGE_TITLE::$subject} when executed.
   * @return string
   */
  public function title_as_text ()
  {
    return 'Running test [' . get_class ($this) . ']...';
  }

  /**
   * Log in as the default user.
   * @access private
   */
  protected function _log_in_as_tester ()
  {
    $this->app->impersonate ('tester', 'password');
  }

  /**
   * {@link _log()} an error if the value is not true.
   * Records a debug message indicating success otherwise.
   * @param boolean $value
   * @param string $msg
   */
  protected function _check ($value, $msg)
  {
    if (! $value)
    {
      $this->_log ('Check failed for: "' . $msg . '"', Msg_type_error);
      if ($this->stop_on_error)
      {
        $this->_abort("Stopping on error.");
      }
    }
    else
    {
      $this->_log ('Check succeeded for: "' . $msg . '"', Msg_type_debug_info);
    }
  }

  /**
   * {@link _log()} an error if the values are not equal.
   * Records a debug message indicating success otherwise.
   * 
   * @param object $expected
   * @param object $actual
   */
  protected function _check_equal ($expected, $actual)
  {
    if ($expected != $actual)
    {
      $this->_log ("Expected [$expected]", Msg_type_error);
      $this->_log_more ("Received [$actual]");

      if ($this->stop_on_error)
      {
        $this->_abort("Stopping on error.");
      }
    }
    else
    {
      $this->_log ("Expected and got [$expected].", Msg_type_debug_info);
    }
  }
}

?>