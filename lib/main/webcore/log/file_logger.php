<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage log
 * @version 3.3.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
 * Records messages to file.
 * @package webcore
 * @subpackage log
 * @version 3.3.0
 * @since 2.2.1
 */
class FILE_LOGGER extends TEXT_OUTPUT_LOGGER
{
  /**
   * Symbol to demarcate lines.
   * @var string
   */
  public $newline = "\n";

  /**
   * Record to this file.
   * Does not open the logger if not already open. Directory must exist.
   * @param string $name
   */
  public function set_file_name ($fn)
  {
    if ($this->_file_name != $fn)
    {
      if ($this->is_open ())
      {
        $this->close ();
      }

      $url = new FILE_URL ($fn);
      $url->ensure_file_exists ();
      $this->_file_handle = @fopen ($fn, 'a');

      if ($this->is_open ())
      {
        $this->_file_name = $fn;
        $this->_has_messages = false;
      }
    }
  }

  /**
   * Closes the file.
   * Stops logging until {@link FILE_LOGGER::set_file_name()} is called again.
   * @access private
   */
  protected function _close ()
  {
    if ($this->is_open ())
    {
      if ($this->_has_messages)
      {
        $this->record ('Log closed', Msg_type_info, Msg_channel_logger);
      }
      @fclose ($this->_file_handle);
      $this->_file_handle = null;
    }
  }

  /**
   * Renders the message to the file (if open).
   * @param string $msg
   * @access private
   */
  protected function _output ($msg)
  {
    if ($this->is_open ())
    {
      if (! $this->_has_messages)
      {
        $this->_has_messages = true;
        $this->record ("Log opened [$this->_file_name]", Msg_type_info, Msg_channel_logger);
      }

      fputs ($this->_file_handle, $msg . $this->_new_line);
    }
  }

  /**
   * @return boolean
   */
  public function is_open ()
  {
    return isset ($this->_file_handle) && ! ($this->_file_handle == false);
  }

  /**
   * @var string Name of the log file.
   * @access private
   */
  protected $_file_name = '';

  /**
   * @var boolean Have any messages been written?
   * @access private
   */
  protected $_has_messages = false;

  /**
   * @var integer Handle to the open file
   * @access private
   */
  protected $_file_handle;
}

?>