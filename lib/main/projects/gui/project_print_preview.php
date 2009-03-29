<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.0.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

/** */
require_once ('webcore/gui/print_preview.php');

/**
 * Handle printing entries in an {@link ALBUM}.
 * @package projects
 * @subpackage gui
 * @version 3.0.0
 * @since 1.7.0
 * @access private
 */
class PROJECT_PRINT_RENDERER_OPTIONS extends PRINT_RENDERER_OPTIONS
{
  /**
   * Show {@link CHANGE}s associated with {@link JOB}s?
   * @var boolean
   */
  public $show_changes = TRUE;
  /**
   * Show files when showing {@link CHANGE}s?
   * @var boolean
   */
  public $show_files = TRUE;

  /**
   * Load values from the HTTP request.
   */
  function load_from_request ()
  {
    parent::load_from_request ();
    $this->show_changes = read_var ('show_changes');
    $this->show_files = read_var ('show_files');
  }
}

/**
 * Handle printing {@link CHANGE}s and {@link JOB}s in {@link PROJECT}s.
 * @package projects
 * @subpackage gui
 * @version 3.0.0
 * @since 1.4.1
 */
class PROJECT_PRINT_PREVIEW extends PRINT_PREVIEW
{
  /**
   * @return PROJECT_PRINT_RENDERER_OPTIONS
   * @access private
   */
  function _make_print_options ()
  {
    return new PROJECT_PRINT_RENDERER_OPTIONS ($this->app);
  }
}

?>