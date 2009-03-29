<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.6.0
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

/** */
require_once ('webcore/obj/webcore_object.php');

/**
 * Base class for maintenance tasks.
  * @package webcore
  * @subpackage sys
  * @version 3.0.0
  * @since 2.6.0
  * @abstract
  * @access private
  */
class TASK extends WEBCORE_OBJECT
{
  /**
   * Log all messages in this channel.
   * @var string
   */
  public $log_channel = Msg_channel_system;

  /**
   * Should debug messages be displayed in the log?
   * If this is enabled, all SQL commands issued during the process are logged to
   * the output console (web page).
   * @var boolean
   */
  public $log_debug = TRUE;

  /**
   * If true, does not actually execute SQL or other tasks.
   * The full process is executed, running through all scripts and emitting
   * all log messages, but no database/files/etc. changes are made.
   * @var boolean
   */
  public $testing;

  /**
   * Number of seconds to run before timeout occurs.
   * @var integer
   */
  public $time_out_in_seconds = 600;

  /**
   * Should the process stop if an error is encountered?
   * The task overrides the exception-handling system, logging everything to screen
   * instead of redirecting to another page. If this is False, the process will continue
   * despite fatal errors.
   */
  public $stop_on_error = TRUE;

  /**
   * Include system error and warning messages in output.
   * @var boolean
   */
  public $log_system_errors = TRUE;

  /**
   * Include generic debugging messages in output.
   * This allows debugging code from messages logged to {@link
   * Log_channel_default} to appear immediately -- without reloading the task
   * under debug mode.
   * @var boolean
   */
  public $log_default_errors = TRUE;

  /**
   * Include database messages in output.
   * @var boolean
   */
  public $log_database = FALSE;

  /**
   * Show more information during migration.
   * @var boolean
   */
  public $verbose = FALSE;
  /**
   * Force emulation of running from the command line.
   * @var boolean
   */
  public $run_as_console = FALSE;
  /**
   * Icon to show in the title bar when executing.
   * @var string
   */
  public $icon = '{icons}indicators/working';
  /**
   * Take care of drawing the page header and footer?
   * By default, a task will "take over" a page. Set this to <code>False</code>
   * to display only task information.
   * @var boolean
   */
  public $owns_page = TRUE;

  /**
   * Return a formatted title for this task.
   * Used as the {@link PAGE_TITLE::$subject} when executed.
   * @return string
   */
  function title_as_text ()
  {
    return 'Processing...';
  }

  /**
   * Execute the task.
   */
  function execute ()
  {
    set_time_limit ($this->time_out_in_seconds);
    $this->_set_up_exception_handling ();
    $this->_set_up_logging ();
    $this->_start ();

    if ($this->_can_be_executed ())
    {
      $this->_pre_execute ();
      $this->_execute ();
      $this->_post_execute ();
    }
    else
    {
      $this->_log ('Could not execute task.', Msg_type_error);
    }

    $this->_finish ();
  }

  /**
   * Return a form to display options and execute this task.
   * @return FORM
   */
  function form ()
  {
    $class_name = $this->context->final_class_name ('EXECUTE_TASK_FORM', 'webcore/forms/execute_task_form.php');
    return new $class_name ($this->context);
  }

  /**
   * Halt the process with an optional error message.
   * @param string $msg
   * @access private
   */
  function _abort ($msg = '')
  {
    if (! $msg)
    {
      $msg = 'Process aborted';
    }
    $this->raise ($msg, '_abort', 'TASK');
  }

  /**
   * Execute an SQL query.
   * The workhorse method for {@link _execute()} implementations.
   * @param string $sql
   * @access private
   */
  function _query ($sql)
  {
    if (! $this->testing)
    {
      $db = $this->_database ();
      $db->query ($sql);
    }
    if ($this->verbose)
    {
      $this->_log ("Executed [$sql]", Msg_type_info);
    }
    else
    {
      $this->_log ("Executed [$sql]");
    }
  }

  /**
   * Return an open database.
   * Returns the databse given in the context, but makes sure it is
   * initialized first.
   * @return DATABASE
   * @access private
   */
  function _database ()
  {
    $this->context->ensure_database_exists ();
    return $this->context->database;
  }

  /**
   * Log a debug message.
   * @param string $msg
   * @param integer $type Can be {@link Msg_type_debug_info}, {@link Msg_type_info} or other log message type.
   */
  function _log ($msg, $type = Msg_type_debug_info, $has_html = FALSE)
  {
    log_message ($msg, $type, $this->log_channel, $has_html);
  }

  /**
   * Log more lines for the last debug message.
   * @see _log()
   * @param string $msg
   * @param integer $type Can be {@link Msg_type_debug_info}, {@link Msg_type_info} or other log message type.
   */
  function _log_more ($msg, $has_html = FALSE)
  {
    log_more ($msg, $has_html);
  }

  /**
   * Returns True if the task can be executed.
   * Defaults to True.
   * @return boolean
   * @access private
   */
  function _can_be_executed ()
  {
    return TRUE;
  }

  /**
   * Set up the process and begin page display.
   * @access private
   */
  function _start ()
  {
    if ($this->env->is_http_server ())
    {
      $this->env->set_buffered (FALSE);
      if ($this->owns_page)
      {
        $this->page->title->subject = $this->title_as_text ();
        $this->page->add_style_sheet ('{styles}log.css');
        $this->page->start_display ();
        echo "<div class=\"box\"><div class=\"box-title\">{$this->page->title->subject}</div><div class=\"box-body\">";
      }
      echo "<div class=\"log-box\">";
    }
  }

  /**
   * Finish displaying the page.
   * @access private
   */
  function _finish ()
  {
    $this->env->logs->close_all ();

    if ($this->env->is_http_server ())
    {
      echo "</div>";
      if ($this->owns_page)
      {
        echo "</div></div>";
        $this->page->finish_display ();
      }
    }
  }

  /**
   * Create a {@link LOGGER_EXCEPTION_HANDLER}, by default.
   * @access private
   */
  function _set_up_exception_handling ()
  {
    $handler = new LOGGER_EXCEPTION_HANDLER ($this->env);
    $handler->stop_on_error = $this->stop_on_error;
    set_default_exception_handler ($handler);
    $this->env->run_as_console = $this->run_as_console;
  }

  /**
   * Initialize any loggers needed for the process.
   * @access private
   */
  function _set_up_logging ()
  {
    $this->_logger = $this->_make_logger ();
    $this->_logger->set_enabled (0);
    $this->_add_log_channel ($this->log_channel);
    if ($this->log_database)
    {
      $this->_add_log_channel (Msg_channel_database);
    }
    if ($this->log_default_errors)
    {
      $this->_logger->set_channel_enabled (Msg_channel_default, Msg_type_all);
    }
    if ($this->log_system_errors)
    {
      $this->_logger->set_channel_enabled (Msg_channel_system, Msg_type_error + Msg_type_warning);
    }
    $this->env->logs->add_logger ($this->_logger, FALSE);
    $this->env->logs->remove_loggers_of_type ('JS_CONSOLE_LOGGER');
  }

  /**
   * Enables the given log channel on the task logger.
   * Allows {@link Msg_type_all} messages in debug mode; Disallows {@link Msg_type_all_debug}
   * if not.
   * @param string $channel
   * @access private */
  function _add_log_channel ($channel)
  {
    if ($this->log_debug)
    {
      $this->_logger->set_channel_enabled ($channel, Msg_type_all);
    }
    else
    {
      $this->_logger->set_channel_enabled ($channel, Msg_type_all - Msg_type_all_debug);
    }
  }

  /**
   * Make a logger to document the process.
   * @return LOGGER
   * @access private
   */
  function _make_logger ()
  {
    $class_name = $this->context->final_class_name ('ECHO_LOGGER', 'webcore/log/echo_logger.php');
    return new $class_name ($this->env);
  }

  /**
   * Perform setup for a process that will run.
   * {@link _can_be_executed()} has returned True.
   * @access private
   */
  function _pre_execute ()
  {
  }

  /**
   * Perform the actual process actions.
   * This is task- and application-specific.
   * @access private
   * @abstract
   */
  function _execute ()
  {
    $this->raise_deferred ('_execute', 'TASK');
  }

  /**
   * Perform cleanup for a process that has run.
   * {@link _can_be_executed()} has returned True.
   * @access private
   */
  function _post_execute ()
  {
  }

  /**
   * Reference to the logger created in {@link _make_logger()}.
   * @var LOGGER
   * @access private
   */
  protected $_logger;
}

?>