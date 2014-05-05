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
require_once ('webcore/obj/webcore_object.php');
require_once ('webcore/sys/date_time.php');
require_once ('webcore/sys/files.php');
require_once ('webcore/config/context_config.php');
require_once ('webcore/config/text_config.php');
require_once ('webcore/sys/resolver.php');

/**
 * Base class for {@link PAGE}s and {@link APPLICATION}s.
 * @package webcore
 * @subpackage sys
 * @version 3.5.0
 * @since 2.2.1
 */
class CONTEXT extends RESOLVER
{
  /**
   * @var ENVIRONMENT
   */
  public $env;

  /**
   * @var COOKIE
   */
  public $cookie;

  /**
   * @var DATABASE
   */
  public $database;

  /**
   * Implementation-independent per-client storage.
   * Points to the {@link $cookie}, by default. Descendents can implement other
   * storage providers.
   * @var CLIENT_STORAGE
   */
  public $storage;

  /**
   * Reference to the {@link ENVIRONMENT::$date_time_toolkit}.
   * Provided for convenience.
   * @var DATE_TIME_TOOLKIT
   */
  public $date_time_toolkit;

  /**
   * Basic mailing options (used when no subscriber is given)
   * @var CONTEXT_MAIL_OPTIONS
   */
  public $mail_options;

  /**
   * Settings for the shared database.
   * @var CONTEXT_DATABASE_OPTIONS
   */
  public $database_options;

  /**
   * Conversion settings for forms and text content.
   * @var TEXT_OPTIONS
   */
  public $text_options;

  /**
   * Display settings for common UI elements.
   * @var CONTEXT_DISPLAY_OPTIONS
   */
  public $display_options;

  /**
   * Controls how uploaded files are handled.
   * @var CONTEXT_UPLOAD_OPTIONS
   */
  public $upload_options;

  /**
   * Aliases uses when storing data to cookie or session.
   * @var CONTEXT_STORAGE_OPTIONS
   */
  public $storage_options;

  /**
   * @var EXCEPTION_HANDLER
   */
  public $exception_handler;

  /**
   * Is this context a page (singleton)?
   * @var boolean
   */
  public $is_page = false;

  /**
   * Text to display as having been searched.
   * @var string
   */
  public $search_text = '';

  /**
   * @param ENVIRONMENT $env Global environment.
   */
  public function __construct ($env)
  {
    $this->env = $env;
    parent::__construct ();

    $this->date_time_toolkit = $this->env->date_time_toolkit;

    $class_name = $this->final_class_name ('COOKIE', 'webcore/util/cookie.php');
    $this->cookie = new $class_name ();
    $this->cookie->path = '/';

    $this->storage = $this->cookie;

    $class_name = $this->final_class_name ('CONTEXT_DISPLAY_OPTIONS');
    $this->display_options = new $class_name ($this);
    $class_name = $this->final_class_name ('CONTEXT_MAIL_OPTIONS');
    $this->mail_options = new $class_name ();
    $class_name = $this->final_class_name ('CONTEXT_DATABASE_OPTIONS');
    $this->database_options = new $class_name ();
    $class_name = $this->final_class_name ('CONTEXT_UPLOAD_OPTIONS');
    $this->upload_options = new $class_name ();
    $class_name = $this->final_class_name ('CONTEXT_STORAGE_OPTIONS');
    $this->storage_options = new $class_name ();

    $this->text_options = global_text_options ();
  }

  /**
   * Add classes to the {@link $classes} object factory.
   * @access private
   */
  protected function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();
    $this->register_class ('FILE_TYPE_MANAGER', 'INI_FILE_TYPE_MANAGER', 'webcore/util/file_type_manager.php');
    $this->register_class ('MAIL_PROVIDER', 'DEFAULT_PHP_MAIL_PROVIDER', 'webcore/mail/default_php_mail_provider.php');
    $this->register_class ('TEST_SUITE', 'INI_TEST_SUITE', 'webcore/util/test_suite.php');
    $this->register_class ('CAPTCHA', 'NUMERIC_CAPTCHA', 'webcore/util/captcha.php');
  }

  /**
   * Resources to use for resolving paths.
   * Returns 'this' by default. Use this function to retrieve the most specific
   * resources with which to resolve paths or files.
   * @return RESOURCE_MANAGER
   */
  public function resources ()
  {
    return $this;
  }

  /**
   * Call this to make sure {@link $database} is initialized.
   */
  public function ensure_database_exists ()
  {
    if (! isset ($this->database))
    {
      $class_name = $this->final_class_name ('DATABASE', 'webcore/db/database.php');
      $this->database = new $class_name ($this->env);
      $this->database->Host = $this->database_options->host;
      $this->database->Database = $this->database_options->name;
      $this->database->User = $this->database_options->user_name;
      $this->database->Password = $this->database_options->password;
    }
  }

  /**
   * Returns the name and version of the context.
   * @var boolean $as_html Return HTML-formatted if <code>true</code>.
   * @return string
   */
  public function description ($as_html = true)
  {
    return $this->env->description ($as_html);
  }

  /**
   * Returns the absolute location of this context.
   * @return string
   */
  public function url ()
  {
    return $this->env->url ();
  }

  /**
   * Should renderers/grids/etc. use DHTML?
   * @return boolean
   */
  public function dhtml_allowed ()
  {
    return $this->display_options->use_DHTML && $this->env->browser ()->supports (Browser_DOM_1);
  }

  /**
   * Should dates use JavaScript to render local times?
   * @return boolean
   */
  public function local_times_allowed ()
  {
    return $this->display_options->show_local_times;
  }

  /**
   * A date which uses this application's formatter.
   * @param integer $time Can be either a timestamp or an ISO-formatted time
   * @param string $type Can be either Date_time_php or Date_time_iso
   * @return DATE_TIME
   */
  public function make_date_time ($time = 0, $type = null)
  {
    $Result = new DATE_TIME ($time, $type);
    $this->date_time_toolkit->formatter->show_local_time = $this->local_times_allowed ();
    $Result->use_toolkit ($this->date_time_toolkit);
    return $Result;
  }

  /**
   * Set the text to highlight in this context.
   * All HTML formatters will have these words automatically set so that they are highlighted.
   * @see html_text_formatter ()
   * @see html_title_formatter ()
   * @param string $text
   */
  public function set_search_text ($text)
  {
    $this->search_text = $text;
  }

  /**
   * Customizable title formatter.
   * This generates all forms of output for titles, including plain text and as HTML text and link.
   * @return TITLE_FORMATTER
   */
  public function title_formatter ()
  {
    return $this->make_object ('title_formatter', 'TITLE_FORMATTER', 'webcore/util/title_formatter.php');
  }

  /**
   * Customizable HTML formatter.
   * This formatter is specialized for displaying blocks of HTML text and handles newlines and
   * many tags like quoting, images, linking, etc.
   * @return HTML_TEXT_MUNGER
   */
  public function html_text_formatter ()
  {
    $Result = $this->make_object ('html_text_formatter', 'HTML_TEXT_MUNGER', 'webcore/util/html_munger.php');
    $Result->highlighted_words = $this->search_text;

    return $Result;
  }

  /**
   * Customizable HTML formatter.
   * This is specialized for displaying titles in the page.
   * @return HTML_TITLE_MUNGER
   */
  public function html_title_formatter ()
  {
    $Result = $this->make_object ('html_title_formatter', 'HTML_TITLE_MUNGER', 'webcore/util/html_munger.php');
    $Result->highlighted_words = $this->search_text;

    return $Result;
  }

  /**
   * Customizable plain text formatter.
   * @return PLAIN_TEXT_MUNGER
   */
  public function plain_text_formatter ()
  {
    return $this->make_object ('plain_text_formatter', 'PLAIN_TEXT_MUNGER', 'webcore/util/plain_text_munger.php');
  }

  /**
   * Customizable plain text formatter.
   * This is specialized for displaying titles in the page.
   * @return PLAIN_TEXT_TITLE_MUNGER
   */
  public function plain_text_title_formatter ()
  {
    return $this->make_object ('plain_text_title_formatter', 'PLAIN_TEXT_TITLE_MUNGER', 'webcore/util/plain_text_munger.php');
  }

  /**
   * Customizable tag stripper.
   * This is specialized for stripping titles of tags.
   * @return MUNGER_STRIPPER
   */
  public function title_stripper ()
  {
    return $this->make_object ('munger_strpper', 'MUNGER_DEFAULT_TITLE_STRIPPER', 'webcore/util/munger_stripper.php');
  }

  /**
   * Create a validator for {@link MUNGER} tags.
   * Used by {@link FORM}s to validate text input. The form can request either a single-line or a multi-line
   * validator (single-line generally supports fewer tags).
   * @param string $type Can be {@link Tag_validator_single_line} or {@link Tag_validator_multi_line}.
   * @return MUNGER_VALIDATOR
   * @throws UNKNOWN_VALUE_EXCEPTION
   */
  public function make_tag_validator ($type)
  {
    switch ($type)
    {
      case Tag_validator_single_line:
        return $this->make_object ($type, 'MUNGER_DEFAULT_TITLE_VALIDATOR', 'webcore/util/munger_validator.php');
      case Tag_validator_multi_line:
        return $this->make_object ($type, 'MUNGER_DEFAULT_TEXT_VALIDATOR', 'webcore/util/munger_validator.php');
      default:
        throw new UNKNOWN_VALUE_EXCEPTION($type);
    }
  }

  /**
   * Full path to a config file.
   * Uses the file in the 'extensions' folder, if it exists. Uses the default if not.
   * @param string $file_name
   * @return string
   */
  public function config_file_name ($file_name)
  {
    $sep = $this->env->file_options->path_delimiter;
    $extension_path = $this->env->library_path . 'plugins' . $sep . 'config' . $sep;
    $full_file_name = $extension_path . $file_name;
    if (file_exists ($full_file_name))
    {
      return $full_file_name;
    }
    $lib_path = $this->env->library_path . 'webcore' . $sep . 'config' . $sep;
    $full_file_name = $lib_path . $file_name;
    if (file_exists ($full_file_name))
    {
      return $full_file_name;
    }

    log_message("[$file_name] was not found in [$extension_path;$lib_path].", Msg_type_warning, Msg_channel_system);
    
    return '';
  }

  /**
   * Returns information on supported file types.
   * @return FILE_TYPE_MANAGER
   */
  public function file_type_manager ()
  {
    return $this->make_object ('file_type_manager', 'FILE_TYPE_MANAGER', 'webcore/util/file_type_manager.php');
  }

  /**
   * @return MAIL_PROVIDER
   */
  public function make_mail_provider ()
  {
    $class_name = $this->final_class_name ('MAIL_PROVIDER', 'webcore/mail/mail_provider.php');
    /** @var $Result MAIL_PROVIDER */
    $Result = new $class_name ($this);

    $opts = $this->mail_options;
    if ($opts->logging_enabled)
    {
      $class_name = $this->final_class_name ('FILE_LOGGER', 'webcore/log/file_logger.php');
      /** @var $logger FILE_LOGGER */
      $logger = new $class_name ();
      $logger->set_file_name ($this->env->resolve_file ($opts->log_file_name));

      if ($this->env->debugging)
      {
        $logger->set_enabled (Msg_type_all);
      }
      else
      {
        $logger->set_enabled (Msg_type_all - Msg_type_all_debug);
        $logger->set_channel_enabled (Msg_channel_publisher, Msg_type_all - Msg_type_all_debug);
        $logger->set_channel_enabled (Msg_channel_mail, Msg_type_all - Msg_type_all_debug);
      }

      $Result->logs->set_logger ($logger);
    }

    return $Result;
  }

  /**
   * Return an object to draw the given form.
   * @param FORM $form
   * @return FORM_RENDERER
   */
  public function make_form_renderer ($form)
  {
    $class_name = $this->final_class_name ('FORM_RENDERER', 'webcore/forms/form_renderer.php');
    return new $class_name ($form);
  }

  /**
   * Return an object to draw controls.
   * @return CONTROLS_RENDERER
   */
  public function make_controls_renderer ()
  {
    return $this->make_object ('control_renderer', 'CONTROLS_RENDERER', 'webcore/forms/controls_renderer.php');
  }

  /**
   * Return an object to draw boxes and columns.
   * @return BOX_RENDERER
   */
  public function make_box_renderer ()
  {
    return $this->make_object ('box_renderer', 'BOX_RENDERER', 'webcore/gui/box_renderer.php');
  }

  /**
   * Return an object to draw menus.
   * @see make_menu()
   * @return MENU_RENDERER
   */
  public function make_menu_renderer ()
  {
    return $this->make_object ('menu_renderer', 'MENU_RENDERER', 'webcore/gui/menu_renderer.php');
  }

  /**
   * Return an object to draw a newsfeed menu.
   * @see make_menu()
   * @return MENU_RENDERER
   */
  public function make_newsfeed_menu_renderer ()
  {
    $Result = $this->make_menu_renderer ();
    $Result->set_size (Menu_size_minimal);
    $Result->content_mode = Menu_show_as_buttons | Menu_show_icon | Menu_show_title;
    $Result->trigger_title = 'Newsfeeds';
    $Result->trigger_icon = "{icons}indicators/newsfeed_rss";
    $Result->trigger_button_css_class = 'newsfeed';
    
    return $Result;
  }
  
  /**
   * Return an object to draw trees.
   * @return TREE
   */
  public function make_tree_renderer ()
  {
    if ($this->dhtml_allowed ())
    {
      return $this->make_object ('tree_renderer', 'DYNAMIC_TREE', 'webcore/gui/dynamic_tree.php');
    }

    return $this->make_object ('tree_renderer', 'STATIC_TREE', 'webcore/gui/static_tree.php');
  }

  /**
   * Return a manager for menus.
   * @see make_menu_renderer()
   * @return MENU
   */
  public function make_menu ()
  {
    return $this->make_object ('menu', 'MENU', 'webcore/gui/menu.php');
  }

  /**
   * Return a command for use by {@link COMMANDS}.
   * This is an optimization to avoid resolving the class name multiple times.
   * @return COMMAND
   */
  public function make_command ()
  {
    return $this->make_object ('command', 'COMMAND', 'webcore/cmd/commands.php');
  }

  /**
   * Return a layer that can be shown/hidden with DHTML.
   * Deliberately creates a copy of the singleton instead of a reference.
   * @param string $name Name for the layer.
   * @return LAYER
   */
  public function make_layer ($name = '')
  {
    $Result = $this->make_object ('layer', 'LAYER', 'webcore/gui/layer.php');
    $Result->name = $name;
    return $Result;
  }

  /**
   * Return an object to format HTML or CSS tags.
   * @param string $type Can be {@link Tag_builder_css} or {@link Tag_builder_html}.
   * @see HTML_TAG_BUILDER
   * @see CSS_TAG_BUILDER
   * @return CSS_STYLE_BUILDER|HTML_TAG_BUILDER
   * @throws UNKNOWN_VALUE_EXCEPTION
   */
  public function make_tag_builder ($type)
  {
    switch ($type)
    {
      case Tag_builder_css:
        return $this->make_object ($type, 'CSS_STYLE_BUILDER', 'webcore/util/tags.php');
      case Tag_builder_html:
        return $this->make_object ($type, 'HTML_TAG_BUILDER', 'webcore/util/tags.php');
      default:
        throw new UNKNOWN_VALUE_EXCEPTION($type);
    }
  }

  /**
   * Adds a message to the output stream.
   * @param string $message The message to display
   * @param string $type The type of message; can be 'error', 'warning' and 'info'
   */
  public function show_message ($message, $type = 'error')
  {
    echo $this->get_begin_message ($type) . $message . $this->get_end_message();
  }

  /**
   * Get the beginning of a message tag formatted for HTML output.
   * @param string $type The type of message; can be 'error', 'warning' and 'info'
   * @param string $tag_name
   * @return string
   */
  public function get_begin_message ($type = 'error', $tag_name = 'p')
  {
    $icon_url = '{icons}indicators/' . $type;
    if ($type == 'info')
    {
      $type = 'caution';
    }

    $sub_tag_name = $tag_name == 'p' ? 'span' : 'div';

    $Result = '<' . $tag_name . ' class="' . $type . '">' . $this->_get_start_icon_container($sub_tag_name, $icon_url, Sixteen_px);

    if ($sub_tag_name == 'span')
    {
      $Result .= '</span><span class="caption">';
    }

    return $Result;
  }

  /**
   * Get a message formatted for HTML output.
   * @param string $tag_name
   * @return string
   */
  public function get_end_message ($tag_name = 'p')
  {
    if ($tag_name == 'p')
    {
      return '</span></p>';
    }

    return '</div></' . $tag_name . '>';
  }

  public function start_icon_container($icon_url, $size)
  {
    echo $this->_get_start_icon_container('div', $icon_url, $size);
  }

  public function finish_icon_container()
  {
    echo '</div>';
  }

  public function start_icon_wrapper($icon_url, $size)
  {
    echo $this->_get_start_icon_container('span', $icon_url, $size);
  }

  public function finish_icon_wrapper()
  {
    echo '</span>';
  }

  public function get_text_with_icon ($icon_url, $text, $size)
  {
    if ($icon_url)
    {
      $Result = $this->_get_start_icon_container('span', $icon_url, $size) . '</span>';

      if ($text)
      {
        $Result .= "<span class=\"caption\">$text</span>";
      }

      return $Result;
    }
    else
    {
      return $text;
    }
  }

  private function _get_start_icon_container($tag_name, $icon_url, $size)
  {
    $expanded_icon_url = $this->get_icon_url($icon_url, $size);
    switch ($size)
    {
      case Fifteen_px:
        $class = 'fifteen';
        break;
      case Sixteen_px:
        $class = 'sixteen';
        break;
      case Twenty_px:
        $class = 'twenty';
        break;
      case Thirty_two_px:
        $class = 'thirty-two';
        break;
      case Fifty_px:
        $class = 'fifty';
        break;
      case One_hundred_px:
        $class = 'one-hundred';
        break;
      default:
        throw new UNKNOWN_VALUE_EXCEPTION($size);
    }

    return '<' . $tag_name . ' class="icon ' . $class . '" style="background-image: url(' . $expanded_icon_url . ')">';
  }

  /**
   * Internal flag for title formatting.
   * The set of accepted tags for which the title formatter was built is stored here. When a title
   * formatter is requested, the current accepted tags are compared to those used to generate the
   * current formatter. If they differ, a new formatter is built. This allows users to change the
   * accepted tags on the fly.
   * @var string
   */
  protected $_accepted_tags = '';
}