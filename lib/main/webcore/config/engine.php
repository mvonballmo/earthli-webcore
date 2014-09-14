<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage config
 * @version 3.6.0
 * @since 2.7.0
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
include_once ('webcore/sys/resolver.php');

/**
 * Manages execution of a WebCore execution.
 * Instantiate an engine to process a web page request or execute a job in a
 * script. Redefine {@link _initialize_class_registry()} to override which
 * {@link $env}, {@link $logger} and {@link $page} objects are created. Override
 * {@link _init_environment()}, {@link _init_logger()} and {@link _init_page()}
 * to configure them after creation.
 * @package webcore
 * @subpackage config
 * @version 3.6.0
 * @since 2.7.0
 */
class ENGINE extends RESOLVER
{
  /**
   * Global environment for this session.
   * Call {@link init()} to create and initialize.
   * @var ENVIRONMENT
   */
  public $env;

  /**
   * Global page for this session.
   * Call {@link init()} to create and initialize.
   * @var PAGE
   */
  public $page;

  /**
   * If true, logging is initialized.
   * Override {@link _make_logger()} to create custom logging.
   * @see _make_console_logger()
   * @see _make_echo_logger()
   * @see _make_text_file_logger()
   * @var boolean
   */
  public $use_logging = true;

  /**
   * If true, checks for and initializes debug mode using
   * {@link apply_debug_settings()}.
   * @var boolean
   */
  public $use_debug_mode = true;

  /**
   * Initialize global objects for this session.
   * Creates {@link $env}, {@link $logger} and {@link $page}; call this
   * before calling {@link run()}.
   */
  public function init ()
  {
    /* Create the environment and connect the debugging tools. */

    $this->env = $this->_make_environment ();

    global $Profiler;
    if (isset ($Profiler))
    {
      $this->env->profiler = $Profiler;
    }

    if ($this->use_logging)
    {
      $logger = $this->_make_logger ($this->env);
      if (isset ($logger))
      {
        $this->_init_logger ($this->env, $logger);
        $this->env->logs->set_logger ($logger);
      }
    }

    /* Init the environment once all debugging objects are attached. */

    $this->_init_environment ($this->env);
    $this->env->run ();

    /* Create and initialize the page object. */

    $this->page = $this->_make_page ($this->env);
    $this->_init_page ($this->env, $this->page);
  }

  /**
   * Register plugins in {@link $classes} during initialization.
   * @access private
   */
  protected function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();
    $this->register_class ('PAGE', 'THEMED_PAGE', 'webcore/sys/themed_page.php');
    $this->register_class ('LOGGER', 'NULL_LOGGER', 'webcore/log/logger.php');
  }

  /**
   * Make the global environment for this session.
   * Uses the {@link US_DATE_TIME_FORMATTER} to provide EST/US settings. Calls
   * {@link _init_environment()} to allow customization.
   * @see _init_environment()
   * @return ENVIRONMENT
   * @access private
   */
  protected function _make_environment ()
  {
    $class_name = $this->final_class_name ('ENVIRONMENT', 'webcore/sys/environment.php');
    return new $class_name ();
  }

  /**
   * Called immediately after creating an environment.
   * Override this function to customize initialization, calling {@link
   * _apply_european_settings()} or {@link _apply_iso_settings()} if desired.
   *
   * A call to {@link ENVIRONMENT::set_buffered()} enables buffering for the
   * page until it is completely rendered on the server. For larger pages, this
   * means a longer delay on slow connections before data begins arriving. This
   * is also automatically enabled when using {@link
   * REDIRECT_EXCEPTION_HANDLER}.
   *
   * @see _make_environment()
   * @param ENVIRONMENT $env
   * @access private
   */
  protected function _init_environment ($env)
  {
    $env->title = 'WebCore';
    $env->set_buffered ();
  }

  /**
   * Adjust the environment for Europe (GMT+1).
   * Replaces the standard {@link US_DATE_TIME_FORMATTER} with the
   * {@link EURO_DATE_TIME_FORMATTER}, which uses a different time zone and
   * different formatting.
   * @param ENVIRONMENT $env
   * @access private
   */
  protected function _apply_european_settings ($env)
  {
    $env->date_time_toolkit->formatter = new EURO_DATE_TIME_FORMATTER ();
  }

  /**
   * Adjust the environment for ISO dates.
   * Replaces the standard {@link US_DATE_TIME_FORMATTER} with the
   * {@link ISO_DATE_TIME_FORMATTER}, which uses different formatting.
   * @param ENVIRONMENT $env
   * @access private
   */
  protected function _apply_iso_settings ($env)
  {
    $env->date_time_toolkit->formatter = new ISO_DATE_TIME_FORMATTER ();
  }

  /**
   * Set up the WebCore session in "debug" mode.
   * Reads the contents of the "debug" request value. If it is non-empty, this
   * function calls {@link ENVIRONMENT::set_up_debugging()}. If not, it calls
   * {@link _apply_release_settings()}. Release web sites should call
   * <code>_apply_release_settings</code> instead.
   */
  protected function _apply_debug_settings ($env)
  {
    $debug = read_var ('debug');
    if ($debug)
    {
      $env->set_up_debugging ($debug);
    }
    else
    {
      $this->_apply_release_settings ($env);
    }
    $env->debug_enabled = true;
  }

  /**
   * Set up the WebCore session in "release" mode.
   * Creates a {@link REDIRECT_EXCEPTION_HANDLER} to abort display of a page
   * and redirect the exception to an error handler page.
   * @see _apply_debug_settings()
   */
  protected function _apply_release_settings ($env)
  {
    $env->debug_enabled = false;
    set_default_exception_handler (new REDIRECT_EXCEPTION_HANDLER ($env));
  }

  /**
   * Make the global logger for this session.
   * Override the "LOGGER" extension point to control which type of object is
   * created or use one of {@link _make_console_logger()}, {@link
   * _make_echo_logger()} or {@link _make_file_logger()} to create one or more
   * loggers. Link loggers together using {@link LOGGER::add_logger()}.
   *
   * This method is called before {@link _init_environment()} -- the "logs"
   * alias will not be available. This is a compromise so that the environment
   * startup can be logged.
   *
   * @param ENVIRONMENT $env
   * @return LOGGER
   * @access private
   */
  protected function _make_logger ($env)
  {
    $class_name = $this->final_class_name ('LOGGER', 'webcore/log/logger.php');
    return new $class_name ();
  }

  /**
   * Return a logger that displays messages dynamically.
   * Logged messages display in a separate popup window generated using
   * JavaScript. Override the "JS_CONSOLE_LOGGER" extension point to control
   * which type of object is created.
   * @see _make_echo_logger()
   * @see _make_text_file_logger()
   * @param ENVIRONMENT $env
   * @return JS_CONSOLE_LOGGER
   * @access private
   */
  protected function _make_console_logger ($env)
  {
    $class_name = $this->final_class_name ('JS_CONSOLE_LOGGER', 'webcore/log/js_console_logger.php');
    return new $class_name ($env);
  }

  /**
   * Return a logger that displays messages dynamically.
   * Logged messages display directly in the generated page. Override the
   * "ECHO_LOGGER" extension point to control which type of object is created.
   * @see _make_text_file_logger()
   * @see _make_console_logger()
   * @param ENVIRONMENT $env
   * @return ECHO_LOGGER
   * @access private
   */
  protected function _make_echo_logger ($env)
  {
    $class_name = $this->final_class_name ('ECHO_LOGGER', 'webcore/log/echo_logger.php');
    return new $class_name ($env);
  }

  /**
   * Return a logger that records messages to a plain text file.
   * Override the "FILE_LOGGER" extension point to control which type of object
   * is created.
   * @see _make_echo_logger()
   * @see _make_console_logger()
   * @param ENVIRONMENT $env
   * @param string $file_name Path to the log file; resolved relative to the
   * environment. Use the {@link Folder_name_logs} alias to store in the default
   * logs folder.
   * @param boolean $is_html False uses plain text formatting.
   * @return FILE_LOGGER
   * @access private
   */
  protected function _make_text_file_logger ($env, $file_name, $is_html = false)
  {
    $class_name = $this->final_class_name ('FILE_LOGGER', 'webcore/log/file_logger.php');
    $Result = new $class_name ($env);

    /* Resolve the log file to use relative to the environment. */
    $Result->set_file_name ($env->resolve_file ($file_name));

    /* Display message type only if the log is plain text (HTML
     * uses a CSS class).
     */
    $Result->set_is_html ($is_html);
    $Result->show_type = ! $is_html;

    return $Result;
  }

  /**
   * Called immediately after creating a logger.
   * Override this function to customize initialization. By default, all errors
   * and warnings are enabled, as well as any messages issued on the default
   * channel (which are messages you are likely to emit with the when calling
   * {@link log_message()}.
   * @see _make_logger()
   * @param LOGGER $logger
   * @access private
   */
  protected function _init_logger ($env, $logger)
  {
    $logger->set_enabled (Msg_type_error | Msg_type_warning);
    $logger->set_channel_enabled (Msg_channel_default, Msg_type_all);
  }

  /**
   * Make the global page for this session.
   * Calls {@link _init_page()} to allow customization.
   * @param ENVIRONMENT $env
   * @return PAGE
   * @access private
   */
  protected function _make_page ($env)
  {
    $class_name = $this->final_class_name ('PAGE', 'webcore/sys/page.php');
    return new $class_name ($env);
  }

  /**
   * Called immediately after creating a page.
   * Override this function to customize initialization.
   * @see _make_page()
   * @param ENVIRONMENT $env
   * @param THEMED_PAGE $page
   * @access private
   */
  protected function _init_page ($env, $page)
  {
    /* Set default properties for the <PAGE_TITLE>. */

    $page->title->group = $env->title;

    /* Force common local storage (client-side cookies) for the entire site. */

    $page->storage->prefix = 'webcore_';
    $page->storage->path = '/';

    /* Customize the page template. The example below adjusts the logo and
     * copyright rendered by the default template (See DEFAULT_TEMPLATE_RENDERER).
     * For more information on individual settings, see the documentation for
     * <PAGE_TEMPLATE_OPTIONS> and <PAGE_ICON_OPTIONS>.
     */

    $page->template_options->logo_file = '{icons}/logos/earthli_webcore_logo_full';
    $page->template_options->logo_title = 'earthli WebCore';
    $page->template_options->copyright = "Copyright (c) " . date ('Y') . " Copyright holder. All Rights Reserved.";

    $page->icon_options->file_name = '{icons}/logos/webcore_icon';
    $page->icon_options->mime_type = 'image/png';

    /* Set the default theme. Look in the '{styles}/fonts' folder for font faces,
     * '{styles}/core' for font sizes and '{styles}/themes' for themes.
     */

    $page->default_theme->main_CSS_file_name = '{styles}themes/ice';
    $page->default_theme->font_name_CSS_file_name = '{styles}fonts/verdana';
    $page->default_theme->font_size_CSS_file_name = '{styles}core/small';

    /* Set up database options. */

    $page->database_options->host = 'localhost';  // sets the default
    $page->database_options->name = 'earthli';
    $page->database_options->user_name = 'root';  // sets the default
    $page->database_options->password = '';

    /* Set up basic mailing options. */

    $page->mail_options->enabled = false;
    $page->mail_options->SMTP_server = $env->default_domain ();
    $page->mail_options->webmaster_address = 'webmaster@' . $env->default_domain ();
    $page->mail_options->send_from_address = 'webmaster@' . $env->default_domain ();
    $page->mail_options->send_from_name = $env->title;
    $page->mail_options->log_file_name = '{logs}earthli_mail.log';

    /* Set up icon aliases; controls which paths are updated by themes. */

    $page->add_as_icon_listener_to ($env);
    $page->add_as_icon_listener_to ($page);
    $page->add_icon_alias ($env, Folder_name_icons);
  }
}

?>