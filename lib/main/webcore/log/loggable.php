<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage log
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
require_once ('webcore/obj/webcore_object.php');
require_once ('webcore/log/logger.php');

/**
 * Manages a {@link LOGGER} object internally.
 * @package webcore
 * @subpackage log
 * @version 3.1.0
 * @since 2.2.1
 */
class LOGGABLE extends WEBCORE_OBJECT
{
  /**
   * Messages are logged to this chain of {@link LOGGER}s.
   * @var LOGGER_CONTAINER
   */
  public $logs;

  /**
   * {@link record()} uses this channel, by default.
   * @var string
   */
  public $default_channel = Msg_channel_default;

  /**
   * {@link record()} uses this type, by default.
   * @var string
   */
  public $default_type = Msg_type_debug_info;
  
  /**
   * @param CONTEXT $context
   */
  public function LOGGABLE ($context)
  {
    WEBCORE_OBJECT::WEBCORE_OBJECT ($context);
    $this->logs = new LOGGER_CONTAINER ();
  }

  /**
   * Record a message to the logger, if one exists.
   * @param string $msg
   * @param string $channel
   * @param integer $type
   * @param boolean $has_html Does the message contain HTML tags that must be
   * preserved?
   */
  public function record ($msg, $type = '', $channel = '', $has_html = false)
  {
    if (isset ($this->logs->logger))
    {
      if (! $channel)
      {
        $channel = $this->default_channel;
      }
      if (! $type)
      {
        $type = $this->default_type;
      }
      $this->logs->logger->record ($msg, $type, $channel);
    }
  }
  
  /**
   * Record additional information. Uses the last message's filter.
   * @param string $msg The message itself.
   * @param boolean $has_html Does the message contain HTML tags that must be
   * preserved?
   */
  public function record_more ($msg, $has_html = false)
  {
    if (isset ($this->logs->logger))
    {
      $this->logs->logger->record_more ($msg, $has_html);
    }
  }
}

?>