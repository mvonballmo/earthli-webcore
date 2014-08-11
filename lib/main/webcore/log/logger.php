<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage log
 * @version 3.5.0
 * @since 2.2.1
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

/***/
require_once ('webcore/log/logger_container.php');

/**
 * A debugging message.
 * This message is of interest only to developers and should be mostly used for
 * establishing context. DebugInfo messages are commonly used to indicate that
 * something is happening or about to happen, but which couldn't possibly interest
 * an end-user. Use them to indicate that an event is about to happen, like Starting
 * web browser..., so then if an error message follows that doesn't explain enough,
 * these messages can provide context.
 * @see log_message()
 */
define ('Msg_type_debug_info', 1);
/**
 * A warning that should not be shown to an end-user.
 * This message is also of interest mainly to developers, but indicates an error condition
 * that was solved internally. The application could continue with default behavior, but the
 * program had to make a decision on it's own and indicates this with a warning. These warnings
 * are especially useful for developers that are working with a library. The library emits
 * these messages to indicate that the library is either being mis-used or not used optimally.
 * @see log_message()
 */
define ('Msg_type_debug_warning', 2);
/**
 * An action was taken by the application.
 * This message is of potential interest to an end-user. It is typically the type of message which
 * an end-user could use to solve problems in the program. For example, imagine a program that
 * loads a configuration file from a particular spot. The program should emit this message when
 * the file is loaded, so that end-user can see which files a program is using if desired.
 * @see log_message()
 */
define ('Msg_type_info', 4);
/**
 * The application made a decision which may affect output.
 * Imagine the same program from {@link Msg_type_info} attempts to load the configuration file, but doesn't find
 * one. This isn't necessarily an error, since the program works fine without a configuration file.
 * However, if the user has created a configuration file, but saved it to the wrong spot, then that
 * user would be very interested in a warning that names the file that was sought, but not found.
 * Given this warning, the user can place the file in the correct location and no longer wonder why the
 * default settings are always applied.
 * @see log_message()
 */
define ('Msg_type_warning', 8);
/**
 * A fatal error, but not an exception.
 * In the same program as above, imagine the user provides a configuration file and puts it in the correct location.
 * However, the user has defined an illegal setting in this file. Depending on the importance of the setting, this is
 * either an error or a warning. If the setting specifies a database file to open that doens't exist, then it's an
 * error, since the program cannot determine an appropriate default behavior on its own. If the setting is for an
 * option with a maximum value of 10, but the user specifies 20, this would be a warning.
 * @see log_message()
 */
define ('Msg_type_error', 16);
/**
 * Represents all messages types.
 * @see LOGGER::set_enabled(), LOGGER::set_channel_enabled()
 */
define ('Msg_type_all', 31);
/**
 * Represents no messages (blocks everything).
 * @see LOGGER::set_enabled(), LOGGER::set_channel_enabled()
 */
define ('Msg_type_none', 0);
/**
 * Represents all debugging message types.
 * @see LOGGER::set_enabled(), LOGGER::set_channel_enabled()
 */
define ('Msg_type_all_debug', 3);

/**
 * Messages without an explicit channel are recorded to this one.
 * The main logging functions allow a user to work without channels. These messages are recorded to
 * the 'default' channel.
 * @see log_message()
 */
define ('Msg_channel_default', 'Default');
/**
 * Messages sent from the logging system are recorded in this channel.
 * Use this channel only in classes descended from the logging system.
 * @access private
 */
define ('Msg_channel_logger', 'Logger');

/**
 * @see log_message()
 */
define ('Msg_has_html', true);

$Logger = 0;

/**
 * Records this message to the global logger.
 * Will not cause an error if the logger does not exist.
 * @see log_more()
 * @param string $msg The message itself.
 * @param string $channel The channel to which the message belongs.
 * @param integer $type The message type.
 * @param boolean $has_html Does the message contain HTML tags that must be preserved?
 */
function log_message ($msg, $type = Msg_type_debug_info, $channel = Msg_channel_default, $has_html = false)
{
  global $Logger;

  if ($Logger)
  {
    $Logger->record ($msg, $type, $channel, $has_html);
  }
}

/**
 * Records this message to the global logger.
 * Records using the same type and channel as the previous message.
 * Use this function for messages with multiple lines. Will not cause
 * an error if the logger does not exist.
 * @see log_message()
 * @param string $msg The message itself.
 * @param boolean $has_html Does the message contain HTML tags that must be preserved?
 */
function log_more ($msg, $has_html = false)
{
  global $Logger;

  if ($Logger)
  {
    $Logger->record_more ($msg, $has_html);
  }
}

/**
 * Starts a new logging block in the global logger.
 * Will not cause an error if the logger does not exist.
 * @param string $title The name of the block.
 */
function log_open_block ($title)
{
  global $Logger;

  if ($Logger)
  {
    $Logger->open_block ($title);
  }
}

/**
 * Close the last open logging block.
 * Will not cause an error if the logger does not exist.
 */
function log_close_block ()
{
  global $Logger;

  if ($Logger)
  {
    $Logger->close_block ();
  }
}

/**
 * Used by the {@link LOGGER} to maintain which filters it uses.
 * Separated from the logger to facilitate sharing.
 * @package webcore
 * @subpackage log
 * @version 3.5.0
 * @since 2.2.1
 * @access private
 */
class LOGGER_FILTER_SETTINGS
{
  /**
   * Is the given filter accepted in the given channel?
   * @param string $channel
   * @param integer $type
   * @return boolean
   */
  public function allowed ($channel, $type)
  {
    $channel = strtolower ($channel);

    if (isset ($this->_channels [$channel]))
    {
      $filter = $this->_channels [$channel];
    }
    else
    {
      $filter = $this->_default_filter;
    }

    return $filter & $type;
  }

  /**
   * Adjust the default filter.
   * Used only when there is no channel-specific filter assigned.
   * @see LOGGER_FILTER_SETTINGS::set_channel_enabled ()
   * @param integer $type Type of messages to enable/disable.
   */
  public function set_enabled ($type)
  {
    $this->_default_filter = $type;
  }

  /**
   * Adjust the filter for a particular channel.
   * @param string $channel Channel in which to adjust the filter.
   * @param integer $type Type of messages to enable/disable.
   */
  public function set_channel_enabled ($channel, $type)
  {
    $this->_channels [strtolower ($channel)] = $type;
  }

  /**
   * Turn all messages back on, clearing channels.
   */
  public function reset ()
  {
    $this->_channels = array ();
    $this->_default_filter = Msg_type_all;
  }

  /**
   * @var array
   * @access private
   */
  protected $_default_filter = Msg_type_all;

  /**
   * @var array
   * @access private
   */
  protected $_channels;
}

/**
 * Records messages of different type.
 * Supports filtering different types of messages and can be chained together
 * with other loggers.
 * @package webcore
 * @subpackage log
 * @version 3.5.0
 * @since 2.2.1
 * @abstract
 */
abstract class LOGGER extends LOGGER_CONTAINER
{
  /**
   * Sub-logger, can be empty.
   * Loggers can be linked in chains, but each is constrained by its parent's filter.
   * @var LOGGER
   */
  public $logger;

  public function __construct ()
  {
    $this->_filter_settings = new LOGGER_FILTER_SETTINGS ();
    $this->set_channel_enabled (Msg_channel_logger, Msg_type_all);
  }

  /**
   * Adjust the default filter.
   * Used only when there is no channel-specific filter assigned.
   * @see LOGGER::set_channel_enabled ()
   * @param integer $type Type of messages to enable/disable.
   */
  public function set_enabled ($type)
  {
    $this->assert (is_int ($type), "Filter must be an integer (if [$type] is a channel, use 'set_channel_enabled' instead).", 'set_enabled', 'LOGGER');

    $this->_filter_settings->set_enabled ($type);
  }

  /**
   * Adjust the filter for a particular channel.
   * @param string $channel Channel in which to adjust the filter.
   * @param integer $type Type of messages to enable/disable.
   */
  public function set_channel_enabled ($channel, $type)
  {
    $this->_filter_settings->set_channel_enabled ($channel, $type);
  }

  /**
   * Turn all messages back on, clearing channels.
   */
  public function reset ()
  {
    $this->_filter_settings->reset ();
    $this->set_channel_enabled (Msg_channel_logger, Msg_type_all);
  }

  /**
   * Copy this logger's filter settings to the parameter.
   * @param LOGGER $logger
   */
  public function copy_settings_to ($logger)
  {
    $logger->_filter_settings = $this->_filter_settings;
  }

  /**
   * Records the message to the global logger if one exists.
   * @param string $msg The message itself.
   * @param integer $type The message type.
   * @param boolean $has_html Does the message contain HTML tags that must be
   * preserved?
   */
  public function record ($msg, $type = Msg_type_debug_info, $channel = Msg_channel_default, $has_html = false)
  {
    if ($this->_passes_filter ($channel, $type))
    {
      $this->_record ($msg, $type, $channel, $has_html);
    }

    $this->_last_channel = $channel;
    $this->_last_type = $type;

    if (isset ($this->logger))
    {
      $this->logger->record ($msg, $type, $channel, $has_html);
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
    $this->_record_more ($msg, $has_html);
    if (isset ($this->logger))
    {
      $this->logger->record_more ($msg, $has_html);
    }
  }

  /**
   * Start a new logging block.
   * @param string $title The name of the block.
   */
  public function open_block ($title)
  {
    $this->_block_level += 1;
    $this->_open_block ($title);
    if (isset ($this->logger))
    {
      $this->logger->open_block ($title);
    }
  }

  /**
   * Close the last open block.
   */
  public function close_block ()
  {
    $this->_block_level -= 1;
    $this->_close_block ();
    if (isset ($this->logger))
    {
      $this->logger->close_block ();
    }
  }

  /**
   * Closes the logger output, performing any cleanup.
   * All nested loggers are closed automatically.
   */
  public function close ()
  {
    $this->_close ();
    if (isset ($this->logger))
    {
      $this->logger->close ();
    }
  }

  /**
   * Returns a generic title for a given message type.
   * @param integer $type
   * @return string
   * @access private
   */
  public function type_to_string ($type)
  {
    switch ($type)
    {
    case Msg_type_debug_info:
      return 'debug-info';
    case Msg_type_debug_warning:
      return 'debug-warning';
    case Msg_type_info:
      return 'info';
    case Msg_type_warning:
      return 'warning';
    case Msg_type_error:
      return 'error';
    default:
      return 'unknown';
    }
  }

  /**
   * Override in descendants to finish logging to specific media.
   * File logs can close their files; Javascript loggers can output to the page.
   * @access private
   */
  protected function _close () {}

  /**
   * Is the given filter accepted in the given channel?
   * @param string $channel
   * @param integer $type
   * @return bool
   * @access private
   */
  protected function _passes_filter ($channel, $type)
  {
    return $this->_filter_settings->allowed ($channel, $type);
  }

  /**
   * @param string $msg
   * @param integer $type
   * @param $channel
   * @param boolean $has_html
   * @see LOGGER::record()
   * @access private
   * @abstract
   */
  protected abstract function _record ($msg, $type, $channel, $has_html);

  /**
   * @param string $msg
   * @param boolean $has_html
   * @see LOGGER::record_more()
   * @access private
   * @abstract
   */
  protected abstract function _record_more ($msg, $has_html);

  /**
   * @param string $title
   * @access private
   * @see LOGGER::open_block()
   */
  protected function _open_block ($title) {}

  /**
   * @param string $title
   * @access private
   * @see LOGGER::close_block()
   */
  protected function _close_block () {}

  /**
   * @var LOGGER_FILTER_SETTINGS
   * @access private
   */
  protected $_filter_settings;

  /**
   * Set by {@link record()}.
   * Used by {@link record_more()} to know to which channel the last message was logged.
   * @var string
   * @access private
   */
  protected $_last_channel;

  /**
   * Set by {@link record()}.
   * Used by {@link record_more()} to know which type the last message was.
   * @var integer
   * @access private
   */
  protected $_last_type;

  /**
   * How many open blocks are there in this logger?
   * @var integer
   * @access private
   */
  protected $_block_level = 0;
}

/**
 * Does not record messages; records to null output.
 * @package webcore
 * @subpackage log
 * @version 3.5.0
 * @since 2.7.0
 */
class NULL_LOGGER extends LOGGER
{
  /**
   * @param string $msg
   * @param integer $type
   * @param boolean $has_html
   * @see LOGGER::record()
   * @access private
   */
  protected function _record ($msg, $type, $channel, $has_html)
  {
  }

  /**
   * @param string $msg
   * @param boolean $has_html
   * @see LOGGER::record_more()
   * @access private
   */
  protected function _record_more ($msg, $has_html)
  {
  }
}

?>