<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage log
 * @version 3.3.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
 * Echoes messages to the screen.
 * If the environment is a server, it uses an HTML line-break.
 * @package webcore
 * @subpackage log
 * @version 3.3.0
 * @since 2.2.1
 */
class ECHO_LOGGER extends TEXT_OUTPUT_LOGGER
{
  /**
   * Flushes output after each message if enabled.
   * @var boolean
   */
  public $flushed = false;

  /**
   * Initialize the on-screen logger by passing {@link ENVIRONMENT::is_http_server()} 
   * to {@link set_is_html()}.
   * @param ENVIRONMENT $env
   */
  public function __construct ($env)
  {
    parent::__construct ();
    $this->set_is_html ($env->is_http_server ());
  }

  /**
   * Renders the message to the screen or console.
   * @param string $msg
   * @access private
   */
  protected function _output ($msg)
  {
    echo $msg . $this->_new_line;
    if ($this->flushed)
    {
      flush ();
    }
  }
}

?>