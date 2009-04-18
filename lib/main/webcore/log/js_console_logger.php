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
require_once ('webcore/log/text_output_logger.php');

/**
 * Pops up a console window using JavaScript to show messages.
 * Messages are recorded to a buffer. If any message with filter set to 'Msg_action_record_popup'
 * came in, it dumps all messages to JavaScript and opens a console to display them. Does not work
 * if the page fails to load completely.
 * @package webcore
 * @subpackage log
 * @version 3.1.0
 * @since 2.2.1
 */
class JS_CONSOLE_LOGGER extends TEXT_OUTPUT_LOGGER
{
  /**
   * Width of the popup console.
   * @var integer
   */
  public $height = 600;

  /**
   * Height of the popup console.
   * @var integer
   */
  public $width = 600;

  /**
   * Name of the external style sheet to apply.
   * File name is resolves using {@link RESOURCE_MANAGER::resolve_file()}.
   * @var string
   */
  public $CSS_file_name;

  /**
   * Font to use. Use any valid CSS font-size.
   * @var string
   */
  public $font_size = '10pt';

  /**
   * Background color. Use any valid CSS color.
   * @var string
   */
  public $bg_color = 'black';

  /**
   * Foreground color. Use any valid CSS color.
   * @var string
   */
  public $fg_color = 'white';

  /**
   * Title for the console window.
   * @var string
   */
  public $title = 'earthli WebCore Console';

  /**
   * Show the time with each log item?
   * Sets the default to false (not really useful for live debugging).
   * @var boolean
   */
  public $show_time = false;

  /**
   * Show the date with each log item?
   * Sets the default to false (not really useful for live debugging).
   * @var boolean
   */
  public $show_date = false;

  /**
   * @param ENVIRONMENT $env Requires the global environment to check JavaScript capabilities.
   */
  public function JS_CONSOLE_LOGGER ($env)
  {
    TEXT_OUTPUT_LOGGER::TEXT_OUTPUT_LOGGER ($env);
    $this->set_is_html (true);
    $this->env = $env;
    $this->CSS_file_name = $env->logger_style_sheet;
  }

  /**
   * Remove all logged messages.
   */
  public function clear ()
  {
    $this->_messages = array ();
  }

  /**
   * Renders all cached messages to JavaScript.
   * Should be called near the end of the page rendering, so this routine stores the list
   * of messages in a relatively convenient (and standards-compliant) spot.
   * @access private
   */
  protected function _close ()
  {
    if (sizeof ($this->_messages) > 0)
    {
  ?>
  <script type="text/javascript">
  <!--
  var console = window.open ("", "_console", "width=<?php echo $this->width; ?>,height=<?php echo $this->height; ?>,screenX=0,screenY=30,resizable=yes,scrollbars=yes");
  console.document.open();
<?php
      if ($this->CSS_file_name)
      {
        $css_file_name = $this->env->resolve_file ($this->CSS_file_name);
        $style_sheet_info = "<link rel=\\\"stylesheet\\\" type=\\\"text/css\\\" href=\\\"$css_file_name\\\">";
      }
      else
      {
        $style_sheet_info = '';
      }
?>
  console.document.write ("<html><head><title><?php echo $this->title; ?><\/title><?php echo $style_sheet_info; ?><\/head><body class=\"log-box\">");
  console.document.write ("<p class=\"log-start\">Log started for [<?php echo $this->env->url (Url_part_file_name); ?>] at [<?php echo date ("Y-n-j H:i:s", time ()); ?>]<\/p>");
<?php
      foreach ($this->_messages as $msg)
      {
        $js_msg = preg_replace("&([^\\\\])/&", "\\1\\/", $msg->message);
?>
  console.document.write ("<?php echo $js_msg; ?><br>\n");
<?php
      }
?>
  console.document.write ("<p class=\"log-finish\">Log finished [<?php echo date ("Y-n-j H:i:s", time ()); ?>]<\/p>");
  console.document.write ("<\/body><\/html>");
  console.document.close();
	//-->
</script>
<?php
    }
  }

  /**
   * Stores the message for later display.
   * Handles HTML/new line conversions and sets a flag if the message should trigger the popup window.
   * @param string $msg
   * @param integer $type
   * @param boolean $has_html
   * @access private
   */
  protected function _record ($msg, $type, $channel, $has_html)
  {
    $this->_log_info->channel = $channel;
    $this->_log_info->type = $type;
    parent::_record ($msg, $type, $channel, $has_html);
  }

  /**
   * Stores the message for later display.
   * Handles HTML/new line conversions.
   * @param string $msg
   * @param boolean $has_html
   * @access private
   */
  protected function _record_more ($msg, $has_html)
  {
    $this->_log_info = $this->_messages [sizeof ($this->_messages) - 1];
    parent::_record_more ($msg, $has_html);
  }

  /**
   * Transforms the given 'msg' to text.
   * Adds a type-specific style to the output string.
   * @param string $msg
   * @return string
   * @access private
   */
  protected function _prepare ($msg)
  {
    $Result = parent::_prepare ($msg);
    $Result = str_replace ("\\", "\\\\", $Result);
    $Result = str_replace ('"', '\"', $Result);
    $Result = str_replace ("'", "\\'", $Result);
    $Result = str_replace ("\n", '\n', $Result);
    $Result = str_replace ("\r", '\n', $Result);
    return $Result;
  }

  /**
   * Renders the message to a specific media.
   * @param string $msg
   * @access private
   */
  protected function _output ($msg)
  {
    $this->_log_info->message = $msg;
    $this->_messages [] = clone($this->_log_info);
  }

  /**
   * Wrap output in an HTML tag.
   * @param string $text
   * @param string $class CSS class to use for the message.
   * @access private
   */
  protected function _format_html_tag ($text, $class)
  {
    return "<span class=\\\"$class\\\">$text</span>";
  }

  /**
   * Reference to the global environment.
   * @var ENVIRONMENT
   * @access private
   */
  public $env = null;
  
  /**
   * The buffer for the message currently being processed.
   *
   * @var LOG_INFO
   */
  protected $_log_info;

  /**
   * @var array[LOG_INFO]
   * @access private
   */
  protected $_messages = array ();
}

class LOG_INFO
{
  /**
   * Channel to which the message was recorded.
   *
   * @var string
   */
  public $channel;
  
  /**
   * The text of the recorded message.
   *
   * @var string
   */
  public $message;
  
  /**
   * The type of the recorded message.
   *
   * @var integer
   */
  public $type;
}

?>