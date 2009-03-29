<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage log
 * @version 3.0.0
 * @since 2.3.0
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
require_once ('webcore/log/logger.php');

/**
 * Echoes messages to the screen.
 * Provides white-space handling for HTML and plain-text rendering.
 * @package webcore
 * @subpackage log
 * @version 3.0.0
 * @since 2.3.0
 * @abstract
 */
class TEXT_OUTPUT_LOGGER extends LOGGER
{
  /**
   * Show the day with each log item?
   * @var boolean
   */
  public $show_date = TRUE;
  /**
   * Show the time with each log item?
   * @var boolean
   */
  public $show_time = TRUE;
  /**
   * Show the message type with each log item?
   * @var boolean
   */
  public $show_type = TRUE;
  /**
   * Show the originating channel?
   * It can be useful to shut this off if a logger only has one channel open.
   * @var boolean
   */
  public $show_channel = TRUE;
  /**
   * Empty messages are replaced with this string (makes it easier to see empty messages).
    * @var string
    */
  public $empty_message = '---';

  function set_is_html ($value = TRUE)
  {
    $this->_is_html = $value;

    if ($value)
    {
      $this->_space = '&nbsp;';
      $this->_new_line = "<br>\n";
    }
    else
    {
      $this->_space = ' ';
      $this->_new_line = "\n";
    }
  }

  /**
   * Adds spaces to the left side of 's'
    * @param string $s
    * @param string $chars Number of chars to pad to.
    * @access private
    */
  function _pad_left ($s, $chars)
  {
    return str_repeat ($this->_space, $chars - strlen ($s)) . $s;
  }

  /**
   * Processes the message and passes it on to {@link TEXT_OUTPUT_LOGGER::output()}.
   * @param string $msg
   * @param string $channel
   * @param string $type
   * @param boolean $has_html
   * @access private
   */
  function _record ($msg, $type, $channel, $has_html)
  {
    $this->_output ($this->_format_initial ($msg, $type, $channel, $has_html));
  }

  /**
   * Processes the message and passes it on to {@link TEXT_OUTPUT_LOGGER::output()}.
   * @param string $msg
   * @param boolean $has_html
   * @access private
   */
  function _record_more ($msg, $has_html)
  {
    $this->_output ($this->_format_more ($msg, $has_html));
  }

  /**
   * Start a new logging block.
   * @param string $title
   * @access private
   */
  function _open_block ($title)
  {
    $this->_output ($this->_format_block ($title));
  }

  /**
   * Formats a message from {@link TEXT_OUTPUT_LOGGER::_record()}.
   * This is separated in order to allow descendants to override with their own formatting.
   * @param string $msg
   * @param string $type
   * @param string $channel
   * @param boolean $has_html
   * @access private
   */
  function _format_initial ($msg, $type, $channel, $has_html)
  {
    $header = $this->_format_header ($type, $channel);
    $this->_last_header_length = strlen ($header);
    $this->_last_type = $type;
    $this->_last_channel = $channel;
    return $this->_format_message ($this->_prepare ($header . $this->_convert_to_text ($msg, $has_html)));
  }

  /**
   * Formats a message from {@link TEXT_OUTPUT_LOGGER::_record_more()}.
   * This is separated in order to allow descendants to override with their own formatting.
   * @param string $msg
   * @param boolean $has_html
   * @access private
   */
  function _format_more ($msg, $has_html)
  {
    return $this->_format_message ($this->_prepare ($this->_pad_left ('', $this->_last_header_length) . $this->_convert_to_text ($msg, $has_html)));
  }

  /**
   * Formats the initial header with type and channel.
   * @param string $type
   * @param string $channel
   * @return string
   * @access private
   */
  function _format_header ($type, $channel)
  {
    $header_parts = array ();

    if ($this->show_channel)
    {
      $header_parts [] = $channel;
    }

    if ($this->show_date)
    {
      if ($this->show_time)
      {
        $header_parts [] = date ('Y-m-j H:i:s');
      }
      else
      {
        $header_parts [] = date ('Y-m-j');
      }
    }
    else if ($this->show_time)
 {
   $header_parts [] = date ('H:i:s');
 }

    if ($this->show_type)
    {
      $header_parts [] = $this->type_to_string ($type);
    }

    $Result = str_repeat ($this->_space, 2 * $this->_block_level);
    $num_parts = sizeof ($header_parts);
    if ($num_parts)
    {
      if ($num_parts == 1)
      {
        $Result .= '[' . $header_parts [0] . ']: ';
      }
      else
      {
        $Result .= '[' . join ('] [', $header_parts) . ']: ';
      }
    }

    return $Result;
  }

  /**
   * Formats a message from {@link _open_block()}.
   * Uses {@link _format_html_tag()} to build the tag for the block in when
   * formatted as HTML.
   * @param string $title
   * @access private
   */
  function _format_block ($title)
  {
    if ($this->_is_html)
    {
      $Result = $this->_format_html_tag ($title, 'log-block-title');
    }
    else
    {
      $Result = str_repeat ($this->_space, 2 * ($this->_block_level - 1)) . $this->_prepare ($title);
    }
    return $Result;
  }

  /**
   * Transforms the given 'msg' to text or HTML.
   * Adds a type-specific style to the output string if formatted as HTML. Uses
   * {@link _format_html_tag()} to build the HTML tag.
   * @param string $msg
   * @return string
   * @access private
   */
  function _format_message ($msg)
  {
    if ($this->_is_html)
    {
      $Result = $this->_format_html_tag ($msg, $this->_CSS_class_for_type ($this->_last_type));
    }
    else
    {
      $Result = $msg;
    }
    return $Result;
  }

  /**
   * Transforms the given 'msg' to text.
   * Converts to human-readable version of False/empty string/unset variable and transforms
   * arrays and objects using PHP's print_r function.
   * @param string $msg
   * @param boolean $has_html
   * @return string
   * @access private
   */
  function _convert_to_text ($msg, $has_html)
  {
    if (is_object ($msg) || is_array ($msg))
    {
      if (is_a ($msg, 'DATE_TIME'))
      {
        $msg = $msg->format_plain ();
      }
      else
      {
        $msg = print_r_capture ($msg);
        if ($this->_is_html)
        {
          $msg = '<pre>' . $msg . '</pre>';
        }
      }
    }
    else
    {
      if (! isset ($msg))
      {
        $msg = $this->empty_message;
      }
      elseif ($msg === '')
        $msg = "'" . $this->empty_message . "'";
      elseif ($msg === FALSE)
        $msg = 'FALSE';
      elseif ($msg === TRUE)
        $msg = 'TRUE';
      else
      {
        if ($this->_is_html)
        {
          if (! is_resource ($msg))
          {
            if (! $has_html)
            {
              $msg = htmlspecialchars ($msg);
            }

            $msg = nl2br ($msg);
          }
        }
      }
    }

    return $msg;
  }

  /**
   * Applies formatting to the final message text.
   * @param string $msg
   * @return string
   * @access private
   */
  function _prepare ($msg)
  {
    return $msg;
  }

  /**
   * Renders the message to a specific media.
   * @param string $msg
   * @access private
   * @abstract
   */
  function _output ($msg)
  {
    $this->raise_deferred ('_output', 'TEXT_OUTPUT_LOGGER');
  }

  /**
   * Wrap output in an HTML tag.
   * @param string $text
   * @param string $class CSS class to use for the message.
   * @access private
   */
  function _format_html_tag ($text, $class)
  {
    return "<span class=\"$class\">$text</span>";
  }

  /**
   * CSS class to use for the given message type.
   * @param string $type
   * @return string
   * @access private
   */
  function _CSS_class_for_type ($type)
  {
    switch ($type)
    {
    case Msg_type_debug_info:
      return 'log-debug-info';
    case Msg_type_debug_warning:
      return 'log-debug-warning';
    case Msg_type_info:
      return 'log-info';
    case Msg_type_warning:
      return 'log-warning';
    case Msg_type_error:
      return 'log-error';
    }
  }

  /**
   * Number of characters in the last message header.
   * Used with {@link TEXT_OUTPUT_LOGGER::_format_more} to left-align all lines of the same message.
   * @var integer
   * @access private
   */
  protected $_last_header_length;
  /**
   * Symbol to demarcate lines.
   * @var string
   * @access private
   */
  protected $_new_line = "\n";
  /**
   * Symbol to make a single space in the text output format.
   * @var string
   * @access private
   */
  protected $_space = ' ';
  /**
   * Set by {@link set_is_html()}.
   * @var boolean
   * @access private
   */
  protected $_is_html = FALSE;
  /**
   * Number of open blocks in the log.
   * @var integer
   * @access private
   */
  protected $_block_level;
}

?>