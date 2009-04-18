<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage mail
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
require_once ('webcore/gui/renderer.php');

/**
 * Base class for all email rendering.
 * @package webcore
 * @subpackage mail
 * @version 3.1.0
 * @since 2.2.1
 */
class MAIL_RENDERER extends RENDERER
{
  /**
   * Sets up the application to generate an email.
   * Makes sure that absolute URLs are on and JavaScript is off.
   * @param MAIL_RENDERER_OPTIONS $options
   * @param MAIL_RENDERER_STATE $state
   * @access private
   */
  protected function _start_rendering ($options, &$state)
  {
    /* Save options to be restored later. */
   
    $state->saved_display_options = clone($this->page->display_options);
    $state->saved_template_options = clone($this->page->template_options); 
    $state->saved_show_interactive_option = $options->show_interactive;
    $state->saved_show_local_time_option = $this->env->date_time_toolkit->formatter->show_local_time;
    
    /* Apply required changes to global options. */
    
    $this->context->set_root_behavior (Force_root_on);
    $this->page->display_options->show_local_times = false;
    
    $options = $this->page->template_options;
    $options->header_visible = true;
    $options->footer_visible = true;
    $options->include_scripts = false;
    $options->show_login = false;
    $options->show_links = false;
    $options->check_browser = false;
    $options->close_logger = false;
    $options->show_statistics = false;
    $options->show_last_time_modified = false;
    $options->show_interactive = false;
    
    $this->env->date_time_toolkit->formatter->show_local_time = false;
  }
  
  /**
   * Restores application from previous call to {@link _start_rendering()}.
   * @param MAIL_RENDERER_OPTIONS $options
   * @param MAIL_RENDERER_STATE $state
   * @access private
   */
  protected function _finish_rendering ($options, $state)
  {
    $this->context->restore_root_behavior ();
    $this->page->display_options = $state->saved_display_options;
    $this->page->template_options = $state->saved_template_options;
    $this->env->date_time_toolkit->formatter->show_local_time = $state->saved_show_local_time_option;
    $options->show_interactive = $state->saved_show_interactive_option;
  }
}

/**
 * Stores the state of an application for a {@link MAIL_RENDERER}.
 * @package webcore
 * @subpackage mail
 * @version 3.1.0
 * @since 2.6.0 
 * @access private
 */
class MAIL_RENDERER_STATE
{
  /**
   * Copy of the page's display options.
   * Stored by {@link _start_rendering()} and restored by {@link _finish_rendering()}. 
   * @var PAGE_DISPLAY_OPTIONS
   */
  public $saved_display_options;

  /**
   * Copy of the page's template options.
   * Stored by {@link _start_rendering()} and restored by {@link _finish_rendering()}. 
   * @var PAGE_TEMPLATE_OPTIONS
   */
  public $saved_template_options;

  /**
   * Saved value for {@link MAIL_RENDERER_OPTIONS::$show_interactive}. 
   * Stored by {@link _start_rendering()} and restored by {@link _finish_rendering()}. 
   * @var boolean
   */
  public $saved_show_local_time_option;

  /**
   * Saved value for {@link MAIL_RENDERER_OPTIONS::$show_interactive}. 
   * Stored by {@link _start_rendering()} and restored by {@link _finish_rendering()}. 
   * @var boolean
   */
  public $saved_show_interactive_option;
}

?>