<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
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
require_once ('webcore/sys/page.php');
require_once ('webcore/obj/webcore_object.php');

/**
 * The set of values used for the current user's theme.
 * Holds some values from the database and other selected locally. Does
 * not need to go to the database to style a {@link PAGE}.
 * @package webcore
 * @subpackage sys
 * @version 3.1.0
 * @since 2.3.0
 */
class THEME_SETTINGS extends WEBCORE_OBJECT
{
  /**
   * Id of theme in database.
   * Uniquely identifies the theme and is used to determine whether it has changed.
   * @var integer
   */
  public $id = 0;

  /**
   * User-friendly title of the theme.
   * This is loaded and stored so that the current theme name can be displayed.
   * @var string
   */
  public $title = 'Default';

  /**
   * Name of the class used to render the page layout.
   * This governs where the basic page elements (like header, footer, etc.) are
   * arranged. Not all styles can be applied to all renderers successfully (some
   * renderers will arrange page elements so that background/foreground colors may
   * be the same in some places.
   * @var string
   */
  public $renderer_class_name = '';

  /**
   * Name of the main style sheet.
   * This governs which theme-specific stylesheet is used in a page.
   * @var string
   */
  public $main_CSS_file_name = '{themes}/ice';

  /**
   * Name of the font-face stylesheet to use.
   * The font is completely separated from the stylesheet, so that users can mix and match font-styles
   * with different themes.
   * @var string
   */
  public $font_name_CSS_file_name = '{styles}fonts/verdana';

  /**
   * Name of the font-size stylesheet to use.
   * The font is completely separated from the stylesheet, so that users can mix and match font-sizes
   * with different themes (font size is nice to adjust to suit their needs).
   * @var string
   */
  public $font_size_CSS_file_name = '{styles}core/small';

  /**
   *  Path to the icons folder.
   * This is always relative to the page's icon folder, specified in {@link PAGE_FOLDER_NAMES}.
   * @var string
   */
  public $icon_set = 'webcore_png';

  /**
   *  Default extension to use for icons.
   * Icons should, in general, not specify an extension, so that this default can be appended
   * and used by the page. This allows the user to decide which kind of icons to use (e.g. Win32 IE
   * users will default to GIF because PNG is not properly supported).
   * @var string
   */
  public $icon_extension = 'png';

  /**
   * Apply theme to form elements (controls)?
   * If this is false, then an additional CSS style sheet is loaded with each page, that applies
   * theme-specific styles for controls. Otherwise, the browser defaults are used. It can be good
   * to eliminate form styling so that browser page controls look uniform with the browser and/or
   * operating system.
   */
  public $dont_apply_to_forms = false;

  /**
   * Load theme from a client-side storage (cookie).
   * @param STORAGE $storage
   */
  public function load_from_client ($storage)
  {
    $this->id = $storage->value ('theme_id');
    $this->title = $storage->value ('theme_title');
    $this->renderer_class_name = $storage->value ('theme_renderer');
    $this->main_CSS_file_name = $storage->value ('theme_name');
    $this->font_name_CSS_file_name = $storage->value ('theme_font_name');
    $this->font_size_CSS_file_name = $storage->value ('theme_font_size');
    $this->icon_set = $storage->value ('theme_icon_set');
    $this->icon_extension = $storage->value ('theme_icon_extension');
    $this->dont_apply_to_forms = $storage->value ('theme_dont_apply_to_forms');
  }

  /**
   * Store theme to a client-side storage (cookie).
   * @param STORAGE $storage
   */
  public function store_to_client ($storage)
  {
    $storage->set_value ('theme_id', $this->id);
    $storage->set_value ('theme_title', $this->title);
    $storage->set_value ('theme_name', $this->main_CSS_file_name);
    $storage->set_value ('theme_renderer', $this->renderer_class_name);
    $storage->set_value ('theme_font_name', $this->font_name_CSS_file_name);
    $storage->set_value ('theme_font_size', $this->font_size_CSS_file_name);
    $storage->set_value ('theme_icon_extension', $this->icon_extension);
    $storage->set_value ('theme_icon_set', $this->icon_set);
    $storage->set_value ('theme_dont_apply_to_forms', $this->dont_apply_to_forms);
  }

  public function clear ()
  {
    $this->id = 0;
    $this->renderer_class_name = '';
    $this->main_CSS_file_name = '';
    $this->font_name_CSS_file_name = '';
    $this->font_size_CSS_file_name = '';
    $this->icon_set = '';
    $this->icon_extension = '';
    $this->dont_apply_to_forms = false;
  }

  /**
   * Load any empty values from the default theme.
   * @param THEME_SETTINGS $default
   */
  public function load_missing_values_from ($default)
  {
    if (! $this->main_CSS_file_name)
    {
      $this->title = $default->title;
      $this->main_CSS_file_name = $default->main_CSS_file_name;
      $this->renderer_class_name = $default->renderer_class_name;
    }

    if (! $this->font_name_CSS_file_name)
    {
      $this->font_name_CSS_file_name = $default->font_name_CSS_file_name;
    }

    if (! $this->font_size_CSS_file_name)
    {
      $this->font_size_CSS_file_name = $default->font_size_CSS_file_name;
    }

    if (! $this->icon_set)
    {
      $this->icon_set = $default->icon_set;
    }

    if (! $this->icon_extension)
    {
      $this->icon_extension = $default->icon_extension;
    }
  }

  /**
   * Are these objects the same?
   * @param THEME_SETTINGS $other
   * @return boolean
   */
  public function equals ($other)
  {
    return (($this->main_CSS_file_name == $other->main_CSS_file_name)
            && ($this->font_name_CSS_file_name == $other->font_name_CSS_file_name)
            && ($this->font_size_CSS_file_name == $other->font_size_CSS_file_name)
            && ($this->dont_apply_to_forms == $other->dont_apply_to_forms));
  }

  /**
   * Copy all values from 'other'.
   * @param THEME_SETTINGS $other
   */
  public function copy_from ($other)
  {
    parent::copy_from($other);
    $this->id = $other->id;
    $this->title = $other->title;
    $this->main_CSS_file_name = $other->main_CSS_file_name;
    $this->renderer_class_name = $other->renderer_class_name;
  }
}

/**
 * Options for setting a {@link THEME}.
 * @package webcore
 * @subpackage sys
 * @version 3.1.0
 * @since 2.2.1
 */
class THEME_OPTIONS
{
  /**
   * How many days should the theme be stored?
   * After this many days, the theme is flushed from the user's browser and reverts to default.
   * This only happens if user-themes are supported by calling {@link load_theme()}.
   * @var integer
   */
  public $duration = 365;

  /**
   * In which database table are themes stored?
   * Theme information is stored in a database table, but is only accessed when the user elects
   * to change their theme settings.
   * @var string
   */
  public $table_name = 'themes';

  /**
   * A list of the font name themes available.
   */
  public function font_names ()
  {
    if (! isset ($this->_font_names))
    {
      $this->_init_font_names ();
    }
    return $this->_font_names;
  }

  /**
   * A list of the font name themes available.
   */
  public function font_sizes ()
  {
    if (! isset ($this->_font_sizes))
    {
      $this->_init_font_sizes ();
    }
    return $this->_font_sizes;
  }

  /**
   * Add a theme font name.
   * Each item maps a font-name to a stylesheet (e.g. 'Verdana' => '{styles}
   * fonts/verdana').
   * @param string $title
   * @param string $file_name
   */
  public function add_font_name ($title, $file_name)
  {
   $this->_font_names [$title] = $file_name;
  }

  /**
   * Add a theme font size.
   * Each item maps a font-name to a stylesheet (e.g. 'Verdana' => '{styles}
   * fonts/verdana').
   * @param string $title
   * @param string $file_name
   */
  public function add_font_size ($title, $file_name)
  {
    $this->_font_sizes [$title] = $file_name;
  }

  /**
   * Initialize the initial list of font names.
   * Called from {@link font_names()}.
   * @access private
   */
  protected function _init_font_names ()
  {
    $this->add_font_name ('Arial', '{styles}fonts/arial');
    $this->add_font_name ('Courier', '{styles}fonts/courier');
    $this->add_font_name ('Geneva', '{styles}fonts/geneva');
    $this->add_font_name ('Georgia', '{styles}fonts/georgia');
    $this->add_font_name ('Times', '{styles}fonts/times');
    $this->add_font_name ('Trebuchet', '{styles}fonts/trebuchet');
    $this->add_font_name ('Verdana', '{styles}fonts/verdana');
  }

  /**
   * Initialize the initial list of font sizes.
   * Called from {@link font_sizes()}.
   * @access private
   */
  protected function _init_font_sizes ()
  {
    $this->add_font_size ('Small', '{styles}core/small');
    $this->add_font_size ('Medium', '{styles}core/medium');
    $this->add_font_size ('Large', '{styles}core/large');
  }

  /**
   * @var array[string,string]
   * @access private
   */
  protected $_font_names;

  /**
   * @var array[string,string]
   * @access private
   */
  protected $_font_sizes;
}

/**
 * Provides automated theme support.
 * This means that the page header will automatically load style sheets and use
 * a renderer that draws the page with the chosen {@link THEME}.
 * @package webcore
 * @subpackage sys
 * @version 3.1.0
 * @since 2.2.1
 */
class THEMED_PAGE extends PAGE
{
  /**
   * calculated theme settings.
   * @var THEME_SETTINGS
   */
  public $theme;

  /**
   * theme settings retrieved from storage.
   * @var THEME_SETTINGS
   */
  public $stored_theme;

  /**
   * default theme settings.
   * used with 'stored_theme' to create 'theme'
   * @var THEME_SETTINGS
   */
  public $default_theme;

  /**
   * @var THEME_OPTIONS
   */
  public $theme_options;

  /**
   * False is the theme could not be loaded.
   * If the storage mechanism changes for themes, {@link validate_theme()} makes
   * changes to the loaded theme. If changes are made, this value is set to
   * False.
   * @var boolean
   */
  public $stored_theme_is_valid = true;

  /**
   * @param ENVIRONMENT $env Global environment.
   */
  public function __construct ($env)
  {
    parent::__construct ($env);

    $class_name = $this->final_class_name ('THEME_OPTIONS');
    $this->theme_options = new $class_name ();

    $this->theme = new THEME_SETTINGS ($this);
    $this->stored_theme = new THEME_SETTINGS ($this);
    $this->default_theme = new THEME_SETTINGS ($this);

    $browser = $env->browser ();
    if (! $browser->supports (Browser_alpha_PNG))
    {
      $this->default_theme->icon_extension = 'gif';
      $this->default_theme->icon_set = 'webcore_gif_silver';
    }

    if ($browser->is (Browser_ie))
    {
      $this->default_theme->dont_apply_to_forms = true;
    }
  }

  /**
   * Add classes to the {@link $classes} object factory.
   * @access private
   */
  protected function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();

    $this->register_class ('CONTEXT_STORAGE_OPTIONS', 'PAGE_STORAGE_OPTIONS');
  }

  /**
   * Adjust theme settings to optimize printing.
   */
  public function set_printable ()
  {
    parent::set_printable ();
    $this->theme->main_CSS_file_name = '{' . Folder_name_themes . '}/printable';
    $this->theme->font_name_CSS_file_name = '{' . Folder_name_styles . '}fonts/georgia';
  }

  /**
   * Fill in values for {@link $stored_theme} from the client.
   * Also sets the value of {@link $stored_theme_is_valid}.
   */
  public function load_theme ()
  {
    $this->storage->load_multiple_values ($this->storage_options->theme_settings_name, false);
    $this->stored_theme->load_from_client ($this->storage);
    $this->theme = $this->stored_theme;

    $old_theme = $this->theme;
    $this->validate_theme ();
    $this->stored_theme_is_valid = $old_theme->equals ($this->theme);

    $this->theme->load_missing_values_from ($this->default_theme);

    if ($this->theme->renderer_class_name)
    {
      $renderer_name = strtolower ($this->theme->renderer_class_name);
      $this->register_class (Custom_page_renderer, $this->theme->renderer_class_name, 'plugins/renderers/' . $renderer_name . '.php');
    }
  }

  /**
   * Verify the loaded theme.
   * If the theme format has changed since the user's local settings were stored (in the cookie),
   * the theme is marked as invalid and the default theme is used. The default renderer displays
   * a message to the user to indicate that the theme was reverted.
   */
  public function validate_theme ()
  {
    if (strpos ($this->theme->main_CSS_file_name, '/') === false)
    {
      $this->theme->main_CSS_file_name = '';
    }
    if (strpos ($this->theme->font_name_CSS_file_name, '/') === false)
    {
      $this->theme->font_name_CSS_file_name = '';
    }
    if (strpos ($this->theme->font_size_CSS_file_name, '/') === false)
    {
      $this->theme->font_size_CSS_file_name = '';
    }
  }

  /**
   * All themes for this page.
   * @return THEME_QUERY
   */
  public function theme_query ()
  {
    return $this->make_object ('theme_query', 'THEME_QUERY', 'webcore/db/theme_query.php');
  }

  /**
   * Set a new stored theme name.
   * @param string $id Name of the new theme (if empty, resets value to default)
   * */
  public function set_theme_main ($id)
  {
    if ($id != $this->stored_theme->id)
    {
      if ($id)
      {
        $theme_query = $this->theme_query ();
        $theme = $theme_query->object_at_id ($id);
      }

      if (isset ($theme))
      {
        $this->storage->expire_in_n_days ($this->theme_options->duration);

        $this->theme->copy_from ($theme);
        $this->stored_theme->copy_from ($theme);

        /* optional style sheets (maintain default if not specified) */

        $this->_apply_theme_setting ('font_name_CSS_file_name', $theme->font_name_CSS_file_name, false);
        $this->_apply_theme_setting ('font_size_CSS_file_name', $theme->font_size_CSS_file_name, false);

        /* optional icon styles (maintain default if not specified) */

        $this->_apply_theme_setting ('icon_set', $theme->icon_set, false);
        $this->_apply_theme_setting ('icon_extension', $theme->icon_extension, false);

        $this->store_to_client ();
      }
      else
      {
        $this->theme->clear ();
        $this->theme->load_missing_values_from ($this->default_theme);
        $this->stored_theme->clear ();
        $this->storage->clear_value ($this->storage_options->theme_settings_name);
      }
    }

    $this->stored_theme_is_valid = true;
  }

  /**
   * Set a new stored theme font name.
   * @param string $name Name of the new theme font (if empty, resets value to default)
   */
  public function set_theme_font_name ($url)
  {
    $this->_apply_theme_setting ('font_name_CSS_file_name', $url);
  }

  /**
   * Set a new stored theme font size.
   * @param string $size Name of the new theme size (if empty, resets value to default)
   */
  public function set_theme_font_size ($url)
  {
    $this->_apply_theme_setting ('font_size_CSS_file_name', $url);
  }

  /**
   * Set whether to apply the theme to form controls.
   * @param string $value
   */
  public function set_theme_dont_apply_to_forms ($value)
  {
    $this->_apply_theme_setting ('dont_apply_to_forms', $value);
  }

  /**
   * Save the current theme settings to the client.
   * @access private
   */
  public function store_to_client ()
  {
    $this->storage->expire_in_n_days ($this->theme_options->duration);
    $this->storage->start_multiple_value ($this->storage_options->theme_settings_name);
    $this->stored_theme->store_to_client ($this->storage);
    $this->storage->finish_multiple_value ();
  }

  /**
   * Apply a single setting to the current theme.
   * Apply the setting in the stored theme and update the current theme,
   * reverting to the default theme if the value is empty. Finally, store the
   * setting on the client (in the cookie).
   * @param string $setting_name Name of a property of {@link THEME_SETTINGS}.
   * @param string $value Value to set.
   * @param boolean $store_immediately
   * @access private
   */
  protected function _apply_theme_setting ($setting_name, $value, $store_immediately = true)
  {
    if ($value != $this->stored_theme->$setting_name)
    {
      $this->stored_theme->$setting_name = $value;
      if ($value != '')
      {
        $this->theme->$setting_name = $value;
      }
      else
      {
        $this->theme->$setting_name = $this->default_theme->$setting_name;
      }

      if ($store_immediately)
      {
        $this->store_to_client ();
      }
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
    parent::_on_icon_alias_changed ($resource_manager, $alias, $path);
    if (isset ($this->default_theme))
    {
      $resource_manager->add_to_path ($alias, $this->default_theme->icon_set);
      $resource_manager->set_extension ($alias, $this->default_theme->icon_extension);
    }
  }
}

?>