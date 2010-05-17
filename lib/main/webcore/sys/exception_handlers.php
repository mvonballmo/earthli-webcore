<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
 * @version 3.3.0
 * @since 3.1.0
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

/***/
require_once ('webcore/sys/system.php');

/**
 * Handler that works with the {@link ENVIRONMENT}.
 * @package webcore
 * @subpackage sys
 * @version 3.3.0
 * @since 2.7.0
 */
class ENVIRONMENT_EXCEPTION_HANDLER extends EXCEPTION_HANDLER
{
  /**
   * @param ENVIRONMENT $env Global environment.
   */
  public function __construct ($env)
  {
    $this->env = $env;
  }

  /**
   * Generates a plain-text page halt with the error information.
   * @param string $message The error message
   * @param string $routine_name The name of the routine where the error occurred (can be empty)
   * @param string $class_name The name of the class where the error occurred (can be empty)
   * @param object $obj Reference to the object where the error occurred (can be empty)
   */
  public function raise ($message, $routine_name, $class_name, $obj)
  {
    $this->env->num_exceptions_raised += 1;
    parent::raise ($message, $routine_name, $class_name, $obj);
  }
}

/**
 * HTML-formatted exception message.
 * @package webcore
 * @subpackage sys
 * @version 3.3.0
 * @since 2.2.1
 */
class HTML_EXCEPTION_HANDLER extends ENVIRONMENT_EXCEPTION_HANDLER
{
  /**
   * Show the error as HTML if running from the server.
   * @param EXCEPTION_SIGNATURE $sig
   * @param string $msg Pre-formatted error message (not used here).
   * @access private
   */
  public function dispatch ($sig, $msg)
  {
    if ($this->env->is_http_server ())
    {
      if (isset ($sig->is_derived_type))
      {
        die ("<div class=\"error\">Fatal error in <span class=\"field\">$sig->dynamic_class_name => $sig->scope</span>: $sig->message</div>");
      }
      else
      {
        die ("<div class=\"error\">Fatal error in <span class=\"field\">$sig->scope</span>: $sig->message</div>");
      }
    }
    else
    {
      parent::dispatch ($sig, $msg);
    }
  }
}

/**
 * Logs the exception using the {@link ENVIRONMENT::$logger}.
 * May also be configured to continue operating using {@link $stop_on_error}.
 * @see function raise()
 * @package webcore
 * @subpackage sys
 * @version 3.3.0
 * @since 2.4.0
 */
class LOGGER_EXCEPTION_HANDLER extends HTML_EXCEPTION_HANDLER
{
  /**
   * Constructs a default instance; seems to be required for PHP5.
   */
  public function __construct($env)
  {
    parent::__construct($env);
  }

  /**
   * Should the handler kill execution?
   * @var boolean
   */
  public $stop_on_error = true;

  /**
   * Send the error to the logger.
   * @param EXCEPTION_SIGNATURE $sig
   * @param string $msg Pre-formatted error message.
   * @access private
   */
  public function dispatch ($sig, $msg)
  {
    log_message ($msg, Msg_type_error, Msg_channel_system);
    if ($this->stop_on_error)
    {
      $this->env->logs->close_all ();
      parent::dispatch ($sig, $msg);
    }
  }
}

/**
 * Fatal errors are redirected to another page.
 * @package webcore
 * @subpackage sys
 * @version 3.3.0
 * @since 2.2.1
 */
class REDIRECT_EXCEPTION_HANDLER extends HTML_EXCEPTION_HANDLER
{
  /**
   * @var ENVIRONMENT
   */
  public $env;

  /**
   * @param ENVIRONMENT $env Global environment.
   * @param string $handler_url Redirect exceptions to this url.
   */
  public function __construct ($env)
  {
    if (! $env->exception_handler_page)
    {
      raise ('Exception handler URL cannot be empty.', 'REDIRECT_EXCEPTION_HANDLER', 'REDIRECT_EXCEPTION_HANDLER');
    }

    $this->env = $env;
  }

  /**
   * Send the error to the logger.
   * @param EXCEPTION_SIGNATURE $sig
   * @param string $msg
   * @access private
   */
  public function dispatch ($sig, $msg)
  {
    if ($this->env->is_http_server () && $this->env->buffered () && $this->env->exception_handler_page)
    {
      $current_url = $this->env->url ();
      $handler_url = $this->env->resolve_file ($this->env->exception_handler_page);
      if (strpos ($current_url, $handler_url) === false)
      {
        $parameters = $sig->as_query_string ();
        $this->env->redirect_root ($handler_url .'?' . $parameters);
      }
      else
      {
        parent::dispatch ($sig, $msg);
      }
    }
    else
    {
      parent::dispatch ($sig, $msg);
    }
  }
}

?>