<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
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
require_once ('webcore/constants.php');
require_once ('webcore/sys/resolver.php');
require_once ('webcore/sys/date_time.php');
require_once ('webcore/sys/exception_handlers.php');
require_once ('webcore/sys/files.php');
require_once ('webcore/sys/url.php');
require_once ('webcore/log/logger_container.php');

/**
 * Global properties of the execution environment.
 * There is only one of these per execution, just like there is only one page. The
 * environment is a subset of functionality that exists outside of the page. Allows
 * some functionality to be used outside of the context of the WebCore page object
 * (like exception handling).
 * @see PAGE
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.2.1
 */
class ENVIRONMENT extends RESOLVER
{
  /**
   * Name of the web site.
   * Identifies the entire web site, not just a single page instance. Used by
   * {@link LOCATION_MENU::add_root_link()} if set.
   * @var string
   */
  public $title = '';

  /**
   * Unique ID for this framework.
   * Do not change this as it is used for migration and versioning.
   * @var string
   */
  public $framework_id = 'com.earthli.webcore';

  /**
   * Version number of the WebCore library.
   * @var string
   */
  public $version = '3.4.0';

  /**
   * Default identifier for this environment.
   * Set this value with {@link set_host_properties()}.
   * @var string
   */
  public $default_domain;

  /* Helper objects */

  /**
   * Defaults to {@link global_date_time_toolkit()}.
   * Provided for convenience.
   * @var DATE_TIME_TOOLKIT
   */
  public $date_time_toolkit;

  /**
   * Settings for file operations.
   * Defaults to {@link global_file_options()}.
   * @see global_url_options() for a URL-style implementation.
   * @var FILE_OPTIONS
   */
  public $file_options;

  /**
   * Settings for url operations.
   * Defaults to {@link global_url_options()}.
   * @see global_file_options() for a URL-style implementation.
   * @var URL_OPTIONS
   */
  public $url_options;

  /**
   * List of logging objects.
   * May be empty.
   * @var LOGGER_CONTAINER
   */
  public $logs;

  /**
   * Profiling object used to collect global performance information.
   * May be null.
   * @var PROFILER
   */
  public $profiler;

  /* Exception handling */

  /**
   * Should redirect requests be honored?
   * This is often set to true when debugging pages like form submissions. It allows things like the
   * {@link JS_CONSOLE_LOGGER} to display output. It will also let you see any PHP warnings and notices
   * issued during the update.
   * @var boolean
   * @access private
   */
  public $ignore_redirects = false;

  /**
   * To which page should exceptions be redirected?
   * This property is used by the {@link REDIRECT_EXCEPTION_HANDLER} if no default page is given.
   * @var string
   */
  public $exception_handler_page = '{pages}/handle_exception.php';

  /**
   * Location of the webcore library files.
   * Real file system path; should end in a path delimiter. Initialized at startup.
   * @var string
   */
  public $library_path;

  /* Debugging/logging */

  /**
   * Is debugging enabled?
   * This property determines whether the "debug" flag has any effect.
   * @var boolean
   */
  public $debug_enabled = false;

  /**
   * Is this a debugging session?
   * Use {@link setup_debugging()} to enable debugging.
   */
  public $debugging = false;

  /**
   * The exact debugging flags.
   * By default, the WebCore can pass debugging flags using post and get variables. These
   * indicate which channels should be loggeed. Use {@link setup_debugging()} to enable debugging.
   */
  public $debugging_flags = '';

  /**
   * Emulate conditions when running from a command line.
   * Forces {@link running_on_default_host()} and {@link is_http_server()} to
   * return <code>False</code>.
   * @var boolean
   */
  public $run_as_console = false;

  /**
   * Database queries executed in this session.
   * @var integer
   */
  public $num_queries_executed = 0;

  /**
   * Number of exceptions raised in this session.
   * If the default exception handler (set by {@link
   * set_default_exception_handler()} is of type {@link
   * ENVIRONMENT_EXCEPTION_HANDLER}, it will track the number of exceptions
   * raised.
   */
  public $num_exceptions_raised = 0;

  /**
   * Number of {@link WEBCORE_OBJECT}s created in this session.
   * @var integer
   */
  public $num_webcore_objects = 0;

  /**
   * Retains all query texts in order to detect duplicates.
   * Should only be used when debugging or doing development.
   * @var boolean
   * @access private
   */
  public $warn_if_duplicate_query_executed = false;

  /**
   * Logs the name of each {@link WEBCORE_OBJECT} created and loaded.
   * Should only be used when debugging or doing development.
   * @var boolean
   * @access private
   */
  public $log_class_names = false;

  /**
   * Style sheet for HTML-style logs.
   * @var string
   */
  public $logger_style_sheet = '{styles}/log.css';

  public function __construct ()
  {
    $this->date_time_toolkit = global_date_time_toolkit ();
    $this->file_options = global_file_options ();
    $this->url_options = global_url_options ();
    $this->logs = new LOGGER_CONTAINER ();

    parent::__construct ();

    $this->auto_detect_os ();

    /* server-local paths */
    $this->set_path (Folder_name_system_temp, temp_folder ());
    $this->set_forced_root (Folder_name_system_temp, false);
    $this->set_path (Folder_name_logs, '/var/log');
    $this->set_forced_root (Folder_name_logs, false);

    /* URLs */
    $this->set_path (Folder_name_root, '/');
    $this->set_path (Folder_name_resources, '{' . Folder_name_root . '}');
    $this->set_path (Folder_name_apps, '{' . Folder_name_root . '}');
    $this->set_path (Folder_name_data, '{' . Folder_name_root . '}data');
    $this->set_path (Folder_name_pages, '{' . Folder_name_resources . '}');
    $this->set_path (Folder_name_functions, '{' . Folder_name_pages . '}');
    $this->set_path (Folder_name_icons, '{' . Folder_name_resources . '}icons');
    $this->set_path (Folder_name_themes, '{' . Folder_name_resources . '}themes');
    $this->set_path (Folder_name_styles, '{' . Folder_name_resources . '}styles');
    $this->set_path (Folder_name_scripts, '{' . Folder_name_resources . '}scripts');

    $this->set_extension (Folder_name_themes, 'css');
    $this->set_extension (Folder_name_styles, 'css');
    $this->set_extension (Folder_name_scripts, 'js');
    $this->set_extension (Folder_name_icons, 'png');

    /* Set up the path to the library. */

    $url = new FILE_URL (realpath (__FILE__));
    $url->strip_name ();
    $url->go_back ();
    $url->go_back ();
    $this->library_path = $url->as_text ();
  }

  /**
   * Full path to the install location of this application.
   * @return FILE_URL
   */
  public function source_path ()
  {
    $Result = new FILE_URL (realpath (__FILE__));
    $Result->strip_name ();
    $Result->go_back ();
    return $Result;
  }

  /**
   * Returns the name and version of the framework.
   * @var boolean $as_html Return HTML-formatted if <code>true</code>.
   * @return string
   */
  public function description ($as_html = true)
  {
    if ($as_html)
    {
      return 'earthli WebCore&trade; ' . $this->version;
    }

    return 'earthli WebCore (TM) ' . $this->version;
  }

  /**
   * Retrieve information about the client.
   * @return BROWSER
   */
  public function browser ()
  {
    if (! isset ($this->_browser))
    {
      $class_name = $this->final_class_name ('BROWSER', 'webcore/util/browser.php');
      $this->_browser = new $class_name ();
    }

    return $this->_browser;
  }

  /**
   * Returns false if running from command line.
   * @return boolean
   */
  public function is_http_server ()
  {
		return !($this->run_as_console || php_sapi_name() == 'cli');
  }

  /**
   * Is this script running on its own server?
   * Returns True is this page is an 'island' of code in a page on a web-server other than that
   * which is the declared web-server. This allows another server to host a 'top ten' articles,
   * for example, while pointing all links to the main server. Will also return False when running
   * from the command line.
   * @see is_http_server()
   * @see server_domain()
   * @see host_name()
   * @return boolean
   */
  public function running_on_declared_host ()
  {
    if ($this->run_as_console)
    {
      return false;
    }

    if (! isset ($this->_running_on_declared_host))
    {
      $this->_running_on_declared_host = preg_match ('/^' . str_replace('/', '\\/', 'http://' . $this->server_domain ()) . '/', $this->url_options->main_domain);
    }

    return $this->_running_on_declared_host;
  }

  /**
   * Description of the server.
   * Usually includes the Apache and PHP versions.
   * @return string
   */
  public function server_info ()
  {
    return read_array_index ($_SERVER, 'SERVER_SOFTWARE');
  }

  /**
   * Name of the server as returned by PHP.
   * Returns empty if the script is not running on a server (running from the
   * command line).
   * @see running_on_declared_host()
   * @see host_name()
   * @return string
   */
  public function server_domain ()
  {
    return read_array_index ($_SERVER, 'HTTP_HOST');
  }

  /**
   * Name of the server as configured.
   * @see running_on_declared_host()
   * @see host_name()
   * @return string
   */
  public function default_domain ()
  {
    return $this->default_domain;
  }

  /**
   * Non-empty name of the server for this process.
   * Returns the {@link current_host_name()} if running on a server; returns the
   * {@link domain()} if running from the command line. Use this function when
   * generating emails or content from the command line to ensure properly
   * resolved absolute URLs. Set the default domain value with {@link
   * set_host_properties()}.
   * @see running_on_declared_host()
   * @see server_domain()
   * @return string
   */
  public function host_name ()
  {
    $host_name = $this->server_domain ();
    if (! $host_name)
    {
      $host_name = $this->default_domain ();
    }
    return ensure_has_protocol ($host_name, 'http');
  }

  /**
   * Returns the requested parts of the current URL.
   * @param int $parts Can be any combination of {@link Url_part_host},
   * {@link Url_part_path}, {@link Url_part_name}, {@link Url_part_ext} and
   * {@link Url_part_args}.
   * @return string
   * @throws Exception
   */
  public function url ($parts = Url_part_no_args)
  {
    if (! isset ($this->_url))
    {
      $this->_url = new URL (read_array_index ($_SERVER, 'REQUEST_URI'));
      if ($this->_url->name () == '')
      {
        $this->_url->replace_name_and_extension ('index.php');
      }
    }

    $url = clone ($this->_url);

    switch ($parts)
    {
    case Url_part_all:
      return $this->host_name () . $url->as_text ();
    case Url_part_path:
      return $url->path ();
    case Url_part_file_name:
      return $url->name ();
    case Url_part_ext:
      return $url->extension ();
    case Url_part_no_host:
      return $url->as_text ();
    case Url_part_no_host_args:
      $url->replace_query_string ('');
      return $url->as_text ();
    case Url_part_no_args:
      $url->replace_query_string ('');
      return $this->host_name () . $url->as_text ();
    case Url_part_no_host_path:
      return $url->name_with_query_string ();
    default:
      throw new Exception('Unsupported combination of parts');
    }
  }

  /**
   * Is page-buffering enabled?
   * If this returns true, then the whole page is first prepared, then sent. This
   * allows redirects and cookies to be set throughout page preparation.
   * @return boolean
   */
  public function buffered ()
  {
    return $this->_buffered;
  }

  /**
   * Finish initializing the environment.
   * Should be called as the last step before calling {@link PAGE::run()}.
   */
  public function run ()
  {
    $this->root_url = $this->host_name ();
    $this->restore_root_behavior ();
  }

  /**
   * Set page buffering on or off.
   * This buffers content for the page until it is completely rendered on the
   * server. For larger pages, this means a longer delay on slow connections
   * before data begins arriving. You should enable this when using the {@link
   * REDIRECT_EXCEPTION_HANDLER}.
   * @param boolean $value
   */
  public function set_buffered ($value = true)
  {
    if ($this->_buffered != $value)
    {
      $this->_buffered = $value;

      if ($value)
      {
        ob_start ();
      }
      else
      {
        @ob_end_flush ();
      }
    }
  }

  /**
   * Set up the defaul domain name and url to document root mapping (used for
   * offline scripts and uploading).
   *
   * @see url_to_file_name()
   * @see url_to_folder()
   * @param string $default_domain Default host to use if ({@link
   * is_http_server()} is <code>False</code>).
   * @param string[] $domains Optional list of domains and their
   * corresponding server-local paths. The default mapping maps the server root
   * to the document root. However, you can customize the mapping with regular
   * expressions or add other sub-domains supported by this server. (e. g. array
   * ('mydomain. [net|com]' => '/var/www/mydomain/', '[www.]? otherdomain\.
   * [com|net|org]' => '/var/www/otherdomain/').
   */
  public function set_host_properties ($default_domain, $domains = null)
  {
    $this->default_domain = $default_domain;
    if (isset($domains))
    {
      $this->url_options->domains = $domains;
      $keys = array_keys($domains);
      $this->url_options->main_domain = $keys[0];
    }
    $this->_running_on_declared_host = null;
  }
  
  /**
   * Sets the time-zone explicitly to avoid relying on the server time-zone setting.
   * See the help for {@link PHP_MANUAL#date_default_timezone_set} for a list of valid time
   * zones. 
   *
   * @param string $time_zone
   */
  public function set_time_zone ($time_zone)
  {
    date_default_timezone_set($time_zone);
  }

  /**
   * Initialize debugging tools for WebCore objects.
   * @see ignore_redirect_requests()
   * @see set_up_release()
   * @param string $flags List of channels to log. Can be 'all' or any channel name.
   */
  public function set_up_debugging ($flags)
  {
    $this->ignore_redirect_requests ();

    if (isset ($this->logs->logger))
    {
      $this->logs->logger->reset ();
      switch ($flags)
      {
      case 'all':
      case '1':
        $this->logs->logger->set_enabled (Msg_type_all);
        break;
      default:
        $this->logs->logger->set_enabled (Msg_type_none);
        $this->logs->logger->set_channel_enabled ($flags, Msg_type_all);
      }
    }

    $this->warn_if_duplicate_query_executed = true;
    $this->log_class_names = true;
    $this->debugging = true;
    $this->debugging_flags = $flags;
  }

  /**
   * Turn off all debugging output and tracking.
   * @see set_up_debugging()
   */
  public function set_up_release ()
  {
    $this->warn_if_duplicate_query_executed = false;
    $this->log_class_names = false;
    $this->debugging = false;
    $this->debugging_flags = '';

    if (isset ($this->logs->logger))
    {
      $this->logs->logger->reset ();
      $this->logs->logger->set_enabled (Msg_type_error | Msg_type_warning);
    }
  }

  /**
   * Set up OS-specific {@link FILE_OPTIONS} automatically.
   * @see set_os()
   */
  public function auto_detect_os ()
  {
    if (strpos (strtoupper (php_uname ('s')), 'WIN') === 0)
    {
      $this->set_os (Os_win);
    }
    else
    {
      $this->set_os (Os_unix);
    }
  }

  /**
   * Set OS-specific {@link FILE_OPTIONS}.
   * @see auto_detect_os()
   * @param integer $os_type Can be {@link Os_win} or {@link Os_unix}.
   */
  public function set_os ($os_type)
  {
    switch ($os_type)
    {
      case Os_win:
        $this->file_options->path_delimiter = "\\";
        $this->file_options->end_of_line = "\r\n";
        break;
      case Os_unix:
        $this->file_options->path_delimiter = "/";
        $this->file_options->end_of_line = "\n";
        break;
      default:
        $this->raise ("Invalid os type [$os_type]", 'set_os', 'ENVIRONMENT');
    }
  }

  /**
   * Prevent any redirections using the WebCore.
   * @see set_up_debugging()
   */
  public function ignore_redirect_requests ()
  {
    $this->ignore_redirects = true;
    set_default_exception_handler (new LOGGER_EXCEPTION_HANDLER ($this));
  }

  /**
   * Redirect to an absolute URL on the same server.
   * This feature makes use of HTTP headers, so if the page is not cached using
   * {@link set_buffered()}, it must be called before any content is emitted.
   * 'location' should not contain the protocol (e.g. HTTP://).
   * To redirect to a fully resolved URL, use {@link redirect_remote()}.
   * To redirect to a relatively resolved URL on the same server, use (@link redirect_local()}.
   * @param string $location
   */
  public function redirect_root ($location)
  {
    $url = new URL ($location);
    $url->prepend ($this->host_name ());

    $this->redirect_remote ($url->as_text ());
  }

  /**
   * Redirect to a URL relative to the current one.
   * This feature makes use of HTTP headers, so if the page is not cached using
   * {@link set_buffered()}, it must be called before any content is emitted.
   * 'location' should not contain the protocol (e.g. HTTP://).
   * To redirect to a fully resolved URL, use {@link redirect_remote()}.
   * To redirect to a fully resolved URL on the same server, use (@link redirect_root()}.
   * @param string $location
   */
  public function redirect_local ($location)
  {
    $host_name = $this->host_name ();
    if (strpos ($location, $host_name) !== 0)
    {
      $url_path = $this->url (Url_part_path);
      if (strpos ($location, $url_path) === 0)
      {
        $url = new URL ($location);
      }
      else
      {
        $url = new URL ($url_path);
        $url->append ($location);
      }
      $url->prepend ($this->host_name ());
      $location = $url->as_text ();
    }

    $this->redirect_remote ($location);
  }

  /**
   * Redirect to an absolute URL.
   * 'location' should not contain the protocol (e.g. HTTP://).
   * This feature makes use of HTTP headers, so if the page is not cached using
   * {@link set_buffered()}, it must be called before any content is emitted.
   * 'location' should not contain the protocol (e.g. HTTP://).
   * To redirect to a relatively resolved URL on the same server, use (@link redirect_local()}.
   * To redirect to a fully resolved URL on the same server, use (@link redirect_root()}.
   * @param string $location
   */
  public function redirect_remote ($location)
  {
    if ($this->ignore_redirects)
    {
      log_message ("Redirection to [$location] was ignored.", Msg_type_warning, Msg_channel_system);
    }
    else
    {
      $this->assert ($this->is_http_server (), "Could not redirect to [$location]: not running from server", 'redirect_remote', 'SYSTEM', $this);
      header ('Location: ' . $location);
      exit;
    }
  }

  /**
   * Called from {@link restore_root_behavior()}.
   * @return boolean
   * @access private
   */
  protected function _default_resolve_to_root ()
  {
    return ! $this->running_on_declared_host ();
  }

  /**
   * Build entire page before sending?
   * With buffering enabled, the entire page is built first, then sent back to the client. With
   * buffering disabled, it is streamed. If buffering is off, the code cannot redirect once content
   * has been sent. Use {@link set_buffered()} to set this value.
   * @var boolean
   */
  protected $_buffered = false;

  /**
   * Access this object through {@link browser()}.
   * @var BROWSER
   * @access private
   */
  protected $_browser;

  /**
   * Cached URL used by {@link url()}.
   * @var URL
   * @access private
   */
  protected $_url;

  /**
   * Cached flag indicating whether the environment is local
   * to the declared server.
   * @var bool
   * @access private
   */
  protected $_running_on_declared_host;
}