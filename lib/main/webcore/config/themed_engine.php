<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage config
 * @version 3.4.0
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
include_once ('webcore/config/engine.php');

/**
 * A WebCore site engine that supports theming.
 * Create and initializes a {@link THEMED_PAGE} instead of a {@link PAGE}.
 * @package webcore
 * @subpackage config
 * @version 3.4.0
 * @since 2.7.0
 */
class THEMED_ENGINE extends ENGINE
{
  /**
   * If true, loads and applies the user theme.
   * Calls {@link THEMED_PAGE:: load_theme()} after calling {@link
   * _init_theme()} to retrieve the user-local values from a {@link COOKIE}. If
   * <code>False</code>, the values from {@link THEMED_PAGE::$default_theme} are
   * used.
   * @var boolean
   */
  public $use_local_theme = true;

  /**
   * Register plugins in {@link $classes} during initialization.
   * @access private
   */
  protected function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();
    $this->register_class ('PAGE', 'THEMED_PAGE', 'webcore/sys/themed_page.php');
  }

  /**
   * Called to set the initial default {@link THEME_OPTIONS}.
   * @param THEMED_PAGE $page
   * @access private
   */
  protected function _init_theme ($page)
  {
    $page->default_theme->main_CSS_file_name = '{themes}/ice';
    $page->default_theme->font_name_CSS_file_name = '{styles}fonts/raleway';
    $page->default_theme->font_size_CSS_file_name = '{styles}core/medium';
  }

  /**
   * Called immediately after creating a page.
   * @param ENVIRONMENT $env
   * @param PAGE $page
   * @access private
   */
  protected function _init_page ($env, $page)
  {
    parent::_init_page ($env, $page);
    $this->_init_theme ($page);
    
    /* Load the user's preferred theme from local storage (using a COOKIE).
     * Leave this out in order to force the 'default theme'.
     */

    if ($this->use_local_theme)
    {
      $page->load_theme ();
    }
  }
}

?>