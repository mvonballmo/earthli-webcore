<?php

/**
 * @copyright Copyright (c) 2002-2007 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 2.8.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2007 Marco Von Ballmoos

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
require_once ('webcore/gui/grid.php');

/**
 * Displays {@link USER}s from a {@link QUERY}.
 * Used when creating user permissions to select a user.
 * @package webcore
 * @subpackage grid
 * @version 2.8.0
 * @since 2.2.1
 */
class USER_BROWSER_GRID extends STANDARD_GRID
{
  /**
   * @var string
   */
  public $object_name = 'User';

  /**
   * @var boolean
   */
  public $show_separator = false;

  /**
   * @var boolean
   */
  public $even_columns = false;

  /**
   * @var boolean
   */
  public $centered = true;

  /**
   * @var integer
   */
  public $padding = 1;

  /**
   * @var string
   */
  public $width = '';

  /**
   * @param CONTEXT $context
   */
  public function USER_BROWSER_GRID ($context)
  {
    GRID::GRID ($context);
    $this->_controls_renderer = $this->app->make_controls_renderer ();
  }

  /**
   * Draw JavaScripts used by this grid.
   * @access private
   */
  protected function _draw_scripts ()
  {
    parent::_draw_scripts ();
?>
  picker = new PICKER ();
  picker.register (Query_string.item ('fieldid'));
<?php
  }

  /**
   * @param USER $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
    echo $obj->title_as_link ();
    echo "</td>\n<td style=\"padding-left: 1em; text-align: center\">";
    echo $this->_controls_renderer->javascript_button_as_html ('Select', 'picker.select_value(\'' . $obj->title .'\')');
  }

  /**
   * @var FOLDER
   * @access private
   */
  protected $_folder;

  /**
   * @var CONTROLS_RENDERER
   * @access private
   */
  protected $_controls_renderer;
}
?>