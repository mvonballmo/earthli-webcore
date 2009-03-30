<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage log
 * @version 3.0.0
 * @since 2.7.0
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
 * Manages a chain of {@link LOGGER}s.
 * @package webcore
 * @subpackage log
 * @version 3.0.0
 * @since 2.7.0
 */
class LOGGER_CONTAINER extends RAISABLE
{
  /**
   * Sub-logger, can be empty.
   * Loggers can be linked in chains, but each is constrained by its parent's filter.
   * @var LOGGER
   */
  public $logger;

  /**
   * Set the logger. Replaces all other loggers.
   * Use {@link add_logger()} to append a logger to the chain.
   * @param LOGGER $logger
   * @param boolean $copy_settings Copies settings from the current logger if
   * <code>True</code>.
   */
  public function set_logger ($logger, $copy_settings = true)
  {
    if ($copy_settings && $this->logger)
    {
      $this->logger->copy_settings_to ($logger);
    }
    $this->logger = $logger;
  }

  /**
   * Add this logger to the chain. 
   * Existing loggers remain in the list. Use {@link set_logger()} to replace
   * the whole chain of current loggers.
   * @param LOGGER $logger
   * @param boolean $copy_settings Copies settings from the current logger if
   * <code>True</code>.
   */
  public function add_logger ($logger, $copy_settings = true)
  {
    if (isset ($this->logger))
    {
      $this->logger->add_logger ($logger, $copy_settings);
    }
    else
    {
      $this->set_logger ($logger, $copy_settings);
    }
  }
  
  /**
   * Removes all loggers of this type (or descendant).
   * @param string $class_name
   */
  public function remove_loggers_of_type ($class_name)
  {
    if (isset ($this->logger))
    {
      if (is_a ($this->logger, $class_name))
      {
        if (isset ($this->logger->logger))
        {
          $this->logger = $this->logger->logger;
        }
        else
        {
          $this->logger = null; 
        }
      }
      if (isset ($this->logger))
      {
        $this->logger->remove_loggers_of_type ($class_name);
      }
    }
  }

  /**
   * Indicates that logging is complete.
   * Called {@link LOGGER::close()} on all nested loggers.
   */
  public function close_all ()
  {
    if (isset ($this->logger))
    {
      $this->logger->close ();
    }
  }
}

?>