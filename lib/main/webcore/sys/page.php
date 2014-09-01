<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
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

/** */
require_once ('webcore/constants.php');
require_once ('webcore/config/php_patches.php');
require_once ('webcore/config/page_config.php');
require_once ('webcore/sys/context.php');

/**
 * Encapsulates page-specific properties.
 * There is only one page object per request; it handles scripts, icon, style locations
 * and other rendering delegation.
 * @package webcore
 * @subpackage sys
 * @version 3.5.0
 * @since 2.2.1
 */
class PAGE extends CONTEXT
{
  /**
   * @var PAGE_TITLE
   */
  public $title;

  /**
   * @var string
   */
  public $doc_type = '<!DOCTYPE HTML>';

  /**
   * @var string
   */
  public $content_type = 'text/html; charset=ISO-8859-1';

  /**
   * Controls the page icon.
   * @var PAGE_ICON_OPTIONS
   */
  public $icon_options;

  /**
   * Controls the newsfeed header tags.
   * @var PAGE_NEWSFEED_OPTIONS
   */
  public $newsfeed_options;

  /**
   * Controls the meta refresh tag.
   * @var PAGE_REFRESH_OPTIONS
   */
  public $refresh_options;

  /**
   * Controls the formatting of the page template.
   * @var PAGE_TEMPLATE_OPTIONS
   * @see DEFAULT_PAGE_TEMPLATE
   */
  public $template_options;

  /**
   * @var string
   */
  public $keywords = '';

  /**
   * @var string
   */
  public $description = '';

  /**
   * @var string
   */
  public $author = '';
  
  /**
   * Name of the class to use for a page renderer.
   * @var string
   * @see DEFAULT_PAGE_RENDERER
   */
  public $renderer_class_name = 'DEFAULT_PAGE_RENDERER';

  /**
   * Path to the folder containing the renderer class file.
   * Class must be in a file with the same name, with extension PHP. The path should
   * be relative to the location of your PHP code.
   * @var string
   * @see DEFAULT_PAGE_RENDERER
   */
  public $renderer_file_path = 'webcore/sys/';

  /**
   * Manages the description of the page's location (used to display navigation)
   * @var LOCATION_MENU
   */
  public $location;

  /**
   * Is this page printable?
   * @var boolean
   */
  public $printable = false;

  /**
   * If true, generated URLs are post-processed to make them relative.
   * Applies only to URLs that are a sub-path of the current path. These are
   * resolved with '..' directories. URLs that fork into other branches are
   * mapped absolutely.
   * @see path_between()
   * @var boolean
   */
  public $prefer_relative_urls = true;

  /**
   * Should access violation errors redirect to another page?
   * @var boolean
   */
  public $redirect_security_violations = true;

  /**
   * @param ENVIRONMENT $env Global environment.
   */
  public function __construct ($env)
  {
    $this->inherit_resources_from ($env);
    $this->resolve_to_root = $env->resolve_to_root;
    $this->root_url = $env->url (Url_part_path);

    parent::__construct ($env);

    $this->is_page = true;
    $this->page = $this;

    $class_name = $this->final_class_name ('PAGE_TITLE', 'webcore/gui/page_title.php');
    $this->title = new $class_name ($this);

    $class_name = $this->final_class_name ('LOCATION_MENU', 'webcore/gui/location_menu.php');
    $this->location = new $class_name ($this);
    $this->location->renderer->separator_class = $this->display_options->location_class;

    $this->template_options = new PAGE_TEMPLATE_OPTIONS ();
    $this->template_options->show_statistics = $env->debug_enabled && $env->debugging;
    $this->icon_options = new PAGE_ICON_OPTIONS ($this);
    $this->refresh_options = new PAGE_REFRESH_OPTIONS ($this);
    $this->newsfeed_options = new PAGE_NEWSFEED_OPTIONS ($this);
  }

  /**
   * Add classes to the {@link $classes} object factory.
   * @access private
   */
  protected function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();
    $this->register_class ('PAGE_RENDERER', 'DEFAULT_PAGE_RENDERER', 'webcore/gui/default_page_renderer.php');
  }
  
  /**
   * Resources to use for resolving paths.
   * Returns {@link $app} if it is assigned to make sure that application-
   * specific resources are also resolved.
   * @return RESOURCE_MANAGER
   */
  public function resources ()
  {
    if (isset ($this->app))
    {
      return $this->app;
    }
    return $this;
  }

  /**
   * Should renderers/grids/etc. use DHTML?
   * @return boolean
   */
  public function dhtml_allowed ()
  {
    return $this->template_options->include_scripts && parent::dhtml_allowed ();
  }

  /**
   * Should dates use JavaScript to render local times?
   * @return boolean
   */
  public function local_times_allowed ()
  {
    return $this->template_options->include_scripts && parent::local_times_allowed ();
  }
  
  /**
   * Start painting the page (show the header).
   */
  public function start_display ()
  {
    $this->_renderer = $this->make_renderer ();
    $this->_renderer->start_display ();
  }

  /**
   * Handle a security violation.
   * Shows a message and uses the context to determine how much additional information
   * to show (in the page title or the navigation bar).
   * @param string $msg
   * @param WEBCORE_OBJECT $context
   */
  public function raise_security_violation ($msg, $context = null)
  {
    if ($this->redirect_security_violations && ! $this->env->ignore_redirects)
    {
      $msg = urlencode ($msg);
      $last_page = urlencode ($this->env->url (Url_part_all));
      $this->env->redirect_local ("access_denied.php?error_message=$msg&last_page=$last_page");
    }
    else
    {
      if ($this->env->ignore_redirects)
      {
        log_message ("Access violation redirection was ignored.", Msg_type_warning, Msg_channel_system);
      }

      if (is_a ($context, 'folder'))
      {
        $this->location->add_folder_link ($context);
        $this->title->add_object ($context);
      }
      else
      {
        $this->location->add_root_link ();
        if (is_a ($context, 'group'))
        {
          $this->location->append ('Groups', 'view_groups.php');
        }
        elseif (is_a ($context, 'user'))
        {
          $this->location->append ('Users', 'view_users.php');
        }
      }

      $this->title->subject = 'Access Denied';
      $this->location->append($this->title->subject, '', '{icons}/indicators/warning');

      $this->start_display ();
      ?>
      <div class="main-box">
        <div class="text-flow">
          <?php $this->show_message($msg); ?>
        </div>
      </div>
      <?php
      $this->finish_display ();
    }
  }

  /**
   * Handle a generic error.
   * Shows a message and uses the context to determine how much additional information
   * to show (in the page title or the navigation bar).
   * @param $message
   * @param string $caption
   * @param int|\WEBCORE_OBJECT $context
   * @internal param string $msg
   */
  public function raise_error ($message, $caption = '', $context = 0)
  {
    if (is_a ($context, 'folder'))
    {
      $this->location->add_folder_link ($context);
      $this->title->add_object ($context);
    }
    else
    {
      $this->location->add_root_link ();
      if (is_a ($context, 'group'))
      {
        $this->location->append ('Groups', 'view_groups.php');
      }
      elseif (is_a ($context, 'user'))
      {
        $this->location->append ('Users', 'view_users.php');
      }
    }
    
    if (! $caption)
    {
      $caption = $this->title->subject;
    }

    $this->title->subject = $caption;

    $this->location->append($this->title->subject, '', '{icons}/indicators/error');

    $this->start_display ();
    ?>
    <div class="main-box">
      <div class="text-flow">
        <?php $this->show_message($message); ?>
      <div>
    </div>
    <?php
    $this->finish_display ();
  }


  /**
   * Finish painting the page (show the footer).
   */
  public function finish_display ()
  {
    $this->_renderer->finish_display ();
  }

  /**
   * Register a WebCore application.
   * Makes this application with this unique id available to the environment.
   * Call {@link make_application()} to create an instance. This function is
   * commonly called from the application "init.php" file.
   * 
   * The albums application is registered like this:
   * 
   * <code>$Env->register_application (Album_application_id,
   * 'ALBUM_APPLICATION_ENGINE');
   * </code>
   * 
   * To customize this application, a deployment can create a new {@link
   * APPLICATION_ENGINE} and register it as a plugin using the standard {@link
   * register_class()} function. This is commonly done from the {@link ENGINE::
   * _init_page()} method in the site customizer class. That plugin registration
   * should look like this:
   * 
   * <code>
   * $page->register_class ('APPLICATION_ENGINE',
   * 'CUSTOM_ALBUM_APPLICATION_ENGINE', 'path/to/custom/application/class/file',
   * Album_application_id);
   * </code>
   * 
   * It is important to include the unique id for the application as the
   * <code>context</code>.
   * 
   * @param string $id Unique name of the application.
   * @param string $engine_name Name of the {@link APPLICATION_ENGINE} class to
   * create by default.
   * @return APPLICATION
   */
  public function register_application ($id, $engine_name)
  {
    if (! $this->is_registered ('APPLICATION_ENGINE', $id))
    {
      $this->register_class ('APPLICATION_ENGINE', $engine_name, 'plugins/' . $id . '.init.php', $id);
    }
  }
  
  /**
   * Create and initialize a WebCore application.
   * Pass in the unique id associated with the application to create. The id must
   * have already been passed to {@link register_application()} (this is commonly
   * done in the "init.php" file for the application). See the help for
   * <code>register_application</code> for details on how to customize the
   * creation of the application.
   * 
   * @param string $id Unique identifier for the application to create.
   * @param boolean $start Calls {@link APPLICATION_ENGINE::start()} if True.
   * @param boolean $set_as_default Calls {@link APPLICATION_ENGINE::
   * set_as_main_for()} using {@link $Page} if True.
   * 
   * @version 3.5.0
   * @since 2.7.0
   * @see APPLICATION_ENGINE
   * @return APPLICATION
   */
  public function make_application ($id, $set_as_default = false, $start = true)
  {
    $class_name = $this->final_class_name ('APPLICATION_ENGINE', 'webcore/config/application_engine.php', $id);
    $engine = new $class_name ();
    
    $engine->init ($this);
    
    if ($set_as_default)
    {
      $engine->set_main_app_for ($this);
    }
    
    if ($start)
    {
      $engine->start ();
    }
      
    return $engine->app;
  }

  /**
   * Add a javascript file to the header.
   * 'url' can include folder aliases (e.g. "{scripts}/webcore_base.css"). Urls
   * are added to {@link $scripts}.
   * @param string $url location of the file
   */
  public function add_script_file ($url)
  {
    if (! isset ($this->scripts) || ! in_array ($url, $this->scripts))
    {
      $this->scripts [] = $url;
    }
  }

  /**
   * Add a style sheet file to the header.
   * 'url' can include folder aliases (e.g. "{styles}/core/large.css"). Urls are
   * added to {@link $styles}.
   * @param string $url location of the file
   */
  public function add_style_sheet ($url)
  {
    if (! isset ($this->styles) || ! in_array ($url, $this->styles))
    {
      $this->styles [] = $url;
    }
  }

  /**
   * Add an alias to treat as an icon path.
   * {@link THEMED_PAGE}s will make sure to redirect these aliases to the theme-
   * specific location when resolved. The resource manager is used to
   * communicate the change and apply the theme settings to a path immediately.
   * @param RESOURCE_MANAGER $resource_manager
   * @see add_as_icon_listener_to()
   * @see refresh_icon_alias()
   * @param string $alias
   */
  public function add_icon_alias ($resource_manager, $alias)
  {
    $this->_icon_aliases [$alias] = $resource_manager;
    $resource_manager->refresh ($alias);
  }
  
  /**
   * "Listen" to changes in the given resource manager.
   * Called to make sure that changes in a resource manager have paths
   * updated to reflect page settings. The {@link THEMED_PAGE} uses these
   * settings to adjust icon paths according to theme.
   * @see add_icon_alias()
   * @param RESOURCE_MANAGER $resource_manager
   */
  public function add_as_icon_listener_to ($resource_manager)
  {
    include_once ('webcore/sys/callback.php');
    $resource_manager->add_listener (new CALLBACK_METHOD ('_on_alias_changed', $this));
  }
  
  /**
   * Add an expiration date to the header
   * @param integer $date A PHP timestamp
   */
  public function set_expire_date ($date)
  {
    $d = date ("D, d M Y H:i:s", $date);
    header ("Expires: $d GMT");
  }

  /**
   * Add the modification date to the header
   * @param integer $date A PHP timestamp
   */
  public function set_modified_date ($date)
  {
    $d = date ("D, d M Y H:i:s", $date);
    header ("Last-Modified: $d GMT");
  }

  /**
   * Disallows caching in the HTTP header.
   * Sets an expiry date in the past so the client reloads this page.
   */
  public function set_no_caching ()
  {
    header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
    header ("Pragma: no-cache");  // HTTP/1.0
  }
  /**
   * Flag this page as a printout.
   * The page renderer checks this flag to determine which stylesheets to load.
   */
  public function set_printable ()
  {
    $this->printable = true;
  }

  /**
   * Makes a renderer for this page.
   * Usually called automatically from {@link start_display()}; call this
   * function to create a renderer when building a custom page.
   * @return PAGE_RENDERER
   */
  public function make_renderer ()
  {
    $class_name = @$this->final_class_name (Custom_page_renderer);
    $error_occurred = ! class_exists ($class_name);
    $overridden = $class_name != Custom_page_renderer;
     
    if ($overridden && $error_occurred)
    {
      log_message ("[{$class_name}] is not a valid custom renderer.", Msg_type_warning, Msg_channel_system);
    }

    if (! $overridden || $error_occurred)
    {
      $class_name = $this->final_class_name ('PAGE_RENDERER', 'webcore/gui/page_renderer.php');
    }
      
    $this->assert (class_exists ($class_name), "[{$class_name}] is not a valid default renderer.", 'make_renderer', 'PAGE');

    return new $class_name ($this);
  }

  /**
   * Called after a path has been changed.
   * This page registers itself as a listener for the {@link ENVIRONMENT} 
   * and itself so that when a path is changed with {@link set_path()}, it can
   * ensure that icon paths are properly redirected by theme.
   * @param RESOURCE_MANAGER $resource_manager Affected resource manager.
   * @param string $alias Affected alias.
   * @param string $path New path.
   * @access private
   */
  public function _on_alias_changed ($resource_manager, $alias, $path)
  {
    if (isset ($this->_icon_aliases [$alias]))
    {
      $this->_on_icon_alias_changed ($resource_manager, $alias, $path);
    }
  }

  /**
   * Called after an icon path has been changed.
   * This event is called if an alias registered with {@link add_icon_alias()}
   * is changed.
   * @param RESOURCE_MANAGER $resource_manager Affected resource manager.
   * @param string $alias Affected alias.
   * @param string $path New path.
   * @access private
   */
  public function _on_icon_alias_changed ($resource_manager, $alias, $path)
  {
  }
  
  /**
   * Called on a fully-resolved URL before returning it.
   * If {@link $prefer_relative_urls} is True, the URL is made relative to the
   * current page.
   * @param string $url
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to {@link Force_root_on}.
   * @return string
   * @access private
   */
  protected function _finalize_url ($url, $root_override)
  {
    $Result = parent::_finalize_url ($url, $root_override);
    if ($this->prefer_relative_urls && $this->root_url && ! $this->_needs_root ($Result, $root_override))
    {
      $Result = path_between ($this->root_url, $Result, global_url_options ());
      if ($Result == '')
      {
        $Result = '.';
      }
    }
    return $Result;
  }

  /**
   * Called from {@link restore_root_behavior()}.
   * @return boolean
   * @access private
   */
  protected function _default_resolve_to_root ()
  {
    return ! $this->env->running_on_declared_host ();
  }
  
  /**
   * @var PAGE_RENDERER
   * @access private
   */
  protected $_renderer;

  /**
   * List of path aliases to treat as icon paths.
   * @var string[]
   * @access private
   */
  protected $_icon_aliases;
}